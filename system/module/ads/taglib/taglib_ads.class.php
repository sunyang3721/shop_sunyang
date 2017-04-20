<?php
class taglib_ads
{
	public function __construct() {
		$this->service = model('ads/adv_position','service');
	}

	public function lists($sqlmap = array(), $options = array()) {
		return $this->service->lists($sqlmap, $options);
	}
};