<?php
class admin_group_service extends service {
	public function _initialize() {
		$this->model = $this->load->table('admin_group');
	}
	/**
	 * [获取所有团队角色]
	 * @param array $sqlmap 数据
	 * @return array
	 */
	public function getAll($sqlmap = array()) {
		$this->sqlmap = isset($this->sqlmap)?array_merge($this->sqlmap, $sqlmap):$sqlmap;
		return $this->model->where($this->sqlmap)->select();
	}

	public function get_lists($sqlmap = array()){
		$users = $this->getAll($sqlmap);
		$lists = array();
		foreach ($users AS $user) {
			$lists[] = array(
				'id' => $user['id'],
				'title' => $user['title'],
				'description' => $user['description'],
				'status' => $user['status'],
			);
		}
		return $lists;
	}
	/**
	 * [启用禁用角色]
	 * @param string $id 支付方式标识
	 * @return TRUE OR ERROR
	 */
	public function change_status($id) {
		$result = $this->model->where(array('id' => $id))->save(array('status' => array('exp', '1-status')));
		if (!$result) {
			$this->error = $this->model->getError();
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * [更新角色]
	 * @param array $data 数据
	 * @param bool $valid 是否M验证
	 * @return bool
	 */
	public function save($data, $valid = FALSE) {
		if (array_key_exists("rules", $data)) $data['rules'] = implode($data['rules'],',');
		runhook('admin_group_save',$data);
		if($valid == TRUE){
			$result = $this->model->update($data);
		}else{
			$result = $this->model->update($data);
		}
		if($result === false) {
			$this->error = $this->model->getError();
			return false;
		}
		return TRUE;
	}
	/**
	* [删除]
	* @param array $ids 主键id
	*/
	public function delete($ids) {
		if(empty($ids)) {
			$this->error = lang('_param_error_');
			return false;
		}
		if(in_array(1, $ids)){
			$this->error = lang('_update_admin_group_success_','admin/language');
			return false;
		}
		$_map = array();
		if(is_array($ids)) {
			$_map['id'] = array("IN", $ids);
		} else {
			$_map['id'] = $ids;
		}
		$result = $this->model->where($_map)->delete();
		if($result === false) {
			$this->error = $this->model->getError();
			return false;
		}
		return true;
	}
	public function get_select_data(){
		return $this->model->get_select_data();
	}
	/**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap = array()){
        $result = $this->model->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->model->getError();
            return false;
        }
        return $result;
    }
}
