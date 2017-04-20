<?php
include_once APP_PATH.'module/pay/library/pay_abstract.class.php';
include_once APP_PATH.'module/pay/function/function.php';
class jdpay extends pay_abstract {
	public function __construct($config = array()) {
		if (!empty($config)) $this->set_config($config);
		$this->config['gateway_url'] = 'https://tmapi.jdpay.com/PayGate?';
		$this->config['gateway_method'] = 'POST';
		$this->config['notify_url'] = return_url('jdpay', 'notify');
		$this->config['return_url'] = return_url('jdpay', 'return');
	}

	public function getpreparedata() {
		$prepare_data['v_mid'] = $this->config['mid'];
		$prepare_data['v_moneytype'] = 'CNY';
		$prepare_data['v_oid'] = $this->product_info['trade_sn'];
		$prepare_data['v_url'] = $this->config['return_url'];
		$prepare_data['remark2'] = '[url:='.$this->config['notify_url'].']';
		$prepare_data['v_amount'] = $this->product_info['total_fee'];
		$prepare_data['pmode_id'] = $this->bank2id($this->product_info['pay_bank']);
		$text = $prepare_data['v_amount'].$prepare_data['v_moneytype'].$prepare_data['v_oid'].$prepare_data['v_mid'].$prepare_data['v_url'].$this->config['key'];//md5加密拼凑串,注意顺序不能变
		$prepare_data['v_md5info'] =  strtoupper(md5($text));
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
        $result = array();
		$md5str = strtoupper(md5($params['v_oid'].$params['v_pstatus'].$params['v_amount'].$params['v_moneytype'].$this->config['key']));
        if(($_GET['v_pstatus'] == '20') && $_GET['v_md5str'] == $md5str) {
            $result['result'] = 'success';
            $result['pay_code'] = 'jdpay';
            $result['trade_no'] = $_GET['v_oid'];
            $result['out_trade_no'] = $_GET['v_oid'];
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

	public function bank2id($bank)
	{
		$data = array(
			'ICBCB2C'=>'1025',
			'CCB'=>'1051',
			'BOCBTC'=>'104',
			'ABC'=>'103',
			'COMM'=>'3407',
			'POSTGC'=>'3230',
			'CMB'=>'3080',
			'CITIC'=>'313',
			'SPDB'=>'314',
			'CIB'=>'309',
			'CMBC'=>'305',
			'CEB-DEBIT'=>'312',
			'SPABANK'=>'307',
			'HXB'=>'311',
			'BJBANK'=>'310',
			'GDB'=>'3061',
			'SHBANK'=>'326',
			'BJRCB'=>'335',
			'CQRCB'=>'342',
			'SHRCB'=>'343',
			'NJCB'=>'316',
			'NBCB'=>'302',
			'HZBANK'=>'324',
			'BOCD'=>'336',
			'QDCCB'=>'3341',
			'HFBANK'=>'344',
			'CBHB'=>'317',
			'XMCCB'=>'401',
			'SXNXS'=>'402',
			'CZCB'=>'403',
			'GZNXBANK'=>'404',
			);
		return $data[$bank];
	}
}