<?php
hd_core::load_class('init', 'admin');
class member_log_control extends init_control
{
    public function _initialize() {
        parent::_initialize();
        $this->service = $this->load->service('member_log');
    }

    public function index() {
        $sqlmap = $this->service->build_sqlmap($_GET);
        if($sqlmap === false) showmessage($this->service->error, url('index'), 1);
        $limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? (int) $_GET['limit'] :20;
        $logs = $this->service->get_lists($sqlmap,$_GET['page'],$limit);
        $count = $this->service->count($sqlmap);
    	$pages = $this->admin_pages($count, $limit);
        $lists = array(
            'th' => array(
                'username' => array('title' => '会员账号','length' => 10),
                'dateline' => array('title' => '操作日期','length' => 20,'style' => 'date'),
                'value' => array('length' => 20,'title' => '操作金额'),
                'msg' => array('title' => '日志描述','length' => 40),
            ),
            'lists' => $logs,
            'pages' => $pages,
        );
        $this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('member_log');
    }
}