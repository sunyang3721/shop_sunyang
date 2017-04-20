<?php
class index_control extends init_control
{
    public function _initialize() {
		parent::_initialize();
		$this->node = $this->load->service('admin/node');
        $this->menu = $this->load->service('admin/admin_menu');
	}

	public function index() {
		/* 自定义菜单 */
		$menus = $this->menu->fetch_all_by_admin_id($this->admin['id']);
		/* 查询菜单列表 */
		$nodes = $this->node->fetch_all_by_ids($this->admin['rules'], 1);
        $nodes = list_to_tree($nodes);
        $this->load->librarys('View')->assign('nodes',$nodes)->assign('menus',$menus)->display('index');
	}
}