<?php
class admin_setup_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->model = $this->load->table('admin/admin_user');
		$this->service = $this->load->service('admin/admin');
	}
	/* 管理员资料 */
	public function edit() {
		if (checksubmit('dosubmit')) {
			$result = $this->service->update($_GET,$_FILES);
			if(!$result) 
				showmessage($this->service->error);
			$this->service->logout();  
			showmessage(lang('relogin','admin/language'),url('admin/public/login'),1);	
		}else{
			$this->load->librarys('View')->display('admin_setup_index');
		}
	}
	/*ajax验证密码*/
	public function ajax_password(){
		$result = $this->service->ajax_password($_GET['param']);
		if(!$result){
			showmessage($this->service->error);
		}
		showmessage('', '', 1);
	}
}
