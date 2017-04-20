<?php
class global_control extends init_control
{
	public function _initialize() {
		parent::_initialize();

	}

    /* 管理登录 */
	public function index() {
		$this->load->librarys('View')->display('global_index');
    }
}