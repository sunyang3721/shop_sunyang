<?php
abstract class pay_abstract
{
	protected $config = array();
	protected $product_info = array();
	protected $gateway_type = 'redirect';

	public function set_config($config) {
		foreach ($config as $key => $value) $this->config[$key] = $value;
		return $this;
	}

	public function set_productinfo($product_info) {
		$this->product_info = $product_info;
		return $this;
	}

	/* 创建支付请求 */
	public function gateway() {
		$result = $this->gateway_url();
		if($result !== false) {
			return array(
				'gateway_type' => $this->gateway_type,
				'gateway_url' => $result,
			);
		}
		return false;
	}
	    
    /* 订单发货接口 */
    abstract public function _delivery();
    /* 同步通知接口 */
    abstract public function _return();
    /* 异步通知接口 */
    abstract public function _notify();
    /* 服务应答接口 */
	abstract public function response($result);
	/* 获取支付地址 */
	abstract public function gateway_url();
	//abstract public function getPrepareData();
}