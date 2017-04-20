<?php
/**
 *		规格模型数据层
 */

class spec_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('goods/spec');
	}
	/**
	 * [spec_list 规格列表]
	 * @return [array] [保存规格信息的数组]
	 */
	public function spec_list($page,$limit){
		$result = $this->db->page($page)->limit($limit)->order('sort asc')->getField('id,name,value,sort,status');
		if(!$result){
    		$this->error = $this->db->getError();
    	}
    	return $result;
    }
	/**
	 * [add_spec 增加规格]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function add_spec($params){
		if(empty($params['value'])){
			$this->error = lang('goods_spec_value_empty','goods/language');
			return FALSE;
		}
		$params['value'] = array_unique($params['value']);
		foreach ($params['value'] as $k => $value) {
			if($value == ''){
				unset($params['value'][$k]);
			}
		}
		$params['value'] = implode(',',$params['value']);
		runhook('before_add_spec',$params);
		$result = $this->db->update($params);
    	if($result === FALSE){
    		$this->error = $this->db->getError();
    	}
    	return $result;
	}
	/**
	 * [edit_spec 编辑规格]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function edit_spec($params){
		if((int)$params['id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if(empty($params['value'])){
			$this->error = lang('goods_spec_value_empty','goods/language');
			return FALSE;
		}
		$params['value'] = array_unique($params['value']);
		foreach ($params['value'] as $k => $value) {
			if($value == ''){
				unset($params['value'][$k]);
			}
		}
		$params['value'] = implode(',',$params['value']);
		runhook('before_edit_spec',$params);
		$result = $this->db->update($params);
    	if($result === FALSE){
    		$this->error = $this->db->getError();
    	}
    	return $result;
	}
	/**
	 * [change_status 改变状态]
	 * @param  [int] $id [规格id]
	 * @return [boolean]     [返回更改结果]
	 */
	public function change_status($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['status']=array('exp',' 1-status ');
		$result = $this->db->where(array('id'=>array('eq',$id)))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}
	/**
	 * [delete_spec 删除规格，可批量删除]
	 * @param  [int||array] $params [规格id或规格id数组]
	 * @return [boolean]         [返回删除结果]
	 */
	public function delete_spec($params){
		$params = (array) $params;
		if(empty($params)){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$sqlmap = array();
		$sqlmap['id'] = array('IN',$params);
		$result = $this->db->where($sqlmap)->delete();
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}
	/**
	 * [del_spec_val 删除规格值]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function del_spec_val($params){
		if(empty($params['id']) || empty($params['value'])){
			$this->error = lang('_PARAM_ERROR_');
			return FALSE;
		}
		$value = $this->db->where(array('id' => $params['id']))->getfield('value');
		$value = explode(',', $value);
		if(!in_array($params['value'],$value)){
			$this->error = lang('_PARAM_ERROR_');
			return FALSE;
		}else{
			$key = array_search($params['value'], $value);
			unset($value[$key]);
			$result = $this->db->where(array('id' => $params['id']))->save(array('value' => implode(',', $value)));
			if($result === FALSE){
				$this->error = '保存失败';
			}
			return $result;
		}
	}
	/**
	 * [change_sort 改变排序]
	 * @param  [array] $params [规格id和排序数组]
	 * @return [boolean]     [返回更改结果]
	 */
	public function change_sort($params){
		if((int)$params['id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['sort'] = $params['sort'];
		$result = $this->db->where(array('id'=>array('eq',$params['id'])))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
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
		$result = $this->db->where(array('id'=>array('eq',$params['id'])))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}
	/**
	 * [get_type_by_id 根据id获取商品类型信息]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_spec_by_id($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->db->find($id);
		$result['spec_array'] = explode(',',$result['value']);
		if(!$result){
			$this->error = $this->logic->error;
		}
		return $result;
	}
	/**
	 * [ajax_add_spec ajax增加规格]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function ajax_add_spec($name){
		if (empty($name)) {
			$this->error = lang('goods_spec_name_require','goods/language');
			return FALSE;
		}
		$result = $this->db->add(array('name'=>$name));
		if (!$result) {
			$this->error = lang('goods_spec_add_fail','goods/language');
		}else{
			$result = array('id' => $result,'name' => $name);
		}
		return $result;
	}
	/**
	 * [ajax_add_pop ajax增加规格属性]
	 * @param  [type] $params [description]
	 * @return [type]       [description]
	 */
	public function ajax_add_pop($params){
		$data       = array();
		$data['id'] = (int)$params['spec_id'];
		$new_value  = trim($params['new_value']);
		if (empty($new_value)){
			$this->error = lang('goods_spec_value_empty','goods/language');
			return FALSE;
		}
		if ($data['id'] < 1){
			$this->error = lang('goods_spec_id_error','goods/language');
		}
		$old_info = $this->db->find($data['id']);
		if (empty($old_info['value'])) {
			$data['value'] = $new_value;
		} else {
			$old_spec = explode(',', $old_info['value']);
			foreach ($old_spec AS $spec_value) {
				if($new_value == $spec_value){
					$this->error = lang('spec_exist','goods/language');
					return FALSE;
				}
			}
			$old_spec[] = $new_value;
			$data['value'] = implode(',', $old_spec);
		}
		$result = $this->db->save($data);
		if ($result === FALSE){
			$this->error = lang('goods_pop_add_fail','goods/language');
		}else{
			$result = array('id'=>$data['id'],'name'=>$old_info['name'],'value'=>$new_value);
		}
		return $result;
	}
	/**
	 * [get_spec_name 获取规格名称]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function get_spec_name(){
		$result = $this->db->order('sort ASC')->getField('id,name,value');
		foreach ($result as $key => $value) {
			if($value['value'] == ''){
				unset($value['value']);
			}else{
				$result[$key]['value'] = explode(',',$value['value']);
			}
		}
		if(!$result){
			$this->error = lang('_operation_fail_');
		}
		return $result;
	}
	/**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap = array()){
        $result = $this->db->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->db->getError();
            return false;
        }
        return $result;
    }
}