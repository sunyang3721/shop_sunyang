<?php
/**
 *      支付模块调用工厂
 */
class pay_factory
{
	public function __construct($adapter_name = '', $adapter_config = array()) {
		$this->set_adapter($adapter_name, $adapter_config);
	}
	/**
	 * 构造适配器
	 * @param  $adapter_name 支付模块code
	 * @param  $adapter_config 支付模块配置
	 */
	public function set_adapter($adapter_name, $adapter_config = array()) {
		if (!is_string($adapter_name)) return false;
		else {
			if (empty($adapter_config)) {
				$payment = model('pay/payment','service')->get();
				if (empty($payment) || empty($payment[$adapter_name]) || empty($payment[$adapter_name]['config'])) {
					die('支付方式未安装或未开启');
				}
				$adapter_config = unserialize($payment[$adapter_name]['config']);
			}
			$class_name = $adapter_name;
			$class_file = APP_PATH.'module/pay/library/driver/pay/'.$class_name.'/'.$class_name.'.class.php';
			if (!file_exists($class_file)) {
				die('支付接口不存在');
			}
			if (!function_exists('require_cache')) {
				echo "存在函数imag_openn";
			}
			require_cache($class_file);
			$this->adapter_instance = new $class_name($adapter_config);
		}
		return $this->adapter_instance;
	}

	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof pay_abstract)
			&& method_exists($this->adapter_instance, $method_name)
		)
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}
}
?>