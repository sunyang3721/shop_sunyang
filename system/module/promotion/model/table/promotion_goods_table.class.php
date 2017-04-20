<?php
class promotion_goods_table extends table {

	public function _after_find($result, $options = array()) {
		$this->data = $this->_format($result);
		return $result;
	}

	public function _after_select($result, $options = array()) {
		foreach ($result as $key => $value) {
			$result[$key] = $this->_format($value);
		}
		return $result;
	}

	private function _format($result) {
		$result['sku_ids'] = ($result['sku_ids']) ? explode(",", $result['sku_ids']) : array();
		$rules = ($result['rules']) ? json_decode($result['rules'], TRUE) : array();
		foreach ($rules as $k => $v) {
			switch ($v['type']) {
				case 'amount_discount':
				case 'number_discount':
					$v['title'] = '满'.$v['condition'].($v['type'] == 'amount_discount' ? '元' : '件').'立减'.$v['discount'].'元';
					break;
				case 'amount_give':
				case 'number_give':
					$sku_info = $this->load->table('goods/goods_sku')->find($v['discount']);
					$v['sku_info'] = $sku_info;
					$v['title'] = '满'.$v['condition'].($v['type'] == 'amount_give' ? '元' : '件').'送赠品：'.$sku_info['sku_name'];
					break;
				default:
					# code...
					break;
			}
			$rules[$k] = $v;
		}
		$result['rules'] = $rules;
		return $result;
	}
}