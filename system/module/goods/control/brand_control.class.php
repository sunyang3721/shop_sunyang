<?php
hd_core::load_class('init', 'admin');
class brand_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('brand');
		$this->attachment_service = $this->load->service('attachment/attachment');
		helper('attachment');
	}
	/**
	 * [lists 品牌列表]
 	 */
	public function index(){
		$sqlmap = $info = array();
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$brands = $this->service->get_lists($_GET['page'],$limit);
		$count = $this->service->count($sqlmap);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array('name' => array('title' => '品牌名称','length' => 20,'style' => 'ident'),'descript' => array('title' => '品牌描述','length' => 50),'sort' => array('style' => 'double_click','length' => 10,'title' => '排序'),'isrecommend' => array('title' => '推荐','style' => 'ico_up_rack','length' => 10)),
			'lists' => $brands,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->display('brand_list');
	}
	/**
	 * [add 添加品牌]
	 */
	public function add(){
		if(checksubmit('dosubmit')) {
			if(!empty($_FILES['logo']['name'])) {
				$code = attachment_init(array('path' => 'goods','mid' => 1,'allow_exts' => array('gif','jpg','jpeg','bmp','png')));
				$_GET['logo'] = $this->attachment_service->setConfig($code)->upload('logo');
				if(!$_GET['logo']){
					showmessage($this->attachment_service->error);
				}
			}
			$result = $this->service->add_brand($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				$this->attachment_service->attachment($_GET['logo'],'',false);
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$this->load->librarys('View')->display('brand_edit');
		}
	}
	/**
	 * [edit 编辑品牌]
	 */
	public function edit(){
		$info = $this->service->get_brand_by_id($_GET['id']);
		if(checksubmit('dosubmit')) {
			if(!empty($_FILES['logo']['name'])) {
				$code = attachment_init(array('path' => 'goods','mid' => 1,'allow_exts' => array('gif','jpg','jpeg','bmp','png')));
				$_GET['logo'] = $this->attachment_service->setConfig($code)->upload('logo');
				if(!$_GET['logo']){
					showmessage($this->attachment_service->error);
				}
			}
			$result = $this->service->edit_brand($_GET);
			if($result === FALSE){
				showmessage($this->service->error);
			}else{
				$this->attachment_service->attachment($_GET['logo'], $info['logo'],false);
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$this->load->librarys('View')->assign('info',$info)->display('brand_edit');
		}
	}
	/**
	 * [ajax_status 品牌列表内更改规格状态]
	 */
	public function ajax_recommend(){
		$result = $this->service->change_recommend($_GET['id']);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_del 删除品牌，可批量删除]
	 */
	public function ajax_del(){
		$result = $this->service->delete_brand($_GET['id']);
		if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	/**
	 * [ajax_sort 改变排序]
	 */
	public function ajax_sort(){
		$result = $this->service->change_sort($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_sort 改变名称]
	 */
	public function ajax_name(){
		$result = $this->service->change_name($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
}