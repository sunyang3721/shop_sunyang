<?php
class admin_user_service extends service
{
	protected $sqlmap = array();

	public function _initialize() {
		$this->model = $this->load->table('admin_user');
	}
	/**
	 * [获取所有团队成员]
	 * @param array $sqlmap 数据
	 * @return array
	 */
	public function getAll($sqlmap = array()) {
		$this->sqlmap = array_merge($this->sqlmap, $sqlmap);
		return $this->model->where($this->sqlmap)->select();
	}

	public function get_lists($sqlmap = array()){
		$users = $this->getAll($sqlmap);
		$lists = array();
		foreach ($users AS $user) {
			$lists[] = array(
				'id' => $user['id'],
				'username' => $user['username'],
				'group_name' => $user['id'] == 1 ? '超级管理员' : (isset($user['group_name']) ? $user['group_name'] : '-'),
				'last_login_time' => $user['last_login_time'],
				'login_num' => $user['login_num']
			);
		}
		return $lists;
	}
	/**
	 * [更新团队]
	 * @param array $data 数据
	 * @param bool $valid 是否M验证
	 * @return bool
	 */
	public function save($data, $valid = FALSE) {
		$data=array_filter($data);
		$data['encrypt'] = random(10);
		$data['password'] = create_password($data['password'], $data['encrypt']);
		if($valid == TRUE){
			$data = $this->model->create($data);
			$result = $this->model->add($data);
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
	/*修改*/
	public function setField($data, $sqlmap = array()){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->model->where($sqlmap)->save($data);
		if($result === false){
			$this->error = $this->model->getError();
			return false;
		}
		return $result;
	}
	public function fetch_by_id($id){
		return $this->model->fetch_by_id($id);;
	}
}