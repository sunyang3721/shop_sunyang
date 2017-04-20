<?php
class member_log_table extends table{

	protected function _after_find(&$result, $options) {
		if ($result['money_detail']) {
			$result['money_detail'] = json_decode($result['money_detail'] ,TRUE);
		}
		$result['dateline_text'] = date('Y-m-d H:i:s', $result['dateline']);
		$result['value'] = $result['value']>0 ? '+'.$result['value'] : $result['value'] ;
		$result['username'] = $this->load->table('member/member')->fetch_by_id($result['mid'], 'username');
		return $result;
	}

	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {
			$this->_after_find($record, $options);
		}
		return $result;
	}

	public function fetch_all($parment) {
		$sqlmap = $parment;
		return $this->where($sqlmap)->select();
	}

	public function wlog($parment = array()) {
		return $this->update($parment);
	}
}