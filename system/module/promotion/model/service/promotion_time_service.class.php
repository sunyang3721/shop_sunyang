<?php
/**
 *      限时营销服务层
 */

class promotion_time_service extends service {
	public function _initialize() {
		$this->table = $this->load->table('promotion_time');
		$this->sku_db = $this->load->table('goods/goods_sku');
		$this->index_db = $this->load->table('goods/goods_index');
	}
	public function get_lists($params){
		$result = $this->table->page($params['page'])->limit($params['limit'])->select();
		foreach ($result as $k => $prom) {
			if(time() < $prom['start_time']){
				$result[$k]['status'] = 1;
			}elseif(time() > $prom['end_time']){
				$result[$k]['status'] = 2;
			}else{
				$result[$k]['status'] = 0;
			}
		}
		if(!$result){
			$this->error = $this->table->getError();
		}
		return $result;
	}

	public function lists($sqlmap = array()){
		$time = $this->get_lists($sqlmap);
			$lists = array();
			foreach ($time AS $value) {
				$start_time = date('Y-m-d H:i', $value['start_time']);
    			$end_time =  date('Y-m-d H:i', $value['end_time']);
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
					'time' => $start_time.'~'.$end_time,
					'status' =>$status,
					);
				
			}
			return array('lists'=>$lists);

	}


	/**
	 * [fetch_by_id 查询单条数据]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function fetch_by_id($id) {
		$result = $this->table->getById($id);
		$result['start_time'] = date('Y-m-d H:i:s',$result['start_time']);
		$result['end_time'] = date('Y-m-d H:i:s',$result['end_time']);
		return $result;
	}
	/**
	 * [updata 更新数据]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function update($params,$isswitch = false){
		if(empty($params)) {
			$this->error = lang('_param_error_');
			return false;
		}
		if(empty($params['name'])) {
			$this->error = lang('promotion_name_empty','promotion/language');
			return false;
		}
		if(isset($params['start_time']) && !empty($params['start_time']) && $isswitch == false) {
			$params['start_time'] = strtotime($params['start_time']);
		}
		if(isset($params['end_time']) && !empty($params['end_time']) && $isswitch == false) {
			$params['end_time'] = strtotime($params['end_time']);
		}
		$params['sku_info'] = $params['prom_price'] ? json_encode($params['prom_price']) : '';
		
		runhook('promotion_time_update',$params);
		$result = $this->table->update($params);
		
		if($result === false) {
			$this->error = $this->table->getError();
			return false;
		}
		$ids = array_keys($params['prom_price']);
		$data = array();
		$data['prom_id'] = $params['id'] ? $params['id'] : $result;
		$data['prom_type'] = 'time';
		$this->sku_db->where(array('prom_id' => $data['prom_id']))->save(array('prom_id' => 0,'prom_type' => ''));
		$this->index_db->where(array('prom_id' => $data['prom_id']))->save(array('prom_id' => 0,'prom_type' => ''));
		$this->sku_db->where(array('sku_id'=>array('IN',$ids)))->save($data);
		$this->index_db->where(array('sku_id'=>array('IN',$ids)))->save($data);
		return $result;
	}
	/**
	 * [delete 删除数据]
	 * @param  [type] $ids [description]
	 * @return [type]      [description]
	 */
	public function delete($ids) {
		if(empty($ids)) {
			$this->error = lang('_param_error_');
			return false;
		}
		$_map = array();
		if(is_array($ids)) {
			$_map['id'] = array("IN", $ids);
		} else {
			$_map['id'] = $ids;
		}
		$result = $this->table->where($_map)->delete();
		if(is_array($ids)){
			$this->sku_db->where(array('id' => array('IN',$ids)))->save(array('prom_id' => 0,'prom_type' => ''));
			$this->index_db->where(array('id' => array('IN',$ids)))->save(array('prom_id' => 0,'prom_type' => ''));
		}else{
			$this->sku_db->where(array('id' => $ids))->save(array('prom_id' => 0,'prom_type' => ''));
			$this->index_db->where(array('id' => $ids))->save(array('prom_id' => 0,'prom_type' => ''));
		}
		if($result === false) {
			$this->error = $this->table->getError();
			return false;
		}
		return true;
	}
	/**
	 * [change_sort 改变名称]
	 * @param  [array] $params [品牌id和name]
	 * @return [boolean]     [返回更改结果]
	 */
	public function change_name($params){
		if((int)$params['id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['name'] = $params['name'];
		$result = $this->table->where(array('id'=>array('eq',$params['id'])))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}

	/* 返回所有 */
	public function fetch_skuid_by_timeed() {
		$sqlmap = $_map = array();
		$_map[] = array('GT',0);
		$_map[] = array('LT',time());
		$sqlmap['end_time'] = $_map;
		$prom_end_ids = $this->table->where($sqlmap)->getfield('id',TRUE);
		foreach ($prom_end_ids AS $prom_end_id) {
			$result = $this->sku_db->where(array('prom_id'=>$prom_end_id,'prom_type' => 'time'))->save(array('prom_id' => 0,'prom_type' => ''));
			$this->index_db->where(array('prom_id'=>$prom_end_id,'prom_type' => 'time'))->save(array('prom_id' => 0,'prom_type' => ''));
		}
		return TRUE;
	}
	/**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap = array()){
        $result = $this->table->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->table->getError();
            return false;
        }
        return $result;
    }
}
