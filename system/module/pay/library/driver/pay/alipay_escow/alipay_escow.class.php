<?php
include_once APP_PATH.'module/pay/library/pay_abstract.class.php';
include_once APP_PATH.'module/pay/function/function.php';
class alipay_escow extends pay_abstract {
	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
        $this->config['service'] = 'create_partner_trade_by_buyer';
		$this->config['gateway_url'] = 'https://mapi.alipay.com/gateway.do?';
		$this->config['gateway_method'] = 'POST';
		$this->config['notify_url'] = return_url('alipay_escow', 'notify');
		$this->config['return_url'] = return_url('alipay_escow', 'return');
	}

	public function getpreparedata() {
		/* 接口名称 */
		$prepare_data['service'] = $this->config['service'];
		/* 合作身份者ID */
		$prepare_data['partner'] = $this->config['partner'];
		/* 编码字符集 */
		$prepare_data['_input_charset'] = CHARSET;
		/* 交易类型 */
		$prepare_data['payment_type'] = '1';
		$prepare_data['seller_email'] = $this->config['account'];

		/* 物流信息 */
		$prepare_data['logistics_type'] = 'EXPRESS';
		$prepare_data['logistics_fee'] = '0.00';
		$prepare_data['logistics_payment'] = 'SELLER_PAY';

		/* 交易费用 */
		$prepare_data['price'] = $this->product_info['total_fee'];
		$prepare_data['quantity'] = 1;

		/* 交易订单号 */
		$prepare_data['out_trade_no'] = $this->product_info['trade_sn'];

		$prepare_data['notify_url'] = $this->config['notify_url'];
		$prepare_data['return_url'] = $this->config['return_url'];
		 // 商品信息
		$prepare_data['subject'] = $this->product_info['subject'];
		$prepare_data['body'] = $this->product_info['body'];

		/* 卖家支付宝账号 */
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

	/* 发货接口 */
	public function _delivery() {
		$params = array();
		$params['service'] = 'send_goods_confirm_by_platform';
		$params['partner'] = $this->config['partner'];
		$params['_input_charset'] = CHARSET;
		/* 支付宝交易号 */
		$params['trade_no'] = $this->product_info['trade_no'];
		$params['logistics_name'] = $this->product_info['logistics_name'];
		$params['invoice_no'] = $this->product_info['invoice_no'];
		$params['transport_type'] = 'EXPRESS';
		$params['sign'] = build_mysign($params,$this->config['key'],'MD5');
		$params['sign_type'] = 'MD5';
		$result =  getHttpResponsePOST($this->config['gateway_url'], $params);
		$doc = new DOMDocument();
		$doc->loadXML($result);
		$result = $doc->getElementsByTagName("alipay")->item(0)->nodeValue;
		if(substr($result, 0, 1) === 'T') {
			return TRUE;
		}
		return FALSE;
	}

    public function _return() {
        $params = $this->filterParameter($_GET);
        $sign = build_mysign($params,$this->config['key'],'MD5');
        $result = array();
        if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' && $sign == $_GET['sign']) {
            $result['result'] = 'success';
            $result['pay_code'] = 'alipay_escow';
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

    /* 处理支付宝返回数据 */
    private function parseResponse($content) {
		//以“&”字符切割字符串
		$para_split = explode('&',$content);
		//把切割后的字符串数组变成变量与数值组合的数组
		foreach ($para_split as $item) {
			//获得第一个=字符的位置
			$nPos = strpos($item,'=');
			//获得字符串长度
			$nLen = strlen($item);
			//获得变量名
			$key = substr($item,0,$nPos);
			//获得数值
			$value = substr($item,$nPos+1,$nLen-$nPos-1);
			//放入数组中
			$para_text[$key] = $value;
		}
		return $para_text;
    }
}