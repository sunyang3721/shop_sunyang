<?php
hd_core::load_class('init', 'goods');
class cp_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		if($this->member['id'] < 1) {
			$url_forward = $_GET['url_forward'] ? $_GET['url_forward'] : urlencode($_SERVER['REQUEST_URI']);
			showmessage(lang('_not_login_'),url('member/public/login',array('url_forward' => $url_forward)),0);
		}
	}
}