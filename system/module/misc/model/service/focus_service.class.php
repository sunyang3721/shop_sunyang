<?php
/**
 *		幻灯片服务层
 */

class focus_service extends service {
		public function _initialize() {
		$this->db = $this->load->table('misc/focus');
		$this->article_service = $this->load->service('misc/article');
	}
	/**
	 * [lists 列表]
	 * @return [type] [description]
	 */
	public function lists(){
		$result = $this->db->select();
		if(!$result){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return $result;
	}

	public function get_lists($sqlmap,$page,$limit){
		$focus = $this->db->where($sqlmap)->page($page)->limit($limit)->order("sort ASC")->select();
		foreach ($focus as $k => $v) {
			$lists[] = array(
				'id' => $v['id'],
				'sort'=>$v['sort'],
				'title'=>$v['title'],
				'url'=>$v['url'],
				'target'=>$v['target'],
				'description'=>$v['description'],
				'display'=>$v['display'],
				'thumb'=>$v['thumb'],
				'width'=>$v['width'],
				'height'=>$v['height'],
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
		$data['title'] = $params['title'];
		$data['thumb'] = $params['thumb'] ? $params['thumb']: '';
		$data['url'] = $params['url'];
		$data['target'] = $params['target'];
		$data['sort'] = $params['sort'];
		$data['description'] = $params['description']?$params['description']:'';
		$data['display'] = isset($params['display']) ? $params['display'] : 1;
		runhook('focus_add',$data);
		$result = $this->db->update($data);
		if(!$result){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return $result;
	}
	/**
	 * [get_focus_by_id 查询某条数据]
	 * @id [number] 传入的id
	 * @return [type] [description]
	 */
	public function get_focus_by_id($id){
		if((int)$id < 1){
			$this->error = lang('slideshow_not_exist','member/language');
			return FALSE;
		}
		$result = $this->db->find($id);
		if(!$result){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return $result;
	}
	/**
	 * [edit 编辑]
	 * @return [type] [description]
	 */
	public function edit($params){
		if(!isset($params['id'])){
			$this->error = lang('slideshow_not_exist','member/language');
			return FALSE;
		}
		$data = array();
		$data['id'] = $params['id'];
		$data['title'] = $params['title'];
		$data['url'] = $params['url'];
		$data['target'] = $params['target'];
		if($params['thumb']){
			$data['thumb'] = $params['thumb'];
		}
		$data['url'] = $params['url'];
		$data['sort'] = $params['sort'];
		runhook('focus_edit',$data);
		$result = $this->db->update($data);
		if($result === FALSE){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * [delete 删除]
	 * @return [type] [description]
	 */
	public function delete($params){
		if(!$this->article_service->is_array_null($params)){
			$this->error = lang('slideshow_not_exist','member/language');
			return FALSE;
		}

		$data = array();
		$data['id'] = array('IN',implode(',',$params['id']));
		$infos = $this->db->where($data)->getField('thumb',true);
		foreach ($infos AS $info) {
			$this->load->service('attachment/attachment')->attachment('', $info,false);
		}
		runhook('focus_delete',$data);
		$result = $this->db->where($data)->delete();
		if(!$result){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return $result;
	}
		/**
	 * [ajax_edit 编辑]
	 * @return [type] [description]
	 */
	public function ajax_edit($params){
		if(!isset($params['id'])){
			$this->error = lang('slideshow_not_exist','member/language');
			return FALSE;
		}
		$result = $this->db->update($this->article_service->assembl_array($params));
		if(!$result){
			$this->error = $this->db->getError();
			return FALSE;
		}
		return $result;
	}
	/**
	 * [total 总记录数]
	 * @return [type]     [返回查询结果结果]
	 */
	public function total(){
		$result = $this->db->count;
		if(!$result){
    		$this->error = $this->db->getError();
    		return FALSE;
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
	public function focus_lists($sqlmap, $options) {
		$this->db->where($this->build_map($sqlmap));
		if($options['limit']){
			$this->db->limit($options['limit']);
		}
		if($sqlmap['order']){
			$this->db->order($sqlmap['order']);
		}
		return $this->db->select();
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