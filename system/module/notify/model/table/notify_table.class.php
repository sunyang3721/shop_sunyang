<?php
class notify_table extends table
{
	protected function _after_select(&$result, $options) {
		$lists = array();
		foreach ($result AS $value) {
			$value['configs'] = json_decode($value['config'], TRUE);
			$lists[$value['code']] = $value;
		}
		return $lists;
	}
}