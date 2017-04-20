<?php
/**
 * 		订单发货单模版 服务层
 */
class order_tpl_parcel_service extends service {

	public function _initialize() {
		$this->model = $this->load->table('order/order_tpl_parcel');
	}

	/**
	 * 修改
	 * @param  $content  	内容
	 * @return [boolean]
	 */
	public function update( $content = '',$extra = FALSE) {
		$data = array();
		$data['id']      = 1;
		$data['name']    = '发货单模版';
		$data['content'] = (string) $content;
		$info = $this->model->where(array('id' => 1))->find();
		if($info){
			$result = $this->model->update($data);
		}else{
			$result = $this->model->add($data);
		}
		
		if (!$result) {
			$this->error = $this->model->getError();
			return FALSE;
		}
		return $result;
	}
	/**
	 * [add 导入发货单模版]
	 * @param [type] $params [description]
	 */
	public function import($params,$extra = FALSE){
		return $this->update($params['content'],$extra);
	}
	/**
	 * 获取订单发货单模版详情
	 * @param  $id  	主键ID
	 * @return [result]
	 */
	public function get_tpl_parcel_by_id($id = 0) {
		$id = (int) trim($id);
		if ($id < 1) {
			$this->error = lang('deliver_order_model_id_foemat_error','order/language');
			return FALSE;
		}
		$result = $this->model->find($id);
		if (!$result) {
			$this->error = lang('no_found_data','order/language');
			return FALSE;
		}
		return $result;
	}	
}