<?php
/**
 *      支付设置服务层
 */

class payment_service extends service {

	public function _initialize() {
		$this->model = model('payment');
	}

	public function fetch_all() {
		$entrydir = 'system/module/pay/library/driver/pay/';
		$folders = glob($entrydir.'*');
        foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $payments[$xmldata['pay_code']] = $xmldata;
                $payments[$xmldata['pay_code']]['pay_install'] = 0;
            }
        }
        $payments = array_merge_multi($payments, $this->get_fetch_all());
		return multi_array_sort($payments,'pay_name');
	}

	public function get_fetch_all() {
		$result = array();
		$result = $this->model->getField('pay_code,pay_name,pay_fee,pay_desc,enabled,config,dateline,sort,isonline,applie', TRUE);
		foreach ($result as $key => $value) {
			$result[$key]['config'] = unserialize($value['config']);
			$result[$key]['pay_install'] = 1;
		}
		return $result;
	}
	/**
	 * [支付方式列表]
	 * @return boolean
	 */

	public function get($key = NULl) {
		$result_enable = $this->model->where(array('enabled' => 1))->cache('payment_enable',3600)->getField('pay_code,pay_name,pay_fee,pay_desc,enabled,config,dateline,sort,isonline,applie',TRUE);
		return is_string($key) ? $result_enable[$key] : $result_enable;
	}

	/**
	 * [启用禁用支付方式]
	 * @param string $pay_code 支付方式标识
	 * @return TRUE OR ERROR
	 */
	public function change_enabled($pay_code) {
		$result = $this->model->where(array('pay_code' => $pay_code))->save(array('enabled' => array('exp', '1-enabled'), 'dateline' => time()));
		if ($result == 1) {
			$result = TRUE;
			cache('payment_enable',NULL);
		} else {
			$result = $this->model -> getError();
		}
		return $result;
	}

	/**
	 * [修改支付方式]
	 * @param array $data 数据
	 * @param bool $valid 是否M验证
	 * @return TRUE OR ERROR
	 */
	public function save($data, $valid = TRUE) {
		if($data['pay_install'] == 0){
			$result = $this->model->add($data);
		}else{
			$result = $this->model->update($data, $valid);
		}
		if ($result) {
			$result = TRUE;
			cache('payment_enable',NULL);
		} else {
			$result = $this->model->getError();
		}
		return $result;
	}

	/**
	 * 获取已开启支付方式
	 * @return array 已开启的支付方式
	 */
	public function getpayments($applie = 'pc', $pays = array()){
		$payments = $this->get();
		foreach ($payments as $key => $pay) {
			if($applie && $pay['applie'] != $applie) unset($payments[$key]);
		}
		$payments = array_intersect_key($payments,array_flip($pays));
		$result = array();
		foreach ($payments as $k => $pay) {
			$pay['pay_ico'] = $pay['pay_code'];
			if($k == 'bank') {
				$config = unserialize($pay['config']);
				$banks = explode(',', $config['banks']);
				foreach ($banks as $bank) {
					$pay['pay_ico'] = $bank;
					$pay['pay_code'] = 'bank';
					$pay['pay_bank'] = $bank;
					$result[] = $pay;
				}
			} elseif ($k == 'jdpay'){
				$config = unserialize($pay['config']);
				if($pay['applie'] == $applie){
					$banks = explode(',', $config['banks']);
					$jd_applies = explode(',',$config['applie']);
					foreach ($banks as $bank) {
						$pay['pay_ico'] = $bank;
						$pay['pay_code'] = 'jdpay';
						$pay['pay_bank'] = $bank;
						$pay['applie'] = $config['applie'];
						$result[] = $pay;
					}
				}
			} else {
				$result[] = $pay;
			}
		}
		return $result;
	}

	/* 创建支付请求 */
	public function gateway($pay_code, $pay_info, $pay_config = null){
		$classfile = APP_PATH.'module/pay/library/pay_factory.class.php';
		require_cache($classfile);
		$pay_factory = new pay_factory($pay_code,$pay_config);
		return $pay_factory->set_productinfo($pay_info)->gateway();
	}

	/* 同步回调 */
	public function _return($driver){
		$classfile = APP_PATH.'module/pay/library/pay_factory.class.php';
		require_cache($classfile);
		$pay_factory = new pay_factory($driver);
		return $pay_factory->_return();

	}

	/* 异步通知 */
	public function _notify($driver){
		$classfile = APP_PATH.'module/pay/library/pay_factory.class.php';
		require_cache($classfile);
		$pay_factory = new pay_factory($driver);
		return $pay_factory->_notify();
	}

	/**
	* [删除]
	* @param array $ids 主键id
	*/
	public function delete($ids) {
		if(empty($ids)) {
			$this->error = lang('_param_error_');
			return false;
		}
		$_map = array();
		if(is_array($ids)) {
			$_map['pay_code'] = array("IN", $ids);
		} else {
			$_map['pay_code'] = $ids;
		}
		$result = $this->model->where($_map)->delete();
		if($result === false) {
			$this->error = $this->model->getError();
			return false;
		}
		return true;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function lists($sqlmap = array(), $limit = 20, $page = 1, $order = "") {
		$count = $this->model->where($sqlmap)->count();
		$lists = $this->model->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		if($count===false || $lists===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return array('lists'=>$lists,'count'=>$count);
	}
}
