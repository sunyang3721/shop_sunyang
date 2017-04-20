<?php
set_time_limit(0);  
ignore_user_abort(true); 
class queue_control extends control {
	public function _initialize() {
		parent::_initialize();
		$this->queue = $this->load->service('notify/queue');
		$this->table = $this->load->table('notify/queue', 'table');
		$this->cloud = $this->load->service('admin/cloud');
	}
}
