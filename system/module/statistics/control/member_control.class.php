<?php
hd_core::load_class('init', 'admin');
class member_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('member');
		$this->deposit = $this->load->service('member_deposit');
	}

	public function index(){
		$sqlmap['_string']='date_format(from_UNIXTIME(`register_time`),\'%Y-%m-%d\') = date_format(now(),\'%Y-%m-%d\')';
		$member['today'] = $this->service->_count($field,$sqlmap);
		$sqlmap['_string']='date_format(from_UNIXTIME(`register_time`),\'%Y-%m\') = date_format(now(),\'%Y-%m\')';
		$member['tomonth'] = $this->service->_count($field,$sqlmap);
		unset($sqlmap);
		$field = "SUM(money) money,count(id) as num";
		$data= $this->service->_query($field,$sqlmap);
		$member['money'] = $data[0]['money'];
		$member['num'] = $data[0]['num'];
		$this->load->librarys('View')->assign('data',$data)->assign('member',$member)->display('member');
	}

	public function ajax_getdata(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$row = $this->service->build_data($_GET);
		$this->load->librarys('View')->assign('row',$row);
        $row = $this->load->librarys('View')->get('row');
		echo json_encode($row);
	}
}