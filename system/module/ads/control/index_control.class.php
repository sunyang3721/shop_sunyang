<?php
class index_control extends control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('ads/adv');
	}
	/**
	 * 跳转广告链接 并统计次数
	 */
	public function adv_view(){
		$id = $url = '';
		extract($_GET,EXTR_IF_EXISTS);
		$this->service->setInc('hist',1,array('id'=>$id));
		redirect($url);
	}

}