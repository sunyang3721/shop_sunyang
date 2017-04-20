<?php
class taglib_prom_group
{
	public function __construct() {
		$this->service = model('promotion/promotion_group','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
        $lists = $this->service->group_lists($sqlmap,$options);
        return $lists;
	}
}