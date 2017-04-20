<?php
class admin_user_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin_user');
		$this->group_service = $this->load->service('admin_group');
	}

	/* 管理团队 */
	public function index() {
		$data = $this->service->get_lists();
		$lists = array(
            'th' => array(
                'username' => array('title' => '用户名','length' => 30,'style' => 'double_click'),
                'group_name' => array('title' => ' 所属分组','length' => 20),
                'last_login_time' => array('title' => ' 最后登录时间','length' => 25,'style' => 'date'),
                'login_num' => array('length' => 10,'title' => '共计登录次数'),
            ),
            'lists' => $data,
        );
		$this->load->librarys('View')->assign('lists',$lists)->display('admin_user_index');
	}

	/* 删除 */
	public function del() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$result = $this->service->delete($_GET['id']);
		if($result === FALSE) showmessage($this->service->error);
		showmessage(lang('_del_admin_user_success_','admin/language'), url('index'), 1);
	}
	/* 添加 */
	public function add() {
		if(checksubmit('dosubmit')){
			$r = $this->service->save($_POST);
			if($r == false) {
				showmessage($this->service->error, url('index'), 1);
			}else{
				showmessage(lang('_update_admin_group_success_','admin/language'), url('index'), 1);
			}
		}else{
			$group = $this->group_service->get_select_data();
			if($this->group_service->count() == 1) showmessage('请先添加角色管理', url('admin/admin_group/add'), 1);
			$this->load->librarys('View')->assign('group',$group)->display('admin_user_update');
		}
	}
	/* 编辑 */
	public function edit() {
		if (checksubmit('dosubmit')) {
			$r = $this->service->save($_POST,FALSE);
			showmessage(lang('_update_admin_group_success_','admin/language'), url('index'), 1);
		} else {
			$group = $this->group_service->get_select_data();
			$data = $this->service->fetch_by_id($_GET['id']);
			$this->load->librarys('View')->assign($data,$data)->assign('group',$group)->display('admin_user_update');
		}

	}
	public function edit_title(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$result = $this->service->setField(array('username' => $_GET['title']),array('id' => $_GET['id']));
		if($result){
			showmessage(lang('_operation_success_','ads/language'), url('index'), 1);
		}else{
			showmessage($this->service->error, url('index'), 0);
		}
	}
}
