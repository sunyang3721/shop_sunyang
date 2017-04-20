<?php
class message_control extends cp_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('member/member_message');
	}
	/* 系统消息 */
	public function index() {
		$sqlmap = array();
		$sqlmap['mid'] = $this->member['id'];
		if(isset($_GET['status'])){
			$sqlmap['status'] = (int)$_GET['status'];
		}
		$_GET['limit'] = $_GET['limit'] ? $_GET['limit'] : 15;
		$result = $this->service->lists($sqlmap, $_GET['limit'], $_GET['page'], 'dateline desc');
		$pages = pages($result['count'],$_GET['limit']);
		$SEO = seo('我的消息 - 会员中心');
		$this->load->librarys('View')->assign('result',$result['lists'])->assign('pages',$pages)->assign('SEO',$SEO)->display('message');
	}
	/* 已读 */
	public function ajax_update(){
		if(!$this->member['id']){
			showmessage(lang('_param_error_'));
			return FALSE;
		}
		$result = $this->service->ajax_update($_GET['id']);
		if($result === FALSE){
			showmessage($this->service->error,'',0);
		}
		showmessage(lang('_operation_success_'),'',1);
	}
	/* 删除 */
	public function delete(){
		if(!$this->member['id']){
			showmessage(lang('_param_error_'));
			return FALSE;
		}
		$result = $this->service->delete($_GET['id']);
		showmessage(lang('_operation_success_'),'',1,'json');
	}
}