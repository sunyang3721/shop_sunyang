<?php
class log_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('log');	
	}

	/* 日志列表 */
	public function index() {
		$sqlmap = array();
		$count = $this->service->count($sqlmap);
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$log = $this->service->get_lists($sqlmap,$_GET['page'],$_GET['limit']);
		$pages = $this->admin_pages($count, $limit);
		$this->load->librarys('View')->assign('log',$log)->assign('pages',$pages)->display('log_index');
	}
	
	/* 删除 */
	public function del() {
		$id = (array)$_GET['id'];
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$result = $this->service->delete($id);
		if($result === false) showmessage($this->service->error);
		showmessage(lang('admin/_del_log_success_'), url('index'), 1);
	}
}
