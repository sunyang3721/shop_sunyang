<?php
/**
 * 		子订单服务层
 */
class order_sub_service extends service {

	public function _initialize() {
		/* 实例化数据层 */
		$this->table       = $this->load->table('order/order_sub');
		$this->table_order = $this->load->table('order/order');
		$this->table_sku   = $this->load->table('order/order_sku');
		$this->table_delivery = $this->load->table('order/order_delivery');
		$this->table_trade = $this->load->table('order/order_trade');
		/* 实例化服务层 */
		$this->service_track  = $this->load->service('order/order_track');
		$this->service_parcel = $this->load->service('order/order_parcel');
		$this->service_setting = $this->load->service('admin/setting');
		$this->service_order_log = $this->load->service('order/order_log');
		$this->service_goods_sku = $this->load->service('goods/goods_sku');
	}

	/**
	 * 根据主订单号获取子订单信息
	 * @param  string 	$sn 	主订单号
	 * @param  boolean 	$skus 	是否查询订单商品
	 * @param  boolean 	$track 	是否查询订单跟踪
	 * @param  boolean 	$group 	是否根据物流分组
	 * @return [result]
	 */
	public function get_subs($sn ,$skus = FALSE ,$track = FALSE ,$group = FALSE) {
		$subs = $this->table->where(array('order_sn' => $sn))->select();
		foreach ($subs as $key => $sub) {
			$_skus = array();
			if ($track  == TRUE) {
				$subs[$key]['_track'] = $this->service_track->get_tracks_by_sn($sub['sub_sn']);
			}
			if ($skus == TRUE) {
				$_skus = $this->table_sku->where(array('sub_sn' =>$sub['sub_sn']))->select();
				foreach ($_skus as $k => $_sku) {
					$goods_sku = $this->service_goods_sku->fetch_by_id($_sku['sku_id']);
					if($goods_sku['edition'] == $_sku['sku_edition']){
	                    $_skus[$k]['url'] = url('goods/index/detail',array('sku_id'=>$_skus[$k]['sku_id']));
	                }else{
	                    $_skus[$k]['url'] = url('goods/index/snapshot',array('sku_id'=>$_skus[$K]['sku_id'],'order_sku_id'=>$_skus[$k]['id']));
	                }
				}
				$subs[$key]['_skus'] = $_skus;
			}
			if ($group == TRUE) {
				if ($skus == FALSE) {
					$_skus = $this->table_sku->where(array('sub_sn' =>$sub['sub_sn']))->select();
					$subs[$key]['_skus'] = $_skus;
				}
				$_group = array();
				foreach ($_skus as $sku) {
					$_group[$sku['delivery_id']]['lists'][] = $sku;
				}
				// 组装物流信息
				foreach ($_group as $o_d_id => $v) {
					// 分组订单商品总额
					$total_amount = 0;
					foreach ($v['lists'] as $sku) {
						$total_amount += $sku['real_price'];
					}
					$_group[$o_d_id]['total_amount'] = sprintf("%.2f", (float) $total_amount);
					// 赋值状态
					$_status = $sub['_status']['wait'];
					if ($sub['delivery_status'] != 0) {	// 发过货的为以下状态
						if ($o_d_id > 0) {
							$_group[$o_d_id]['delivery'] = $this->table_delivery->find($o_d_id);
							$_status = ($_group[$o_d_id]['delivery']['isreceive'] == 1) ? 'all_finish' : 'load_finish';
						} else {
							$_status = 'load_delivery';
						}
					}
					$_group[$o_d_id]['_status'] = $_status;
				}
				$subs[$key]['_group'] = $_group;
			}
		}
		return $subs;
	}

