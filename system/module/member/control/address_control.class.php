<?php
class address_control extends cp_control
{
	public function _initialize() {
		parent::_initialize();
		if($this->member['id'] < 1) {
			redirect(url('cp/index'));
		}
		$this->service = $this->load->service('member/member_address');
	}

	public function index() {
		$sqlmap = array();
		$sqlmap['mid'] = $this->member['id'];
		$result = $this->service->lists($sqlmap);
		$pages = pages($count, 10);
		$SEO = seo('收货地址 - 会员中心');
		$this->load->librarys('View')->assign('pages',$pages)->assign($result,$result)->assign('SEO',$SEO)->display('address');
	}

	public function add() {
		if(checksubmit('dosubmit')) {
			$_GET['mid'] = $this->member['id'];
			$result = $this->service->add($_GET);
			if($result === FALSE) {
				showmessage($this->service->error);
			}
			showmessage(lang('_operation_success_'), url('index'), 1);
		} else {
			$SEO = seo('添加收货地址 - 会员中心');
			$this->load->librarys('View')->assign('SEO',$SEO)->display('address_add');
		}
	}

	public function edit() {
		$_GET['id'] = (int) $_GET['id'];
		if($_GET['id'] < 1) {
			showmessage(lang('_param_error_'));
		}
		$address = $this->service->mid($this->member['id'])->fetch_by_id($_GET['id']);
		if(!$address) {
			showmessage(lang('_valid_access_'));
		}
		if(checksubmit('dosubmit')) {
			$_GET['mid'] = $this->member['id'];
			$result = $this->service->edit($_GET);
			if($result === FALSE) {
				showmessage($this->service->error);
			}
			showmessage(lang('_operation_success_'), url('index'), 1);
		} else {
			$SEO = seo('编辑收货地址 - 会员中心');
			$this->load->librarys('View')->assign('SEO',$SEO)->assign('address',$address)->display('address_edit');
		}
	}

	public function delete() {
		$id = (int) $_GET['id'];
		if($id < 1) showmessage(lang('_param_error_'));
		$result = $this->service->mid($this->member['id'])->delete($id);
		if($result === FALSE) {
			showmessage($this->service->error);
		}
		showmessage(lang('_operation_success_'), url('index'), 1);
	}

	/* 设置默认 */
	public function set_default() {
		$id = (int) $_GET['id'];
		if($id < 1) {
			showmessage(lang('_param_error_'));
		}
		$r = $this->service->mid($this->member['id'])->fetch_by_id($id);
		if(!$r) showmessage(lang('_valid_access_'));
		$result = $this->service->set_default($id, $this->member['id']);
		if($result === FALSE) {
			showmessage($this->service->error);
		}
		showmessage(lang('_operation_success_'), url('index'), 1);
	}

	public function ajax_district(){
		$id = (int) $_GET['id'];
		$result = $this->load->service('admin/district')->get_children($id);
		$this->load->librarys('View')->assign('result',$result);
		$result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}

}