<?php
include_once APP_PATH.'module/notify/library/driver/notify_abstract.class.php';
class wechat extends notify_abstract{
	public function __construct($_config) {
		$this->config = $_config;
	}
	public function send($params){
		$_setting = model('notify/notify','service')->get_fech_all();
		$config = $_setting['wechat']['configs'];
		$wechat = new we($config);
		$r = $wechat->sendTemplateMessage($this->config['params']);
		$send = ($r['errcode'] == 0 && $r['errmsg'] == 'ok') ? TRUE : FALSE;
		return $this->_notify($send);
	}
}
