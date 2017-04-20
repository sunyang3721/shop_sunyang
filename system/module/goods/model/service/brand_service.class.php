<?php
/**
 *		商品品牌数据层
 */

class brand_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('goods/brand');
		$this->goods_spu_model = $this->load->table('goods/goods_spu');
		$this->cate_service = $this->load->service('goods/goods_category');
	}
	/**
	 * [get_lists 获取品牌列表]
	 * @return [type] [description]
	 */
	public function get_lists($page = 1,$limit = 20){
		$result = $this->db->page($page)->limit($limit)->order('sort asc')->getField('id,name,descript,sort,logo,isrecommend',TRUE);
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [add_spec 增加品牌]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function add_brand($params = array()){
		runhook('before_add_brand', $params);
		$result = $this->db->update($params);
    	if($result === FALSE){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}
    	return $result;
	}
	/**
	 * [edit_spec 编辑品牌]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function edit_brand($params){
		runhook('before_edit_brand', $params);
		if((int)$params['id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->db->update($params);
    	if($result === FALSE){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}
    	return $result;
	}
	/**
	 * [change_recommend 改变状态]
	 * @param  [int] $id [规格id]
	 * @return [boolean]     [返回更改结果]
	 */
	public function change_recommend($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['isrecommend']=array('exp',' 1-isrecommend ');
		$result = $this->db->where(array('id'=>array('eq',$id)))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
    	return $result;
    }
	/**
	 * [delete_spec 删除规格，可批量删除]
	 * @param  [int||array] $params [规格id或规格id数组]
	 * @return [boolean]         [返回删除结果]
	 */
	public function delete_brand($params){
		if(empty($params)){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		runhook('before_delete_brand', $params);
		$sqlmap = array();
		$sqlmap['id'] = array('IN',$params);
		$infos = $this->db->where($data)->getField('logo',true);
		foreach ($infos AS $info) {
			$this->load->service('attachment/attachment')->attachment('', $info,false);
		}
		$result = $this->db->where($sqlmap)->delete();
		if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
		runhook('after_delete_brand', $params);
    	return $result;
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
    		return FALSE;
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
		$result = $this->db->where(array('id'=>$params['id']))->save($data);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
    	return $result;
    }
	/**
	 * [get_brand_name 获取品牌名称]
	 * @param  [type] $id [品牌id]
	 * @return [type]     [description]
	 */
	public function get_brand_name($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->db->where(array('id'=>array('EQ',$id)))->getField('name');
		if(!$result){
			$this->error = lang('_operation_fail_');
    	}
		return $result;
	}
	/**
	 * [search_brand 关键字查找品牌]
	 * @param  [type] $keyword [description]
	 * @return [type]          [description]
	 */
	public function ajax_brand($keyword){
		$sqlmap = array();
		if($keyword){
			$sqlmap = array('name'=>array('LIKE','%'.$keyword.'%'));
		}
		$result = $this->db->where($sqlmap)->getField('id,name',TRUE);
		if(!$result){
			$this->error = lang('_operation_fail_');
    	}
		return $result;
	}
	/**
	 * [get_brand_by_id 根据id获取品牌信息]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_brand_by_id($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->db->find($id);
		if(!$result){
			$this->error = lang('goods/no_found_brand');
		}
		return $result;
	}

	public function detail($id, $field=''){
    	return $this->db->detail($id, $field)->output();
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


	public function lists($sqlmap = array(), $options = array()) {
        if(isset($sqlmap['catid']) && is_numeric($sqlmap['catid']) && $sqlmap['catid'] > 0) {
            $catids = $this->cate_service->get_child($sqlmap['catid']);
            if(empty($catids)) return FALSE;
            foreach ($catids as $catid) {
                $join[] = "catid =".$catid;
            }
            $brand_map = array();
            $brand_map['status'] = 1;
            $brand_map['brand_id'] = array("GT", 0);
            $brand_map['_string'] = implode(' OR ', $join);
            $brand_ids = $this->goods_spu_model->where($brand_map)->group('brand_id')->getField('brand_id', TRUE);
            if(!$brand_ids) return FALSE;
        }
        $map = array();
        $map['isrecommend']=1;
        if($brand_ids) {
            $map['id'] = array("IN", $brand_ids);

        }
        if(!isset($sqlmap['order'])){
           $sqlmap['order'] = 'sort asc,id desc';
        }
        $this->db->order($sqlmap['order']);
        if(isset($options['limit'])){
            $this->db->limit($options['limit']);
        }
        if(isset($options['page'])){
            $this->db->page($options['page']);
        }
        $result = $this->db->where($map)->select();
        return $result;
	}
}