	/**
	 * 设置订单
	 * @param  string 	$sn  		订单号(确认支付时传主订单号，其它传子订单号)
	 * @param  string 	$action 	操作类型
	 *         (order:订单 || pay:支付 || confirm:确认 || delivery:发货 || finish:完成)
	 * @param  int 		$status 	状态(只有$action = 'order'时必填)
	 * @param  array 	$options 	附加参数
	 * @return [boolean]
	 */
	public function set_order($sn = '',$action = '',$status = 0 ,$options = array()) {
		$sn     = (string) trim($sn);
		$action = (string) trim($action);
		$status = (int) $status;
		$msg    = (string) trim($options['msg']);
		unset($options['msg']);
		if (empty($sn)) {
			$this->error = lang('order_sn_not_null','order/language');
			return FALSE;
		}
		if (empty($action)) {
			$this->error = lang('operate_type_empty','order/language');
			return FALSE;
		}
		if (!in_array($action, array('order','pay','confirm','delivery','finish'))) {
			$this->error = lang('operate_type_error','order/language');
			return FALSE;
		}
		// 检测订单是否存在
		$this->order = $this->table_order->where(array('sn' => $sn))->find();
		if (!$this->order) {
			$this->order = $this->table->where(array('sub_sn' => $sn))->find();
		}
		if (!$this->order) {
			$this->error = lang('order_not_exist','order/language');
			return FALSE;
		}
		// 获取订单状态
		$this->order['_status'] = $this->table->get_status($this->order);
		switch ($action) {
			case 'order':	// (2：已取消，3：已回收，4：已删除)
					$result = $this->_order($status ,$options);
					// 后台删除订单直接返回
					if (IN_ADMIN && $status == 4 && $result !== FALSE) {
						return TRUE;
					}
				break;
			case 'pay':	// 针对所有子订单操作
				$result = $this->_pay($options);
				break;
			case 'confirm':
				$result = $this->_confirm();
				break;
			case 'delivery':
				$result = $this->_delivery($options);
				break;
			case 'finish':
				$result = $this->_finish($options);
				break;
		}
		if ($result === FALSE) return FALSE;
		// 订单日志
		$operator = get_operator();	// 获取操作者信息
		$data = array();
		if ($action == 'pay') {
			$data['order_sn'] = $sn;
			$data['action']        = $result['action'];
			$data['operator_id']   = $operator['id'];
			$data['operator_name'] = $operator['username'];
			$data['operator_type'] = $operator['operator_type'];
			$data['msg']           = $msg;
			foreach ($result['sub_sns'] as $sub_sn) {
				$data['sub_sn'] = $sub_sn;
				$this->service_order_log->add($data);
			}
 		} else {
 			$data['order_sn'] = $this->order['order_sn'];
			$data['sub_sn']   = $sn;
			$data['action']        = $result;
			$data['operator_id']   = $operator['id'];
			$data['operator_name'] = $operator['username'];
			$data['operator_type'] = $operator['operator_type'];
			$data['msg']           = $msg;
			$this->service_order_log->add($data);
 		}
		return TRUE;
	}

	/**
	 * 支付操作 (针对所有子订单操作)
	 * @param  array $options
	 *         			paid_amount ：实付金额
	 *         			pay_method  ：支付方式
	 *         			pay_sn 		：支付流水号
	 * @return [string]
	 */
	private function _pay($options) {
		$order = $this->order;
		if ($order['pay_type'] != 1 || $order['pay_status'] != 0) {
			$this->error = lang('_valid_access_');
			return FALSE;
		}
		$data = array();
		$data['pay_status'] = 1;
		$data['pay_time']   = time();
		
		// 设置子订单表
		$result = $this->table->where(array('order_sn' => $order['sn']))->save($data);
		if (!$result) return FALSE;
		// 设置主订单表信息
		if (isset($options['paid_amount'])) {	// 实付金额
			$data['paid_amount'] = sprintf("%.2f", (float) $options['paid_amount']);
		} else {
			$data['paid_amount'] = $order['real_amount'];
		}
		if (isset($options['pay_method'])) {	// 支付方式
			$data['pay_method']  = (string) $options['pay_method'];
		}
		if (isset($options['pay_sn'])) {		// 支付流水号
			$data['pay_sn'] = (string) $options['pay_sn'];
		}
		$result = $this->table_order->where(array('sn' => $order['sn']))->save($data);
		if (!$result) return FALSE;
		
		if (isset($options['paid_amount']) && isset($options['pay_method']) && isset($options['pay_sn'])) {
			$_map = array();
			$_map['order_sn'] = $order['sn'];
			$_map['trade_no'] = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
			$_map['total_fee'] = $options['paid_amount'];
			$_map['status'] = 1;
			$_map['time'] = time();
			$_map['method'] = $options['pay_method'];
			$_map['pay_sn'] = $options['pay_sn'];
			$set_pay_sn = $this->table_trade->add($_map);			
		}else{
			$set_pay_sn = $this->table_trade->where(array('order_sn' => $order['sn']))->setField('pay_sn',$data['pay_sn']);
		}
		if ($set_pay_sn === FALSE){
			$this->error = $this->table_trade->getError();
			return FALSE;
		}
		// 获取主订单号下的所有子订单号
		$sub_sns = $this->table->where(array('order_sn' => $order['sn']))->getField('sub_sn' ,TRUE);
		foreach ($sub_sns as $sub_sn) {
			// 订单跟踪
			$this->service_track->add($order['sn'] ,$sub_sn , '您的订单已付款，请等待系统确认');
		}
		// 钩子：支付成功
		runhook('order_pay_success');
		return array('action' => '支付订单','sub_sns' => $sub_sns);
	}

