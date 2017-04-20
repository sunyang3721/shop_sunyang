<?php
/**
 *		帮助服务层
 */

class help_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('misc/help');	
		$this->article_service = $this->load->service('misc/article');
	}
	/**
	 * [get_index 获取帮助文章列表]
	 * @return [type] [description]
	 */
	public function get_index(){
       $_helpinfo = $this->db->order("sort asc")->select();
	   $result = $this->get_help_tree($_helpinfo,0);
       if(!$result){
		   $this->error = $this->db->getError();
	   }
	   return $result;
	}
	/**
	 * [get_help_tree 帮助文章分类树]
	 * @param [array] $params [帮助文章信息]
	 * @param [type] $parent_id [帮助文章父id]
	 * @return [type] [description]
	 */
	public function get_help_tree($params,$parent_id){
		$get_params = array();
		$tem_help = array();
		foreach ($params as $key => $value) {
			if ($value['parent_id'] == $parent_id) {
					$tem_help = $this->get_help_tree($params, $value['id']);
					$tem_help && $value['son_help'] = $tem_help;
					$get_params[] = $value;
				}
			}
		return $get_params;
	}
		/**
	 * [get_help_by_id 根据id获取文章信息]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_help_by_id($id){
		if((int)$id < 1){
			$this->error = lang('article_not_exist','misc/language');
			return FALSE;
		}
		$result = $this->db->find($id);
		if($result['parent_id']){
			$result['top_class'] = $this->db->where(array('id'=>array('eq',$result['parent_id'])))->getField('title');
		}
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [change_title 改变标题]
	 * @param  [array] $params [文章id和title]
	 * @return [boolean]     [返回更改结果]
	 */
	public function edit($params){
		if((int)$params['id'] < 1){
			$this->error = lang('article_not_exist','misc/language');
			return FALSE;
		}		
		$data = array();
		$data['id'] = $params['id'];
		$data['title'] = $params['title'];
		$data['keywords'] = $params['keywords'];
		$data['content'] = isset($params['content']) ? $params['content'] : '';
		$data['parent_id'] = isset($params['parent_id']) ? $params['parent_id'] : '';
		runhook('help_edit',$data);
 		$result = $this->db->update($data);
		if($result === FALSE){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [add 添加]
	 * @param  [array] $params [文章id和title]
	 * @return [boolean]     [返回更改结果]
	 */
	public function add($params){
		$data = array();
		if($params['id'])$data['id'] = $params['id'];
		$data['title'] = $params['title'] ? $params['title'] : '';
		$data['keywords'] = $params['keywords'] ? $params['keywords'] : '';
		$data['content'] = isset($params['content']) ? $params['content'] : '';
		$data['parent_id'] = isset($params['parent_id']) ? $params['parent_id'] : '';
		$data['display'] = isset($data['display']) ? $data['display'] : 1;
		$data['sort'] = isset($data['sort']) ? $data['sort'] : 100;
 		runhook('help_add',$data);
 		$result = $this->db->update($data);
 		if($result === FALSE){
    		$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}
	}
	/**
	 * [delete 删除文章]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function delete($params){
		if((int)count($params['id']) < 1){
			$this->error = lang('article_not_exist','misc/language');
			return FALSE;
		}	
		$data = array();	
		if($this->get_parent(explode(',',$params['id'][0]))){
			$this->error = lang('child_article_not_delete','misc/language');
			return FALSE;
		}
		$data['id'] = array('IN', explode(',',$params['id'][0]));
		$infos = $this->db->where($data)->getField('content',true);
		foreach ($infos AS $info) {
			$this->load->service('attachment/attachment')->attachment('', $info);
		}
 		runhook('help_delete',$data);
		$result = $this->db->where($data)->delete();	
    	if(!$result){
			$this->error = $this->db->getError();
    		return FALSE;
    	}else{
    		return TRUE;
    	}	
	}
	/**
	 * [get_parent 是否有父分类]
	 * @param [array] $params [规格信息]
	 * @return [boolean]         [返回ture or false]
	 */
	public function get_parent($params){
		$row = $this->db->where(array('parent_id'=>$params[0]))->count();
		return $row ? TRUE : FALSE;
	}
	/**
	 * [get_parents 获取所有的父分类]
	 * @return [type] [description]
	 */
	public function get_parents(){
       $result = $this->db->where(array('parent_id'=>array('eq',0)))->getField('id,title');
       if(!$parents){
		   $this->error = $this->db->getError();
	   }
	   return $result;
	}
	/**
	 * [batch 添加]
	 * @return [type] [description]
	 */
	public function batch($params){
		$data = array();
		  foreach($params['edit'] as $key => $value){
				 $data['id'] = $key;
				 $data['sort'] = $value['sort'];
				 $data['title'] = $value['title'];
				 $result = $this->db->update($data);
				 unset($data);
			}
		 foreach($params['add_son'] as $key => $values){
				 $data['parent_id'] = $key;
				 foreach($values as $key => $value){
						$data['sort'] = $value['sort'];
						$data['title'] = $value['title'];
						$result = $this->db->update($data);
				 } 
				 unset($data);
			}
		  foreach($params['add_parent'] as $key => $value){
				 $data['sort'] = $value['sort'];
				 $data['title'] = $value['title'];
				 $result = $this->db->update($data); 
				 unset($data);
			}
	 // if(!$result){暂时无法判断
		 // $this->error = $this->db->getError();
		 // return FALSE;
	 // }
	 return TRUE;
	}
	/**
	 * [ajax_edit 编辑分类信息]
 	 */
	public function ajax_edit($params){
	    $result = $this->db->update($this->article_service->assembl_array($params));
    	if(!$result){
    		$this->error = $this->db->getError();
			return FALSE;
    	}else{
    		return TRUE;
    	}	
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
		$names = $this->db->where(array('id'=>array('IN',$ids)))->getField('title',TRUE);
		$cat_name = '';
		foreach($names as $k => $v){
			$cat_name .= $v.' > ';
		}
		return  rtrim($cat_name,' > ');
	}

	//标签调用
	public function help_lists($sqlmap, $options) {
		$this->db->where($this->build_map($sqlmap));
		if($sqlmap['order']){
			$this->db->order($sqlmap['order']);			
		}
		if($options['limit']){
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