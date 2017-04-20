<?php
class money_control extends cp_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('pay/payment');
		$this->member_log_service = $this->load->service('member/member_log');
	}

	public function log($sqlmap=array()) {
		//配置文件
		$_config = model('admin/setting','service')->get();
		$member = $this->member;
		$sqlmap['mid'] = $member['id'];
		$sqlmap['type'] = 'money';
		$result = $this->member_log_service->lists($sqlmap, 15, $_GET['page'], "id DESC");
		$pages = pages($result['count'],15);
		$SEO = seo('账户余额 - 会员中心');
		$this->load->librarys('View')->assign('_config',$_config)->assign('member',$member)->assign('pages',$pages)->assign('log',$result['lists'])->assign('SEO',$SEO)->display('money_log');
	}

	/* 余额充值 */
	public function pay() {
		$current = $this->load->service('admin/setting')->get('balance_deposit');
		if(config('TPL_THEME') == 'wap'){
			$payments = $this->service->getpayments('wap', $current);
		}else{
			$payments = $this->service->getpayments('pc', $current);
		}
		if(checksubmit('dosubmit')) {
			$money = $pay_code = $pay_bank = '';
			extract($_GET,EXTR_IF_EXISTS);
			$pay_info = array();
			$pay_info['trade_sn'] = build_order_no('m');
			$pay_info['total_fee'] = $money;
			$pay_info['subject'] = '用户充值：'.$money;
			$pay_info['pay_bank'] = $pay_bank;
			//记录订单
			$data = array();
			$data['mid'] = $this->member['id'];
			$data['money'] = $money;
			$data['order_sn'] = $pay_info['trade_sn'];
			$data['order_time'] = time();
			$this->load->service('member/member_deposit')->wlog($data);
			cookie('last_sn', $pay_info['trade_sn']);
			/* 请求支付 */
			$gateway = $this->service->gateway($pay_code,$pay_info,$pay_config);
			if($gateway === false) {
				showmessage(lang('pay_set_error','pay/language'));
			}
			if (defined('MOBILE') && $gateway['gateway_type'] == 'redirect') {
				redirect($gateway['gateway_url']);
			}
			$gateway['order_sn'] = $data['order_sn'];
            $gateway['trade_no'] = $data['order_sn'];
			$gateway['total_fee'] = $money;
			/* 支付成功后的跳转 */
			if(config('TPL_THEME') == 'wap'){
				$gateway['url_forward'] = url('success');
			}else{
				$gateway['url_forward'] = url('log');
			}
			include template('cashier', 'pay');
		}else{
			$SEO = seo('余额充值 - 会员中心');
			$this->load->librarys('View')->assign('SEO',$SEO)->assign('current',$current)->assign('payments',$payments)->display('money_pay');
		}
	}
	/* 检测是否支付成功 */
	public function payissuccess() {
		$status = $this->load->service('member/member_deposit')->is_sucess(array('mid'=>$this->member['id'],'order_sn'=> cookie('last_sn')));
		exit(json_encode(array('status' => (int)$status)));
	}
	public function success(){
		$order = $this->load->service('member/member_deposit')->find(array('order_sn' => $_GET['order_sn']));
		if (!$order) showmessage(lang('order_not_exist','order/language'));
		if ($order['mid'] != $this->member['id']) showmessage(lang('no_promission_view','order/language'));
		$SEO = seo('充值成功');
		$this->load->librarys('View')->assign('order',$order)->assign('SEO',$SEO)->display('pay_success');
	}
	public function get_log(){
		//配置文件
		$member = $this->member;
		$sqlmap['mid'] = $member['id'];
		$sqlmap['type'] = 'money';
		$result = $this->member_log_service->lists($sqlmap, $_GET['limit'], $_GET['page'], "id DESC");
		$this->load->librarys('View')->assign('log',$result['lists']);
        $log = $this->load->librarys('View')->get('log');
		echo json_encode($log);
	}
}