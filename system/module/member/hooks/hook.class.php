<?php
class module_member_hook
{
	public function pay_success(&$ret) {
		if($ret['result'] == 'success') {
			if($ret['out_trade_no'][0] == 'm') {
				$_map = array();
				$_map['order_sn'] = $ret['out_trade_no'];
				$_map['order_status'] = 0;
				$deposit = model('member_deposit')->where($_map)->find();
				if($deposit) {
					model('member/member','service')->change_account($deposit['mid'], 'money', '+'.$deposit['money'], $msg = '在线充值');
					$_deposit = array(
						'id' => $deposit['id'],
						'trade_sn' => $ret['trade_no'],
						'trade_time' => TIMESTAMP,
						'trade_status' => 1,
						'pay_code' => $ret['pay_code'],
						'order_status' => 1
					);
					model('member_deposit')->update($_deposit, false);
					$member = array();
					$member['member'] = model('member/member')->where(array('id' => $deposit['mid']))->find();
					$member['user_money'] = $member['member']['money'];
					$member['recharge_money'] = $deposit['money'];
					model('notify/notify','service')->execute('recharge_success', $member);
				}
				showmessage(lang('pay/order_pay_success'), '../../index.php?m=member&c=money&a=log');
			}
		}
	}
	/* 邮箱验证 */
	public function email_validate(&$params){
		$member = $params;
		$member['email_validate'] = $_SERVER['HTTP_HOST'].url('member/public/resetemail',array('vcode'=>base64_encode(authcode(json_encode(array($params['mid'],$params['vcode'],$params['email'])),'ENCODE'))));
		model('notify/notify','service')->execute('email_validate', $member, 0);
	}
	/* 注册验证 */
	public function register_validate(&$params){
		model('notify/notify','service')->execute('register_validate', $params, 0);
	}
	/* 手机验证 */
	public function mobile_validate(&$params){
		model('notify/notify','service')->execute('mobile_validate', $params, 0);
	}
	/* 余额变更 */
	public function money_change(&$params){
		model('notify/notify','service')->execute('money_change', $params);
	}
	/*忘记密码*/
	public function forget_pwd(&$params){
	    model('notify/notify','service')->execute('forget_pwd', $params,0);
	}
	/**
	 * [member_init description]
	 * @param  [type] &$params [description]
	 * @return [type]          [description]
	 */
	public function member_init(&$params){
		if($params['id'] > 0){
			$load = hd_load::getInstance();
			$params['counts'] = $load->table('order/order')->buyer_id($params['id'])->out_counts();
		}
	}
	public function after_login(&$params) {
		model('member/member','service')->change_group($params['id']);
	}
}