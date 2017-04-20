<?php
class notify_template_service extends service
{
	public function _initialize() {
		$this->table = $this->load->table('notify/notify_template');
	}

	/* 根据标识 */
	public function fetch_by_code($code) {
		$data = $this->table->where(array('id'=>$code))->find();
		if(is_null($data))return FALSE;
		$data['enabled'] = json_decode($data['enabled'], TRUE);
		$data['template'] = json_decode($data['template'], TRUE);
        return $data;
	}
	
	/* 根据标识 */
	public function fetch_by_hook($hook) {
		$data = $this->table->where(array('_string'=>'enabled regexp \''.$hook.'\''))->select();
		if(is_null($data))return FALSE;
		foreach($data as $k=>$v){
			$data[$k]['enabled'] = json_decode($v['enabled'], TRUE);
			$data[$k]['template'] = json_decode($v['template'], TRUE);
			$data[$k]['template'] = $data[$k]['template'][$hook];
		}
        return $data;
	}
	/*导入模版*/
	public function import($params){
		$drivers = json_decode($params['driver'],TRUE);
		$template = json_decode($params['template'],TRUE);
		switch ($params['id']) {
			case 'n_order_success':
				$code = 'create_order';
				break;
			case 'n_pay_success':
				$code = 'pay_success';
				break;
			case 'n_confirm_order':
				$code = 'confirm_order';
				break;
			case 'n_order_delivery':
				$code = 'order_delivery';
				break;
			case 'n_recharge_success':
				$code = 'recharge_success';
				break;
			case 'n_money_change':
				$code = 'money_change';
				break;
			case 'n_goods_arrival':
				$code = 'goods_arrival';
				break;
			case 'n_back_pwd':
				$code = 'forget_pwd';
				break;
			case 'n_reg_validate':
				$code = 'register_validate';
				break;
			case 'n_reg_success':
				$code = 'register_success';
				break;
			default:
				break;
		}
		foreach ($drivers AS $key => $driver) {
			if($driver == 1){
				$data = array();
				$data['id'] = $key;
				$notifys = array();
				$notifys = $this->table->find($data);
				$notify_code = $notify_template = array();
				if(!empty($notifys)){
					$notify_code = json_decode($notifys['enabled'],TRUE);
					$notify_template = json_decode($notifys['template'],TRUE);
				}
				$notify_code[] = $code;
				$notify_template[$key] = $template[$key];
				$data['enabled'] = json_encode($notify_code);
				$data['template'] = json_encode($notify_template);
				$this->table->add($data);
			}
		}
		return TRUE;
	}
	/**
	 * [update 更新数据]
	 * @param  [type] $params [参数]
	 * @return [type]         [description]
	 */
	public function update($params){
		if(empty($params)){
			$this->error = lang('_params_error_');
			return false;
		}
		$result = $this->table->update($params);
		if($result === false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
	/**
	* [删除]
	* @param array $ids 主键id
	*/
	public function delete($ids) {
		if(empty($ids)) {
			$this->error = lang('_param_error_');
			return false;
		}
		$_map = array();
		if(is_array($ids)) {
			$_map['id'] = array("IN", $ids);
		} else {
			$_map['id'] = $ids;
		}
		$result = $this->table->where($_map)->delete();
		if($result === false) {
			$this->error = $this->table->getError();
			return false;
		}
		return true;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function fetch($sqlmap = array(), $limit = 20, $page = 1, $order = "") {
		$result = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		if($result===false){
			$this->error = lang('_param_error_');
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
}