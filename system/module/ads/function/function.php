<?php 
/**
 * 格式化广告位数据以从调用
 */
function format_select_data($data) {
	$r = array();
	foreach ($data as $k => $v) {
		$r['items'][$v['id']] = $v['format_name'];
		$r['type'][$v['id']] = $v['type'];
	}
	$r['type'] = $r['type'];
	return $r;
}