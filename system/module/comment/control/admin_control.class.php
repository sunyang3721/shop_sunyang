<?php
hd_core::load_class('init', 'admin');
class admin_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('comment/comment');
	}

	public function index() {
		$sqlmap = array();
		$_map = array();
		if($_GET['starttime']) $_map[] = array("GT", strtotime($_GET['starttime']));
		if($_GET['endtime']) $_map[] = array("LT", strtotime($_GET['endtime']));
		if($_map) $sqlmap['datetime'] = $_map;
		if($_GET['keyword']) $sqlmap['username'] = array("LIKE", "%".$_GET['keyword']."%");
		if(isset($_GET['is_shield']) && $_GET['is_shield'] != 2) $sqlmap['is_shield'] = $_GET['is_shield'];
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$result = $this->service->get_lists($sqlmap, $limit, 'id DESC', $_GET['page']);
		$pages = $this->admin_pages($result['count'], $limit);
		$lists = array(
            'th' => array(
                'sku_name' => array('title' => '评价商品','length' => 45,'style' => 'goods'),
                'mood' => array('title' => '评分','length' => 10),
                'username' => array('title' => '会员账号','length' => 15),
                'dateline' => array('length' => 15,'title' => '评价时间','style' => 'date'),
                'is_shield' => array('length' => 5,'title' => '显示','style' => 'ico_up_rack'),
            ),
            'lists' => $result['lists'],
            'pages' => $pages,
        );
		$this->load->librarys('View')->assign('lists',$lists)->display('comment_list');
	}


	public function reply() {
		if(checksubmit('dosubmit')) {
			$result = $this->service->reply($_GET['id'], $_GET['reply_content']);
			if($result === false) {
				showmessage($this->service->error);
			}
			showmessage(lang('reply_success','comment/language'), url('index'), 1);
		} else {
			showmessage(lang('_error_action_'));
		}
	}

	public function change_status(){
		$result = $this->service->change_status($_GET['id']);
		if(!$result){
			showmessage($this->service->error,'',0);
		}else{
			showmessage(lang('_operation_success_'),'',1);
		}
	}

	public function delete() {
		$ids = (array) $_GET['id'];
		$result = $this->service->delete($ids);
		if($result === false){
			showmessage($this->service->error,'',0);
		}
		showmessage(lang('estimate_delete_success','comment/language'), url('index'), 1);
	}
}