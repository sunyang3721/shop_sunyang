<?php
hd_core::load_class('init', 'admin');
class admin_control extends init_control {

	public function _initialize() {
		parent::_initialize();
		$this->model = $this->load->table('adv');
		$this->service = $this->load->service('adv');

		$this->position_service = $this->load->service('adv_position');
		$this->position_model = $this->load->table('adv_position');

		$this->attachment_service = $this->load->service('attachment/attachment');
		$this->attachment_service->setConfig(authcode(serialize(array('module'=>'common','path' => 'common','mid' => 1,'allow_exts' => array('gif','jpg','jpeg','bmp','png'))), 'ENCODE'));
	}

	/*广告=====*/
	/**
	 * 获取广告方式列表
	 */
	public function index() {
		$sqlmap = array();
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$ads = $this->service->get_lists($sqlmap,$_GET['page'],$limit);
		$count = $this->service->count($sqlmap);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
            'th' => array(
                'title' => array('title' => '广告名称','length' => 20,'style' => 'double_click'),
                'position_name' => array('title' => '所属广告位','length' => 15,'style' => 'left_text'),
                'type_text'=>array('title' => '类别','length' => 10),
                'startime_text' => array('title' => '开始时间','length' => 20),
                'endtime_text'=>array('title' => '结束时间','length' => 20),
                'hist'=>array('title' => '点击数','length' => 5),
            ),
            'lists' => $ads,
            'pages' => $pages,
            );
		$this->load->librarys('View')->assign('lists',$lists)->display('index');
	}

	/**
	 * 添加广告
	 */
	public function add() {
		$position = $this->position_service->getposition();
		if(!$position)showmessage(lang('_no_advposition_','ads/language'),url('position_add'),0);
		$position_format = format_select_data($position);
		if (checksubmit('dosubmit')) {
			if(!empty($_FILES['content_pic']['name'])) {
				$_GET['content_pic'] = $this->attachment_service->upload('content_pic');
				if(!$_GET['content_pic']){
					showmessage($this->attachment_service->error);
				}
			}
			$_GET['content'] = $_GET['type'] == 0 ? $_GET['content_pic'] : $_GET['content_text'];
			$_GET['content'] = is_null($_GET['content']) ? '' : $_GET['content'];
			$r = $this->service->save($_GET);
			if($r == FALSE)showmessage($this->service->error, url('add'), 0);
			$this->attachment_service->attachment($_GET['content_pic'],'',false);
			showmessage(lang('_update_adv_success_','ads/language'), url('index'), 1);
		} else {
			$this->load->librarys('View')->assign('position',$position)->assign('position_format',$position_format)->display('update');
		}
	}

	/**
	 * 编辑广告
	 */
	public function edit() {
		$position = $this->position_service->getposition();
		$position_format = format_select_data($position);
		$data = $this->service->fetch_by_id($_GET['id']);
		if (checksubmit('dosubmit')) {
			if(!empty($_FILES['content_pic']['name'])) {
				$_GET['content_pic'] = $this->attachment_service->upload('content_pic');
				if(!$_GET['content_pic']){
					showmessage($this->attachment_service->error);
				}
			}
			$_GET['content'] = $_GET['type'] == 0 ? $_GET['content_pic'] : $_GET['content_text'];
			$_GET = array_filter($_GET);
			$r = $this->service->save($_GET);
			$this->attachment_service->attachment($_GET['content_pic'],$data['content'],false);
			showmessage(lang('_update_adv_success_','ads/language'), url('index'), 1);
		} else {
			extract($data);
			$this->load->librarys('View')->assign('position',$position)->assign('position_format',$position_format)->assign($data,$data)->display('update');
		}
	}

	/**
	 * 删除广告
	 */
	public function del() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$data = $this->service->fetch_by_id($_GET['id']);
		$this->attachment_service->attachment('',$data['content'],false);
		$result = $this->service->delete((array)$_GET['id']);
		if($result === false) showmessage($this->service->error);
		showmessage(lang('_del_adv_success_','ads/language'), url('index'), 1);
	}

	/**
	 * 编辑标题
	 */
	public function save_title() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$this->service->save_title(array('id' => $_GET['id'], 'title' => $_GET['title']));
		showmessage(lang('_update_adv_success_','ads/language'), url('index'), 1);
	}


	/*广告位=======================================================*/
	/**
	 * 获取广告方式列表
	 */
	public function position_index() {
		$sqlmap = array();
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$count = $this->position_service->count($sqlmap);
		$position = $this->position_service->get_lists($sqlmap,$_GET['page'],$limit);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
            'th' => array(
                'name' => array('title' => '名称','length' => 35,'style' => 'double_click'),
                'type_text' => array('title' => '类别','length' => 10),
                'width'=>array('title' => '宽度','length' => 10),
                'height' => array('title' => '高度','length' => 10),
                'adv_count'=>array('title' => '已发布','length' => 10),
                'status'=>array('title' => '启用','length' => 10,'style' => 'ico_up_rack'),
            ),
            'lists' => $position,
            'pages' => $pages,
            );
		$this->load->librarys('View')->assign('lists',$lists)->display('position_index');
	}

	/**
	 * 启用禁用广告位
	 */
	public function ajax_status() {
		$id = $_GET['id'];
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		if ($this->position_service->change_status($id)) {
			showmessage(lang('_status_success_','ads/language'), '', 1);
		} else {
			showmessage(lang('_status_error_','ads/language'), '', 0);
		}
	}

	/**
	 * 删除广告位
	 */
	public function position_del() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$result = $this->service->count(array('position_id' => array('IN', (array)$_GET['id'])));
		if($result > 0) showmessage(lang('no_delete_advposition_','ads/language'), url('position_index'), 0);
		$position = $this->position_service->fetch_by_id($_GET['id']);
		$this->attachment_service->attachment('',$position['defaultpic'],false);
		$this->position_service->delete((array)$_GET['id']);
