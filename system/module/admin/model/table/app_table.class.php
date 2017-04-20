<?php
class app_table extends table{
	protected $_validate = array(
		array('identifier', 'require', '{admin/plugin_id_require}', table::MUST_VALIDATE),
		array('identifier','','{admin/plugin_id_not_unique}',table::MUST_VALIDATE,'unique'),
		array('name', 'require', '{admin/plugin_name_require}', table::MUST_VALIDATE),
		array('name','','{admin/plugin_name_not_unique}',table::MUST_VALIDATE,'unique'),
	);
	public function fetch_by_identifier($identifier) {
		return $this->where(array('identifier' => $identifier))->find();
	}
}