<?php
class admin_group_table extends table
{
		protected $_validate = array( 
		array('title', 'require', '{admin/role_name_require}', table::MUST_VALIDATE), 
	);	
	public function get_select_data() {
		return $this->where(array("status" => 1,"id"=>array('neq',1)))->getField('id,title',true);
	}
	public function fetch_by_id($id) {
		return $this->where(array("id" => $id))->find();
	}
}