//		$this->model->where(array('position_id' => array('IN', (array)$_GET['id'])))->delete();
		showmessage(lang('_del_adv_position_success_','ads/language'), url('position_index'), 1);
	}

	/**
	 * 添加广告位
	 */
	public function position_add() {
		if (checksubmit('dosubmit')) {
			if(!empty($_FILES['defaultpic']['name'])) {
				$_GET['defaultpic'] = $this->attachment_service->upload('defaultpic');
				if(!$_GET['defaultpic']){
					showmessage($this->attachment_service->error);
				}
			}
			$r = $this->position_service->save($_GET);
			if(!$r)showmessage($this->position_service->getError, url('position_add'), 0);
			$this->attachment_service->attachment($_GET['defaultpic'],'',false);
			showmessage(lang('_update_adv_position_success_','ads/language'), url('position_index'), 1);
		} else {
			$status = 1;
			$this->load->librarys('View')->display('position_update');
		}
	}

	/**
	 * 编辑广告位
	 */
	public function position_edit() {
		$position = $this->position_service->fetch_by_id($_GET['id']);
		if (checksubmit('dosubmit')) {
			if(!empty($_FILES['defaultpic']['name'])) {
				$_GET['defaultpic'] = $this->attachment_service->upload('defaultpic');
				if(!$_GET['defaultpic']){
					showmessage($this->attachment_service->error);
				}
			}
			$r = $this->position_service->save($_GET);
			$this->attachment_service->attachment($_GET['defaultpic'],$position['defaultpic'],false);
			showmessage(lang('_update_adv_position_success_','ads/language'), url('position_index'), 1);
		} else {
			extract($position);
			$this->load->librarys('View')->assign($position,$position)->display('position_update');
		}
	}

	/**
	 * 编辑标题
	 */
	public function position_save_name() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$this->position_service->save(array('id' => $_GET['id'], 'name' => $_GET['name']));
		showmessage(lang('_update_adv_position_success_','ads/language'), url('index'), 1);
	}

}
