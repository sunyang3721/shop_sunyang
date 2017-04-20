<?php
hd_core::load_class('init', 'admin');
class category_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('goods/goods_category');
	}
	/**
	 * [lists 分类列表]
	 */
	public function index(){
		$result = $this->service->category_lists();
		$lists = array(
			'th' => array('sort' => array('title' => '排序','length' => 10,'style' => 'level_sort'),'name' => array('title' => '名称','length' => 55,'style' => 'level_name'),'type_name' => array('length' => 15,'title' => '关联属性'),'status' => array('title' => '启用','style' => 'ico_up_rack','length' => 10)),
			'lists' => $result,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->display('category_list');
	}
	/**
	 * [ajax_category ajax获取分类]
	 * @return [type] [description]
	 */
	public function ajax_category(){
		$result = $this->service->ajax_category($_GET['id']);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	/**
	 * [get_list 获取分类的子分类]
	 */
	public function get_list(){
		$result = $this->service->category_lists($_GET['parent_id']);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	/**
	 * [add 添加分类]
	 */
	public function add(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->add_category($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			if($_GET['pid']){
				$categorys = $this->service->get_parent($_GET['pid']);
				array_unshift($categorys,$_GET['pid']);
				$parent_name = $this->service->create_cat_format($categorys,TRUE);
				$cat_format = $this->service->create_format_id($categorys,TRUE);
				$this->load->librarys('View')->assign('parent_name',$parent_name)->assign('cat_format',$cat_format);
			}
			$this->load->librarys('View')->display('goods_category_edit');
		}
	}
	/**
	 * [edit 编辑分类]
	 */
	public function edit(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->edit_category($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$cate = $this->service->get_category_by_id($_GET['id']);
			$this->load->librarys('View')->assign('cate',$cate)->display('goods_category_edit');
		}
	}
	/**
	 * [ajax_status 分类列表内更改规格状态]
	 */
	public function ajax_status(){
		$result = $this->service->change_status($_GET['id']);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	/**
	 * [ajax_del 删除分类]
	 */
	public function ajax_del(){
		$result = $this->service->delete_category($_GET['id']);
		if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'));
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
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_sort 改变排序]
	 */
	public function ajax_sort(){
		$result = $this->service->change_info($_GET);
		if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'));
		}
	}
	/**
	 * [category_popup 商品选择分类页]
	 * @return [type] [description]
	 */
	public function category_popup(){
		$cache = $this->service->get();
		$category = $this->service->get_category_tree($cache);
		$this->load->librarys('View')->assign('category',$category)->display('add_category_popup');
	}
	/**
	 * [category_main 分类选择父级分类页]
	 * @return [type] [description]
	 */
	public function category_main(){
		$cache = $this->service->get();
		$cache[0] = array('id' => 0,'name' => '顶级分类','level' => 0,'parent_id' => -1);
		$category = $this->service->get_category_tree($cache);
		$this->load->librarys('View')->assign('category',$category)->display('add_category');
	}
	/**
	 * [category_relation 分类选择关联类型页]
	 * @return [type] [description]
	 */
	public function category_relation(){
		$type = $this->load->service('goods/type')->get_all_type();
		$this->load->librarys('View')->assign('type',$type)->display('category_relation_type');
	}
}