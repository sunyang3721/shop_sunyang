<?php
class district_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin/district');
	}

	/* 地区管理 */
	public function index() {
		$districts = $this->service->get_lists();
		 $lists = array(
			'th' => array(
				'sort' => array('title' => '排序','length' => 10,'style'=>'double_click'),
				'name' => array('title' => '名称','length' => 80,'style'=>'data'),
			),
			'lists' => $districts,
			);


		$this->load->librarys('View')->assign('lists',$lists)->display('district_index');
	}

	/* 添加地区 */
	public function add() {
		$parent_id = (int)$_GET['parent_id'];
		$parent_pos = array('顶级地区');
		if ($parent_id > 0) {
			$parent_pos = $this->service->fetch_position($parent_id);
		}
		if (checksubmit('dosubmit')) {
			if (FALSE === $this->service->update($_GET)) {
				showmessage($this->service->error);
			}
			showmessage(lang('add_address_success','admin/language'), url('index'), 1);
		} else {
			$this->load->librarys('View')->assign('parent_id',$parent_id)->assign('parent_pos',$parent_pos)->display('district_add');
		}
	}

	public function edit() {
		$id = $_GET['id'];
		if ($id < 1) {
			showmessage(lang('_param_error_'));
		}
		$r = $this->service->fetch_by_id($id);
		$parent_pos = array('顶级地区');
		if ($r['parent_id'] > 0) {
			$parent_pos = $this->service->fetch_position($r['id']);
		}
		if (checksubmit('dosubmit')) {
			$params = array_merge($r, $_GET);
			if (FALSE === $this->service->update($params)) {
				showmessage($this->service->error);
			}
			showmessage(lang('edit_address_success','admin/language'), url('index'), 1);
		} else {
			$this->load->librarys('View')->assign('parent_pos',$parent_pos)->assign('r',$r)->display('district_edit');
		}
	}

	public function delete() {
		$ids = (array)$_GET['ids'];
		$result = $this->service->delete($ids);
		if ($result === false) {
			showmessage($this->service->error);
		}
		showmessage(lang('delete_region_success','admin/language'));
	}

	/* 更新排序 */
	public function ajax_sort() {
		$id = (int)$_GET['id'];
		$sort = (int)$_GET['sort'];
		$result = $this->service->setField(array('sort' => $sort),array('id' => $id));
		if ($result === FALSE) {
			showmessage($this->service->error);
		} else {
			showmessage(lang('edit_sort_success','admin/language'), url('index'), 1);
		}
	}

	/* 更新地区名称 */
	public function ajax_name() {
		$params = array();
		$params['id'] = (int)$_GET['id'];
		$params['name'] = trim($_GET['name']);
		if (empty($params['name'])) {
			showmessage(lang('region_not_exist','admin/language'));
		}
		$pinyin = pinyin($params['name']);
		$params['pinyin'] = implode($pinyin, '');
		$result = $this->service->setField($params);
		if ($result === FALSE) {
			showmessage($this->service->error);
		} else {
			showmessage(lang('edit_region_success','admin/language'), url('index'), 1);
		}
	}

	public function ajax_district() {
		$id = (int)$_GET['id'];
		$result = (array)$this->service->get_children($id);
		if ($result) {
			foreach ($result as $key => $value) {
				$value['_child'] = $this->service->count(array('parent_id' => $value['id']));
				$result[$key] = $value;
			}
		}
		$this->load->librarys('View')->assign('result',$result);
		$result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}

}
