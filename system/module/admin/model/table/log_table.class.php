<?php
class log_table extends table
{
	protected function _after_find(&$result, $options) {
		$username = $this->load->table('admin_user')->getFieldById($result['user_id'],'username');
		$result['username'] = isset($username) ? $username : '--';
		$result['dateline_text'] = date('Y-m-d H:i:s', $result['starttime']);
		return $result;
	}
	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {
			$this->_after_find($record, $options);
		}
		return $result;
	}
}