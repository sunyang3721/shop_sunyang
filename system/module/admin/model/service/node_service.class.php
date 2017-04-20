<?php
class node_service extends service {
    protected $sqlmap = array();

    public function _initialize() {
        $this->model = $this->load->table('node');
    }
    public function setAdminid($admin_id) {
        return $this;
    }

    public function getAll() {
        $this->sqlmap['status'] = 1;
        $result =  $this->model->where($this->sqlmap)->order('sort ASC,id ASC')->select();
        return $this->_format($result);
    }


	public function fetch_all_by_ids($ids, $status = -1) {
		$_map = array();
		if($ids) {
			$_map['id'] = array("IN", explode(",", $ids));
		}
        if($status > -1) {
            $_map['status'] = $status;
        }
		$result = $this->model->where($_map)->order('sort ASC,id ASC')->select();;
		return $this->_format($result);
	}

	public function get_checkbox_data(){
		return$this->model->where(array('status'=>1))->getField('id as id,parent_id,name',TRUE);
	}

    private function _format($data) {
        if(empty($data)) return false;
        $result = array();
        foreach($data as $k => $v) {
            $v['url'] = $v['url'] ? $v['url'] : url($v['m'].'/'.$v['c'].'/'.$v['a'], $param);
            $result[$k] = $v;
        }
        return $result;
    }
    /**
     * @param  string  获取的字段
     * @param  array    sql条件
     * @return [type]
     */
    public function getField($field = '', $sqlmap = array()) {
        $result = $this->model->where($sqlmap)->getfield($field);
        if($result === false){
            $this->error = $this->model->getError();
            return false;
        }
        return $result;
    }
}
