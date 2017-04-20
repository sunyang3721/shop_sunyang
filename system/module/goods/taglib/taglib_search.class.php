<?php
class taglib_search
{
	public function __construct() {
	}
	public function lists($sqlmap = array(), $options = array()) {
		$data = model('admin/setting','service')->get();
		$seach = $data['hot_seach'];
		return explode("\r\n",trim($seach));
	}
}