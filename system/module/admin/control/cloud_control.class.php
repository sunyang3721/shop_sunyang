<?php
class cloud_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin/cloud');
	}

	public function index() {
		$this->service->update_site_userinfo();
		$cloud = $this->service->get_account_info();
		$site_isclosed = (int)$this->load->service('admin/setting')->get('site_isclosed');
		include $this->admin_tpl('cloud_index');
	}
	public function bulid(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->getMemberLogin($_GET['account'], $_GET['password']);
			if($result){
				showmessage(lang('_operation_success_'), url('index'), 1,$result);
			}else{
				showmessage($this->service->error, url('index'), 0);
			}
		}else{
			include $this->admin_tpl('cloud_bulid');
		}
	}
	public function getcloudstatus(){
		$cloud = $this->service->get_account_info();
		if(!isset($cloud)){
			$r = 2;
		}else{
			$r = $cloud ? $this->service->getcloudstatus() : false ;
		}
		showmessage('', '', $r);
	}
	public function bind(){
		$result = $this->service->site_bind($_GET['identifier']);
		if($result){
			showmessage('绑定成功','',1,$result);
		}else{
			showmessage($this->service->error,'',0);
		}
	}
}