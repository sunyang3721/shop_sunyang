<?php
/**
 *		广告列表数据层
 */

class adv_position_table extends table {
	protected $type_arr = array('图片', '文字');
	protected $_validate = array( 
		array('name', 'require', '{ads/adv_require}', table::MUST_VALIDATE), 
	);
	protected $_auto = array(
	);
	protected function _after_find(&$result, $options) {

		$result['type_text'] = $this->type_arr[$result['type']];
		$result['format_name'] = $result['type'] ? '[文字]'.$result['name']  : '[图片]'.$result['name'].'('.$result['width'].'*'.$result['height'].')' ;
		$result['adv_count'] = $this->load->table('ads/adv')->where(array('position_id'=>$result['id']))->count() ;
		return $result;
	}
	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {
			$this->_after_find($record, $options);
		}
		return $result;
	}
	protected function _after_getdetail(&$result, $options){
		return $result;
	}
}
