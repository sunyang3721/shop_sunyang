<?php
/**
 * 		物流地区服务层
 */
class delivery_district_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('order/delivery_district');
	}

	public function import($params){
		return $this->table->update($params);
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