<?php
/**
 *		商品促销
 */
hd_core::load_class('init', 'admin');
class goods_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->model = $this->load->service('promotion/promotion_goods');
	}

	/**
	 * [lists 订单促销列表]
 	 */
	public function index() {
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$result = $this->model->get_lists($sqlmap, $limit, $_GET['page']);
		$pages = $this->admin_pages($result['count'], $limit);
		$lists = array(
			'th' => array(
				'name' => array('title' => '促销名称','length' => 40,'style' => 'double_click'),
				'start_time' => array('title' => '促销时间','length' => 40),
 				'status' => array('title' => '状态','length' => 10)
			),
			'lists' => $result['lists'],
            'pages' => $pages,
			);
		$this->load->librarys('View')->assign('lists',$lists)->display('goods_index');
	}

	public function add() {
		if(checksubmit('dosubmit')) {
			$result = $this->model->update($_GET);
			if($result === false) {
				showmessage($this->model->error);
			} else {
				showmessage(lang('add_activity_success','promotion/language'), url('index'), 1);
			}
		} else {
			$this->load->librarys('View')->display('goods_add');
		}
	}

	public function edit() {
		$id = (int) $_GET['id'];
		$info = $this->model->fetch_by_id($_GET['id']);
		if(!$info) {
			showmessage(lang('_param_error_'));
		}
		if(checksubmit('dosubmit')) {
			$result = $this->model->update($_GET);
			if($result === false) {
				showmessage($this->model->error);
			} else {
				showmessage(lang('edit_activity_success','promotion/language'), url('index'), 1);
			}
		} else {
			$info['sku_lists'] = $this->load->service('goods/goods_sku')->sku_detail($info['sku_ids']);
			$this->load->librarys('View')->assign('info',$info)->display('goods_edit');
		}
	}

	public function delete() {
		$ids = (array) $_GET['id'];
		if(empty($ids)) {
			showmessage(lang('_param_error_'));
		}
		$result = $this->model->delete($ids);
		if($result === false) {
			showmessage($this->model->error);
		} else {
			showmessage(lang('delete_activity_success','promotion/language'), url('index'), 1);
		}
	}

	public function ajax_name() {
		$id = (int) $_GET['id'];
		$name = $_GET['name'];
		if($id < 1 || empty($name)) {
			showmessage(lang('_param_error_'));
		}
		$result = $this->model->setField(array('name'=>$name), array('id' => $id));
		if($result === false) {
			showmessage($this->model->getError());
		} else {
			showmessage(lang('_operation_success_'), url('index'), 1);
		}
	}
}
