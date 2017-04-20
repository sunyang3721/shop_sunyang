<?php
class template_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->model = $this->load->service('admin/template');
	}

	public function index() {
		$tpls = $this->model->fetch_all();
		$this->load->librarys('View')->assign('tpls',$tpls)->display('template');
	}

	public function setdefault() {
		$theme = trim($_GET['theme']);
		if(empty($theme)) {
			showmessage(lang('theme_empty','admin/language'));
		}
		$tpls = $this->model->fetch_all();
		if(!$tpls[$theme]) {
			showmessage(lang('theme_no_exist','admin/language'));
		}
		$load = hd_load::getInstance();
		$this->config = $load->librarys('hd_config');
		$this->config->file('template')->note('模板配置配置文件')->space(32)->to_require_one(array('TPL_THEME' => $theme));
		$this->load->service('admin/cache')->template();
		$this->load->service('admin/cache')->taglib();
		showmessage(lang('_operation_success_'), url('index'), 1);
	}
}