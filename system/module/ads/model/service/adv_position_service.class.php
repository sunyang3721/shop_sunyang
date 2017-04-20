<?php
/**
 *      广告位设置服务层
 */

class adv_position_service extends service {

	protected $count;
    protected $pages;

	public function _initialize() {
		$this->model = $this->load->table('ads/adv_position');
		$this->adv_table = $this->load->table('adv');
	}


	/**
     * 获广告位列表
     * @param type $sqlmap
     * @return type
     */
    public function getposition($sqlmap = array()) {
        $advs = $this->model->where($sqlmap)->select();
		return $advs;
    }

    public function get_lists($sqlmap,$page,$limit){
		$position = $this->model->where($sqlmap)->page($page)->limit($limit)->select();
		foreach ($position as $k => $v) {
			$lists[] = array(
				'id' => $v['id'],
				'name'=>$v['name'],
				'type_text'=>$v['type_text'],
				'width'=>$v['width'],
				'height'=>$v['height'],
				'adv_count'=>$v['adv_count'],
				'status'=>$v['status'],
				'format_name'=>$v['format_name'],
				'adv_count' =>$v['adv_count'],
				'defaulttext'=>$v['defaulttext'],
				'type'=>$v['type'],
				'sort'=>$v['sort'],
			);
		}
			return $lists;
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

	/**
	 * [更新广告位]
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
	 * [启用禁用广告位]
	 * @param string $id 支付方式标识
	 * @return TRUE OR ERROR
	 */
	public function change_status($id) {
		$result = $this->model->where(array('id' => $id))->save(array('status' => array('exp', '1-status')));
		if ($result == 1) {
			$result = TRUE;
		} else {
			$result = $this->model->getError();
		}
		return $result;
	}
	public function lists($params = array(), $options = array()) {
		$data = array();
		$map = array();
		$map['status'] = array('EQ', 1);
		$map['id'] = array('EQ', $params['position']);

		if (isset($options['order'])) {
			$this->adv_table->order($options['order']);
		}
		if (isset($options['limit'])) {
			$this->adv_table->limit($options['limit']);
		}
		//广告位
		$data = $this->model->where($map)->find();
		//广告明细
		unset($map);
		$map['starttime'] = array("LT", time());
		$map['endtime'] = array("GT", time());
		$map['status'] = array('EQ', 1);
		$map['position_id'] = array('EQ', $params['position']);
		$data['list'] = $this->adv_table->where($map)->select();
		return dstripslashes($data);
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
}
