<?php
hd_core::load_class('init', 'admin');
class category_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('article_category');
	}
	/**
	 * [index 文章分类列表]
 	 */
	public function index(){
		$sqlmap = array();
		$sqlmap['parent_id'] = 0;
		$_GET['limit'] = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$category = $this->load->table('article_category')->where($sqlmap)->page($_GET['page'])->limit($_GET['limit'])->order("sort ASC")->select();
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count,$_GET['limit']);
        $this->load->librarys('View')->assign('category',$category)->assign('pages',$pages)->display('category_index');
	}
	/**
	 * [add 添加分类]
	 */
	public function add(){
		if(checksubmit('dosubmit')){
			$result = $this->service->add($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('misc/category/index'),1);
			}
		}else{
			if(!empty($_GET['parent_id'])){
				$parent_name = $this->service->get_category_by_id($_GET['parent_id'],'parent_name');
				$this->load->librarys('View')->assign('parent_name',$parent_name);
			}
			$this->load->librarys('View')->display('category_edit');
		}
	}
	/**
	 * [edit_category 编辑分类]
 	 */
	public function edit(){
		if(checksubmit('dosubmit')){
			$result = $this->service->edit($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('misc/category/index'),1);
			}
		}else{
			$info = $this->service->get_category_by_id($_GET['id']);
			$result =  $this->service->get_category_by_id($info['parent_id']);
			$parent_name = $result['parent_name'];
			$this->load->librarys('View')->assign('info',$info)->assign('result',$result)->assign('parent_name',$parent_name)->display('category_edit');
		}
	}
	/**
	 * [delete 删除分类]
 	 */
	public function delete(){
		$result = $this->service->delete($_GET);
		if(!$result){
			showmessage($this->service->error);
		}
		showmessage(lang('_operation_success_'),url('misc/category/index'),1);
	}
	/**
	 * [ajax_sun_class ajax获取分类]
	 */
	public function ajax_son_class(){
		$result = $this->service->ajax_son_class($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	/**
	 * [ajax_edit 编辑分类名称]
 	 */
	public function ajax_edit(){
		$result = $this->service->ajax_edit($_GET);
		if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'),'',1);
		}
	}
}