	/* 确认操作 */
	private function _confirm() {
		$order = $this->order;
		if ($order['confirm_status'] == 2 || $order['delivery_status'] != 0 || ($order['pay_type'] == 1 && $order['pay_status'] != 1)) {
			$this->error = lang('_valid_access_');
			return FALSE;
		}
		$data = $sqlmap = array();
		$data['confirm_status'] = 2;
		$data['confirm_time']   = time();
		$result = $this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
		if (!$result) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		/* 检测标记主订单确认状态 */
		$sqlmap['order_sn'] = $order['order_sn'];
		$all_count = $this->table->where($sqlmap)->count();	// 当前主订单下子订单个数
		// 当前已确认的订单个数
		$sqlmap['confirm_status'] = 2;
		$already_count = $this->table->where($sqlmap)->count();
		if ($all_count == $already_count) {	// 已确认
			$this->table_order->where(array('sn' => $order['order_sn']))->setField('confirm_status' ,2);
		} else {	// 部分确认
			$this->table_order->where(array('sn' => $order['order_sn']))->setField('confirm_status' ,1);
		}
		/* 生成发货单 */
		$this->service_parcel->create($order);
		/* 物流跟踪 */
		$this->service_track->add($order['order_sn'] ,$order['sub_sn'] , '您的订单已确认，正在配货');
		// 钩子：确认订单
		runhook('confirm_order',$order);
		return '确认订单';
	}

