<?php
/**
 * 		后台售后 控制器
 */
hd_core::load_class('init', 'admin');
class admin_server_control extends init_control {

	public function _initialize() {
		parent::_initialize();
		$this->track_service = $this->load->service('order/order_track');
		$this->service = $this->load->service('order/order_server');
		$this->service_order = $this->load->service('order/order');
	}

	/* 退货列表管理 */
	public function index_return() {
		$options = $sqlmap = array();
		$options['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 10;
		$options['page'] = ($_GET['page']) ? $_GET['page'] : 1;
		$sqlmap = $this->service->build_map($_GET);
		$infos = $this->service->get_returns($options ,$sqlmap);
        $pages = $this->admin_pages($infos['count'], $options['limit']);
        $lists = array(
			'th' => array(
				'sku_name' => array('title' => '退货商品','length' => 30,'style' => 'goods'),
				'username' => array('title' => '会员账号','length' => 10),
				'amount' => array('length' => 10,'title' => '退款金额'),
				'dateline' => array('title' => '申请时间','length' => 15,'style'=>'date'),
				'status' => array('length' => 15,'title' => '物流信息','style'=>'delivery_status'),
				'_status' => array('length' => 10,'title' => '处理状态'),
			),
			'lists' => $infos['lists'],
			'pages' => $pages,
		);
        $this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('index_return');
	}

	/* 退款列表页 */
	public function index_refund() {
		$options = $sqlmap = array();
		$options['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 10;
		$options['page'] = ($_GET['page']) ? $_GET['page'] : 1;
		$sqlmap = $this->service->build_map($_GET);
		$infos = $this->service->get_refunds($options ,$sqlmap);
        $pages = $this->admin_pages($infos['count'], $options['limit']);
        $lists = array(
			'th' => array(
				'sku_name' => array('title' => '退货商品','length' => 30,'style' => 'goods'),
				'_type' => array('title' => '退款类型','length' => 15),
				'username' => array('title' => '会员账号','length' => 10),
				'amount' => array('length' => 10,'title' => '退款金额'),
				'dateline' => array('title' => '申请时间','length' => 15,'style'=>'date'),
				'_status' => array('length' => 10,'title' => '处理状态'),
			),
			'lists' => $infos['lists'],
			'pages' => $pages,
		);
        $this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('index_refund');
	}

	public function alert_return(){
		$this->load->librarys('View')->display('alert_return');
	}

	public function alert_refund(){
		$this->load->librarys('View')->display('alert_refund');
	}
	/* 退货详情页 */
	public function detail_return() {
		if (checksubmit('dosubmit')) {
			$status = ($_GET['status'] == 1) ? 1 : -2;
			$operator = get_operator();
			$result = $this->service->handle_return($_GET['id'] , $status , $_GET['msg'], $operator['id'], $operator['operator_type']);
			if (!$result) showmessage($this->service->error);
			showmessage(lang('_operation_success_'),'', 1,'json');
		} else {
			$_return = $this->service->return_detail((int) $_GET['id']);
			if (!$_return) showmessage(lang('record_no_exist','order/language'));
			$this->load->librarys('View')->assign('_return',$_return)->display('detail_return');
		}
	}

	/* 退款详情页 */
	public function detail_refund() {
		if (checksubmit('dosubmit')) {
			$status = ($_GET['status'] == 1) ? 1 : -2;
			$operator = get_operator();
			$result = $this->service->handle_refund($_GET['id'] , $status , $_GET['msg'], $operator['id'], $operator['operator_type']);
			if (!$result) showmessage($this->service->error);
			showmessage(lang('_operation_success_'),'', 1,'json');
		} else {
			$_refund = $this->service->refund_detail((int) $_GET['refund_id']);
			$server = $this->service_order->order_return_find(array('id'=>$_refund['return_id']));
			$track = $this->track_service->kuaidi100($server['delivery_name'],$server['delivery_sn']);
			if (!$_refund) showmessage(lang('record_no_exist','order/language'));
			$this->load->librarys('View')->assign('_refund',$_refund)->assign('server',$server)->assign('track',$track)->display('detail_refund');
		}
	}

}