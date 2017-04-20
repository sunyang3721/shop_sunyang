<?php
class module_order_hook
{
	public function create_order(&$member) {
		// echo '下单成功钩子';
		model('notify/notify','service')->execute('create_order', $member);
	}

	public function pay_success(&$ret) {
		helper('order/function');
		// echo '钩子：支付成功';
		if($ret['result'] == 'success') {
			if((int)$ret['out_trade_no'][0] > 0) {
				$sqlmap = array();
				$sqlmap['trade_no'] = $ret['out_trade_no'];
				$sqlmap['status'] = 0;
				$sqlmap['method'] = $ret['pay_code'];
				$pay_info = model('order/order_trade')->where($sqlmap)->find();
				$order_sn = model('order_trade')->where(array('trade_no'=>$ret['out_trade_no']))->getField('order_sn');
				if($pay_info){
			        model('order_trade')->where(array('trade_no'=>$ret['out_trade_no']))->setField('status',1);
			        model('order/order_sub', 'service')->set_order($order_sn, 'pay', 0, array(
			          'pay_method' => $ret['pay_code'],
			          'pay_sn' => $ret['trade_no'],
			        ));
			        $sub_order_info = model('order/order_sub')->where(array('order_sn' => $order_sn))->find();
			        $member = array();
			        $member['member'] = model('member/member')->where(array('id'=>$sub_order_info['buyer_id']))->find();
			        $member['real_amount'] = $sub_order_info['real_price'];
			        model('notify/notify','service')->execute('pay_success', $member);
				}
				showmessage(lang('pay/order_pay_success'),'../../index.php?m=order&c=order&a=pay_success&order_sn='.$order_sn);
		     }
		}
	}

	public function confirm_order(&$order) {
		// echo '钩子：卖家确认订单';
		$member = array();
		$member['member'] = model('member/member')->where(array('id' => $order['buyer_id']))->find();
		$member['order_sn'] = $order['order_sn'];
		model('notify/notify','service')->execute('confirm_order', $member);
	}

	public function skus_delivery(&$order) {
		// echo '钩子：订单商品已发货';
		$member = array();
		$member['member'] = model('member/member')->where(array('id' => $order['buyer_id']))->find();
		$member['delivery_sn'] = $order['delivery_sn'];
		$member['delivery_type'] = $order['delivery_name'];
		$member['order_sn'] = $order['order_sn'];
		model('notify/notify','service')->execute('order_delivery',$member);
	}

	public function delivery_finish() {
		// echo '钩子：确认收货(完成)';
	}

	// 钩子：整个订单完成
	public function order_finish(&$order_sn) {
		$order = model('order/order')->where(array('sn' => $order_sn))->find();
		/* 增加会员经验值 */
		// 获取后台经验获取比例配置
		$exp_rate = (float) model('admin/setting','service')->get('exp_rate');
		if ($order && $exp_rate) {
			$exps = sprintf('%.2f', $order['paid_amount'] * $exp_rate);
			model('member/member')->where(array('id'=>$order['buyer_id']))->setInc('exp',$exps);
			model('member/member','service')->change_group($order['buyer_id']);
		}
		/* 扣除订单冻结余额 */
		if ($order['balance_amount'] > 0) {
			model('member/member','service')->action_frozen($order['buyer_id'],$order['balance_amount'],false,'完成订单，扣除冻结余额中的余额支付部分金额');
		}
		/* 增加skus销量 */
		$skus = model('order/order_sku','service')->get_by_order_sn($order_sn);
		if ($skus) {
			foreach ($skus as $sku) {
				model('goods/goods_index')->where(array('sku_id' => $sku['sku_id']))->setInc('sales',$sku['buy_nums']);
			}
		}
	}
	public function after_login(&$params) {
		model('order/cart','service')->cart_sync($params['id']);
	}
	public function after_register(&$member){
		model('order/cart','service')->cart_sync($member['id']);
	}
}