<?php
include_once APP_PATH.'module/pay/library/pay_abstract.class.php';
include_once APP_PATH.'module/pay/function/function.php';
class bank extends pay_abstract {
	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
        $this->config['service'] = 'create_direct_pay_by_user';
		$this->config['gateway_url'] = 'https://mapi.alipay.com/gateway.do?';
		$this->config['gateway_method'] = 'POST';
		$this->config['notify_url'] = return_url('bank', 'notify');
		$this->config['return_url'] = return_url('bank', 'return');
	}

	public function getpreparedata() {
		$prepare_data['service'] = $this->config['service'];
		$prepare_data['payment_type'] = '1';
		$prepare_data['seller_email'] = $this->config['account'];
		$prepare_data['partner'] = $this->config['partner'];
		$prepare_data['_input_charset'] = CHARSET;
		$prepare_data['notify_url'] = $this->config['notify_url'];
		$prepare_data['return_url'] = $this->config['return_url'];
		if ($this->product_info['pay_bank']) {
			$prepare_data['defaultbank'] = $this->product_info['pay_bank'];
		}
		unset($this->product_info['pay_bank']);
		// 商品信息
		$prepare_data['subject'] = $this->product_info['subject'];
		$prepare_data['body'] = $this->product_info['body'];
		if (array_key_exists('url', $this->product_info)) $prepare_data['show_url'] = $this->product_info['url'];

		if (!array_key_exists('total_fee', $this->product_info)) {
			$prepare_data['price'] = $this->product_info['price'];
			$prepare_data['quantity'] = $this->product_info['quantity'];
		} else {
			$prepare_data['total_fee'] = $this->product_info['total_fee'];
		}

		//订单信息
		$prepare_data['out_trade_no'] = $this->product_info['trade_sn'];
		// 物流信息
		if($this->config['service'] == 'create_partner_trade_by_buyer' || $this->config['service'] == 'trade_create_by_buyer') {
			$prepare_data['logistics_type'] = 'EXPRESS';
			$prepare_data['logistics_fee'] = '0.00';
			$prepare_data['logistics_payment'] = 'SELLER_PAY';
			$prepare_data['price'] = $this->product_info['total_fee'];
			$prepare_data['quantity'] = 1;
			unset($prepare_data['total_fee']);
		}
		//买家信息
		$prepare_data['buyer_email'] = $this->product_info['buyer_email'];
		$prepare_data = arg_sort($prepare_data);
		// 数字签名
		$prepare_data['sign'] = build_mysign($prepare_data,$this->config['key'],'MD5');
		$prepare_data['sign_type'] = 'MD5';
		return $prepare_data;
	}

	public function gateway_url() {
		return $this->config['gateway_url'].http_build_query($this->getpreparedata());
	}

	public function _delivery() {
		return TRUE;
	}

    public function _return() {
        $params = $this->filterParameter($_GET);
        $sign = build_mysign($params,$this->config['key'],'MD5');
        $result = array();
        if(($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') && $sign == $_GET['sign']) {
            $result['result'] = 'success';
            $result['pay_code'] = 'bank';
            $result['trade_no'] = $_GET['trade_no'];
            $result['out_trade_no'] = $_GET['out_trade_no'];
            $result['out_trade_no'] = $_GET['out_trade_no'];
            return $result;
        } else {
            return FALSE;
        }
    }
	/**
	 * POST接收数据
	 * 状态码说明  （0 交易完成 1 待付款 2 待发货 3 待收货 4 交易关闭 5交易取消
	 */
	public function _notify() {
        return $this->_return();
	}

	/**
	 * 相应服务器应答状态
	 * @param $result
	 */
	public function response($result) {
		if (FALSE == $result) echo 'fail';
		else echo 'success';
	}

	/**
	 * 返回字符过滤
	 * @param $parameter
	 */
	private function filterParameter($parameter)
	{
		$para = array();
		foreach ($parameter as $key => $value) {
			if ('sign' == $key || 'sign_type' == $key || '' == $value || 'm' == $key  || 'a' == $key  || 'c' == $key   || 'code' == $key || 'method' == $key || 'page' == $key) continue;
			else $para[$key] = $value;
		}
		return $para;
	}
	public function getCodeUrl(){

	}
}