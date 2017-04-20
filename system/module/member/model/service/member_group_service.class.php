<?php
class member_group_service extends service {
    protected $count;
    protected $pages;

    public function _initialize() {
        $this->table = $this->load->table('member/member_group');
	}

    public function add($params = array()) {
        runhook('before_member_group_add', $params);
        $result = $this->table->update($params);
        if(!$result) {
            $this->error = $this->table->getError();
            return FALSE;
        }
        runhook('after_member_group_add', $params);
        return $this->table->getLastInsID();
    }

    public function edit($params = array()) {
        runhook('before_member_group_edit', $params);
        $result = $this->table->update($params);
        if($result === false) {
            $this->error = $this->table->getError();
            return FALSE;
        }
        runhook('after_member_group_edit', $params);
        return TRUE;
    }

    /**
     * 查询单个会员信息
     * @param int $id
     * @return mixed
     */
    public function fetch_by_id($id) {
        $r = $this->table->find($id);
        if(!$r) {
            $this->error = '_select_not_exist_';
            return FALSE;
        }
        return $r;
    }

    /**
     * 删除指定分组
     * @param type $id
     */
    public function delete_by_id($id) {
        $ids = (array) $id;
        foreach($ids AS $id) {
            $this->table->where(array('id' => $id))->delete();
        }
        return TRUE;
    }

    public function get_lists($sqlmap,$page,$limit = 10){
        $groups = $this->table->where($sqlmap)->page($page)->limit($limit)->order("id ASC")->select();
        $lists = array();
        foreach ($groups AS $group) {
            $lists[] = array(
                'id' => $group['id'],
                'name' => $group['name'],
                'min_points' => $group['min_points'],
                'max_points' => $group['max_points'],
                'discount' => $group['discount']
            );
        }
        return $lists;
    }
    /**
     * 获取会员等级缓存
     */
    public function get($key = NULL){
        $groups = $this->table->cache('groups',3600)->select();
        return is_string($key) ? $groups[$key] : $groups;
    }
    /*
     * @param  string  获取的字段
     * @param  array    sql条件
     * @return [type]
     */
    public function getField($field = '', $sqlmap = array()) {
        if(substr_count($field, ',') < 2){
            $result = $this->table->where($sqlmap)->getfield($field);
        }else{
            $result = $this->table->where($sqlmap)->field($field)->select();
        }
        if($result===false){
            $this->error = lang('_param_error_');
            return false;
        }
        return $result;
    }
    /**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap = array()){
        $result = $this->table->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->table->getError();
            return false;
        }
        return $result;
    }
    /*
     * 获取会员等级缓存
     */
    public function get_cache(){
        if(!cache('groups','','member')){
            $groups = $this->table->select();
            cache('groups', $groups, 'member');
        }
        return cache('groups','','member');

    }
}