<?php
class member_log_service extends service {
	public function _initialize() {
         $this->model = $this->load->table('member/member_log');
         $this->member = $this->load->table('member/member');
	}
	public function add($params){
		runhook('member_log_add',$params);
        return $this->model->update($params);
    }
    public function get_lists($sqlmap,$page,$limit){
    	$logs = $this->model->where($sqlmap)->page($page)->limit($limit)->order('dateline desc')->select();
    	$lists = array();
    	foreach ($logs AS $log) {
    		$lists[] = array(
    			'id' => $log['id'],
    			'username' => $log['username'],
    			'dateline' => $log['dateline'],
    			'value' => $log['value'],
    			'msg' => $log['msg']
    		);
    	}
    	return $lists;
    }
    public function build_sqlmap($params){
        $sqlmap['type'] = 'money';
        if($params['start']) {
            $time[] = array("GT", strtotime($params['start']));
        }
        if($params['end']) {
            $time[] = array("LT", strtotime($params['end']));
        }
        if($time){
            $sqlmap['dateline'] = $time;
        }
        if($params['keywords']){
            $mid = $this->member->where(array('username' => $params['keywords']))->getField('id');
            if($mid > 0){
                $sqlmap['mid'] = (int)$mid;
            }else{
                $this->error = lang('请输入正确会员名');
                return false;
            }
        }
        return $sqlmap;
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
     * @param  array    sql条件
     * @param  integer  条数
     * @param  integer  页数
     * @param  string   排序
     * @return [type]
     */
    public function lists($sqlmap = array(), $limit = 20, $page = 1, $order = "") {
        $count = $this->model->where($sqlmap)->count();
        $lists = $this->model->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
        if($count===false || $lists===false){
            $this->error = lang('_param_error_');
            return false;
        }
        return array('lists'=>$lists,'count'=>$count);
    }
}