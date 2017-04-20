<?php
class taglib_type
{
	public function __construct() {
        $this->service = model('goods/type', 'service');
        $this->type_db = model('goods/type');
        $this->cate_db = model('goods/goods_category');
        $this->spec_db = model('goods/spec');
	}
	public function lists($sqlmap = array(), $options = array()) {
        return $this->service->lists($sqlmap, $options);
    }

    public function specs($sqlmap = array(), $options = array()) {
        $type_id = $this->cate_db ->where(array('id'=>$sqlmap['catid']))->getfield('type_id');
        $content = $this->type_db ->where(array('id'=>$type_id))->getfield('content');
        $_attrs_info = json_decode($content,TRUE);
        $_specs = $_attrs_info['spec'];
        foreach ($_specs as $k =>$_spec) {
             $result['spec_'.$_spec] = $this->spec_db->where(array('id'=>$_spec))->field('id,name,value')->find();
             $result['spec_'.$_spec]['value'] = explode(',',$result['spec_'.$_spec]['value']);
        }
        return $result;
    }
}