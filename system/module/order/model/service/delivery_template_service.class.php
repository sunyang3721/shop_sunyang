<?php
/**
 * 		物流服务层
 */
class delivery_template_service extends service {

	public function _initialize() {
		$this->table 			= $this->load->table('order/delivery_template');
		$this->district_table 	= $this->load->table('admin/district');
	}

	/**
	 * [get_lists 获取列表]
	 * @param  array   $sqlmap [sql条件]
	 * @param  integer $page   [页数]
	 * @param  integer $limit  [条数]
	 * @param  string  $order  [排序]
	 * @return [type]          [description]
	 */
	public function get_lists($sqlmap = array(), $page = 1, $limit = 10, $order = 'sort ASC', $field = ''){
		$result = $this->table->page($page)->order($order)->limit($limit)->where($sqlmap)->field($field)->select();
		if ($result === false) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		foreach ($result as $k => $v) {
			switch ($v['type']) {
				case 'weight':
					$result[$k]['type'] = '按重量';
					break;
				case 'number':
					$result[$k]['type'] = '按件数';
					break;
				case 'volume':
					$result[$k]['type'] = '按体积';
					break;
			}			
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

	/**
	 * [添加|编辑]运费模板
	 * @param array $params - 运费模板相关参数 (必传参数)
					$params[enabled] - 是否开启运费模板(int ,1 开启 0 不开启)
					$params[name] - 运费模板名称
					$params[type] - 计费类型
					$params[template] - 运费模板
	 * @return [boolean]
	 */
	public function update($params = array()) {
		unset($params['formhash']);
		unset($params['dosubmit']);
		unset($params['page']);
		// 数据校验
		if(empty($params)){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if(empty($params['name'])){
			$this->error = lang('name_empty','order/language');
			return FALSE;
		}
		if(empty($params['type'])){
			$this->error = lang('template_type_error','order/language');
			return FALSE;
		}
		if(empty($params['template'])){
			$this->error = lang('template_empty','order/language');
			return FALSE;
		}

		/* 如果没有收货地址则设为默认 */
		if($this->count() == 0) {
			$params['isdefault'] = 1;
		}

		//整理template
		$deliverys = array();
		foreach ($params['template'] as $k => $v) {
			$delivery = array();
			if(in_array(substr($k, 0, 4), array('news', 'edit'))){
				$delivery['template']['first_value'] = $v['first_value'];
				$delivery['template']['first_fee'] = $v['first_fee'];
				$delivery['template']['follow_value'] = $v['follow_value'];
				$delivery['template']['follow_fee'] = $v['follow_fee'];
				$delivery['district_ids'] = $v['district_ids'];
				$deliverys[] = $delivery;
			}
		}
		unset($params['template']);
		$params['delivery_info'] = json_encode($deliverys);
		$result = $this->table->update($params);
		if ($result === FALSE) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		return $result;
	}

	/**
	 * 根据运费模板ID获取物流信息
	 * @param int $id 运费模板主键ID
	 * @return [result]
	 */
	public function get_by_id($id = 0) {
		if ((int)$id < 1) {
			$this->error = lang('logistics_id_require','order/language');
			return FALSE;
		}
		$result = $this->table->where(array('id' => $id))->find();
		if($rseult === FALSE){
			$this->error = $this->table->getError();
			return FALSE;
		}

		// 处理template
		$delivery_info = json_decode($result['delivery_info'], true);
		$templates =array();
		foreach ($delivery_info as $k => $v) {
			$template =array();
			$template['first_value'] 	= $v['template']['first_value'];
			$template['first_fee'] 		= $v['template']['first_fee'];
			$template['follow_value'] 	= $v['template']['follow_value'];
			$template['follow_fee'] 	= $v['template']['follow_fee'];
			$district_names =array();
			foreach (explode(',', $v['district_ids']) as $key => $value) {
				$district_names[] = $this->district_table->where(array('id' => $value))->getField('name');
			}
			$template['district_ids'] 	= $v['district_ids'];
			$template['district_names'] 	= $district_names;

			// 获取物流地区配置
			$templates[] = $template;
		}
		$result['_templates'] = $templates;
		return $result;
	}

	/**
	 * 根据运费模板ID更改字段值
	 * @param int 	 $id 运费模板ID(必传)
	 * @param string $field 字段名(必传)
	 * @param string $val 要更改的值(必传)
	 * @return [boolean]
	 */
	public function update_field_by_id($id ,$field ,$val) {
		$id    = (int) $id;
		$field = (string) remove_xss($field);
		$val   = (string) remove_xss($val);
		if ($id < 1) {
			$this->error = lang('logistics_id_empty','order/language');
			return FALSE;
		}
		if ($field == '') {
			$this->error = lang('edit_field_empty','order/language');
			return FALSE;
		}
		if ($val == '') {
			$this->error = lang('edit_value_empty','order/language');
			return FALSE;
		}
		$delivery = $this->get_by_id($id);
		if (!$delivery) {
			$this->error = lang('logistics_not_exist','order/language');
			return FALSE;
		}
		$data          = array();
		$data['id']    = $id;
		$data[$field]  = $val;
		return $this->table->update($data);
	}

	/**
	 * 删除运费模板(支持批量操作)
	 * @param array	 $ids 运费模板主键ids (必传)
	 * @return [boolean]
	 */
	public function deletes($ids = array()) {
		if (!is_array($ids)) {
			$this->error = lang('delete_parame_error','order/language');
			return FALSE;
		}

		// 判断是否存在默认运费模板
		if($this->table->where(array('id' => array("IN", $ids), 'isdefault' => 1))->find()){
			$this->error = lang('delivery_default_cannot_delete','order/language');
			return FALSE;
		}
		
		$_map = array();
		$_map['id'] = array("IN", $ids);
		$result = $this->table->where($_map)->delete();
		if ($result === false) {
			$this->error = lang('delete_logistics_error','order/language');
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * [default 设为默认]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function set_default($id) {
		if ((int)$id < 1) {
			$this->error = lang('delete_parame_error','order/language');
			return FALSE;
		}
		$this->table->where(array("id" => $id))->setField('isdefault', 1);
		$this->table->where(array("id" => array("NEQ", $id)))->setField('isdefault', 0);
		return TRUE;
	}

	public function getField($sqlmap = array(), $field = '', $flag = false){
		return $this->table->where($sqlmap)->getField($field, $flag);
	}
}