	/**
	 * 发货操作
	 * @param  array  $options
	 *         				is_choise ：是否选择物流
	 *         				delivery_id ：物流主键
	 *         				delivery_sn ：快递单号
	 *         				o_sku_ids ：要发货的订单商品ids(多个以 ，分割)
	 * @return [string]
	 */
	private function _delivery($options = array()) {
		$order = $this->order;
		$is_choise     = (int) $options['is_choise'];	// 是否选择物流
		$delivery_id   = (int) $options['delivery_id'];
		$delivery_sn   = (string) trim($options['delivery_sn']);
		$sub_sn 	   = (string) trim($options['sub_sn']);
		$o_sku_ids = array_filter(explode(',', $options['o_sku_ids']));	// 要发货的订单商品ids
		if ($is_choise === 1) {
			if ($delivery_id < 1) {
				$this->error = lang('logistics_empty','order/language');
				return FALSE;
			}
			if (empty($delivery_sn)) {
				$this->error = lang('logistics_not_exist','order/language');
				return FALSE;
			}
			$delivery_name = $this->load->table('order/delivery')->getFieldById($delivery_id,'name');
		} else {
			$delivery_id   = 0;
			$delivery_name = '无需物流运输';
			$delivery_sn   = '';
		}
		// 检测订单商品是否已发货，已发货的注销变量值
		foreach ($o_sku_ids as $k => $id) {
			$ret = '';
			$ret = $this->table_sku->getFieldById($id ,'delivery_status');
			if ($ret != 0) {
				unset($o_sku_ids[$k]);
			}
		}
		if (count($o_sku_ids) == 0) {
			$this->error = lang('order_goods_empty','order/language');
			return FALSE;
		}
		$data = $sqlmap = array();
		// 创建订单物流信息
		$data['o_sku_ids']     = implode(',',$o_sku_ids);
		$data['sub_sn']        = $order['sub_sn'];
		$data['delivery_id']   = $delivery_id;
		$data['delivery_name'] = $delivery_name;
		$data['delivery_sn']   = $delivery_sn;
		$data['delivery_time'] = time();
		$addid = $this->table_delivery->update($data);
		if (!$addid) {
			$this->error = $this->table_delivery->getError();
			return FALSE;
		}
		//支付宝担保交易
		$order_sn = $this->table->where(array('sub_sn' => $sub_sn))->getField('order_sn');
		$pay = $this->table_order->where(array('sn' => $order_sn))->Field('pay_method,pay_sn')->find();
		if($pay['pay_method'] == 'alipay_escow'){
			$classfile = APP_PATH.'module/pay/library/pay_factory.class.php';
			require_cache($classfile);
			$pay_factory =  new pay_factory($pay['pay_method']);
			$pay_info = array();
			$pay_info['trade_no'] = $pay['pay_sn'];//支付宝交易号
			$pay_info['logistics_name'] = $delivery_name;//物流公司名称
			$pay_info['invoice_no'] = $delivery_sn;//物流发货单号
			$pay_factory->set_productinfo($pay_info)->gateway();
			$pay_factory->_delivery();
		}
		/* 标记订单商品为已发货状态，并关联订单物流id */
		$sqlmap['id'] = array('IN' , $o_sku_ids);
		$data = array();
		$data['delivery_id'] = $addid;
		$data['delivery_status'] = 1;
		$this->table_sku->where($sqlmap)->setField($data);
		/* 标记子订单发货状态 */
		$sku_count = $already_count = 0;
		$sqlmap = $data = array();
		// 子订单的商品总数
		$sqlmap['sub_sn'] = $order['sub_sn'];
		$sku_count = $this->table_sku->where($sqlmap)->count();
		// 子订单已发货的商品总数
		$sqlmap['delivery_status'] = array('GT' ,0);
		$already_count = $this->table_sku->where($sqlmap)->count();
		if ($sku_count == $already_count) {	// 已发货
			$data['delivery_status'] = 2;
		} else {	// 部分发货
			$data['delivery_status'] = 1;
		}
		$data['delivery_time'] = time();
		$result = $this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
		if (!$result) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		/* 标记主订单发货状态 */
		$main_count = $already_count = 0;
		$sqlmap = $data = array();
		// 主订单的商品总数
		$sqlmap['order_sn'] = $order['order_sn'];
		$main_count = $this->table_sku->where($sqlmap)->count();
		// 主订单已发货的商品总数
		$sqlmap['delivery_status'] = array('GT' ,0);
		$already_count = $this->table_sku->where($sqlmap)->count();
		if ($main_count == $already_count) {	// 已发货
			$data['delivery_status'] = 2;
		} else {	// 部分发货
			$data['delivery_status'] = 1;
		}
		$result = $this->table_order->where(array('sn' => $order['order_sn']))->save($data);
		if (!$result) {
			$this->error = $this->table_order->getError();
			return FALSE;
		}
		// 如果后台设置发货减库存 => 减库存
		$stock_change = $this->service_setting->get('stock_change');
		if ($stock_change != NULL && $stock_change == 2) {
			foreach ($o_sku_ids as $k => $id) {
				$o_sku = $this->table_sku->where(array('id' => $id))->field($id ,'sku_id,buy_nums')->find();
				$this->load->service('goods/goods_sku')->set_dec_number($o_sku['sku_id'],$o_sku['buy_nums']);
			}
		}
		// 物流跟踪
		$string = '';
		if ($order['delivery_name'] == $delivery_name || empty($order['delivery_name'])) {
			$string = '快递单号：'.$delivery_sn;
		} else {
			$string = '从「'.$order['delivery_name'].'」修改到「'.$delivery_name.'」';
			if ($is_choise === 1) {
				$string .= '; 快递单号：'.$delivery_sn;
			}
		}
		$this->service_track->add($order['order_sn'] ,$order['sub_sn'] , '您的订单配货完毕，已经发货。'.$string,0,$addid);
		// 钩子：订单商品已发货
		$order['delivery_sn'] = $delivery_sn;
		$order['delivery_name'] = $delivery_name;
		$order['order_sn'] = $order['order_sn'];
		runhook('skus_delivery',$order);
		return '订单发货';
	}

