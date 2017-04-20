<?php
/**
 *		导航服务层
 */

class navigation_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('misc/navigation');
		$this->article_service = $this->load->service('misc/article');
	}
	/**
	 * [lists 列表]
	 * @return [type] [description]
	 */
	public function lists($sqlmap = array(),$options = array()){
		$db = $this->db->where($this->build_map($sqlmap));
		if($options['limit']) $db->limit($options['limit']);
		if($sqlmap['order']) $db->order($sqlmap['order']);
		$info = $db->select();
		if(!$info){
			$this->error = $db->getError();
			return false;
		}
		return $info;
	}

	public function get_lists($sqlmap){
		$info = $this->lists($sqlmap); 
		foreach ($info as $k => $v) {
			$lists[] = array(
				'id' => $v['id'],
				'sort'=>$v['sort'],
				'name'=>$v['name'],
				'url'=>$v['url'],
				'target'=>$v['target'],
				'display'=>$v['display'],
			);
		}
		return $lists;
	}


	private function build_map($data){
		$sqlmap = array();
		if($data['_string']) $sqlmap['_string'] = $data['_string'];
		return $sqlmap;
	}
	/**
	 * [add 添加]
	 * @return [type] [description]
	 */
	public function add($params){
		$data = array();
		if($params['id']) $data['id'] = $params['id'];
		$data['name'] = $params['name'];
		$data['url'] = $params['url'];
		$data['target'] = $params['target'];
		$data['sort'] = $params['sort'];
		runhook('navigation_add',$data);
		$result = $this->db->update($data);
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
	/**
	 * [ajax_edit 编辑]
	 * @return [type] [description]
	 */
	public function ajax_edit($params){
		if(!isset($params['id'])){
			$this->error = lang('nav_no_exist','misc/language');
			return FALSE;
		}
		$result = $this->db->update($this->article_service->assembl_array($params));
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
			$this->error = lang('nav_no_exist','misc/language');
			return FALSE;
		}
		$data['id'] = $params['id'];
		$data['name'] = $params['name'];
		$data['url'] = $params['url'];
		$data['target'] = $params['target'];
		$data['sort'] = $params['sort'];
		runhook('navigation_edit',$data);
		$result = $this->db->update($data);
		if($result ===FALSE){
			$this->error = $this->db->getError();
		}
		return TRUE;
	}
	/**
	 * [get_navigation_by_id 查询某条数据]
	 * @id [number] 传入的id
	 * @return [type] [description]
	 */
	public function get_navigation_by_id($id){
		if((int)$id < 1){
			$this->error = lang('nav_no_exist','misc/language');
			return FALSE;
		}
		$result = $this->db->find($id);
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
			$this->error = lang('nav_no_exist','misc/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = array('IN', explode(',',$params['id'][0]));
		runhook('navigation_delete',$data);
		$result = $this->db->where($data)->delete();
		if(!$result){
			$this->error = $this->db->getError();
		}
		return $result;
	}
}