<?php
class index_control extends control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('pay/payment');
		$this->service_payment = $this->load->service('pay/payment');
		$this->member_deposit_service = $this->load->service('member/member_deposit');
		$this->order_service = $this->load->service('order/order');
		$this->order_trade_service = $this->load->service('order/order_trade');
	}

	public function dnotify() {
		if(!defined('_PAYMENT_')) {
			showmessage(lang('_error_action_'));
		}
		$method = _PAYMENT_;
		$ret = $this->service->_notify(_PAYMENT_);
		runhook('pay_success',$ret);
	}

	public function dreturn() {
		if(!defined('_PAYMENT_')) {
			showmessage(lang('_error_action_'));
		}
		$method = _PAYMENT_;
		$ret = $this->service->_return(_PAYMENT_);
		runhook('pay_success',$ret);
	}

	public function ajax_check() {
		$order_sn = $_GET['order_sn'];
		if(empty($order_sn)) {
			showmessage(lang('order_sn_not_null','order/language'));
		}
		if($order_sn[0] == 'm') {
			$_map = array();
			$_map['order_sn'] = $order_sn;
			$_map['order_status'] = 1;
			$result = $this->member_deposit_service->find($_map);
		} else {
			$_map = array();
			$_map['sn'] = $order_sn;
			$_map['pay_status'] = 1;
			$result = $this->order_service->find($_map);
		}
		if($result) {
			showmessage(lang('order_pay_success','pay/language'), url('index'), 1);
		}
	}

	public function wechat() {
		$member = $this->load->service('member/member')->init();
		$pay_info = array();
		if($_GET['trade_no'][0] == 'm'){
			$m_order = $this->member_deposit_service->find(array('order_sn' => $_GET['trade_no']));
			if (!$m_order || $member['id'] != $m_order['mid']) {
				showmessage(lang('no_promission_view','order/language'));
			}
			if ($order['trade_status'] == 1) {
				showmessage(lang('order_paid','pay/language'));
			}
			$pay_info['trade_sn']  = $m_order['order_sn'];
			$pay_info['total_fee'] = $m_order['money'];
			$pay_info['subject']   = '订单号：'.$m_order['order_sn'];
			$pay_info['pay_bank']  = '';
			//回调链接
			$success_url = url('member/money/success',array('order_sn'=>$pay_info['trade_sn']));
			$error_url = url('member/money/pay');
		}else{
			$order = $this->order_trade_service->find(array('trade_no' => $_GET['trade_no']));
			$order_info = $this->order_service->find(array('sn' => $order['order_sn']));
			if (!$order || $member['id'] != $order_info['buyer_id']) {
				showmessage(lang('no_promission_view','order/language'));
			}
			if ($order_info['pay_status'] == 1) {
				showmessage(lang('order_paid','pay/language'));
			}
			/* 发起wechat支付请求 */
			$pay_info['trade_sn']  = $order['trade_no'];
			$pay_info['total_fee'] = $order['total_fee'];
			$pay_info['subject']   = '订单号：'.$order['trade_no'];
			$pay_info['pay_bank']  = '';
			//回调链接
			$success_url = url('order/order/pay_success',array('order_sn'=>$order['order_sn']));
			$error_url = url('order/order/detail',array('order_sn'=>$order['order_sn']));
		}
		/* 请求支付 */
		$gateway = $this->service_payment->gateway($_GET['pay_code'],$pay_info);
		if($gateway === false) showmessage(lang('pay_set_error','pay/language'));
		include template('wechat_js');
	}
}