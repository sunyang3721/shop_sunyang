<?php
class taglib_focus
{
	public function __construct() {
		$this->service = model('misc/focus','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
		$lists = $this->service->focus_lists($sqlmap,$options);
		return $lists;
	}
}