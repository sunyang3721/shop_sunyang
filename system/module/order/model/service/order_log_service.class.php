<?php
/**
 * 		订单日志服务层
 */
class order_log_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('order/order_log');
	}

	/**
	 * 写入订单日志
	 * @param $params 日志相关参数
	 * @return [boolean]
	 */
	public function add($params = array(),$extra = FALSE) {
		$params = array_filter($params);
		if (empty($params)) {
			$this->error = lang('order_log_empty','order/language');
			return FALSE;
		}
		runhook('order_log_add',$params);
		$result = $this->table->update($params);
		if (!$result) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		return $result;
	}

	/**
	 * 根据子订单号获取日志
	 * @param $sub_sn : 子订单号(默认空)
	 * @param $order  : 排序(默认主键升序)
	 * @return [result]
	 */
	public function get_by_order_sn($sub_sn = '' , $order = 'id ASC') {
		$sub_sn = (string) remove_xss($sub_sn);
		if (!$sub_sn) {
			$this->error = lang('order_sn_not_null','order/language');
			return FALSE;
		}
		$order = (string) remove_xss($order);
		$sqlmap = array();
		$sqlmap['sub_sn'] = $sub_sn;
		return $this->table->where($sqlmap)->order($order)->select();
	}
}