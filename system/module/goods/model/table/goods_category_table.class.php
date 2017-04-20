<?php
/**
 *		商品分类数据层
 */

class goods_category_table extends table {
    protected $result;
	protected $_validate = array(
        array('name','require','{goods/classify_name_require}',table::MUST_VALIDATE),
        array('status','number','{goods/state_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
        array('sort','number','{goods/sort_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
    );
    protected $_auto = array(
    );
    public function get_fields(){
        return $this->fields['_type'];
    }
    public function detail($id,$field = TRUE){
        $exist = strpos($field, ',');
        if($exist === false && is_string($field)){
            $this->result['category'] = $this->where(array('id' => $id))->getField($field);
        }else{
            $this->result['category'] = $this->field($field)->find($id);
        }
        return $this;
    }
    public function output(){
        return $this->result['category'];
    }
}