	/**
	 * 确认收货(完成)
	 * @param  array  $options
	 *         			o_delivery_id ：订单发货主键id
	 * @return [string]
	 */
	private function _finish($options = array()) {
		$site_name = $this->load->service('admin/setting')->get('site_name');
		$order = $this->order;
		if ($order['finish_status'] == 2 || $order['delivery_status'] == 0) {
			$this->error = lang('_valid_access_');
			return FALSE;
		}
		$o_delivery_id = (int) remove_xss($options['o_delivery_id']);
		if (!defined('IN_ADMIN')) {
			if ($o_delivery_id == 0) {
				$this->error = lang('_param_error_');
				return FALSE;
			}
			// 检测该订单物流操作合法性
			$o_delivery = $this->table_delivery->find($o_delivery_id);
			if (!$o_delivery || $o_delivery['isreceive'] == 1) {
				$this->error = lang('_valid_access_');
				return FALSE;
			}
			// 标记当前订单物流为已收货 (order_delivery & order_sku)
			$data = $sqlmap = array();
			$data['id'] = $o_delivery_id;
			$data['isreceive'] = 1;
			$data['receive_time'] = time();
			$result = $this->table_delivery->update($data);
			if (!$result) {
				$this->error = $this->table_delivery->getError();
				return FALSE;
			}
			$sqlmap['sub_sn'] = $order['sub_sn'];
			$sqlmap['delivery_id'] = $o_delivery_id;
			$this->table_sku->where($sqlmap)->setField('delivery_status' ,2);
			/* 标记子订单完成状态 */
			$sqlmap = $receives = $arr = $sku_ids = array();
			$sku_count = 0;
			$sqlmap['sub_sn'] = $order['sub_sn'];
			$sku_count = $this->table_sku->where($sqlmap)->count();	// 统计该子订单下的所有订单商品数
			// 查询已确认收货订单物流的sku_ids
			$sqlmap['isreceive'] = 1;
			$receives = $this->table_delivery->where($sqlmap)->getField('o_sku_ids' , TRUE);
			foreach ($receives as $val) {
				$arr = array_filter(explode(',', $val));
				foreach ($arr as $id) {
					$sku_ids[] = $id;
				}
			}
			$data = array();
			$string = '';
			if (count($sku_ids) == $sku_count) {	// 所有订单物流已确认收货
				$data['finish_status'] = 2;
				$string = '感谢您在'.$site_name.'购物，欢迎您的再次光临';
			} else {	// 部分订单物流已确认收货
				$data['finish_status'] = 1;
				$string = '感谢您在'.$site_name.'购物，欢迎您的再次光临';
			}
			$data['finish_time'] = time();
			$result = $this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
			if (!$result) {
				$this->error = $this->table->getError();
				return FALSE;
			}
		} else {
			// 标记子订单为已完成
			$data = array();
			$data['finish_status'] = 2;
			$data['finish_time'] = time();
			$string = '感谢您在'.$site_name.'购物，欢迎您的再次光临';
			$result = $this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
			// 标记所有订单物流为已收货 (order_delivery & order_sku)
			$sqlmap = $data = array();
			$sqlmap['sub_sn'] = $order['sub_sn'];
			$data['isreceive'] = 1;
			$data['receive_time'] = time();
			$result = $this->table_delivery->where($sqlmap)->save($data);
			$this->table_sku->where($sqlmap)->setField('delivery_status' ,2);
		}
		/* 标记主订单的完成状态 */
		// 统计子订单总数
		$sqlmap = $data = array();
		$sub_count = $already_count = 0;
		$sqlmap['sub_sn'] = $order['sub_sn'];
		$sub_count = $this->table->where($sqlmap)->count();
		// 统计子订单已完成的总数
		$sqlmap['finish_status'] = 2;
		$already_count = $this->table->where($sqlmap)->count();
		if ($sub_count == $already_count) {	// 所有子订单已确认收货
			$data['finish_status'] = 2;
			// 钩子：整个订单完成
			$order_finish_sn = $order['order_sn'];
			runhook('order_finish',$order_finish_sn);
		} else {	// 部分子订单已确认收货
			$data['finish_status'] = 1;
		}
		$result = $this->table_order->where(array('sn' => $order['order_sn']))->save($data);
		if (!$result) {
			$this->error = $this->table_order->getError();
			return FALSE;
		}
		// 订单跟踪
		if($o_delivery_id){
			$this->service_track->add($order['order_sn'] ,$order['sub_sn'] , $string,0,$o_delivery_id);
		}else{
			foreach($order['_skus'] as $k => $v){
				foreach($v as $r){
					if($r['delivery_status'] != 2 ){
						$this->service_track->add($order['order_sn'] ,$order['sub_sn'] , $string,0,$r['delivery_id']);
					}
				}
			}
		}
		// 钩子：确认收货(完成)
		runhook('delivery_finish');
		return $string;
	}

