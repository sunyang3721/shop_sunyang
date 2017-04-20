<?php
class wap_template_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('wap/wap_template');
	}

	/**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getField($field = '', $sqlmap = array()) {
		//field是否存在',' 
		$exist = strpos($field, ',');
		if($exist === false){
			$result = $this->table->where($sqlmap)->getfield($field);
		}else{
			$result = $this->table->where($sqlmap)->field($field)->select();
		}
		if($result===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
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
}