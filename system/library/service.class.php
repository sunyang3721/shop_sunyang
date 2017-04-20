<?php
class service extends hd_base {
	public function __call($method, $args) {
		throw new Exception(lang('_method_not_exist_', 'language', array('class' => get_class($this),'method' => $method)));
	}
}	