	/* 订单操作 */
	private function _order($status ,$options) {
		$order = $this->order;
		$data = $sqlmap = array();
		switch ($status) {
			case 2:	// 取消订单
				$string = '您的订单已取消';
				if ($order['status'] != 1 || $order['delivery_status'] != 0) {
					$this->error = lang('order_dont_operate','order/language');
					return FALSE;
				}
				/* 在线支付：取消整个订单，货到付款：取消当前子订单 */
				$data['status'] = 2;
				$data['system_time'] = time();
				if ($order['pay_type'] == 1) {
					// 标记所有子订单为已取消
					$this->table->where(array('order_sn' => $order['order_sn']))->save($data);
					// 主订单信息
					$order_main = $this->table_order->where(array('sn' =>$order['order_sn']))->find();
					/* 未发货&已付款的&是否退款到账户余额 ==> 退款到账户余额 */
					if ($order['delivery_status'] == 0 && $order['pay_status'] == 1 && $options['isrefund'] == 1) {

						$this->load->service('member/member')->change_account($order_main['buyer_id'],'money',$order_main['paid_amount'],'取消订单退款,订单号:'.$order['order_sn']);
						// 解冻余额支付的金额
						if ($order_main['balance_amount'] > 0) {
							$this->load->service('member/member')->action_frozen($order_main['buyer_id'],$order_main['balance_amount'],false);
						}
						$string = '您的订单已取消，已退款到您的账户余额，请查收';
					}
					/* 未发货&未付款的&是否退款到账户余额 ==> 退款到账户余额 */
					if ($order['delivery_status'] == 0 && $order['pay_status'] == 0 && $options['isrefund'] == 1) {

						$this->load->service('member/member')->change_account($order_main['buyer_id'],'money',$order_main['balance_amount'],'取消订单退款,订单号:'.$order['order_sn']);
						// 解冻余额支付的金额
						if ($order_main['balance_amount'] > 0) {
							$this->load->service('member/member')->action_frozen($order_main['buyer_id'],$order_main['balance_amount'],false);
						}
						$string = '您的订单已取消，已退款到您的账户余额，请查收';
					}
				} else {
					// 标记当前子订单为已取消
					$this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
				}
				/* 检测是否标记主订单状态 */
				$sub_count = $already_count = 0;
				// 统计子订单总数
				$sqlmap['order_sn'] = $order['order_sn'];
				$sub_count = $this->table->where($sqlmap)->count();
				// 统计子订单已取消的总数
				$sqlmap['status'] = 2;
				$already_count = $this->table->where($sqlmap)->count();
				// 所有子订单都已取消 ==> 标记主订单为全部取消
				if ($sub_count == $already_count) {
					$result = $this->table_order->where(array('sn' =>$order['order_sn']))->save($data);
					if (!$result) {
						$this->error = $this->table_order->getError();
						return FALSE;
					}
				}
				/* 后台设置为下单减库存 ==> goods_sku加上库存 */
				$stock_change = $this->service_setting->get('stock_change');
				if (isset($stock_change) && $stock_change == 0) {
					$skuids = array();
					if ($order['pay_type'] == 1) {
						$skuids = $this->table_sku->where(array('order_sn' => $order['order_sn'],'buyer_id' => $order['buyer_id']))->getField('sku_id,buy_nums' ,TRUE);
					} else {
						$skuids = $this->table_sku->where(array('sub_sn' => $order['sub_sn'],'buyer_id' => $order['buyer_id']))->getField('sku_id,buy_nums' ,TRUE);
					}
					if ($skuids) {
						foreach ($skuids as $skuid => $num) {
							$this->service_goods_sku->set_inc_number($skuid ,$num);
						}
					}
				}
				return $string;
				break;

			case 3:	// 订单回收站
				if ($order['status'] != 2) {
					$this->error = lang('_valid_access_');
					return FALSE;
				}
				// 标记当前子订单为已回收
				$data['status'] = 3;
				$data['system_time'] = time();
				$result = $this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
				if (!$result) {
					$this->error = $this->table->getError();
					return FALSE;
				}
				/* 检测是否标记主订单状态 */
				$sub_count = $already_count = 0;
				// 统计子订单总数
				$sqlmap['order_sn'] = $order['order_sn'];
				$sub_count = $this->table->where($sqlmap)->count();
				// 统计子订单已取消的总数
				$sqlmap['status'] = 3;
				$already_count = $this->table->where($sqlmap)->count();
				if ($sub_count == $already_count) { // 全部子订单都已取消，标记主订单为全部回收
					$this->table_order->where(array('sn' => $order['order_sn']))->save($data);
				}
				return '您的订单已放入回收站';
				break;

			/* 订单删除 */
			case 4:
				if ($order['status'] != 3) {
					$this->error = lang('_valid_access_');
					return FALSE;
				}
				// 前台用户删除的只更改状态，管理员删除的需删除所有订单相关的信息
				if (defined('IN_ADMIN')) {
					// 删除子订单
					$sqlmap['sub_sn'] = $order['sub_sn'];
					$sqlmap['status'] = 3;
					$result = $this->table->where($sqlmap)->delete();
					if (!$result) {
						$this->error = lang('delete_order_error','order/language');
						return FALSE;
					}
					// 删除订单商品
					$this->table_sku->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单日志
					$this->load->table('order/order_log')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单跟踪
					$this->load->table('order/order_track')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单发货单
					$this->load->table('order/order_parcel')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单发货单日志
					$this->load->table('order/order_parcel_log')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单售后服务表信息
					$this->load->table('order/order_server')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单退货
					$this->load->table('order/order_return')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单退货日志
					$this->load->table('order/order_return_log')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单退款
					$this->load->table('order/order_refund')->where(array('sub_sn' => $order['sub_sn']))->delete();
					// 删除订单退款日志
					$this->load->table('order/order_refund_log')->where(array('sub_sn' => $order['sub_sn']))->delete();
					return '订单删除成功';
				} else {
					// 标记当前子订单为删除
					$data['status'] = 4;
					$data['system_time'] = time();
					$result = $this->table->where(array('sub_sn' => $order['sub_sn']))->save($data);
					if (!$result) {
						$this->error = $this->table->getError();
						return FALSE;
					}
					/* 检测是否标记主订单状态 */
					$sub_count = $already_count = 0;
					// 统计子订单总数
					$sqlmap['order_sn'] = $order['order_sn'];
					$sub_count = $this->table->where($sqlmap)->count();
					// 统计子订单已回收的总数
					$sqlmap['status'] = 4;
					$already_count = $this->table->where($sqlmap)->count();
					if ($sub_count == $already_count) { // 全部子订单都已回收，标记主订单为全部删除
						$this->table_order->where(array('sn' => $order['order_sn']))->save($data);
					}
					return '您的订单已从回收站删除';
				}
				break;
		}
	}

