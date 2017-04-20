<?php
/**
 * 		订单售后服务层
 */
class order_server_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('order/order_server');
		$this->table_return = $this->load->table('order/order_return');
		$this->table_refund = $this->load->table('order/order_refund');
		$this->table_order  = $this->load->table('order/order');
		$this->table_sub    = $this->load->table('order/order_sub');
		$this->table_sku    = $this->load->table('order/order_sku');
		$this->table_member = $this->load->table('member/member');
		$this->admin_user_table = $this->load->table('admin/admin_user');

		$this->table_return_log = $this->load->table('order/order_return_log');
		$this->table_refund_log = $this->load->table('order/order_refund_log');

		$this->server_sku = $this->load->service('order/order_sku');
		// 状态(-2：未通过，-1：已取消，0：待审核，1：通过)
		$this->status = array(-2 => '未通过',-1 => '已取消',0 => '待审核' , 1 => '通过' , 2 => '已退货');
	}

	/**
	 * 获取售后列表
	 * @param  int $type     售后类型(0:全部 1:退货并退款 2:仅退款，默认 0)
	 * @param  int $buyer_id 会员主键id (默认0)
	 * @param  int $limit 获取条数 (默认10)
	 * @param  int $page 分页页码 (默认1)
	 * @param  int $status 处理进度(-1:全部 0:处理中 1:已完成，默认 -1)
	 * @return [result]
	 */
	public function get_servers($type = 0 ,$buyer_id = 0 ,$limit = 10,$page = 1,$status = -1) {
		$sqlmap = array();
		if (in_array($type, array(1,2))) {
			$sqlmap['type'] = $type;
		}
		if($status == 0){
			$sqlmap['status'] = array('IN','0,1');
		}elseif ($status == 1) {
			$sqlmap['status'] = array('IN','-2,-1,2');
		}
		if ((int) $buyer_id) {
			$sqlmap['buyer_id'] = $buyer_id;
		}
		$result = $this->table->where($sqlmap)->page($page)->limit($limit)->order('id DESC')->select();
		// 组装售后信息
		foreach ($result as $k => $v) {
			// 获取退货退款详情
			$result[$k]['_server'] = $this->_server($v);
			// 获取售后所有商品
			if ($v['type'] == 1) {	// 退货并退款
				$o_sku_id = $this->table_return->where(array('id' => $v['return_id']))->getField('o_sku_id');
			} else {	// 仅退款
				$o_sku_id = $this->table_refund->where(array('id' => $v['refund_id']))->getField('o_sku_id');
			}
			$result[$k]['_skus'] = $this->table_sku->where(array('id' => $o_sku_id))->select();
		}
		$lists['lists'] = $result;
		$lists['count'] = $this->table->where($sqlmap)->count();
		return $lists;
	}

	/**
	 * 创建退货并退款记录
	 * @param  int  	$o_sku_id 订单sku表主键id (必传)
	 * @param  float 	$amount   退款金额 (必传)
	 * @param  string  	$cause    原因 (必传)
	 * @param  string  	$desc 	  描述
	 * @param  array   	$images   上传截图
	 * @param int   	$mid      操作者id
	 * @param int   	$operator_type   操作者类型
	 * @return [boolean]
	 */
	public function create_return ($o_sku_id ,$amount = 0 ,$cause , $desc = '',$images = array(), $mid = 0, $operator_type = 2) {
		$o_sku_id = (int) $o_sku_id;
		$amount   = (float)  remove_xss($amount);
		$cause    = (string) remove_xss($cause);
		$desc     = (string) remove_xss($desc);
		if((int)$mid < 1){
			$this->error = lang('user_id_not_empty','order/language');
			return FALSE;
		}
		if((int)$operator_type < 1){
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		if ($amount <= 0) {
			$this->error = lang('refund_money_require','order/language');
			return FALSE;
		}
		if (!$cause) {
			$this->error = lang('return_cause_empty','order/language');
			return FALSE;
		}
		$o_sku = $this->load->table('order/order_sku')->find($o_sku_id);
		if (!$o_sku) {
			$this->error = lang('order_goods_not_exist','order/language');
			return FALSE;
		}
		if ($amount > $o_sku['real_price']) {
			$this->error = '退款金额不能大于：'.$o_sku['real_price'];
			return FALSE;
		}
		$check_has = $this->table_return->where(array('o_sku_id'=>$o_sku_id))->count();
		if ($check_has > 0) {
			$this->error = lang('repeat_submit','order/language');
			return FALSE;
		}
		$data = $server = $log = array();
		$data['order_sn'] = $o_sku['order_sn'];
		$data['sub_sn']   = $o_sku['sub_sn'];
		$data['o_sku_id'] = $o_sku_id;
		$data['buyer_id'] = $o_sku['buyer_id'];
		$data['cause']    = $cause;
		$data['number']   = $o_sku['buy_nums'];
		$data['amount']   = $amount;
		$data['desc']     = $desc;
		if (!empty($images)) {
			$data['images'] = json_encode($images);
		}
		$result = $this->table_return->update($data);
		if (!$result) {
			$this->error = $this->table_return->getError();
			return FALSE;
		}
		// 创建索引表记录
		if($operator_type == 1){
			$username = $this->admin_user_table->where(array('id' => $mid))->getField('username');
			$_operator_type = '管理员';
		}else{
			$username = $this->table_member->where(array('id' => $mid))->getField('username');
			$_operator_type = '会员';
		}
		$server['type'] = 1;
		$server['order_sn']  = $o_sku['order_sn'];
		$server['sub_sn']    = $o_sku['sub_sn'];
		$server['buyer_id']  = $o_sku['buyer_id'];
		$server['o_sku_id']  = $o_sku_id;
		$server['return_id'] = $result;
		$this->table->update($server);
		// 写入退货日志
		$log['return_id']     = $result;
		$log['order_sn']      = $o_sku['order_sn'];
		$log['sub_sn']        = $o_sku['sub_sn'];
		$log['o_sku_id']      = $o_sku_id;
		$log['action']        = '申请退货并退款';
		$log['operator_id']   = $mid;
		$log['operator_name'] = $username;
		$log['operator_type'] = $operator_type;
		$log['msg']           = $desc;
		$this->load->table('order/order_return_log')->update($log);
		return $result;
	}

	/**
	 * 创建退款记录
	 * @param int 		$type 退款类型 (1：退货并退款 ，2：仅退款，必传)
	 * @param string 	$sub_sn 子订单号 (必传)
	 * @param float 	$amount 退款金额 (必传)
	 * @param string  	$cause  退款原因 (必传)
	 * @param string 	$desc 退款描述
	 * @param int   	$id 退款商品id (当type=1：退货主键id，type=2：订单商品id ，必传)
	 * @param array 	$images 图片
	 * @param int   	$mid      操作者id
	 * @param int   	$operator_type   操作者类型
	 * @return [boolean]
	 */
	public function create_refund ($type,$sub_sn ,$amount = 0 ,$cause ,$desc = '' ,$id ,$images = array(),$mid = 0, $operator_type = 2) {
		$type   = (int) $type;
		$sub_sn = (string) remove_xss($sub_sn);
		$amount = (float)  remove_xss($amount);
		$desc   = (string) remove_xss($desc);
		$id     = (int) $id;
		if((int)$mid < 1){
			$this->error = lang('user_id_not_empty','order/language');
			return FALSE;
		}
		if((int)$operator_type < 1){
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		if (!in_array($type, array(1,2))) {
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		if ($amount <= 0) {
			$this->error = lang('refund_money_require','order/language');
			return FALSE;
		}
		$order = $this->table_sub->where(array('sub_sn' => $sub_sn))->find();
		if (!$order) {
			$this->error = lang('order_not_exist','order/language');
			return FALSE;
		}
		if ($type == 1) {
			$return = $this->table_return->find($id);	// 获取退货记录信息
			if (!$return) {
				$this->error = $this->table_return->getError();
				return FALSE;
			}
			if ($amount > $return['amount']) {
				$this->error = '退款金额不能大于：'.$return['amount'];
				return FALSE;
			}
			$o_sku_id = $return['o_sku_id'];
		} else {
			if (!$cause) {
				$this->error = lang('return_cause_empty','order/language');
				return FALSE;
			}
			$order_sku = $this->table_sku->find($id);	// 订单商品信息
			if (!$order_sku) {
				$this->error = $this->table_sku->getError();
				return FALSE;
			}
			if ($amount > $order_sku['real_price']) {
				$this->error = '退款金额不能大于：'.$order_sku['real_price'];
				return FALSE;
			}
			$o_sku_id = $id;
		}
		$check_has = $this->table_refund->where(array('o_sku_id'=>$o_sku_id))->count();
		if ($check_has > 0) {
			$this->error = lang('repeat_submit','order/language');
			return FALSE;
		}
		$data = $log = $server = array();
		switch ($type) {
			case 1:	// 退货并退款
				$data['return_id'] = $return['id'];
				$data['order_sn']  = $order['order_sn'];
				$data['sub_sn']    = $order['sub_sn'];
				$data['o_sku_id']  = $return['o_sku_id'];
				$data['buyer_id']  = $order['buyer_id'];
				$data['type']      = 1;
				$data['amount']    = $return['amount'];
				$data['desc']      = '退货并退款，创建订单退款记录';
				if (!empty($images)) {
					$data['images'] = json_encode($images);
				}
				$data['cause'] = $return['cause'];
				$log['order_sn'] = $order['order_sn'];
				$log['sub_sn']   = $order['sub_sn'];
				$log['action']   = '创建退货并退款的退款记录';
				$log['o_sku_id'] = $return['o_sku_id'];
				break;
			case 2:	// 仅退款
				$data['o_sku_id'] = $id;
				$data['order_sn'] = $order['order_sn'];
				$data['sub_sn']   = $order['sub_sn'];
				$data['buyer_id'] = $order['buyer_id'];
				$data['type']     = 2;
				$data['amount']   = $amount;
				$data['desc']     = $desc;
				$data['cause'] = $cause;
				if (!empty($images)) {
					$data['images'] = json_encode($images);
				}

				$log['order_sn'] = $order['order_sn'];
				$log['sub_sn']   = $order['sub_sn'];
				$log['action']   = '申请仅退款';
				$log['o_sku_id'] = $id;

				$server['order_sn'] = $order['order_sn'];
				$server['sub_sn']   = $order['sub_sn'];
				break;
		}
		$result = $this->table_refund->update($data);
		if (!$result) {
			$this->error = $this->table_refund->getError();
			return FALSE;
		}
		// 索引表记录(退货并退款:添加退款主键ID,仅退款||取消订单退款：创建退款记录)
		if ($type == 1) {
			$this->table->where(array('return_id' => $id))->setField('refund_id' ,$result);
		} else {
			$server['type'] = 2;
			$server['refund_id'] = $result;
			$server['buyer_id']  = $order['buyer_id'];
			$server['o_sku_id']  = $o_sku_id;
			$this->table->update($server);
		}
		// 创建退款日志
		if($operator_type == 1){
			$username = $this->admin_user_table->where(array('id' => $mid))->getField('username');
			$_operator_type = '管理员';
		}else{
			$username = $this->table_member->where(array('id' => $mid))->getField('username');
			$_operator_type = '会员';
		}
		$log['refund_id']     = $result;
		$log['operator_id']   = $mid;
		$log['operator_name'] = $username;
		$log['operator_type'] = $operator_type;
		$log['msg']           = $data['desc'];
		$this->load->table('order/order_refund_log')->update($log);
		return $result;
	}

	/**
	 * 处理退货申请
	 * @param int $id 	  订单退货表主键id (必传)
	 * @param int $status 处理状态 (必传)
	 * @param string $msg 操作备注 (默认空)
	 * @param int   	$mid      操作者id
	 * @param int   	$operator_type   操作者类型
	 * @return [boolean]
	 */
	public function handle_return($id ,$status ,$msg = '',$mid = 0,$operator_type = 1) {
		$id     = (int) $id;
		$status = (int) $status;
		$msg    = (string) remove_xss($msg);
		$info   = $this->table_return->find($id);
		if((int)$mid < 1){
			$this->error = lang('user_id_not_empty','order/language');
			return FALSE;
		}
		if((int)$operator_type < 1){
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		if (!$info) {
			$this->error = lang('operate_record_not_exist','order/language');
			return FALSE;
		}
		if (in_array($info['status'], array(-2,-1,2))) {
			$this->error = lang('record_ban_operate','order/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = $id;
		$data['status'] = $status;
		$data['admin_id'] = ADMIN_ID;
		$data['admin_desc'] = $msg;
		$data['admin_time'] = time();
		runhook('before_return_update',$data);
		$result = $this->table_return->update($data);
		runhook('after_return_update',$data);
		if (!$result) {
			$this->error = $this->table_return->getError();
			return FALSE;
		}
		if($status == -2){
			$this->table->where(array('return_id' => $id))->setField('status',-2);
		}else{
			$this->table->where(array('return_id' => $id))->setField('status',1);
		}
		// 退货日志
		if($operator_type == 1){
			$username = $this->admin_user_table->where(array('id' => $mid))->getField('username');
			$_operator_type = '管理员';
		}else{
			$username = $this->table_member->where(array('id' => $mid))->getField('username');
			$_operator_type = '会员';
		}
		$log = array();
		$log['return_id']   = $id;
		$log['order_sn']    = $info['order_sn'];
		$log['sub_sn']      = $info['sub_sn'];
		$log['o_sku_id']    = $info['o_sku_id'];
		$log['action']      = '处理退货';
		$log['operator_id'] = $mid;
		$log['operator_name'] = $username;
		$log['operator_type'] = $operator_type;
		$str = '';
		if (!empty($msg)) $str = '&nbsp;&nbsp; 操作日志：'.$msg;
		$log['msg'] = $_operator_type.'处理退货单为'.$this->status[$status].$str;
		$this->table_return_log->update($log);
		return $result;
	}

	/**
	 * 处理退款申请
	 * @param int $id 	订单退款表主键id (必传)
	 * @param int $status 处理状态 (默认0)
	 * @param string $msg 操作备注 (默认空)
	 * @param int   	$mid      操作者id
	 * @param int   	$operator_type   操作者类型
	 * @return [boolean]
	 */
	public function handle_refund($id ,$status = 0 ,$msg = '',$mid = 0,$operator_type = 1) {
		$id     = (int) $id;
		$status = (int) $status;
		$msg    = (string) remove_xss($msg);
		$info   = $this->table_refund->find($id);
		if((int)$mid < 1){
			$this->error = lang('user_id_not_empty','order/language');
			return FALSE;
		}
		if((int)$operator_type < 1){
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		if (!$info) {
			$this->error = lang('operate_record_not_exist','order/language');
			return FALSE;
		}
		if ($info['status'] != 0) {
			$this->error = lang('record_ban_operate','order/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = $id;
		$data['status'] = $status;
		$data['admin_id'] = ADMIN_ID;
		$data['admin_desc'] = $msg;
		$data['admin_time'] = time();
		runhook('before_refund_update',$data);
		$result = $this->table_refund->update($data);
		if (!$result) {
			$this->error = $this->table_refund->getError();
			return FALSE;
		}
		runhook('after_refund_update',$data);
		if ($status == 1) {
			$this->load->service('member/member')->change_account($info['buyer_id'],'money',$info['amount'],'通过售后申请并已退款到账户余额');
			$this->table->where(array('refund_id' => $id))->setField('status',2);
		}else{
			$this->table->where(array('refund_id' => $id))->setField('status',-2);
		}
		// 退款日志
		if($operator_type == 1){
			$username = $this->admin_user_table->where(array('id' => $mid))->getField('username');
			$_operator_type = '管理员';
		}else{
			$username = $this->table_member->where(array('id' => $mid))->getField('username');
			$_operator_type = '会员';
		}
		$log = array();
		$log['refund_id'] = $id;
		$log['order_sn']  = $info['order_sn'];
		$log['sub_sn']    = $info['sub_sn'];
		$log['o_sku_id']  = $info['o_sku_id'];
		$log['action']    = '处理退款';
		$log['operator_id']   = $mid;
		$log['operator_name'] = $username;
		$log['operator_type'] = $operator_type;
		$str = '';
		if (!empty($msg)) $str = '&nbsp;&nbsp; 操作日志：'.$msg;
		$log['msg'] = $_operator_type.'处理退款单为'.$this->status[$status].$str;
		$this->table_refund_log->update($log);
		return $result;
	}

	/**
	 * 买家退货
	 * @param int 	 $id 	退货表主键id (必传)
	 * @param string $name 	物流名称 (必传)
	 * @param string $sn 	运单号 (必传)
	 * @param int   	$mid      操作者id
	 * @param int   	$operator_type   操作者类型
	 * @return [boolean]
	 */
	public function return_goods($id ,$name ,$sn,$mid = 0,$operator_type = 2) {
		$id = (int) $id;
		$name = (string) trim($name);
		$sn   = (string) trim($sn);
		if((int)$mid < 1){
			$this->error = lang('user_id_not_empty','order/language');
			return FALSE;
		}
		if((int)$operator_type < 1){
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		if (empty($name)) {
			$this->error = lang('logistics_name_empty','order/language');
			return FALSE;
		}
		if (empty($sn)) {
			$this->error = lang('waybill_sn_empty','order/language');
			return FALSE;
		}
		$return = $this->table_return->find($id);
		if (!$return) {
			$this->error = $this->table_return->getError();
			return FALSE;
		}
		if ($return['status'] != 1) {
			$this->error = lang('record_ban_operate','order/language');
			return FALSE;
		}
		$data = array('delivery_name' => $name , 'delivery_sn' => $sn ,'status' => 2);
		$result = $this->table_return->where(array('id' => $id))->setField($data);
		if (!$result) {
			$this->error = $this->table_return->getError();
			return FALSE;
		}
		// 生成退款单记录
		$this->create_refund(1 ,$return['sub_sn'] ,$return['amount'] ,$return['cause'] ,$return['desc'],$return['id'],json_decode($return['images'],TRUE),$mid,$operator_type);
		return $result;
	}

	/**
	 * 获取订单下的售后详情
	 * @param  string $sn 主订单号
	 * @return [result]
	 */
	public function get_after_by_sn($sn = '') {
		$sn = (string) remove_xss($sn);
		$servers = $this->table->where(array('order_sn' => $sn))->select();
		foreach ($servers as $k => $val) {
			$servers[$k] = $this->_server($val);
		}
		return $servers;
	}

	/**
	 * 获取退货列表
	 * @param  array  $options 参数说明 [page : 分页，limit：显示条数]
	 * @param  array  $sqlmap 查询条件
	 * @return [result]
	 */
	public function get_returns($options = array(),$sqlmap = array()) {
		$page = isset($options['page']) ? (int) $options['page'] : 1;
		$limit = isset($options['limit']) ? (int) $options['limit'] : 10;
		$infos = $result = array();
		$result = $this->table_return->where($sqlmap)->page($page)->limit($limit)->order('id DESC')->select();
		$lists = array();
		foreach ($result as $k => $v) {
			$result[$k] = $this->_more($v);
			$lists[] = array(
				'id' => $v['id'],
				'sku_name' => $result[$k]['_sku']['sku_name'],
				'username' => $result[$k]['_buyer']['username'],
				'amount'  => $v['amount'],
				'dateline' => $v['dateline'],
				'status' => $v['status'],
				'_status' => $result[$k]['_status'],
				'delivery_name' => $v['delivery_name'],
				'delivery_sn' => $v['delivery_sn'],
				'specs' => $result[$k]['_sku']['sku_spec'],
				'sku_thumb' => $result[$k]['_sku']['sku_thumb']
			);
		}
		$infos['lists'] = $lists;
		$infos['count'] = $this->table_return->where($sqlmap)->count();
		return $infos;
	}

	/**
	 * 获取退款列表
	 * @param  array  $options 参数说明 [page : 分页，limit：显示条数]
	 * @param  array  $sqlmap 查询条件
	 * @return [result]
	 */
	public function get_refunds($options = array(),$sqlmap = array()) {
		$page = isset($options['page']) ? (int) $options['page'] : 1;
		$limit = isset($options['limit']) ? (int) $options['limit'] : 10;
		$infos = $result = array();
		$result = $this->table_refund->where($sqlmap)->page($page)->limit($limit)->order('id DESC')->select();
		$lists = array();
		foreach ($result as $k => $v) {
			$result[$k] = $this->_more($v);
			$result[$k]['_type'] = ($v['type'] == 1) ? '退货并退款' : '仅退款';
			$lists[] = array(
				'id' => $v['id'],
				'sku_name' => $result[$k]['_sku']['sku_name'],
				'_type' => $result[$k]['_type'],
				'username' => $result[$k]['_buyer']['username'],
				'amount'  => $v['amount'],
				'dateline' => $v['dateline'],
				'_status' => $result[$k]['_status'],
				'specs' => $result[$k]['_sku']['sku_spec'],
				'status' => $v['status'],
				'sku_thumb' => $result[$k]['_sku']['sku_thumb']
			);
		}
		$infos['lists'] = $lists;
		$infos['count'] = $this->table_refund->where($sqlmap)->count();
		return $infos;
	}

	/* 获取记录所有数据 */
	private function _more($info = array()) {
		if (empty($info)) return FALSE;
		// 获取商品信息
		$info['_sku'] = $this->table_sku->field('sku_name ,sku_thumb,sku_spec,real_price,buy_nums')->find($info['o_sku_id']);
		// 获取会员信息
		$info['_buyer'] = $this->table_member->field('username ,mobile')->find($info['buyer_id']);
		$info['_status'] = $this->status[$info['status']];
		if ($info['images']) $info['_images'] = json_decode($info['images'] ,TRUE);
		return $info;
	}

	/* 获取退货退款详情 */
	private function _server($server) {
		$arr = $logs1 = $logs2 = array();
		$arr['type'] = '仅退款';
		$refund = $this->table_refund->find($server['refund_id']);
		if ($server['type'] == 1) {	// 退货并退款
			$return = $this->table_return->find($server['return_id']);
			$arr['type'] = '退货并退款';
			$arr['cause'] = $return['cause'];
			$arr['amount'] = $return['amount'];
			$arr['images'] = json_decode($return['images']);
			$arr['status'] = $return['status'];
			$arr['axis']['create'] = $return['dateline'];	// 申请售后时间
			$arr['axis']['admin_time'] = $return['admin_time'];	// 卖家处理时间
			$arr['axis']['delivery'] = 0;	// 买家退货发货时间
			$arr['axis']['finish'] = 0;	// 完成时间
			if ($refund) {
				$arr['axis']['delivery'] = $refund['dateline'];	// 买家退货发货时间
				$arr['axis']['finish'] = $refund['admin_time'];	// 完成时间
				$arr['_status'] = $refund['status'];
			}
			$logs1 = $this->load->table('order/order_return_log')->where(array('return_id' => $server['return_id']))->order('id DESC')->select();
		} else {
			$arr['cause'] = $refund['cause'];
			$arr['amount'] = $refund['amount'];
			$arr['images'] = json_decode($refund['images']);
			$arr['axis']['create'] = $refund['dateline'];	// 申请售后时间
			$arr['status'] = $refund['status'];
			$arr['axis']['finish'] = $refund['admin_time'];	// 完成时间
		}
		$logs2 = $this->load->table('order/order_refund_log')->where(array('refund_id' => $server['refund_id']))->order('id DESC')->select();
		$arr['logs'] = array_merge($logs2 ? $logs2 : array() ,$logs1 ? $logs1 : array());
		return $arr;
	}

	/**
	 * 获取退货详情
	 * @param  int $id return表主键id
	 * @return [result]
	 */
	public function return_detail($id) {
		$result = $this->table_return->find($id);
		$result = $this->_more($result);
		$result['logs'] = $this->table_return_log->where(array('return_id' => $id))->order('id DESC')->select();
		return $result;
	}

	/**
	 * 获取退款详情
	 * @param  int $id refund表主键id
	 * @return [result]
	 */
	public function refund_detail($id) {
		$result = $this->table_refund->find($id);
		$result = $this->_more($result);
		$result['logs'] = $this->table_refund_log->where(array('refund_id' => $id))->order('id DESC')->select();
		return $result;
	}

	/**
	 * 生成查询条件
	 * @param  array  $params
	 * @return [sqlmap]
	 */
	public function build_map($params = array()) {
		$sqlmap = array();
		if (isset($params['type'])) {
			switch ($params['type']) {
				case 1:	// 已处理
					$sqlmap['status'] = array('NEQ' ,0);
					break;
				default:	// 待处理
					$sqlmap['status'] = 0;
					break;
			}
		}
		if (isset($params['keywords']) && $params['keywords']) {
			switch ($params['keywords']) {
				// 会员手机||账号
				case ((is_mobile($params['keywords']) == TRUE) || (strlen($params['keywords']) != 20)):
					$map = array();
					$map['username|mobile'] = array('LIKE','%'.$params['keywords'].'%');
					$sqlmap['buyer_id'] = $this->table_member->where($map)->getField('id');
					break;
				// 订单号
				default:
					$sqlmap['order_sn|sub_sn']  = $params['keywords'];
					break;
			}
		}
		return $sqlmap;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	读取的字段
	 * @return [type]
	 */
	public function find($sqlmap = array(), $field = "") {
		$result = $this->table->where($sqlmap)->field($field)->find();
		if($result===false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
	/*修改*/
	public function setField($data, $sqlmap = array()){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table->where($sqlmap)->save($data);
		if($result === false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
}