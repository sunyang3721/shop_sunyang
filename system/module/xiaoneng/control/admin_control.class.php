<?php
hd_core::load_class('init', 'admin');
class admin_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->xneng_service = $this->load->service('xiaoneng');
	}

	public function index(){
		$info = $this->xneng_service->detail();
		$status = $this->xneng_service->get_config('status');
		$this->load->librarys('View')->assign('status',$status)->assign('info',$info)->display('index');
	}

	public function apply(){
		$config = unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
		if(!$config){
			showmessage('请先绑定云平台',url('admin/cloud/index'));
		}
		if(checksubmit('dosubmit')){
			$result = $this->xneng_service->apply($_GET);
			if(!$result){
				showmessage($this->xneng_service->error);
			}else{
				showmessage('开通成功！',url('index'));
			}
		}else{
			$this->load->librarys('View')->display('apply');
		}
	}

	public function distrib(){
		if(checksubmit('dosubmit')){
			$result = $this->xneng_service->update($_GET);
			if(!$result){
				showmessage($this->xneng_service->error);
			}else{
				showmessage('保存成功！');
			}
		}else{
			$info = $this->xneng_service->get_config('config');
			$select = $this->xneng_service->fetch_all_config();
			if(empty($select)) showmessage('请先配置接待组信息',url('edit'));
			$this->load->librarys('View')->assign('info',$info)->assign('select',$select)->display('distrib');
		}
	}

	public function edit(){
		if(checksubmit('dosubmit')){
			$result = $this->xneng_service->edit($_GET);
			if(!$result){
				showmessage($this->xneng_service->error);
			}else{
				showmessage('保存成功！');
			}
		}else{
			$infos = $this->xneng_service->get_server_config();
			$this->load->librarys('View')->assign('infos',$infos)->display('edit');
		}
	}

	public function update(){
		$result = $this->xneng_service->update($_GET);
		if(!$result){
			showmessage($this->xneng_service->error,'',0);
		}else{
			showmessage('状态改变成功！','',1);
		}
	}
}