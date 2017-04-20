<?php
class admin_menu_service extends service
{
	protected $sqlmap = array();

	public function _initialize() {
		$this->model = $this->load->table('admin/admin_menu');
	}
	
	public function fetch_all_by_admin_id($admin_id = 0) {
		return $this->model->where(array('admin_id' => $admin_id))->select();
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
    /**
	 * [update 更新数据]
	 * @param  [type] $params [参数]
	 * @return [type]         [description]
	 */
	public function update($params){
		if(empty($params)){
			$this->error = lang('_params_error_');
			return false;
		}
		$result = $this->model->update($params);
		if($result === false){
			$this->error = $this->model->getError();
			return false;
		}
		return $result;
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
}