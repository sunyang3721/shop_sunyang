<?php
class taglib_navigation
{
	public function __construct() {
		$this->misc_service = model('misc/navigation','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
		return $this->misc_service->lists($sqlmap,$options);
	}
}