<?php
hd_core::load_class('init', 'admin');
class member_group_control extends init_control {
    public function _initialize() {
        parent::_initialize();
        $this->service = $this->load->service('member_group');
    }

    public function index() {
        $sqlmap = array();
        $groups = $this->service->get_lists($sqlmap,$_GET['page'],$_GET['limit']);
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, 10);
        $lists = array(
            'th' => array(
                'name' => array('title' => '等级名称','length' => 25),
                'min_points' => array('title' => '最小经验','length' => 20),
                'max_points' => array('length' => 15,'title' => '最大经验'),
                'discount' => array('title' => '折扣率','length' => 20),
            ),
            'lists' => $groups,
            'pages' => $pages,
        );
        $this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('member_group');
    }

    public function add(){
        if(checksubmit('dosubmit')) {
            $result = $this->service->add($_GET);
            if(!$result) {
                showmessage($this->service->error);
            }
            showmessage('_operation_success_', url('index'), 1);
        } else {
            $this->load->librarys('View')->display('member_group_add');
        }
    }

    public function edit() {
        $r = $this->service->fetch_by_id($_GET['id']);
        if(!$r) showmessage($this->service->error);
	    if(checksubmit('dosubmit')) {
           $result = $this->service->edit($_GET);
            if(!$result) {
                showmessage($this->service->error);
            }
            showmessage('_operation_success_', url('index'), 1);
	    } else {
            $this->load->librarys('View')->assign('r',$r)->display('member_group_edit');
		}
    }

    public function delete() {
        if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) {
            showmessage('_token_error_',url('index'));
        }
        $ids = (array) $_GET['id'];
        $result = $this->service->delete_by_id($ids);
        showmessage('_operation_success_',url('index'),1);
    }
}
