<?php
include_once APP_PATH.'module/pay/library/pay_abstract.class.php';
include_once APP_PATH.'module/pay/function/function.php';
class wechat_qr extends pay_abstract {

	protected $gateway_type = 'qrcode';
    
    public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
        $this->config['gateway_url'] = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $this->config['gateway_method'] = 'POST';
        $this->config['notify_url'] = return_url('wechat_qr', 'notify');
		$this->config['return_url'] = return_url('wechat_qr', 'return');
	}
    public function _delivery() {
        return TRUE;
    }

    public function _return(){
        $data=xmlToArray($GLOBALS['HTTP_RAW_POST_DATA']);
        $tmpData=$data;
        unset($tmpData['sign']);
        $return=array('return_code' => 'FAIL', 'return_msg' => '');
        $sign = $this->getSign($tmpData,$this->config['key']);//本地签名
        if ($data['sign'] == $sign) {
            $return['return_code']='SUCCESS';
            $return['return_msg']='OK';
            $result=array();
            $result['result'] = 'success';
            $result['pay_code'] = 'wechat_qr';
            $result['out_trade_no'] = $data['out_trade_no'];
            return $result;
        }
        http::postRequest($this->config['gateway_url'], $this->_array2xml($return),'xml');
    }
    
    public function _notify(){
	    return $this->_return();
    }
    
    public function getpreparedata(){
        $prepare_data['appid'] = $this->config['appid'];
        $prepare_data['body'] = $this->product_info['subject'];
        $prepare_data['mch_id'] = $this->config['mch_id'];
        $prepare_data['nonce_str'] = random(32);
        $prepare_data['notify_url'] = $this->config['notify_url'];
        $prepare_data['out_trade_no'] = $this->product_info['trade_sn'];
        $prepare_data['product_id'] = $this->product_info['trade_sn'];
        // 商品信息
        $prepare_data['spbill_create_ip'] = get_client_ip();
        $prepare_data['trade_type'] = 'NATIVE';
        $prepare_data['total_fee'] = (int)(100*$this->product_info['total_fee']);
        //订单信息
        $prepare_data['sign'] = $this->getSign($prepare_data,$this->config['key']);
        return $prepare_data;
    }

    public function response($result){
        if (FALSE == $result) echo 'fail';
        else echo 'success';
    }
    public function gateway_url() {
	    $result = http::postRequest($this->config['gateway_url'], $this->_array2xml($this->getpreparedata()),'xml');
	    $result = $this->_xml2array($result);
	    if($result['return_code'] === 'SUCCESS' ) {
		    return $result['code_url'];
	    }
	    return false;
    }


	private function _array2xml($array) {
        $xml = "<xml>";
        foreach ($array as $key=>$val)
        {
             $xml.="<".$key.">".$val."</".$key.">";
        }
        $xml.="</xml>";
        return $xml;
	}

	private function _xml2array($xml) {
		return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	}

	
    
    /**
     *  作用：格式化参数，签名过程需要使用
     */
    private function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if($urlencode) {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     *  作用：生成签名
     */
    private function getSign($Obj,$key) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        $String = $String."&key=".$key;
        $String = md5($String);
        $result_ = strtoupper($String);
        return $result_;
    }
}
