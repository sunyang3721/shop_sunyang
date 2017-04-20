<?php
/**
 *		文章分类服务层
 */

class article_category_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('misc/article_category');
	}


	/**
	 * 获取文章分类
	 */
	public function get_lists($sqlmap,$page,$limit,$order){

		$category = $this->db->where($sqlmap)->page($page)->limit($limit)->order($order)->select();
		if(!$category){
			$this->error = $this->db->getError();
		}
		foreach ($category AS $k => $v) {
			if($this->has_child($v['id'])){
				$level = 1;
			}
			$lists[] = array(
					'id' =>$v['id'],
					'sort' =>$v['sort'],
					'name'=>$v['name'],
					'display' =>$v['display'],
					'parent_id' =>$v['parent_id'],
					'level' =>$level,
				);
		}
		return array('lists'=>$lists);

	}

	/**
	 * [get_category_by_id 根据id获取文章信息]
	 * @param  [type] $id [description]
	 * @field  [string] 字段
	 * @return [type]     [description]
	 */
	public function get_category_by_id($id,$field = FALSE){
		if((int)$id < 1 && $id != 'all'){
			$this->error = lang('goods_category_not_exist','goods/language');
			return FALSE;
		}
		if((int)$id < 1 && $id == 'all'){
			return '所有';
		}
		$result = $this->db->find($id);
		$result['category_id'] = $this->get_parents_id($result['id']);
		$result['parent_name'] = $this->get_parents_name($id);
		if(!$result){
			$this->error = $this->db->getError();
		}
		if($field) return $result[$field];
		return $result;
	}
	//获取父分类id
	public function get_parents_id($id){
		if($id < 0){
			$this->error = lang('goods_category_not_exist','goods/language');
			return false;
		}
		static $ids = array();
		$row = $this->db->where(array('id'=>$id))->find();
		if($row['parent_id'] != 0){
			$ids[] = $row['id'];
			$parent_id = $row['parent_id'];
			$this->get_parents_id($parent_id);
		} else {
			$ids[] = $row['id'];
		}
		return array_reverse($ids);
	}
	//获取父分类名称
	public function get_parents_name($id){
		$ids = $this->get_parents_id($id);
		$names = $this->db->where(array('id'=>array('IN',$ids)))->getField('name',TRUE);
		$cat_name = '';
		foreach($names as $k => $v){
			$cat_name .= $v.' > ';
		}
		return  rtrim($cat_name,' > ');
	}
	/**
	 * [edit 编辑分类]
	 * @param [array] $params [分类信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function edit($params){
		if((int)$params['id'] < 1){
			$this->error = lang('goods_category_not_exist','goods/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = $params['id'];
		$data['name'] = $params['name'];
		$data['parent_id'] = $params['parent_id'];
		$data['sort'] = $params['sort'];
		runhook('article_category_edit',$data);
		$result = $this->db->update($data);
    	if($result === FALSE){
			$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [delete 删除分类]
	 * @param [type] $id [分类id]
	 * @return [boolean]         [返回ture or false]
	 */
	public function delete($params){
		if($this->has_child($params['id'])){
			$this->error = lang('goods_has_child_category','goods/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = array('IN', $params['id']);
		runhook('article_category_delete',$params);
		$result = $this->db->where($data)->delete();
    	if(!$result){
			$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
     * [has_child 判断分类是否有子分类]
     * @param  [array]  $id [分类id]
     * @return boolean     [description]
     */
    public function has_child($params){
		$params_ids = explode(',',$params[0]);
		for($i=0; $i<count($params_ids); $i++){
			$tem_data[] = $this->db->where(array('parent_id' =>array('eq',$params_ids[$i])))->count();
			if($tem_data[$i] > 0){
				return TRUE;
			}
		}
		return FALSE;
	}
	/**
	 * [get_format_category 获取文章分类树]
	 * @return [type] [description]
	 */
    public function get_category_tree(){
		$_catinfo = $this->db->select();
		if(!$_catinfo){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}
		$first = array(
			'id' => '0',
            'name' => '顶级分类',
            'parent_id' => '-1'
		);
		array_unshift($_catinfo,$first);
    	return $_catinfo;
	}
	/**
	 * [add 增加分类]
	 * @param [array] $params [增加分类信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function add($params){
		if(empty($params['parent_id'])){
			$params['parent_id'] = 0;
		}else{
			$count = $this->db->where(array('id'=>array('eq',(int)$params['parent_id'])))->count();
			if($count < 1){
				$this->error = lang('parent_category_not_exist','misc/language');
				return false;
			}
		}
		$data = array();
		if($params['id']) $data['id'] = $params['id'];
		$data['name'] = $params['name'];
		$data['parent_id'] = $params['parent_id'];
		$data['sort'] = $params['sort'];
		runhook('article_category_add',$data);
		$result = $this->db->update($data);
    	if(!$result){
    		$this->error = $this->db->getError();
			return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [ajax_sun_class ajax获取分类]
	 */
	public function ajax_son_class($params){
		if((int)$params['id'] < 1){
			$this->error = lang('goods_category_not_exist','goods/language');
			return FALSE;
		}
		$result = $this->db->where(array('parent_id' =>array('eq',$params['id'])))->select();
		foreach($result as $key => $value){
			$result[$key]['row'] = $this->db->where(array('parent_id' =>array('eq',$value['id'])))->count();
		}
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [ajax_edit 编辑分类信息]
 	 */
	public function ajax_edit($params){
		unset($params['page']);
		$result = $this->db->update($params);
		if($result == false){
			$this->error = $this->db->getError();
			return false;
		}
    	return true;
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


	//标签调用
	public function category_lists($sqlmap, $options) {
		$this->db->where($this->build_map($sqlmap));
		if(isset($sqlmap['order'])){
			$this->db->order($sqlmap['order']);
		}
		if(isset($options['limit'])){
			$this->db->limit($options['limit']);
		}
		return $this->db->select();
	}
	public function build_map($data){
		$sqlmap = array();
		$sqlmap['display'] = 1;
		if (isset($data['id'])) {
			if(preg_match('#,#', $data['id'])) {
				$sqlmap['parent_id'] = array("IN", explode(",", $data['id']));
			} else {
				$sqlmap['parent_id'] = $data['id'];
			}
		}
		if(isset($data['_string'])){
			$sqlmap['_string'] = $data['_string'];
		}
		return $sqlmap;
	}
}