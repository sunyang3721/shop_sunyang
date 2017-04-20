<?php
hd_core::load_class('init', 'admin');
class focus_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('focus');
		$this->attachment_service = $this->load->service('attachment/attachment');
		helper('attachment');
	}
	/**
	 * [index 列表]
	 */
	public function index(){
		$sqlmap = array();
		$_GET['limit'] = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$focus = $this->service->get_lists($sqlmap,$_GET['page'],$_GET['limit']);
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $_GET['limit']);
        $lists = array(
            'th' => array(
                'sort' => array('title' => '排序','length' => 10,'style' => 'double_click'),
                'title' => array('title' => '幻灯片名称','length' => 20,'style' => 'ident'),
                'url'=>array('title' => '幻灯片链接','length' => 50,'style' => 'double_click'),
                'target' => array('title' => '新窗口打开','length' => 10,'style' => 'ico_up_rack'),
            ),
            'lists' => $focus,
            'pages' => $pages,
            );
        $this->load->librarys('View')->assign('lists',$lists)->display('focus_index');
	}
	/**
	 * [add 添加]
	 */
	public function add(){
		if(checksubmit('dosubmit')){
			if(!empty($_FILES['thumb']['name'])) {
				$code = attachment_init(array('module'=>'common','path'=>'common','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','jpeg','png','gif')));
				$_GET['thumb'] = $this->attachment_service->setConfig($code)->upload('thumb');
				if(!$_GET['thumb']){
					showmessage($this->attachment_service->error);
				}
			}
			$result = $this->service->add($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				$this->attachment_service->attachment($_GET['thumb'],'',false);
				showmessage(lang('_operation_success_'),url('index'),1);
			}
		}else{
			$this->load->librarys('View')->display('focus_edit');
		}
	}
	/**
	 * [edit 编辑]
	 */
	public function edit(){
		$info = $this->service->get_focus_by_id($_GET['id']);
		if(checksubmit('dosubmit')){
			if(!empty($_FILES['thumb']['name'])) {
				$code = attachment_init(array('module'=>'common','path'=>'common','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','jpeg','png','gif')));
				$_GET['thumb'] = $this->attachment_service->setConfig($code)->upload('thumb');
				if(!$_GET['thumb']){
					showmessage($this->attachment_service->error);
				}
			}
			$result = $this->service->edit($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				$this->attachment_service->attachment($_GET['thumb'], $info['thumb'],false);
				showmessage(lang('_operation_success_'),url('index'),1);
			}
		}else{
			$this->load->librarys('View')->assign('info',$info)->display('focus_edit');
		}
	}
	/**
	 * [delete 删除]
	 */
	public function delete(){
		$result = $this->service->delete($_GET);
		if(!$result){
			showmessage($this->service->error);
		}
		showmessage(lang('_operation_success_'),url('misc/focus/index'),1);
	}
	/**
	 * [ajax_edit ajax编辑]
	 */
	public function ajax_edit(){
		$result = $this->service->ajax_edit($_GET);
		$this->ajaxReturn($result);
	}
}