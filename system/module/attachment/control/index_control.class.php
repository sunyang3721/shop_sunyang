<?php
class index_control extends control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('attachment/attachment');
	}

	public function index() {
		include template('index');
	}

	public function upload() {
		if(IS_POST) {
			$file = (isset($_GET['file'])) ? $_GET['file'] : 'upfile';
			$result = $this->service->setConfig($_GET['upload_init'])->upload($file, FALSE);
			if($result === FALSE) {
				showmessage($this->service->error,'',0,'','json');
			} else {
				showmessage(lang('upload_success','attachment/language'), '', 1, $this->service->output(), 'json');
			}
		}
	}

	/* 编辑器图片上传 */
	public function editor() {
		$result = $this->service->setConfig($_GET['upload_init'])->upload('editor', $code);
		if($result === FALSE) {
			showmessage($this->service->error, '', 0, '', 'json');
		} else {
			showmessage(lang('upload_success','attachment/language'), '', 1, $this->service->output(), 'json');
			exit;
		}
	}

	/* 远程图片保存 */
	public function remote() {
		$this->service->setConfig($_GET['upload_init']);
		$imgs = (array) $_GET['editor'];
        $lists = array();
        foreach ($imgs as $img) {
        	$result = $this->service->remote($img);
        	if($result === false) continue;
        	/* 保存多维数组 */
            array_push($lists, array(
                "state" => 'SUCCESS',
                "url" => $result['url'],
                "source" => $img
            ));
        }
        showmessage(lang('upload_success','attachment/language'), '', 1, $lists, 'json');
	}
}