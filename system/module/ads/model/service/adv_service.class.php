<?php
/**
 *      广告设置服务层
 */

class adv_service extends service {
		
	protected $count;
    protected $pages;
	
	public function _initialize() {
		$this->model = $this->load->table('adv');
	}
	/**
     * 查询单个信息
     * @param int $id 主键ID
     * @param string $field 被查询字段
     * @return mixed
     */
    public function fetch_by_id($id, $field = NULL) {
        $r = $this->model->find($id);
        if(!$r) return FALSE;
        return ($field !== NULL) ? $r[$field] : $r;
    }


    public function get_lists($sqlmap,$page,$limit){
    	$ads = $this->model->where($sqlmap)->page($page)->limit($limit)->select();
    	foreach ($ads as $k => $v) {
    		$lists[] = array(
				'id' => $v['id'],
				'title'=>$v['title'],
				'position_name'=>$v['position_name'],
				'type_text'=>$v['type_text'],
				'startime_text'=>$v['startime_text'],
				'endtime_text'=>$v['endtime_text'],
				'hist'=>$v['hist'],
			);
    		
    	}
    	return $lists;

    }

	/**
	 * [更新广告]
	 * @param array $data 数据
	 * @param bool $valid 是否M验证
	 * @return bool
	 */
	public function save($data, $valid = FALSE) {
		if($valid == TRUE){
			$data = $this->model->create($data);
			$result = $this->model->add($data);
		}else{
			$result = $this->model->update($data);
		}
		return $result;
	}

	/**
	 * 编辑广告title
	 */
	public function save_title($data) {
		$result = $this->model->save($data);
		if(!$result){
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
	/**
	 * @param [值]
	 * @param [数]
	 * @param [sql条件]
	 */
	public function setInc($val, $num, $sqlmap){
		return $this->model->where($sqlmap)->setInc($val,$num);
	}

}
