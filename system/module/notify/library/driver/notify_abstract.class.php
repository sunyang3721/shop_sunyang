<?php
abstract class notify_abstract
{   
	protected $config = array();
	public function _notify($send){
		$load = hd_load::getInstance();
    	$queue = $load->librarys('queue');
		if(!$send){
			$queue->send_fail($this->config['id']);
		}else{
			$queue->update($this->config['id']);
		}
		return true;
    }
}