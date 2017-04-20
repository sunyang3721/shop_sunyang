<?php
define('IN_ADMIN', TRUE);
class public_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin');
		$this->admin_menu_service = $this->load->service('admin/admin_menu');
	}
    
    /* 管理登录 */
	public function login() {		
        if(checksubmit('dosubmit')) {
            $result = $this->service->login($_GET['username'], $_GET['password']);
            if($result === FALSE) {
                showmessage($this->service->error);
            } else {
                redirect(url('index/index'));
            }
        } else {
        	$this->load->librarys('View')->display('login');
        }
	}
    
    public function logout() {
        $this->service->logout();
        redirect(url('public/login'));
    }
	
	//添加自定义菜单
	public function ajax_menu_add(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$_m = $_c = $_a = '';
		$data = array();
		extract($_GET,EXTR_IF_EXISTS);
		$data['admin_id'] = ADMIN_ID;
		$data['url'] = url("$_m/$_c/$_a");
		$data['title'] = $this->load->service('admin/node')->getField('name', array('m'=>$_m,'c'=>$_c,'a'=>$_a));
		if($this->admin_menu_service->count($data) == 0 ){
			$this->admin_menu_service->update($data);
			showmessage(lang('add_menu_success','admin/language'),'',1);
		}
		showmessage(lang('menu_exist','admin/language'),'',0);
	}
	//删除自定义菜单
	public function ajax_diymenu_del(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$ids = '';
		extract($_GET,EXTR_IF_EXISTS);
		$ids = explode(',', $ids);
		$this->admin_menu_service->delete($ids);
	}
	//获取自定义菜单
	public function ajax_menu_refresh(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$menus = $this->admin_menu_service->setAdminid(ADMIN_ID)->getAll();
		$html = '';
		foreach($menus as $k=>$v){
			$html.="<li><a href='{$v["url"]}'>{$v["title"]}</a></li> ";
		}
		$this->load->librarys('View')->assign('html',$html);
		$html = $this->load->librarys('View')->get('html');
		echo json_encode($html);
		
	}
}