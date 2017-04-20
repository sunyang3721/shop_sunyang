<?php
class taglib_friendlink
{
	public function __construct() {
		$this->service = model('misc/friendlink','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
		$lists = $this->service->friendlink_lists($sqlmap,$options);
		return $lists;
	}
}