<?php
class setting_table extends table
{
	protected function _after_select(&$result, $options) {
		$lists = array();
		foreach($result as $k => $value){
			$lists[$value['key']] = unserialize($value['value']) ? unserialize($value['value']) : $value['value'];
		}
		runhook('setting_get_setting',$lists);
		return $lists;
	}
}