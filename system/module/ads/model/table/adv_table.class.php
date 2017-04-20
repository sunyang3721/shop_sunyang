<?php
/**
 *		广告列表数据层
 */

class adv_table extends table {
	protected $type_arr = array('图片', '文字');
	protected $_validate = array( 
		array('title', 'require', '{adv/adv_name_require}', table::MUST_VALIDATE), 
		array('position_id', 'require', '{adv/adv_position_require}', table::MUST_VALIDATE), 
		array('starttime,endtime', 'checkDay', '{adv/endtime_not_gt_atarttime}', 1,'callback', 3),
	);
	protected $_auto = array( 
		array('starttime', 'strtotime', 3, 'function'), 
		array('endtime', 'strtotime', 3, 'function'), 
	);
	protected function _after_find(&$result, $options) {
		$position = $this->load->table('adv_position')->where(array('id' => $result['position_id']))->find();
		$result['position_name'] = isset($position['name']) ? $position['name'] : '--';
		$result['type_text'] = $this->type_arr[$position['type']];
		$result['startime_text'] = date('Y-m-d H:i:s', $result['starttime']);
		$result['endtime_text'] = date('Y-m-d H:i:s', $result['endtime']);
		return $result;
	}
	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {
			$this->_after_find($record, $options);
		}
		return $result;
	}
	//开始结束时间
	protected function checkDay($data){
	    if($data['starttime'] > $data['endtime'])
	        return false;
	    else
	        return true;
	}
}
