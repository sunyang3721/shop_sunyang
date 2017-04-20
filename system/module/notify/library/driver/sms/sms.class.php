<?php
include_once APP_PATH.'module/notify/library/driver/notify_abstract.class.php';
class sms extends notify_abstract{
	public function __construct($_config) {
		$this->config = $_config;
		$this->cloud = model('admin/cloud','service');
		$this->sms = json_decode($this->config['params'],TRUE);
	}
	
	public function send(){
		$params = array();
		$params['tpl_id'] = $this->sms['tpl_id'];
		$params['mobile'] = $this->sms['mobile'];
		$params['tpl_vars'] = $this->sms['tpl_vars'];
		$params['sms_sign'] = $this->config['configs']['sms_sign'];
		$r = $this->cloud->send_sms($params);
		$send = ($r['code'] == 200 && $r['result'] == TRUE) ? TRUE : FALSE;
		return $this->_notify($send);
	}
}
