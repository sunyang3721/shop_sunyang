<?php
hd_core::load_class('init', 'admin');
class spec_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('spec');
	}
	/**
	 * [lists 后台规格显示列表]
	 */
	public function index(){
		$sqlmap = array();
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$spec = $this->service->spec_list($_GET['page'],$limit);
		$count = $this->service->count($sqlmap);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array('name' => array('style' => 'double_click','title' => '规格名称','length' => 20),'value' => array('title' => '规格属性','length' => 50),'sort' => array('style' => 'double_click','length' => 10,'title' => '排序'),'status' => array('title' => '启用','style' => 'ico_up_rack','length' => 10)),
			'lists' => $spec,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('spec_list');
	}
	/**
	 * [add 添加规格]
	 */
	public function add(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->add_spec($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$this->load->librarys('View')->display('spec_edit');
		}
	}
	/**
	 * [edit 编辑规格]
	 */
	public function edit(){
		if(checksubmit('dosubmit')) {
			$result = $this->service->edit_spec($_GET);
			if($result === FALSE){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$spec = $this->service->get_spec_by_id($_GET['id']);
			$this->load->librarys('View')->assign('spec',$spec);
			$this->load->librarys('View')->display('spec_edit');
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
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_del 删除规格，可批量删除]
	 */
	public function ajax_del(){
		$result = $this->service->delete_spec($_GET['id']);
		if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	/**
	 * [del_spec_val 删除规格值]
	 * @return [type] [description]
	 */
	public function del_spec_val(){
		$result = $this->service->del_spec_val($_GET);
		if(!$result){
			showmessage($this->service->error,'',0);
		}else{
			showmessage(lang('_OPERATION_SUCCESS_'),url('index'),1);
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
	 * [ajax_name ajax更改名称]
	 * @return [type] [description]
	 */
	public function ajax_name(){
		$result = $this->service->change_name($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_add_spec ajax增加规格]
	 * @return [type] [description]
	 */
	public function ajax_add_spec(){
		$result = $this->service->ajax_add_spec($_GET['name']);
		if(!$result){
			showmessage($this->service->error,'',0,$result,'json');
		}else{
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	/**
	 * [ajax_add_pop ajax增加规格属性]
	 * @return [type] [description]
	 */
	public function ajax_add_pop(){
		$result = $this->service->ajax_add_pop($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,$result,'json');
		}else{
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
}