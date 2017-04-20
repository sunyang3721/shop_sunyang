<?php
class menu_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin_menu');
	}

    /* 管理登录 */
	public function index() {
		$menus = $this->service->fetch_all_by_admin_id($this->admin['id']);
		$this->load->librarys('View')->assign('menus',$menus)->display('menu_index');
	}
}