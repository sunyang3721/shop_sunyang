<?php
/**
 *		商品品牌数据层
 */

class goods_category_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('goods/goods_category');
		$this->type_service = $this->load->service('goods/type');
	}
	/**
     * [category_lists 后台分类列表]
     * @return [type]            [description]
     */
	public function category_lists(){
		$result = $this->db->where(array('parent_id' => 0))->order('sort asc')->getField('id,sort,name,parent_id,type_id,status');
		if(!$result){
    		$this->error = $this->db->getError();
    	}
		foreach ($result as $key => $value) {
			$type_name = $this->type_service->fetch_by_id($value['type_id'],'name');
			$result[$key]['type_name'] = $type_name ? $type_name : '';
			if($this->has_child($value['id'])){
				$result[$key]['level'] = 1;
			}
			$lists[] = array('id'=>$value['id'],'sort'=>$value['sort'],'name'=>$value['name'],'type_name'=>$result[$key]['type_name'],'status'=>$value['status'],'level'=>$result[$key]['level']);
		}
		return $lists;
    }
    /**
     * [ajax_category ajax获取分类]
     * @param  [type] $parent_id [description]
     * @return [type]            [description]
     */
	public function ajax_category($parent_id){
		if((int)$parent_id < 1){
			$this->error = lang('goods_category_not_exist','goods/language');
			return FALSE;
		}
		$result = $this->db->where(array('parent_id' =>$parent_id))->order('sort desc')->select();
		foreach ($result as $key => $value) {
			$type_name = $this->type_service->fetch_by_id($value['type_id'],'name');
			$result[$key]['type_name'] = $type_name ? $type_name : '';
			$result[$key]['row'] = $this->db->where(array('parent_id' =>array('eq',$value['id'])))->count();
		}
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [add_spec 增加分类]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function add_category($params){
		if(isset($params['grade'])){
			$params['grade'] =preg_replace("/(\s)/",'',preg_replace("/(\n)|(\t)|(\')|(')|(，)/" ,',' ,$params['grade']));
		}
		if($params['url'] == 'http://'){
			unset($params['url']);
		}
		runhook('before_add_category',$params);
		$data = $this->db->create($params);
		$result = $this->db->add($data);
		cache('goods_category',NULL);
    	if($result === FALSE){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [edit_spec 编辑分类]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function edit_category($params){
		if((int)$params['id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if(isset($params['grade'])){
			$params['grade'] =preg_replace("/(\s)/",'',preg_replace("/(\n)|(\t)|(\')|(')|(，)/" ,',' ,$params['grade']));
		}
		if($params['url'] == 'http://'){
			unset($params['url']);
		}
		runhook('before_edit_category',$params);
		$result = $this->db->update($params);
		cache('goods_category',NULL);
    	if($result === FALSE){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}
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
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [delete_spec 删除分类]
	 * @param  [int] $params [分类id]
	 * @return [boolean]     [返回删除结果]
	 */
	public function delete_category($id){
		if($this->has_child($id)){
			$this->error = lang('goods_has_child_category','goods/language');
			return FALSE;
		}
		if($id < 0){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		runhook('before_delete_category',$params);
		$result = $this->db->delete($id);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}else{
    		cache('goods_category',NULL);
			runhook('after_delete_category',$params);
    		return TRUE;
    	}
	}
	/**
	 * [change_sort 改变排序]
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
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [get_category_tree 获取商品分类树]
	 * @param type $lists 分类列表
	 * @return [type] [description]
	 */
	public function get_category_tree($lists){
		$result = $this->get_tree($lists);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
    	return $result;
    }
    /**
	 * 分类层级
	 * @staticvar array $tree
	 * @param type $lists 分类信息
	 * @param type $parent_id	分类父级id
	 * @param type $level 级别
	 * @return array
	 */
	public function get_tree(&$lists ,$parent_id = 0,$level = 0){
		foreach ($lists as $k => $list){
			if($list['parent_id'] == $parent_id){
				$lists[$k]['level'] = $level;
				$this -> get_tree($lists, $list['id'],$level+1);
			}
		 }
		 return $lists;
	}
	/**
	 * [get_parent 获取商品分类所有父级id]
	 * @param  string  $cid   [需要获取的商品分类cid]
	 * @return [array]	$result       [返回父级id数组]
	 */
    public function get_parent($cid,$istop = false){
    	 if ($istop) {
            $result = 0;
            $category = $this->get($cid);
            $result = $category['id'];
            if ($category['parent_id']) {
                $result = $this->get_parent($category['parent_id'], $istop);
            }
        } else {
            $result = array();
            $category = $this->get($cid);
            if ($category['parent_id'] ) {
                $result[] = $category['parent_id'];
                if ($category['parent_id'] != $cid) {
                    $parent_id = $this->get_parent($category['parent_id'], $istop);
                    if(!empty($parent_id)){
                        $result = array_merge($result,$parent_id);
                    }
                }

            }
        }
        return $result;
	}
	/**
	 * [create_cat_format 组合父子级分类关系]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function create_cat_format($category = array(),$extra = FALSE){
		$names = array();
		if(empty($category)){
			if($extra == TRUE) $names = array('0' => '顶级分类');
		}else{
			$names = $this->db->where(array('id'=>array('IN',$category)))->getField('name',TRUE);
			if($extra == TRUE) array_unshift($names,'顶级分类');
		}
		foreach ($names as $key => $value) {
			if($key == count($names)-1){
				$cat_str .= $value;
			}else{
				$cat_str .= $value.'>';
			}
		}
		return $cat_str;
	}
	/**
	 * [create_format_id 格式父子级分类id]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function create_format_id($categorys,$isparent = FALSE){
		if(empty($categorys)){
			if($isparent) $categorys = array(0,0);
		}else{
			krsort($categorys);
			if($isparent) array_unshift($categorys,0);
		}
    	return implode(',',$categorys);
	}
    /**
     * [has_child 判断分类是否有子分类]
     * @param  [type]  $id [分类id]
     * @return boolean     [description]
     */
    public function has_child($id){
    	if((int)$id < 0){
    		$this->error = lang('_param_error_');
    		return FALSE;
    	}
		$rows = $this->db->where(array('parent_id'=>$id))->count();
		return $rows > 0 ? true : false;
	}
	/**
	 * [get_child 根据父分类获取所有子分类]
	 * @param  [type] $cid [父id]
	 * @return [type]      [所有子分类]
	 */
    public function get_child($cid){
    	if((int)$cid < 0){
    		$this->error = lang('_param_error_');
    		return FALSE;
    	}
        $return = array();
        $ids = $this->db->where(array('parent_id'=>$cid))->getField('id',TRUE);
        $return = $ids;
        if(is_array($ids)){
            foreach ($ids as $key => $value) {
                $child = $this->get_child($value);
                if(!empty($child)){
                    $return = array_merge($return,$child);
                }
            }
        }
        return $return;
    }
    /**
     * [get_cache 获取分类缓存]
     * @return [type] [description]
     */
    public function get($key = NULL) {
		$result = $this->db->order('sort asc,id desc')->cache('goods_category',3600)->getField(implode(',', array_keys($this->db->get_fields())), TRUE);
		return is_string($key) ? $result[$key] : $result;
    }
    /**
	 * [get_cate_grades 获取分类的价格分级]
	 * @param  [type] $id [分类id]
	 * @return [type] [description]
	 */
    public function get_cate_grades($id){
    	if((int)$id < 0){
    		$this->error = lang('_param_error_');
    		return FALSE;
    	}
    	$category = $this->get($id);
    	$grades = explode(',',$category['grade']);
    	$result = array();
    	foreach ($grades as $grade) {
			if($grade != ''){
				$result[] = explode('-',$grade);
		    }
		}
		return $result;
    }
    /**
     * [get_brand_info 获取分类及子分类下的所有品牌信息]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function get_brand_info($id){
    	if((int)$id < 0){
    		$this->error = lang('_param_error_');
    		return FALSE;
    	}
    	$_cat_arr = array($id);
    	$_childcat_arr = $this->get_child($id);
    	if($_childcat_arr){
    		$_cat_arr = array_merge($_cat_arr,$_childcat_arr);
    	}
    	$sqlmap = $join = $brand_arr = array();
		foreach ($_cat_arr as $_childid) {
			$join[] = 'catid = '.$_childid;
		}
		$sqlmap['brand_id'] = array("GT", 0);
		$sqlmap['_string'] = JOIN(" OR ", $join);
		$brand_ids = $this->load->table('goods/goods_spu')->where($sqlmap)->getField('brand_id', TRUE);
		if($brand_ids) {
			$brand_arr = $this->load->table('goods/brand')->where(array('id' => array("IN", $brand_ids)))->order('sort ASC')->select();
		}
		return $brand_arr;
    }
    /**
     * [get_category_by_id 根据id获取分类信息]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function get_category_by_id($id,$flag = TRUE){
    	if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
    	$result = $this->db->detail($id)->output();
    	$categorys = $this->get_parent($id);
    	if(!$flag) array_unshift($categorys, $id);

    	$result['cat_format'] = $this->create_format_id($categorys,$flag);
    	$result['parent_name'] = $this->create_cat_format($categorys,$flag);
    	$typeinfo = $this->type_service->fetch_by_id($result['type_id']);
    	$result['type_name'] = $typeinfo['name'];
    	if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
    	return $result;
     }
    /**
     * [get_top_parent 获取顶级分类id]
     * @param  string  $cid   [description]
     * @param  integer $istop [description]
     * @return [type]         [description]
     */
    private function get_top_parent($cid, $istop = 1) {
    	if((int)$cid < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
        if ($istop ==  1) {
            $result = 0;
            $category = $this->get($cid);
            $result = $category['id'];
            if ($category['parent_id']) {
            	$result = $this->get_top_parent($category['parent_id'], $istop);
            }
        }
        return $result;
    }
    /**
     * [create_category 组织列表页的分类层级显示]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function create_category($id){
    	if((int)$id < 1) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$goods_category = $this->get();
		$category = $goods_category[$id];
		if(!$category){
			$this->error = lang('goods_category_not_exist','goods/language');
			return FALSE;
		}
		$top_parentid = $this->get_top_parent($id);
		$category['top_parentid'] = $goods_category[$top_parentid];
		return $category;
    }
    /**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
    public function detail($id, $field = TRUE){
    	return $this->db->detail($id, $field)->output();
    }
	/**
	 * 分类列表
	 */
    public function lists($sqlmap = array(), $options = array()) {
        $sqlmap['status'] = 1;
        if ($sqlmap['only']) {
            $catids = str_replace('，', ',', $sqlmap['catid']);
            $catids = explode(',', $catids);
            $sqlmap['id'] = array('IN',$catids);
        } else {
            if($sqlmap['type'] == 'nav') {
                $sqlmap['show_in_nav'] = 1;
                $cat_ids = $this->get_child($sqlmap['catid']);
                $sqlmap['id'] = array('IN',$cat_ids);
            }else{
                $sqlmap['parent_id'] = $sqlmap['catid'] ? $sqlmap['catid'] : 0;
            }
        }
        if(!isset($sqlmap['order'])){
           $sqlmap['order'] = 'sort asc,id asc';
        }
        $this->db->order($sqlmap['order']);
        if(isset($options['limit'])){
            $this->db->limit($options['limit']);
        }
        if(isset($options['page'])){
            $this->db->page($options['page']);
        }
        $lists = $this->db->where($sqlmap)->select();
        return $lists;
    }
}