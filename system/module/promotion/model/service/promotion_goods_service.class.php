<?php
class promotion_goods_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('promotion/promotion_goods');
		$this->sku_db = $this->load->table('goods/goods_sku');
		$this->index_db = $this->load->table('goods/goods_index');
	}


	public function lists($sqlmap = array(), $limit = 10, $page = 1, $order = "sort ASC, id DESC") {
		$result['count'] = $this->table->where($sqlmap)->count();
		$result['lists'] = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		foreach ($result['lists'] as $key => $value) {
			if($value['start_time'] && $value['start_time'] > TIMESTAMP) {
				$value['status'] = 1;
			} elseif($value['end_time'] && $value['end_time'] < TIMESTAMP) {
				$value['status'] = 2;
			} else {
				$value['status'] = 0;
			}
			$result['lists'][$key] = $value;
		}
		return $result;
	}


	public function get_lists($sqlmap = array(), $limit = 10, $page = 1, $order = "sort ASC, id DESC") {
		$goods = $this->lists($sqlmap,$limit,$page,$order);
		$lists = array();
		foreach ($goods['lists'] AS $value) {
			$start_time = $value['start_time'] ? date('Y-m-d H:i', $value['start_time']) : '无限制';
    		$end_time = $value['end_time'] ? date('Y-m-d H:i', $value['end_time']) : '无限制';
			if($value['status'] == 1){
    			$status = '未开始';
    		}elseif($value['status'] == 2){
    			$status = '已结束';
    		}else{
    			$status = '进行中';
    		}
			$lists[] =array(
				'id'=>$value['id'],
				'name'=>$value['name'],
				'start_time'=>$start_time.'~'.$end_time,
				'status' =>$status,
				);
			
		}
		return array('lists'=>$lists,'count'=>$order['count']);
	} 


	public function fetch_by_id($id, $field = null) {
		$r = $this->table->find($id);
		return (empty($field)) ? $r : $r[$field];
	}

	/**
	 * 更新活动
	 * @param array $params 数组
	 * @return bool
	 */
	public function update($params = array(),$isswitch = false) {
		$params = $this->_format($params,$isswitch);
		if($params === false) {
			return FALSE;
		}
		$after = explode(",", $params['sku_ids']);
		runhook('promotion_goods_update',$params);
		if(isset($params['id']) && $params['id'] > 0 && $isswitch == false) {
			$id = $params['id'];
			$operation = 'edit';
			$this->excute_by_skuid($after, $id, $operation);
			$result = $this->table->update($params);
		} else {
			$result = $this->table->update($params);
			$operation = 'add';
			$this->excute_by_skuid($after, $result, $operation);
		}
		if($result === false) {
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}

	public function delete($id) {
		$ids = (array) $id;
		if(empty($ids)) {
			$this->error = lang('_param_error_');
			return false;
		}
		foreach($ids as $id ) {
			$sku_ids = $this->fetch_by_id($id, 'sku_ids');
			$_map = array();
			$_map['sku_id'] = array("IN", $sku_ids);
			$this->load->table('goods_sku')->where($_map)->save(array("prom_id" => 0, "prom_type" => ''));
			$this->load->table('goods_index')->where($_map)->save(array("prom_id" => 0, "prom_type" => ''));
			$this->table->delete($id);
		}
		return true;
	}


	private function _format($params = array(),$isswitch = false) {
		if(empty($params)) {
			$this->error = lang('_param_error_');
			return false;
		}
		if(empty($params['name'])) {
			$this->error = lang('promotion_name_empty','promotion/language');
			return false;
		}
		if(empty($params['sku_ids'])) {
			$this->error = lang('join_goods_empty','promotion/language');
			return false;
		}
		$params['sku_ids'] = implode(",", $params['sku_ids']);
		if(empty($params['rules'])) {
			$this->error = lang('set_activity_empty','promotion/language');
			return false;
		}
		$params['rules'] = json_encode($params['rules']);
		$params['share_order'] = (int) $params['share_order'];

		if(isset($params['start_time']) && !empty($params['start_time']) && $isswitch == false) {
			$params['start_time'] = strtotime($params['start_time']);
		}
		if(isset($params['end_time']) && !empty($params['end_time']) && $isswitch == false) {
			$params['end_time'] = strtotime($params['end_time']);
		}
		$params['dateline'] = TIMESTAMP;
		$params['sort'] = 100;
		return $params;
	}

	/**
	 * 执行商品ID活动变更
	 * @param $after array 当前操作商品
	 * @param $before array 待操作商品
	 */
	private function excute_by_skuid($after = array(), $id = 0, $operation = 'add') {
		$before = array();
		// 需要计算出本次操作后新增、删除的差集
		$adds = array_diff((array) $after, (array) $before);
		if($adds) {
			$_map = array();
			$_map['sku_id'] = array("IN", $adds);
			$_map['prom_id'] = 0;
			$this->load->table('goods_sku')->where($_map)->save(array("prom_id" => $id, "prom_type" => 'goods'));
			$this->load->table('goods_index')->where($_map)->save(array("prom_id" => $id, "prom_type" => 'goods'));
		}
		if($operation == 'edit') {
			$before = $this->fetch_by_id($id, 'sku_ids');
			$dels = array_diff((array) $before, (array) $after);
			if($dels) {
				$_map = array();
				$_map['sku_id'] = array("IN", $dels);
				$this->load->table('goods_sku')->where($_map)->save(array("prom_id" => 0, "prom_type" => ''));
				$this->load->table('goods_index')->where($_map)->save(array("prom_id" => 0, "prom_type" => ''));
			}
		}
		return true;
	}

	public function switch_goods_prom($params,$isswitch = false){
		if($params['rules_type'] == 2){
			$params['rules_type'] = 'amount_discount';
		}else{
			$params['rules_type'] = '';
		}
		$params['rules'][0]['type'] = $params['rules_type'];
		$params['rules'][0]['condition'] = $params['rules_condition'];
		$params['rules'][0]['discount'] = $params['rules_discount'];
		unset($params['rules_type'],$params['rules_condition'],$params['rules_discount']);
		return $this->update($params,$isswitch);
	}


	public function fetch_skuid_by_goods() {
		$sqlmap = $_map = array();
		$_map[] = array('GT',0);
		$_map[] = array('LT',time());
		$sqlmap['end_time'] = $_map;
		$prom_end_ids = $this->table->where($sqlmap)->getfield('id',TRUE);
		foreach ($prom_end_ids AS $prom_end_id) {
			$result = $this->sku_db->where(array('prom_id'=>$prom_end_id,'prom_type' => 'goods'))->save(array('prom_id' => 0,'prom_type' => ''));
			$this->index_db->where(array('prom_id'=>$prom_end_id,'prom_type' => 'goods'))->save(array('prom_id' => 0,'prom_type' => ''));
		}
		return TRUE;
	}	
	/*修改*/
	public function setField($data, $sqlmap = array()){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table->where($sqlmap)->save($data);
		if($result === false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	读取的字段
	 * @return [type]
	 */
	public function find($sqlmap = array(), $field = "") {
		$result = $this->table->where($sqlmap)->field($field)->find();
		if($result===false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
}