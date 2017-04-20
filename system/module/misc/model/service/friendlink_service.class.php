<?php
/**
 *		友情链接服务层
 */

class friendlink_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('misc/friendlink');
		$this->article_service = $this->load->service('misc/article');
	}


	/**
	 * 获取友情链接列表
	 */

	public function get_lists($sqlmap,$page,$limit){
		$friendlink = $this->db->where($sqlmap)->page($page)->limit($limt)->order("sort ASC")->select();
		foreach ($friendlink as $k => $v) {
			$lists[] = array(
				'id' => $v['id'],
				'sort'=>$v['sort'],
				'name'=>$v['name'],
				'url'=>$v['url'],
				'target'=>$v['target'],
				'logo'=>$v['logo'],
				'display'=>$v['display'],
			);
		}
		return $lists;
	}

	/**
	 * [add 添加]
	 * @return [type] [description]
	 */
	public function add($params){
		$data = array();
		if($params['id']) $data['id'] = $params['id'];
		$data['name'] = $params['name'];
		$data['display'] = $params['display'];
		$data['target'] = $params['target'];
		$data['logo'] = $params['logo'] ? $params['logo'] : '';
		$data['url'] = $params['url'];
		$data['sort'] = $params['sort'];
		runhook('friendlink_add',$data);
		$result = $this->db->update($data);
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [get_friendlink_by_id 查询某条数据]
	 * @id [number] 传入的id
	 * @return [type] [description]
	 */
	public function get_friendlink_by_id($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->db->find($id);
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [edit 编辑]
	 * @return [type] [description]
	 */
	public function edit($params){
		if(!isset($params['id'])){
			$this->error = lang('link_no_exist','misc/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = $params['id'];
		$data['name'] = $params['name'];
		$data['display'] = $params['display'];
		$data['target'] = $params['target'];
		if($params['logo']){
			$data['logo'] = $params['logo'];
		}
		$data['url'] = $params['url'];
		$data['sort'] = $params['sort'];
		runhook('friendlink_edit',$data);
		$result = $this->db->update($data);
		if($result === FALSE){
			$this->error = $this->db->getError();
		}
		return TRUE;
	}
		/**
	 * [ajax_edit 编辑]
	 * @return [type] [description]
	 */
	public function ajax_edit($params){
		if(!isset($params['id'])){
			$this->error = lang('link_no_exist','misc/language');
			return FALSE;
		}
		$result = $this->db->update($this->article_service->assembl_array($params));
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [delete 删除]
	 * @return [type] [description]
	 */
	public function delete($params){
		if(!$this->article_service->is_array_null($params)){
			$this->error = lang('link_no_exist','misc/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = array('IN', $params['id']);
		runhook('friendlink_delete',$data);
		$result = $this->db->where($data)->delete();
		if(!$result){
			$this->error = $this->db->getError();
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
 	//标签调用
  	public function friendlink_lists($sqlmap, $options) {
		$this->db->where($this->build_map($sqlmap));
		if($options['limit']){
			$this->db->limit($options['limit']);
		}
		if($sqlmap['order']){
			$this->db->order($sqlmap['order']);
		}
		return  $this->db->select();
	}
	public function build_map($data){
		$sqlmap = array();
		$sqlmap['display'] = 1;
		if($data['_string']){
			$sqlmap['_string'] = $data['_string'];
		}
		return $sqlmap;
	}
}