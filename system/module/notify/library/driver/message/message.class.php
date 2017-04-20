<?php
include_once APP_PATH.'module/notify/library/driver/notify_abstract.class.php';
class message extends notify_abstract{
	public function __construct($_config) {
		$this->config = $_config;
		$this->message = model('member/member_message','service');
		$this->message_params = json_decode($_config['params'],TRUE); 
	}
	public function send(){
		$params = array();
		$params['mid'] = $this->message_params['mid'];
		$params['title'] = $this->message_params['title'];
		$params['message'] = $this->message_params['content'];
		$send = $this->message->add($params);
		return $this->_notify($send);
	}
}
