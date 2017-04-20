<?php
class delivery_district_table extends table {
	public function _after_select($result, $options) {
		foreach($result AS $k => $v) {
			$v['district_ids'] = $v['district_names'] = array();
			if($v['district_id']) {
				$result[$k]['district_ids'] = explode(",", $v['district_id']);
				$result[$k]['district_names'] = $this->load->table('district')->where(array("id" => array("IN",$result[$k]['district_ids'] )))->getField('name', TRUE);
			}
			
		}
		return $result;
	}
}	