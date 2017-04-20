<?php
class member_deposit_service extends service
{
	protected $sqlmap = array();

	public function _initialize(){
		$this->table = $this->load->table('member_deposit');
	}

	public function wlog($data = array(),$sqlmap = array()) {
		if(!empty($sqlmap) && $sqlmap !== TRUE){
			$r = $this->table->where($sqlmap)->save($data);
		}else{
			$r = $this->table->add($data);
		}
		return $r;
	}
	public function is_sucess($sqlmap){
		return  $this->table->where($sqlmap)->order('id DESC')->getField('order_status');
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