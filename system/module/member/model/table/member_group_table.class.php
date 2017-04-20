<?php
class member_group_table extends table {
    protected $_validate = array(
        array('name', 'require', '{member_group_name_require}'),
        array('min_points', 'number', '{member_group_name_min_points}'),
        array('max_points', 'number', '{member_group_name_max_points}'),
        array('discount', 'number', '{member_group_name_discount}'),
    );


    /**
     * 查询单个信息
     * @param int $id 主键ID
     * @param string $field 被查询字段
     * @return mixed
     */
    public function fetch_by_id($id, $field = NULL) {
        $r = $this->find($id);
        if(!$r) return FALSE;
        return ($field !== NULL) ? $r[$field] : $r;
    }

    public function _after_select($result, $options = array()) {
	    $groups = array();
	    foreach( $result as $k => $v) {
	    	$groups[$v['id']] = $v;
	    }
	    return $groups;
    }
}
