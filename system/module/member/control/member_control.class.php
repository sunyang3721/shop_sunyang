<?php
hd_core::load_class('init', 'admin');
class member_control extends init_control {
    public function _initialize() {
        parent::_initialize();
        $this->service = $this->load->service('member/member');
    }

    public function index() {
        $sqlmap = array();
        $sqlmap['username|email|mobile'] = array("LIKE", '%'.$_GET['keyword'].'%');
        if($_GET['group_id']){
            $sqlmap['group_id'] = array('EQ',$_GET['group_id']);
        }

        $limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
        $lists = $this->service->get_lists($sqlmap,$_GET['page'],$limit);
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $limit);
        $member_group = $this->load->service('member/member_group')->getfield('id,name');
        array_unshift($member_group,'所有等级');
        $lists = array(
            'th' => array(
                'username' => array('title' => '会员','length' => 25,'style' => 'member'),
                'member_level' => array('title' => '等级&经验','length' => 20,'style' => 'level'),
                'money' => array('length' => 15,'title' => '账户余额','style' => 'money'),
                'login' => array('title' => '注册&登录','length' => 20,'style' => 'login'),
                'lock' => array('length' => 10,'title' => '状态'),
            ),
            'lists' => $lists,
            'pages' => $pages,
        );
        $this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->assign('member_group',$member_group)->display('member_index');
    }

    public function update() {
        $id = (int) $_GET['id'];
        $member = $this->service->fetch_by_id($id);
        if(!$member) showmessage($this->service->error);
        if(checksubmit('dosubmit')) {
            foreach ($_POST['info'] as $t => $v) {
                if(is_numeric($v['num']) && !empty($v['num'])) {
                    $v['num'] = ($v['action'] == 'inc') ? '+'.$v['num'] : '-'.$v['num'];
                    $this->service->change_account($id, $t, $v['num'], $_POST['msg']);
                }
            }
            showmessage('_operation_success_', url('index'), 1);
        } else{
            $this->load->librarys('View')->assign('member',$member)->display('member_update');
        }
    }

    public function delete() {
        if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) {
            showmessage('_token_error_',url('index'),0);
        }
        $result = $this->service->delete_by_id($_GET['ids']);
        showmessage('_operation_success_',url('index'),1);
    }
	public function togglelock() {
        if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) {
           showmessage('_token_error_',url('index'),0);
        }
        $ids = (array) $_GET['ids'];
        $result = $this->service->togglelock_by_id($ids,$_GET['type']);
        showmessage('_operation_success_',url('index'),1);
    }

    public function address() {
        $_GET['mid'] = (int) $_GET['mid'];
        if((int) $_GET['has_address'] == 1){
            $limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 5;
            $lists = $this->load->service('member/member_address')->lists(array('mid' => $_GET['mid']), $limit,$_GET['page']);
        }
        $pages = $this->admin_pages($lists['count'], $limit);
        $this->load->librarys('View')->assign('pages',$pages)->assign('lists',$lists)->display('member_address');
    }
}
