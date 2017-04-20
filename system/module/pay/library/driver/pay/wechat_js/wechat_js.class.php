<?php
include_once APP_PATH.'module/pay/library/pay_abstract.class.php';
include_once APP_PATH.'module/pay/function/function.php';
class wechat_js extends pay_abstract {
    public $parameters;
    public $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
    public function __construct($config = array()) {
        if (!empty($config)) $this->set_config($config);
        $this->config['gateway_url'] = '';
        $this->config['gateway_method'] = 'POST';
        $this->config['notify_url'] = return_url('wechat_js', 'notify');
        $this->config['return_url'] = return_url('wechat_js', 'return');
    }
    public function _delivery() {
        return TRUE;
    }

    public function _return(){
        $data=xmlToArray($GLOBALS['HTTP_RAW_POST_DATA']);
        $tmpData=$data;
        unset($tmpData['sign']);
        $return=array();
        $sign = $this->getSign($tmpData,$this->config['key']);//本地签名
        if ($data['sign'] == $sign) {
            $return['return_code']='SUCCESS';
            $return['return_msg']='OK';
            $result=array();
            $result['result'] = 'success';
            $result['pay_code'] = 'wechat_js';
            $result['out_trade_no'] = $data['out_trade_no'];
            return $result;
        }else{
            $return['return_code']='FAIL';
            $return['return_msg']='';
            return FALSE;
        }
    }
    public function _notify(){
        return $this->_return();
    }

    public function response($result){
        if (FALSE == $result) echo 'fail';
        else echo 'success';

    }
    public function getOpenid(){
        //触发微信返回code码
        $redirectUrl = urlencode((is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].url('pay/index/wechat',array('showwxpaytitle'=>1,'trade_no'=>$this->product_info['trade_sn'],'pay_code'=>'wechat_js')));
        $url_code = array();
        $url_code["appid"] = $this->config['appid'];
        $url_code["redirect_uri"] = $redirectUrl;
        $url_code["response_type"] = "code";
        $url_code["scope"] = "snsapi_base";
        $url_code["state"] = "STATE"."#wechat_redirect";
        $get_code_url = "https://open.weixin.qq.com/connect/oauth2/authorize?".$this->ToUrlParams($url_code);
        if(!$_GET['code']){
            redirect($get_code_url);
        }
        //获取code码，以获取openid
        $code = $_GET['code'];
        $url_openid = array();
        $url_openid["appid"] = $this->config['appid'];
        $url_openid["secret"] = $this->config['secret'];
        $url_openid["code"] = $code;
        $url_openid["grant_type"] = "authorization_code";
        $get_openid_url = "https://api.weixin.qq.com/sns/oauth2/access_token?".$this->ToUrlParams($url_openid);
        $openid_info = http::postRequest($get_openid_url,'','xml');
        $openid_info = json_decode($openid_info,TRUE);
        return $openid_info['openid'];
    }

    public function getpreparedata() {
        $prepare_data=array();
        $prepare_data['appid'] = $this->config['appid'];
        $prepare_data['body'] = $this->product_info['subject'];
        $prepare_data['mch_id'] = $this->config['mch_id'];
        $prepare_data['nonce_str'] = strval(random(32));
        $prepare_data['notify_url'] = $this->config['notify_url'];
        $prepare_data['out_trade_no'] = $this->product_info['trade_sn'];
        $prepare_data['openid'] = $this->getOpenid();
        // 商品信息
        $prepare_data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'] ;
        $prepare_data['trade_type'] = 'JSAPI';
        $prepare_data['total_fee'] = (int)(100*$this->product_info['total_fee']);
        //订单信息
        $prepare_data['sign'] = $this->getSign($prepare_data,$this->config['key']);
        // 数字签名
        $prepare_xml = arrayToXml($prepare_data);
        $this->result = http::postRequest($this->url, $prepare_xml,'xml');
        $this->result = xmlToArray($this->result);
        $jsApiData=array();
        $jsApiData['appId']=$this->config['appid'];
        $jsApiData['timeStamp']=strval(time());
        $jsApiData['nonceStr']=strval(random(32));
        $jsApiData['package']='prepay_id='.$this->result['prepay_id'];
        $jsApiData['signType']='MD5';
        $jsApiData['paySign']=$this->getSign($jsApiData,$this->config['key']);;
        $jsApiData_json=json_encode($jsApiData);
        return $jsApiData_json;
    }
    /**
     *  作用：格式化参数，签名过程需要使用
     */

    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     *  作用：生成签名
     */
    private function getSign($Obj,$key){
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        $String = $String."&key=".$key;
        $String = md5($String);
        $result_ = strtoupper($String);
        return $result_;
    }
    private function ToUrlParams($urlObj){
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    public function gateway_url() {
        return $this->getpreparedata();
    }
}
