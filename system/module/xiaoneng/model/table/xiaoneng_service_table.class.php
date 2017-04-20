<?php

/**
 *       小能配置表数据层
 */

class xiaoneng_service_table extends table{
	protected function _after_find(&$result, $options) {
		$result['config'] = json_decode($result['config'],TRUE);
		return $result;
	}
}
