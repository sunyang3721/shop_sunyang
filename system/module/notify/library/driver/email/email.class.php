<?php
include_once APP_PATH.'module/notify/library/driver/notify_abstract.class.php';
class email extends notify_abstract{
	public function __construct($_config) {
		$this->config = $_config;
		$this->smtp_config = $_config['configs'];
	}
	/* 发送通知 */
	public function send($params){
		$params = json_decode($this->config['params'],TRUE);
		$params['sender'] = empty($params['sender']) ? $this->smtp_config['mail_formmail'] : $params['sender'];
		$params['mailtype'] = 'HTML';
		$smtp = new smtp(
			$this->smtp_config['mail_smtpserver'], 
			$this->smtp_config['mail_smtpport'], 
			true, 
			$this->smtp_config['mail_mailuser'], 
			$this->smtp_config['mail_mailpass'], 
			$this->smtp_config['mail_formmail']
		);
		$send = $smtp->sendmail($params['to'],$params['sender'],$params['subject'],$params['body'],$params['mailtype']);
		$this->_notify($send);
		return $send;
	}
}
