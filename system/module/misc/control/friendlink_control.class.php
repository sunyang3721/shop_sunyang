<?php
hd_core::load_class('init', 'admin');
class friendlink_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('friendlink');
		$this->attachment_service = $this->load->service('attachment/attachment');
		$this->load->helper('attachment');
	}
	/**
	 * [index 列表]
	 */
	public function index(){
		$sqlmap = array();
		$_GET['limit'] = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$friendlink = $this->service->get_lists($sqlmap,$_GET['page'],$_GET['limit']);
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $_GET['limit']);
        $lists = array(
            'th' => array(
                'sort' => array('title' => '排序','length' => 10,'style' => 'double_click'),
                'name' => array('title' => '链接名称','length' => 20,'style' => 'ident'),
                'url'=>array('title' => '链接地址','length' => 50,'style' => 'double_click'),
                'target' => array('title' => '新窗口打开','length' => 10,'style' => 'ico_up_rack'),
            ),
            'lists' => $friendlink,
            'pages' => $pages,
            );

        $this->load->librarys('View')->assign('lists',$lists)->display('friendlink_index');
	}
	/**
	 * [add 添加]
	 */
	public function add(){
		if(checksubmit('dosubmit')){
			if(!empty($_FILES['logo']['name'])) {
				$code = attachment_init(array('module'=>'common','path'=>'common','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','png','jpeg','gif')));
				$_GET['logo'] = $this->attachment_service->setConfig($code)->upload('logo');
				if(!$_GET['logo']){
					showmessage($this->attachment_service->error);
				}
			}
			$result = $this->service->add($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				$this->attachment_service->attachment($_GET['logo'],'',false);
				showmessage(lang('_operation_success_'),url('misc/friendlink/index'),'1');
			}
		}else{
			$this->load->librarys('View')->display('friendlink_edit');
		}
	}
	/**
	 * [edit 编辑]
	 */
	public function edit(){
		$info = $this->service->get_friendlink_by_id($_GET['id']);
		if(checksubmit('dosubmit')){
			if(!empty($_FILES['logo']['name'])) {
				$code = attachment_init(array('module'=>'article','path'=>'article','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','gif','jpg','jpeg','png')));
				$_GET['logo'] = $this->attachment_service->setConfig($code)->upload('logo');
				if(!$_GET['logo']){
					showmessage($this->attachment_service->error);
				}
				$this->attachment_service->attachment($_GET['logo'],$info['logo'],false);
			}
			$result = $this->service->edit($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('misc/friendlink/index'),'1');
			}
		}else{
			$this->load->librarys('View')->assign('info',$info)->display('friendlink_edit');
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
		showmessage(lang('_operation_success_'),url('misc/friendlink/index'),'1');
	}
	/**
	 * [ajax_edit ajax编辑]
 	 */
	public function ajax_edit(){
		$result = $this->service->ajax_edit($_GET);
		$this->ajaxReturn($result);
	}
}