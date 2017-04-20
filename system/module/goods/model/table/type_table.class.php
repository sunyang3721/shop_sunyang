<?php
/**
 *		商品类型数据层
 */

class type_table extends table {
	protected $_validate = array(
        array('name','require','{goods/goods_type_name_require}',table::MUST_VALIDATE),
        array('status','number','{goods/state_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
        array('sort','number','{goods/sort_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
    );
    protected $_auto = array(
    );
}