	/**
	 * 确认发货弹窗：根据子订单号获取skus发货信息
	 * @param  string $sub_sn 子订单号
	 * @return [result]
	 */
	public function sub_delivery_skus($sub_sn) {
		return $this->table_sku->where(array('sub_sn' => $sub_sn))->order('delivery_status asc')->select();
	}

	/**
	 * 获取子订单详情
	 * @param  string $sub_sn 子订单号
	 * @param  int    $o_delivery_id 订单物流id
	 * @return [result]
	 */
	public function sub_detail($sub_sn = '' , $o_delivery_id = 0) {
		$result = $main = $sub = array();
		$result = $this->table->where(array('sub_sn' => $sub_sn))->field('order_sn,delivery_status')->find();
		if (!$result) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		$main = $this->get_subs($result['order_sn'] ,TRUE , TRUE, TRUE);
		foreach ($main as $k => $v) {
			if ($v['sub_sn'] == $sub_sn) {
				if ($o_delivery_id || $result['delivery_status'] == 1) {
					foreach ($v['_group'] as $o_d_id => $va) {
						foreach ($va['lists'] as $key => $sku) {
							if ($sku['delivery_status'] != 0) {
								$sqlmap = array();
								$server_count = 0;
								$sqlmap['sub_sn'] = $sku['sub_sn'];
								$sqlmap['o_sku_id'] = $sku['sku_id'];
								$server_count = $this->load->table('order/order_refund')->where($sqlmap)->count();
								if ($server_count == 0) {
									$server_count = $this->load->table('order/order_return')->where($sqlmap)->count();
								}
								$sku['_showserver'] = ($server_count == 0) ? TRUE : FALSE;
							}
							$va['lists'][$key] = $sku;
						}
						$v['_group'][$o_d_id] = $va;
					}
				} else {
					foreach ($v['_skus'] as $key => $sku) {
						if ($sku['delivery_status'] != 0) {
							$sqlmap = array();
							$server_count = 0;
							$sqlmap['sub_sn'] = $sku['sub_sn'];
							$sqlmap['o_sku_id'] = $sku['sku_id'];
							$server_count = $this->load->table('order/order_refund')->where($sqlmap)->count();
							if ($server_count == 0) {
								$server_count = $this->load->table('order/order_return')->where($sqlmap)->count();
							}
							$sku['_showserver'] = ($server_count == 0) ? TRUE : FALSE;
						}
						$v['_skus'][$key] = $sku;
					}
				}
				$sub = $v;
				break;
			}
		}
		// 已发货的详情
		if ($o_delivery_id) {
			$sub['_skus'] = $sub['_group'][$o_delivery_id]['lists'];
			$sub['_delivery'] = $sub['_group'][$o_delivery_id]['delivery'];
			$sqlmap = $data = array();
			$data['delivery_id'] = 0;
			$sqlmap['delivery_id'] = $o_delivery_id;
			$sqlmap['sub_sn'] = $data['sub_sn'] = $sub_sn;
			$sub['_track'] = $this->load->table('order/order_track')->where($sqlmap)->order('id DESC')->select();
			$sub['public'] = $this->load->table('order/order_track')->where($data)->order('id DESC')->select();
			$sub['_track'] = array_merge ($sub['_track'],$sub['public']);
			unset($sub['_group']);
			if ($sub['_delivery']['isreceive'] == 1) {
				$sub['finish_status'] = 2;
				$sub['_status']['now'] = 'all_finish';
				$sub['_status']['wait'] = 'all_finish';
			} else {
				$sub['finish_time'] = 0;
				$sub['_status']['now'] = 'all_delivery';
				$sub['_status']['wait'] = 'load_finish';
			}
			$sub['_axis'] = $this->detail_axis($sub);
			return $sub;
		}
		// 部分发货中待发货的详情
		if ($sub['delivery_status'] == 1) {
			$sub['_status']['now'] = 'all_confirm';
			$sub['_status']['wait'] = 'load_delivery';
			$sub['delivery_status'] = 0;
			$sub['delivery_id'] = 0;
			$sub['finish_status'] = 0;
			$sub['delivery_time'] = 0;
			$sub['finish_time'] = 0;
			$sub['_skus'] = $sub['_group'][0]['lists'];
			unset($sub['_track']);
			unset($sub['_group']);
			//调用订单跟踪数据
			$sqlmap =  array();
			$sqlmap['delivery_id'] = 0;
			$sqlmap['sub_sn'] = $sub_sn;
			$sub['_track'] = $this->load->table('order/order_track')->where($sqlmap)->order('id DESC')->select();
			$sub['_axis'] = $this->detail_axis($sub);
			return $sub;
		}
		$sub['_axis'] = $this->detail_axis($sub);
		return $sub;
	}

	private function detail_axis($sub) {
		$result = array();
        $result['create'] = $sub['system_time'];
        if ($sub['pay_type'] == 1) {
            $result['pay'] = $sub['pay_time'];
        }
        if ($sub['status'] != 1) {
        	switch ($sub['status']) {
        		case 2:
        			if ($sub['pay_status'] == 0 && $sub['pay_type'] == 1) {
        				unset($result['pay']);
        			}
        			$result['cancel'] = $sub['system_time'];
        			break;
        		case 3:
        			if ($sub['pay_status'] == 0 && $sub['pay_type'] == 1) {
        				unset($result['pay']);
        			}
        			$result['cancel'] = $sub['system_time'];
        			$result['recycle'] = $sub['system_time'];
        			break;
        	}
        	return $result;
        }
        $result['confirm'] = $sub['confirm_time'];
        $result['delivery'] = $sub['delivery_time'];
        $result['finish'] = $sub['finish_time'];
        return $result;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function fetch($sqlmap = array(), $limit = 20, $page = 1, $order = "", $field = "") {
		$result = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->field($field)->select();
		if($result===false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
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
	/**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getField($field = '', $sqlmap = array()) {
		$result = $this->table->where($sqlmap)->getfield($field);
		if($result === false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
}