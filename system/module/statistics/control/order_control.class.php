<?php
hd_core::load_class('init', 'admin');
class order_control extends init_control {

	public function _initialize() {
		parent::_initialize();
		$this->service_order = $this->load->service('order/statistics');
		helper('order/function');
	}

	public function index(){
		$datas = $this->service_order->build_sqlmap(array('days' => 7))->output('sales,districts,payments');
		/* 组装地区信息 */
		if ($datas['districts']) {
			foreach ($datas['districts'] as $k => $v) {
				$datas['districts'][$k]['name'] = $v['name'];
				$datas['districts'][$k]['value'] = $v['value'];
			}
		}
		/* 组装支付方式 */
		if ($datas['payments']) {
			foreach ($datas['payments'] as $k => $v) {
				$datas['pays'][$k] = $v['name'];
			}
		}
		$this->load->librarys('View')->assign('datas',$datas)->display('order');
	}

	public function ajax_getdata(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$days = (int) $_GET['days'];
		$start_time = strtotime($_GET['start_time']);
		$end_time = ($_GET['end_time']) ? strtotime($_GET['end_time']) : strtotime(date('Y-m-d 00:00:00'));
		$datas = $this->service_order->build_sqlmap(array('days' => $days ,'start_time' => $start_time ,'end_time' => $end_time))->output('sales');
		showmessage(lang('request_success','statistics/language') ,'', 1 ,$datas);
	}

	/* 后台首页获取统计数据 */
	public function ajax_home() {
		$datas = $this->service_order->get_data();
		$this->load->librarys('View')->assign('datas',$datas);
        $datas = $this->load->librarys('View')->get('datas');
		echo json_encode($datas);
	}
}