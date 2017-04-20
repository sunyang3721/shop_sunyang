<?php
/**
 *		商品类型服务层
 */

class type_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('goods/type');
		$this->attr_db = $this->load->table('goods/attribute');
		$this->cate_db = $this->load->table('goods/goods_category');
		$this->spec_db = $this->load->table('goods/spec');
	}
	/**
	 * [get_lists 获取商品类型列表]
	 * @return [type] [description]
	 */
	public function get_lists($page,$limit){
		$result = $this->db->page($page)->limit($limit)->order('sort asc')->getfield('id,name,content,sort,status',TRUE);
		$attrs = array();
		foreach ($result as $k => $v) {
			$content = '';
			$attrs[$k] = $this->get_pop_by_id($v['id']);
			foreach ($attrs[$k] as $attr) {
				$content .= $attr['name'].'&nbsp;&nbsp;&nbsp;';
			}
			$result[$k]['content'] = $content;
		}

		if(!$result){
			$this->error = $this->logic->error;
		}
		return $result;
	}
	/**
	 * [add_model 添加、编辑类型]
	 * @param [type] $params [description]
	 */
	public function add_type($params,$flag = false){
		if($flag == true){
			$params['spec_id'] = $params['spec_id'][0] ? explode(',',$params['spec_id'][0]) : '';
		}
		$attrinfo = $params;
		if($params['delId'] && count($params['delId']) > 0){
			$delIds = implode(',',$params['delId']);
			$attrDel = $this->attr_db->where(array('id'=>array('IN',$delIds)))->delete();
		}
		foreach ($attrinfo['attr_name'] as $key => $value) {
			$info = array(
				'id' => $attrinfo['attr_ids'][$key],
				'name' => $value,
				'value' => $attrinfo['attr_val'][$key],
				'type' => $attrinfo['type'][$key],
				'sort' => $attrinfo['attr_sort'][$key],
				'search' => $attrinfo['search'][$key],
			);
			if(empty($info['id']) || $flag == true){
				if($flag == false){
					unset($info['id']);
				}else{
					unset($attrinfo['attr_ids'][$key]);
					if(!$attrinfo['attr_val'][$key]) continue;
				}
				$result = $this->attr_db->add($info);
				$attrinfo['attr_ids']['attr'][$key] = 'att_'.$result;
				if($result === FALSE){
					$this->error = $this->attr_db->getError();
					return FALSE;
				}
			}else{
				$result = $this->attr_db->update($info);
				$attrinfo['attr_ids']['attr'][$key] = 'att_'.$attrinfo['attr_ids'][$key];
				unset($attrinfo['attr_ids'][$key]);
				if($result === FALSE){
					$this->error = $this->attr_db->getError();
					return FALSE;
				}
			}
		}
		foreach ($attrinfo['spec_id'] AS $spec_id) {
			$attrinfo['spec_ids']['spec'][] = $spec_id;
		}
		if(empty($attrinfo['spec_ids'])){
			$attrinfo['spec_ids'] = array();
		}
		$attrinfo['content'] = array_merge($attrinfo['attr_ids'],$attrinfo['spec_ids']);
		$params['content'] = json_encode($attrinfo['content']);
		if(isset($params['id']) && $params['id'] > 0 && $flag == false) {
			$result = $this->db->update($params);
		} else {
		    $result = $this->db->add($params);
		}
		if($result === FALSE){
			$this->error = $this->db->getError();
			return FALSE;
		}else{
			return TRUE;
		}
	}
	/**
	 * [delete 删除规格，可批量删除]
	 * @param  [fixed] $params [类型id]
	 * @return [boolean]         [返回删除结果]
	 */
	public function delete($params){
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
		$result = $this->db->where(array('id' => $id))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}
	/**
	 * [change_info 更改分类信息]
	 * @param  [array] $params [规格id和排序数组]
	 * @return [boolean]     [返回更改结果]
	 */
	public function change_info($params){
		if((int)$params['id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->db->where(array('id'=>$params['id']))->save($params);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}
	/**
	 * [get_attrs 根据分类id获取商品属性，提供给商品添加页面选择属性]
	 * @param  [type] $catid [分类id]
	 * @return [type]        [array]
	 */
	public function get_attrs($catid){
		if((int)$catid < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$type_id = $this->load->service('goods/goods_category')->detail($catid,'type_id');
		$type = $this->fetch_by_id($type_id);
		$attrs = json_decode($type['content'],TRUE);
		$goods_attrs = array();
		foreach ($attrs['attr'] as $key => $value) {
			$goods_attrs[] = substr($value,strpos($value,'_')+1);
		}
		return $goods_attrs;
	}
	/**
	 * [get_attrs 根据id获取商品类型信息]
	 * @param  [type] $catid [分类id]
	 * @return [type]        [array]
	 */
	public function fetch_by_id($id,$field = TRUE){
		$exist = strpos($field, ',');
		if($exist === false && is_string($field)){
			$result = $this->db->where(array('id'=>$id))->getfield($field);
		}else{
			$result = $this->db->where(array('id'=>$id))->field($field)->find();
		}
		if($result === false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
	}
	/**
	 * [is_search 判断属性是否参与筛选]
	 * @param  [type]  $id [description]
	 * @return boolean     [description]
	 */
	private function is_search($attr_id){
		if((int)$attr_id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->attr_db->where(array('id'=>$attr_id))->getfield('search');
		if($result == 1){
			return $id;
		}
		return FALSE;
	}
	/**
	 * [get_pop_by_id 根据id获取商品属性信息]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_pop_by_id($id,$flag = FALSE){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$content = $this->db->where(array('id'=>$id))->getfield('content');
		$_attrs_info = json_decode($content,TRUE);
		$_attrs = $_attrs_info['attr'];
		$attrs = array();
		foreach ($_attrs as $key => $value) {
			$attrs[] = substr($value,strpos($value,'_')+1);
		}
		$sqlmap = array();
		$sqlmap['id'] = array('IN',$attrs);
		if($flag == TRUE){
			$sqlmap['type'] = array('NEQ','input');
		}
		$result = $this->attr_db->where($sqlmap)->order('sort asc')->getfield('id,name,value,sort,search,type');
		if(!$result){
			$this->error = $this->logic->error;
		}
		return $result;
	}
	/**
	 * [get_spec_by_id 根据id获取类型关联规格信息]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_spec_by_id($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$content = $this->db->where(array('id'=>$id))->getfield('content');
		$_attrs_info = json_decode($content,TRUE);
		if(!empty($_attrs_info['spec'])){
			foreach ($_attrs_info['spec'] AS $v) {
				$attr[$v] = $v;
			}
		}
		return $attr;
	}

	/**
	 * [get_attr_by_id 根据id获取商品属性信息]
	 * @param  [type] $catid [分类id]
	 * @return [type]        [array]
	 */
	public function get_attr_by_id($attr_id){
		if((int) $attr_id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->attr_db->find($attr_id);
		if(!$result){
			$this->error = $this->logic->error;
			return FALSE;
		}
		if($result['value']) $result['value'] = explode(',',$result['value']);
		return $result;
	}
	/**
	 * [get_all_type 获取类型id和名称]
	 * @return [type] [description]
	 */
	public function get_all_type(){
		$result = $this->db->getField('id,name');
		if(!$result){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return $result;
	}


	/**
	 * [get_type_info 获取商品类型和属性]
	 * @return [type] [description]
	 */
	public function get_type_by_catid($catid){
		$cat_type = array();
		$cat_ids = $this->load->service('goods/goods_category')->get_parent($catid);
		array_push($cat_ids, $catid);
		foreach ($cat_ids as $key => $value) {
			$k = $this->cate_db->where(array('id'=>$value))->getfield('type_id');
			$cat_type[$value] = $k;
		}
		$cat_type = array_unique($cat_type);
		$cat_ids = array_keys($cat_type);
		foreach ($cat_ids as $key => $catid) {
			if($catid != 0){
				$type_id = $this->load->service('goods/goods_category')->detail($catid,'type_id');
				$type_name = $this->db->where(array('id' => $type_id))->getfield('name');
				if($type_name) $result['types'][$type_id] = $type_name;
				$attrs = $this->get_attrs($catid);
				foreach ($attrs AS $attr) {
					$result['attr'][$type_id][] = $this->get_attr_by_id($attr);
				}
			}
		}
		ksort($result['types']);
		return $result;
	}

	/**
	 * [get_type_by_goods_id 根据商品id获取类型数据]
	 * @param  [type] $goods_id [description]
	 * @return [type]           [description]
	 */
	public function get_type_by_goods_id($spu_id){
		$result = array();
		if((int) $spu_id > 0) {
			$sku_id = $this->load->table('goods/goods_sku')->where(array('spu_id' => $spu_id))->getField('sku_id');
			$attrs =  $this->load->table('goods/goods_attribute')->where(array('sku_id' => (int) $sku_id, 'type' => 1))->field('attribute_id,attribute_value')->select();
			foreach ($attrs AS $attr) {
				if(in_array($attr['attribute_value'], $result[$attr['attribute_id']])) continue;
				$result[$attr['attribute_id']][] = $attr['attribute_value'];
			}
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
    /**
     * 商品属性列表
     */
	public function lists($sqlmap = array(), $options = array()) {
        $type_id = $this->cate_db->where(array('id'=>$sqlmap['catid']))->getfield('type_id');
        $content = $this->db ->where(array('id'=>$type_id,'status'=>1))->getfield('content');
        $_attrs_info = json_decode($content,TRUE);
        $_attrs = $_attrs_info['attr'];
        $attrs = array();
        $map = array();
        if(isset($sqlmap['order'])){
            $order = $sqlmap['order'];
        }
        if(isset($options['limit'])){
            $limit =$options['limit'];
        }
        $map['type'] = array('neq','input');
        foreach ($_attrs as $key => $value) {
            $attrs[$key] = substr($value,strpos($value,'_')+1);
            $map['id'] = $attrs[$key];
            $result[$value] = $this->attr_db->where($map)->field('id,name,value,type,search')->find();
            if(!empty($result[$value]['value'])){
                $result[$value]['value'] = explode(',', $result[$value]['value']);
            }else{
                unset($result[$value]);
            }
        }
        return $result;
    }

    public function specs($sqlmap = array(), $options = array()) {
        $type_id = $this->cate_db ->where(array('id'=>$sqlmap['catid']))->getfield('type_id');
        $content = $this->db ->where(array('id'=>$type_id))->getfield('content');
        $_attrs_info = json_decode($content,TRUE);
        $_specs = $_attrs_info['spec'];
        foreach ($_specs as $k =>$_spec) {
             $result['spec_'.$_spec] = $this->spec_db->where(array('id'=>$_spec))->field('id,name,value')->find();
             $result['spec_'.$_spec]['value'] = explode(',',$result['spec_'.$_spec]['value']);
        }
        return $result;
    }
}
