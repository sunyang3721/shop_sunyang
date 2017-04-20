<?php
/**
 * 		发货单日志服务层
 */
class order_parcel_log_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('order/order_parcel_log');
	}

	public function add($params){
		return $this->table->update($params);
	}
}