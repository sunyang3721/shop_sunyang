<?php
/**
 * 		发货单日志 模型
 */
class order_parcel_log_table extends table {
	
	protected $_validate = array(
        // 订单号
		array('order_sn', 'require', '{order/order_require}',0),
        array('sub_sn', 'require', '{order/order_sub_require}',0),
    );

	
	/*添加日志*/
	public function add_log($params,$log){
		if(!$params){
			$this->error = lang('_error_action_');
			return false;
		}
		$data = array();
		$data['parcel_id'] = $params['id'];
		$data['order_sn'] = $params['order_sn'];
		$data['sub_sn'] = $params['sub_sn'];
		$data['member_name'] = $params['member_name'];
		$data['msg'] = $log;
		$data['operator_id'] = (int)ADMIN_ID;
		$data['buyer_id'] = $this->load->table('order_sub')->where(array('sub_sn'=>array('eq',$data['sub_sn'])))->getField('buyer_id');
		$data['operator_name'] = $this->load->table('admin_user')->where(array('id'=>$data['operator_id']))->getField('username');
		$data['system_time'] = time();
		switch($params['parcel_status']){
			case 1:
				$action = '{order/thsi_operator_a}';
				break;
			case -1:
				$action = '{order/thsi_operator_b}';
				break;
			case 2:
				$action = '{order/thsi_operator_c}';
				break;
			default:
				$action = '{order/thsi_operator_d}';
		}
		$data['action'] = $action;
		$result = $this->update($data);
		if(!$result){
			$this->error = $this->getError();
			return false;
		}
		return true;
	}

}