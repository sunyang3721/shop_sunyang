<?php
hd_core::load_class('init', 'admin');
class type_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('goods/type');
		$this->spec_service = $this->load->service('goods/spec');
	}
	/**
	 * [lists 类型列表]
	 * @return [type] [description]
	 */
	public function index(){
		$sqlmap = array();
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$type = $this->service->get_lists($_GET['page'],$limit);
		$count = $this->service->count($sqlmap);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array('name' => array('style' => 'double_click','title' => '属性名称','length' => 20),'content' => array('title' => '属性标签','length' => 50),'sort' => array('style' => 'double_click','length' => 10,'title' => '排序'),'status' => array('title' => '启用','style' => 'ico_up_rack','length' => 10)),
			'lists' => $type,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('type_list');
	}
	/**
	 * [add 添加类型]
	 */
	public function add(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->add_type($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$specs = $this->spec_service->get_spec_name();
			$this->load->librarys('View')->assign('specs',$specs)->display('type_edit');
		}
	}
	/**
	 * [edit 类型编辑]
	 * @return [type] [description]
	 */
	public function edit(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->add_type($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$specs = $this->spec_service->get_spec_name();
			$type = $this->service->fetch_by_id($_GET['id']);
			$pop = $this->service->get_pop_by_id($_GET['id']);
			$type_spec = $this->service->get_spec_by_id($_GET['id']);
			$this->load->librarys('View')->assign('specs',$specs)->assign('type',$type)->assign('pop',$pop)->assign('type_spec',$type_spec)->display('type_edit');
		}
	}
	/**
	 * [delete 删除类型]
	 * @return [type] [description]
	 */
	public function delete(){
		$result = $this->service->delete($_GET['id']);
		if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	/**
	 * [ajax_status 规格列表内更改规格状态]
	 */
	public function ajax_status(){
		$result = $this->service->change_status($_GET['id']);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_fail_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_sort 改变排序]
	 */
	public function ajax_sort(){
		$result = $this->service->change_info($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_fail_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_name ajax更改名称]
	 * @return [type] [description]
	 */
	public function ajax_name(){
		$result = $this->service->change_info($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_fail_'),'',1,'','json');
		}
	}
	/**
	 * [edit_pop 商品属性编辑弹窗页]
	 * @return [type] [description]
	 */
	public function edit_pop(){
		$this->load->librarys('View')->display('type_pop_edit');
	}
}