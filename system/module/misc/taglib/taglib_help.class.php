<?php
class taglib_help
{
	public function __construct() {
		$this->service = model('misc/help','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
		$lists = $this->service->help_lists($sqlmap,$options);
		return $lists;
	}
}