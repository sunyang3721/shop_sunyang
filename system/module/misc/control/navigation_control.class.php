<?php
hd_core::load_class('init', 'admin');
class navigation_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('navigation');
	}
	/**
	 * [index 导航设置]
 	 */
	public function index(){
		$navigation = $this->service->get_lists(array('order'=>'sort ASC'));
		$lists = array(
            'th' => array(
                'sort' => array('title' => '排序','length' => 10,'style' => 'double_click'),
                'name' => array('title' => '导航名称','length' => 10,'style' => 'double_click'),
                'url'=>array('title' => '导航链接','length' => 60,'style' => 'double_click'),
                'target' => array('title' => '新窗口打开','length' => 10,'style' => 'ico_up_rack'),
            ),
            'lists' => $navigation,
            );

		$this->load->librarys('View')->assign('lists',$lists)->display('navigation_index');
	}
	/**
	 * [add 添加]
 	 */
	public function add(){
		if(checksubmit('dosubmit')){
			$result = $this->service->add($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('misc/navigation/index'),'1');
			}
		}else{
			$this->load->librarys('View')->display('navigation_edit');
		}		
	}
	/**
	 * [ajax_edit ajax编辑]
	 */
	public function ajax_edit(){
		$result = $this->service->ajax_edit($_GET);
		$this->ajaxReturn($result);
	}
	/**
	 * [edit 编辑]
	 */
	public function edit(){
		if(checksubmit('dosubmit')){
			$result = $this->service->edit($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('misc/navigation/index'),'1');
			}
		}else{
			$info = $this->service->get_navigation_by_id($_GET['id']);
			$this->load->librarys('View')->assign('info',$info)->display('navigation_edit');
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
		showmessage(lang('_operation_success_'),url('misc/navigation/index'),'1');
	}
}