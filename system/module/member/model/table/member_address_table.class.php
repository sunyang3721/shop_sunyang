<?php
class member_address_table extends table
{
	protected $_validate = array(
		/* array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]) */
		array('mid', 'number', '{member/this_user_name_require}', table::EXISTS_VALIDATE, 'regex', table:: MODEL_BOTH),
		array('name', 'require', '{member/consignee_name_not_empty}', table::MUST_VALIDATE, 'regex', table:: MODEL_BOTH),
		array('mobile', 'mobile', '{member/mobile_format_exist}', table::MUST_VALIDATE, 'regex', table:: MODEL_BOTH),
		array('district_id', 'number', '{member/receiving_area_not_correct}', table::MUST_VALIDATE, 'regex', table:: MODEL_BOTH),
		array('address', 'require', '{member/receiving_address_not_empty}', table::MUST_VALIDATE, 'regex', table:: MODEL_BOTH),
	);


	public function fetch_all_by_mid($mid, $order = '') {
		return $this->where(array('mid' => $mid))->order($order)->select();
	}
}