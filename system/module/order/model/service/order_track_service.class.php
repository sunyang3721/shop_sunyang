<?php
/**
 * 		订单跟踪服务层
 */
class order_track_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('order/order_track');
		$this->table_delivery = $this->load->table('order/delivery');
		$this->table_o_delivery = $this->load->table('order/order_delivery');
	}

	/**
	 * 添加订单跟踪
	 * @param  string 	$order_sn  	主订单号 (必传)
	 * @param  string 	$sub_sn  	子订单号 (必传)
	 * @param  string 	$msg  		跟踪内容
	 * @param  int 		$time  		时间戳 (默认当前时间戳)
	 * @param  int 		$delivery_id 订单物流关联id (默认0)
	 * @return [boolean]
	 */
	public function add($order_sn ,$sub_sn ,$msg = '' ,$time = 0 ,$delivery_id = 0) {
		$order_sn = (string) remove_xss($order_sn);
		$sub_sn = (string) remove_xss($sub_sn);
		if (!$order_sn) {
			$this->error = lang('parent_order_sn_empty','order/language');
			return FALSE;
		}
		if (!$sub_sn) {
			$this->error = lang('child_order_sn_empty','order/language');
			return FALSE;
		}
		$data = array();
		$data['order_sn'] = $order_sn;
		$data['sub_sn']   = $sub_sn;
		$data['msg']      = (string) remove_xss($msg);
		$data['time']     = ((int) $time == 0) ? time() : $time;
		if ($delivery_id > 0) $data['delivery_id'] = (int) remove_xss($delivery_id);
		$result = $this->table->update($data);
		if (!$result) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		return $result;
	}
	/**
	 * [order_track_import 订单跟踪导入]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function order_track_import($params){
		$data = $this->table->create($params);
		return $this->table->add($data);
	}
	/**
	 * 更新快递100数据
	 * @param  string 	$sub_sn  子订单号 (必传)
	 * @param  string 	$o_d_id  订单物流主键id (必传)
	 * @return [boolean]
	 */
	public function update_api100 ($sub_sn,$o_d_id) {
		$sub_sn = (string) remove_xss($sub_sn);
		if (!$sub_sn) {
			$this->error = lang('child_order_sn_empty','order/language');
			return FALSE;
		}
		$o_d_id = (int) $o_d_id;
		$o_d_info = $this->table_o_delivery->find($o_d_id);
		if (!$o_d_info) {
			$this->error = lang('order_logistics_not_exist','order/language');
			return FALSE;
		}
        if(!$o_d_info['delivery_sn']){
        	$this->error = lang('waybill_sn_not_exist','order/language');
        	return FALSE;
        }
        // 获取物流标识
       	$identif = $this->table_delivery->getFieldById($o_d_info['delivery_id'] ,'identif');
       	if (!$identif) {
       		$this->error = lang('deliver_identify_not_exist','order/language');
       		return FALSE;
       	}
        $datas = $this->kuaidi100($identif,$o_d_info['delivery_sn']);
        if ($datas == FALSE) return FALSE;
		krsort($datas['data']);
		// 统计当前已发货物流跟踪条数、和最后一条记录
		$sqlmap = array();
		$sqlmap['delivery_id'] = $o_d_id;
		$sqlmap['sub_sn'] = $sub_sn;
		$count = $this->table->where($sqlmap)->count();
		$track = $this->table->where($sqlmap)->field('order_sn,time')->order('id DESC')->find();
		foreach($datas['data'] as $v){
			$_time = strtotime($v['time']);
			if ($count != 1 && $_time <= $track['time']) {
				continue;
			}
			$this->add($track['order_sn'],$sub_sn,$v['context'],$_time,$o_d_id);
		}
        return TRUE;
	}

	/**
	 * 根据子订单号获取订单跟踪列表
	 * @param  $sub_sn  	子订单号 (必传)
	 * @param  $order  		排序(默认id降序)
	 * @return [result]
	 */
	public function get_tracks_by_sn($sub_sn ,$order = 'id DESC') {
		$sub_sn = (string) remove_xss($sub_sn);
		$order  = (string) remove_xss($order);
		if (empty($sub_sn)) {
			$this->error = lang('child_order_sn_empty','order/language');
			return FALSE;
		}
		$sqlmap = array();
		$sqlmap['sub_sn'] = $sub_sn;
		return $this->table->where($sqlmap)->order($order)->select();
	}

	/**
	 * 根据快递单号查询快递100获取快递信息
	 * @param  string 	$com  	快递代码 (必传)
	 * @param  string 	$nu  	快递单号 (必传)
	 * @return [result]
	 */
	public function kuaidi100($com , $nu) {
		if(empty($com) || empty($nu)) {
			$this->error = lang('submit_parameters_error','order/language');
			return FALSE;
		}
		$url = 'http://www.kuaidi100.com/query?';
		$par = array();
		$par['id']     = 1;		// id
		$par['type']   = $com;	// 物流代码
		$par['postid'] = $nu;	// 快递单号
		$result = dfsockopen($url.http_build_query($par));
		if($result) {
			$result =  json_decode($result, TRUE);
			if ($result['status'] == 200) {
				unset($result['status']);
				switch ($result['state']) {
					case '0':
						$result['message'] = '在途';
						break;
					case '1':
						$result['message'] = '揽件';
						break;
					case '2':
						$result['message'] = '疑难';
						break;
					case '3':
						$result['message'] = '签收';
						break;
					case '4':
						$result['message'] = '退签';
						break;
					case '5':
						$result['message'] = '派件';
						break;
					case '6':
						$result['message'] = '退回';
						break;
					default:
						$result['message'] = '其他';
						break;
				}
				return $result;
			} else if($result['status'] == 201) {
				$this->error = $result['message'];
				return FALSE;
			} else if($result['status'] == 2) {
				$this->error = lang('connector_error','order/language');
				return FALSE;
			} else {
				$this->error = lang('logistics_no_msg','order/language');
				return FALSE;
			}
		} else {
			$this->error = lang('inquire_error','order/language');
			return FALSE;
		}
	}
}