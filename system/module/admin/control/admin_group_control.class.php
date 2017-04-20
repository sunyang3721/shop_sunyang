<?php
class admin_group_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin_group');
	}

	/* 团队角色 */
	public function index() {
		$data = $this->service->getAll();
		$lists = array(
            'th' => array(
                'title' => array('title' => '权限名','length' => 20),
                'description' => array('title' => ' 权限描述','length' => 50),
                'status' => array('title' => ' 是否启用','length' => 10,'style' => 'status'),
            ),
            'lists' => $data,
        );
        $this->load->librarys('View')->assign('lists',$lists)->display('admin_group_index');
	}

	/* 删除 */
	public function del() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$result = $this->service->delete($_GET['id']);
		if($result === FALSE) showmessage($this->service->error);
		showmessage(lang('_del_admin_group_success_','admin/language'), url('index'), 1);
	}
	/* 添加 */
	public function add() {
		if(checksubmit('dosubmit')){
			$_GET=array_filter($_GET);
			$r = $this->service->save($_GET);
			if(!$r)showmessage($this->service->getError(), url('index'), 1);
			showmessage(lang('_update_admin_group_success_','admin/language'), url('index'), 1);
		}else{
			//节点
			$this->node = $this->load->service('node');
			$nodes = $this->node->get_checkbox_data();
			$nodes = list_to_tree($nodes);
			$this->load->librarys('View')->assign('nodes',$nodes)->display('admin_group_update');
		}
	}
	/* 编辑 */
	public function edit() {
		if (checksubmit('dosubmit')) {
			$_GET=array_filter($_GET);
			$_GET['id'] = (int) $_GET['id'];
			if($_GET['id'] > 1) {
				$r = $this->service->save($_GET);
				if($r === false) {
					showmessage($this->service->getError(), url('index'));
				}
			}
			showmessage(lang('_update_admin_group_success_','admin/language'), url('index'), 1);
		} else {
			//个人信息
			$data = current($this->service->getAll(array('id'=>$_GET['id'])));
			$data['rules'] = explode(',',$data['rules']);
			//节点
			$this->node = $this->load->service('node');
			$nodes = $this->node->get_checkbox_data();
			$nodes = list_to_tree($nodes);
			$this->load->librarys('View')->assign('nodes',$nodes)->assign('data',$data)->display('admin_group_update');
		}
	}


	public function ajax_status() {
		$id = $_GET['id'];
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		if((int)$id === 1) showmessage(lang('_update_admin_group_success_','admin/language'));
		if ($this->service->change_status($id)) {
			showmessage(lang('_status_success_','admin/language'), '', 1);
		} else {
			showmessage($this->service->error, '', 0);
		}
	}

}
