<?php
class log_service extends service {
	public function _initialize() {
		$this->model = $this->load->table('log');
	}

	public function get_lists($sqlmap = array(), $page = 1,$limit = 10){
		$result = $this->model->where($sqlmap)->page($page)->limit($limit)->select();
		if(!$result){
    		$this->error = $this->model->getError();
    	}
    	return $result;
    }
	/**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap){
        $result = $this->model->where($sqlmap)->count();
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
		if(in_array(1, $ids)){
			$this->error = lang('_del_admin_user_error_','admin/language');
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
