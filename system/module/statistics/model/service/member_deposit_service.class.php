<?php
/**
 *      统计服务层
 */

class member_deposit_service extends service {

	public function _initialize() {
		$this->deposit_model = $this->load->table('member_deposit');
	}

	public function _query($field,$sqlmap,$group){
		return $this->deposit_model->field($field)->where($sqlmap)->group($group)->select();
	}

}
