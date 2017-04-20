<?php
class setting_service extends service {
	public function _initialize() {
		$this->db = $this->load->table('admin/setting');
	}

	/**
	 * 编辑
	 * @param array 	$params 内容
	 * @return [boolean]
	 */
	public function update($params){
		$settings = $this->get();
		runhook('setting_update',$params);
		foreach ($params as $key => $value) {
			if (is_array($value)) $value = serialize($value);
			if (isset($settings[$key]) === true) {
				$this->db->where(array('key' => $key))->save(array('value' => $value));
			} else {
				$this->db->add(array('key' => $key ,'value' => $value));
			}
		}
		cache('setting',NULL);
		return TRUE;
	}


	/**
	 * 获取设置缓存
	 */
	public function get($key = NULL){
		$setting = $this->load->table('setting')->cache('setting',3600)->select();
		return is_string($key) ? $setting[$key] : $setting;
	}
}