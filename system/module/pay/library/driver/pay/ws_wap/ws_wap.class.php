<?php
include_once APP_PATH.'module/pay/library/pay_abstract.class.php';
include_once APP_PATH.'module/pay/function/function.php';
class ws_wap extends pay_abstract {
	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
        // 授权接口地址
        $this->config['service'] = 'alipay.wap.trade.create.direct';
		$this->config['gateway_url'] = 'http://wappaygw.alipay.com/service/rest.htm?';
		$this->config['gateway_method'] = 'POST';
        $this->config['format'] = 'xml';
        $this->config['v'] = '2.0';
        $this->config['sec_id'] = 'MD5';

		$this->config['notify_url'] = return_url('ws_wap', 'notify');
		$this->config['return_url'] = return_url('ws_wap', 'return');
	}

    public function getAuthData() {
        $req_data = '';
        $req_data = $this->getXmlFormat('subject', $this->product_info['subject']);//商品名称
        $req_data .= $this->getXmlFormat('out_trade_no', $this->product_info['trade_sn']);//订单号
        $req_data .= $this->getXmlFormat('total_fee', $this->product_info['total_fee']);//交易金额
        $req_data .= $this->getXmlFormat('seller_account_name', $this->config['account']);//卖家支付宝账号
        $req_data .= $this->getXmlFormat('call_back_url', $this->config['return_url']);//支付成功跳转页
        $req_data .= $this->getXmlFormat('notify_url', $this->config['notify_url']);//支付成功异步通知页
        $this->req_data = $req_data;
        $AuthArgs = array();
        $AuthArgs['_input_charset'] = 'utf-8';
        $AuthArgs['service'] = 'alipay.wap.trade.create.direct';
        $AuthArgs['format'] = $this->config['format'];
        $AuthArgs['v'] = $this->config['v'];
        $AuthArgs['partner'] = $this->config['partner'];
        $AuthArgs['req_id'] = $this->product_info['trade_sn'];
        $AuthArgs['sec_id'] = $this->config['sec_id'];
        $AuthArgs['req_data'] = '<direct_trade_create_req>'.$req_data.'</direct_trade_create_req>';
        $AuthArgs['sign'] = build_mysign($AuthArgs, $this->config['key'], 'MD5');
        $result = getHttpResponsePOST($this->config['gateway_url'], $AuthArgs);
        $result = urldecode($result);
        $result = $this->parseResponse($result);
        return $result;
    }


    public function getpreparedata() {
    	$auth = $this->getAuthData();
    	$prepare_data = array();
		$prepare_data['service'] = 'alipay.wap.auth.authAndExecute';
		$prepare_data['format'] = $this->config['format'];
		$prepare_data['v'] = $this->config['v'];
		$prepare_data['partner'] = $this->config['partner'];
		$prepare_data['sec_id'] = $this->config['sec_id'];
		$prepare_data['req_data'] = '<auth_and_execute_req><request_token>'.$auth['request_token'].'</request_token></auth_and_execute_req>';
		// $prepare_data['request_token'] = $auth['request_token'];
		$prepare_data['sign'] = build_mysign($prepare_data, $this->config['key'], 'MD5');
		return $prepare_data;
	}

    public function _delivery() {
        return TRUE;
    }

    /**
	 * POST接收数据
	 * 状态码说明  （0 交易完成 1 待付款 2 待发货 3 待收货 4 交易关闭 5交易取消
     * 返回值 {
     *  失败：false;
     *  成功:{
     *      result : success
     *      out_trade_no :系统订单号
     *      trade_no ：支付宝交易号
     *  }
     * }
	 */
    public function _return() {
        $params = $this->filterParameter($_GET);
        $sign = build_mysign($params,$this->config['key'],'MD5');
        if($_GET['sign'] == $sign && $_GET['result'] == 'success') {
            return array(
                'result' => 'success',
                'out_trade_no' => $_GET['out_trade_no'],
                'trade_no' => $_GET['trade_no'],
                'pay_code' => 'ws_wap'
            );
        } else {
            return FALSE;
        }
    }

    public function _notify() {
        $params = array();
        $params['service'] = $_GET['service'];
        $params['v'] = $_GET['v'];
        $params['sec_id'] = $_GET['sec_id'];
        $params['notify_data'] = htmlspecialchars_decode($_GET['notify_data']);
        $sign = build_mysign($params,$this->config['key'],'MD5', FALSE);
        $notify_data = $this->xmlToArray($params['notify_data']);
        if($_GET['sign'] == $sign && ($notify_data['trade_status'] == 'TRADE_FINISHED' || $notify_data['trade_status'] == 'TRADE_SUCCESS')) {
            return array(
                'result' => 'success',
                'out_trade_no'  => $notify_data['out_trade_no'],
                'trade_no'  => $notify_data['trade_no'],
                'pay_code'  => 'ws_wap',
            );
        }
        return FALSE;
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

    private function xmlToArray($xml)
    {
        $xml_obj = simplexml_load_string($xml, 'SimpleXMLIterator');
        if(!is_object($xml_obj)) return FALSE;
        $arr = array();
        $xml_obj->rewind(); //指针指向第一个元素
        while (1) {
            if( ! is_object($xml_obj->current()) )
            {
                break;
            }
            $arr[$xml_obj->key()] = $xml_obj->current()->__toString();
            $xml_obj->next(); //指向下一个元素
        }
        return $arr;
    }


    private function getXmlFormat($key, $value) {
        return '<'.$key.'>'.$value.'</'.$key.'>';
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

		if(!empty($para_text['res_data'])) {
			$doc = new DOMDocument();
			$doc->loadXML($para_text['res_data']);
			$para_text['request_token'] = $doc->getElementsByTagName( "request_token" )->item(0)->nodeValue;
		}
		return $para_text;
    }
    public function getCodeUrl(){

    }

    public function gateway_url() {
        return $this->config['gateway_url'].http_build_query($this->getpreparedata());
    }

}