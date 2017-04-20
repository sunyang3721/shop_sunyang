<?php
hd_core::load_class('init', 'admin');
class article_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('article');
		$this->category_service = $this->load->service('article_category');
		$this->attachment_service = $this->load->service('attachment/attachment');
		$this->load->helper('attachment');
	}
	/**
	 * [index 文章列表]
 	 */
	public function index(){
		$sqlmap = array();
		$_GET['limit'] = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$article = $this->service->get_lists($sqlmap,$_GET['page'],$_GET['limit']);
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $_GET['limit']);
        $lists = array(
            'th' => array(
                'sort' => array('title' => '排序','length' => 10,'style' => 'double_click'),
                'title' => array('title' => '标题','length' => 30,'style' => 'double_click'),
                'category'=>array('title' => '文章分类','length' => 15),
                'dataline' => array('title' => '发布时间','length' => 15,'style'=>'date'),
                'display'=>array('title' => '显示','length' => 10,'style' => 'ico_up_rack'),
                'recommend'=>array('title' => '推荐','length' => 10,'style' => 'ico_up_rec'),
            ),
            'lists' => $article,
            'pages' => $pages,
            );
        $this->load->librarys('View')->assign('lists',$lists)->display('article_index');
	}
	/**
	 * [article_category_choose 选择框]
 	 */
	public function article_category_choose(){
		$category = $this->category_service->get_category_tree();
		$this->load->librarys('View')->assign('category',$category)->display('article_category_choose');
	}
	/**
	 * [add 添加文章]
 	 */
	public function add(){
		if(checksubmit('dosubmit')){
			if(!empty($_FILES['thumb']['name'])) {
				$code = attachment_init(array('module'=>'article','path'=>'article','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','jpeg','gif','png')));
				$_GET['thumb'] = $this->attachment_service->setConfig($code)->upload('thumb');
				if(!$_GET['thumb']){
					showmessage($this->attachment_service->error);
				}
			}
			$result = $this->service->add($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				$this->attachment_service->attachment($_GET['thumb'], '',false);
				$this->attachment_service->attachment($_GET['content'],'');
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$this->load->librarys('View')->display('article_edit');
		}
	}
	/**
	 * [edit 编辑文章]
 	 */
	public function edit(){
		$info = $this->service->get_article_by_id($_GET['id']);
		if(checksubmit('dosubmit')){
			if(!empty($_FILES['thumb']['name'])) {
				$code = attachment_init(array('module'=>'article','path'=>'article','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','jpeg','gif','png')));
				$_GET['thumb'] = $this->attachment_service->setConfig($code)->upload('thumb');
				if(!$_GET['thumb']){
					showmessage($this->attachment_service->error);
				}
				$this->attachment_service->attachment($_GET['thumb'], $info['thumb'],false);
			}
			$this->attachment_service->attachment($_GET['content'], $info['content']);
			$result = $this->service->edit($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$this->load->librarys('View')->assign('info',$info)->display('article_edit');
		}
	}
	/**
	 * [delete 删除文章]
 	 */
	public function delete(){
		$result = $this->service->delete($_GET);
		if(!$result){
			showmessage($this->service->error);
		}
		showmessage(lang('_operation_success_'),url('misc/article/index'),1);
	}
	/**
	 * [ajax_edit 编辑文章]
 	 */
	public function ajax_edit(){
		$result = $this->service->ajax_edit($_GET);
		if(!$result){
			showmessage($this->service->error,'',0);
		}
		showmessage(lang('_operation_success_'),'',1);
	}
}