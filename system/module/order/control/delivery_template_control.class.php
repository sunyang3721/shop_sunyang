<?php
hd_core::load_class('init', 'admin');
class delivery_template_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('order/delivery_template');
		$this->load->helper('attachment');
	}

	/* 物流配置管理 */
	public function index() {
		$sqlmap = array();
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$deliverys = $this->service->get_lists($sqlmap,$_GET['page'],$limit);
		$count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $limit);
        $lists = array(
			'th' => array(
				'name' => array('title' => '运费模板名称','length' => 35, 'style'=>'default'),
				'type' => array('title' => '计费类型','length' => 30),
				'sort' => array('title' => '排序','length' => 15,'style'=>'double_click'),
			),
			'lists' => $deliverys,
            'pages' => $pages,
			);
        $this->load->librarys('View')->assign('lists',$lists)->display('delivery_template_index');
	}

	/* [添加|编辑] 物流 */
	public function update() {
		if (checksubmit('do_submit')) {
			$result = $this->service->update($_GET);
			if ($result === false) showmessage($this->service->error,'',0,'json');
			showmessage(lang('_operation_success_'),url('index'),1,'json');
		} else {
			$delivery = array();
			$id = (int) $_GET['id'];
			// 获取当前物流信息
			if ($id > 0) {
				$delivery = $this->service->get_by_id($id);
			}
			$this->load->librarys('View')->assign('delivery',$delivery)->display('delivery_template_update');
		}
	}

	/* 根据运费模板ID更改字段值 */
	public function update_field_by_id() {
		if (checksubmit('dosubmit')) {
			$result = $this->service->update_field_by_id($_GET['id'],$_GET['field'],$_GET['val']);
			if ($result === false) showmessage($this->service->error);
			showmessage(lang('_operation_success_'),'',1,'json');
		} else {
			showmessage(lang('_error_action_'));
		}
	}

	/* 删除物流(支持多条) */
	public function deletes() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$ids = (array)$_GET['ids'];
		$result = $this->service->deletes($ids);
		if ($result === false) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),url('index'),1,'json');
	}

	/* 设为默认 */
	public function set_default() {
		$id = (int)$_GET['id'];
		$result = $this->service->set_default($id);
		if ($result === false) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),url('index'),1,'json');
	}

}