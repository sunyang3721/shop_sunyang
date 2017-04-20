<?php
class admin_user_table extends table
{
	protected $_validate = array(
		array('username','','{admin/user_name_exist}',0,'unique',1),
		array('username', 'require', '{admin/username_not_exist}', table::MUST_VALIDATE),
		array('password', 'require', '{admin/password_not_standard}', table::VALUE_VALIDATE,'', table::MODEL_BOTH),
	);

	protected function _after_find(&$result, $options) {
		$result['group_name'] = model('admin_group')->where(array('id'=>$result['group_id']))->getField('title');
		return $result;
	}
	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {
			$this->_after_find($record, $options);
		}
		return $result;
	}

	public function fetch_by_username($username) {
		return $this->where(array("username" => $username))->find();
	}
	public function fetch_by_id($id) {
		return $this->where(array("id" => $id))->find();
	}
}