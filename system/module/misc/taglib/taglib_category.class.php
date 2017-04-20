<?php
class taglib_category
{
	public function __construct() {
		$this->service = model('misc/article_category','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
		$lists = $this->service->category_lists($sqlmap,$options);
		return $lists;
	}
}