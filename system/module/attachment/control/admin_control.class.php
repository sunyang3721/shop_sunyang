<?php
hd_core::load_class('init', 'admin');
class admin_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		helper('attachment');
		$this->service = $this->load->service('attachment/attachment');
		$this->notify_service = $this->load->service('notify/notify');
		$this->setting_service = $this->load->service('admin/setting');
	}

	public function index() {
		$result = $this->service->get_lists();
		$this->load->librarys('View')->assign('attachments',$result['attachments'])->assign('spaces',$result['spaces'])->display('attachment_index');
	}

	public function manage() {
		$sqlmap = array();
		$sqlmap['module']  = $_GET['folder'];
		$sqlmap['isimage'] = 1;
		if(isset($_GET['type']) && $_GET['type'] == 'use') {
			$sqlmap['use_nums'] = 0;
		}
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$result = $this->service->lists($sqlmap,$limit,$_GET['page'],'aid DESC');
		$pages = $this->admin_pages($result['count'], $limit);
		$this->load->librarys('View')->assign('lists',$result['lists'])->assign('pages',$pages)->display('attachment_manage');
	}

	public function replace() {
		if(IS_POST) {
			$file = (isset($_GET['file'])) ? $_GET['file'] : 'upfile';
			$fileinfo = $this->service->getField('filepath,filename,url',array('aid' => $_GET['aid']));
			$fileinfo['filename'] = substr($fileinfo['filename'], 0 ,strpos($fileinfo['filename'],'.'));
			$filepath = $fileinfo['url'];
			$code = attachment_init(array('mid' => 1,'path' => $fileinfo['filepath'],'md5' => true,'replace' => true,'saveName' => array('trim', $fileinfo['filename'])));
			$this->service->setConfig(attachment_init(array('mid' => 1)))->remove_thumb($filepath);
			$result = $this->service->setConfig($code)->replace($file, $_GET['aid']);
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			if($result === FALSE) {
				showmessage($this->service->error);
			} else {
				showmessage(lang('upload_success','attachment/language'), '', 1, $result, 'json');
			}
		}
	}

	public function delete() {
		$aids = (array) $_GET['aid'];
		if(empty($aids)) {
			showmessage(lang('delete_not_exist','attachment/language'));
		}
		foreach ($aids as $aid) {
			$this->service->delete($aid);
		}
		showmessage(lang('delete_success','attachment/language'), -1, 1);
	}

	public function setting(){
		$sqlmap = array();
		$sqlmap['isimage'] = 1;
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$result = $this->service->lists($sqlmap,$limit,$_GET['page'],'aid DESC');
		$pages = $this->admin_pages($result['count'], $limit);

		//统计
		helper('order/function');
		$datas = $this->service->attach_output();
		//* 组装支付方式 */
		if ($datas['all_img']) {
			foreach ($datas['all_img'] as $k => $v) {
				$datas['img'][] = $v['name'];
			}
		}
		$attach = $this->service->fetch_attachment();
		$setting = $this->setting_service->get();
		$setting['attach_name'] = $this->service->get_attach_name();
		$setting['attach_module_all'] = model('admin/app','service')->get_module();
		$this->load->librarys('View')->assign('setting',$setting)->assign('attach',$attach)->assign('datas',$datas)->assign('lists',$result['lists'])->assign('pages',$pages)->display('attachment_setting');
	}

	/* 配置参数 */
	public function config() {
		$notify = $this->service->fetch_by_code($_GET['code']);
		if($notify === FALSE) {
			showmessage($this->notify_service->error);
		}
		if(checksubmit('dosubmit')) {
			$r = $this->service->config($_GET['vars'], $_GET['code']);
			if($r === FALSE) {
				showmessage($this->notify_service->error);
			}
			showmessage(lang('_operation_success_'), url('setting'), 1);
		} else {
			$_setting = $this->service->get_attachemnt();
			$_config = $_setting[$_GET['code']]['configs'];
			$this->load->librarys('View')->assign('notify',$notify)->assign('_setting',$_setting)->assign('_config',$_config)->display('notify_local_config');
		}
	}

	public function setting_update(){
		if(checksubmit('dosubmit')){
			$code = attachment_init(array('module' => 'attachment','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','gif','jpeg','png')));
			$_GET['attach_logo'] =  $this->service->setConfig($code)->upload('attach_logo');
			$_GET['attach_ext'] =preg_replace("/(\s)/",'',preg_replace("/(\n)|(\t)|(\')|(')|(，)/" ,',' ,$_GET['attach_ext']));
			$result = $this->setting_service->update($_GET);
			if($result === FALSE){
				showmessage($this->setting_service->error);
			}
			showmessage(lang('_operation_success_'),url('setting'),1);
		}

	}
	/*已使用(used)和未使用(unused)*/
	public function picture_space() {
		$sqlmap = $this->service->picture_space($_GET['folder'],$_GET['type'],$_GET['time']);
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 12;
		$result = $this->service->lists($sqlmap,$limit,$_GET['page'],'aid DESC');
		$pages = $this->admin_pages($result['count'], $limit);
		$module = model('admin/app','service')->get_module();
		$this->load->librarys('View')->assign('lists',$result['lists'])->assign('module',$module)->assign('pages',$pages)->display('popup_picture_space');
	}
}