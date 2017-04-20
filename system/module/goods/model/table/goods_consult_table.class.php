<?php
/**
 *		商品咨询数据层
 */

class goods_consult_table extends table {
	protected $_validate = array(
        // array('name','require','{GOODS_CATEGORY_NAME_REQUIRE}',table::MUST_VALIDATE),
        // array('status','number','{STATUS_TYPE_ERROR}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
        // array('sort','number','{SORT_TYPE_ERROR}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
    );
    protected $_auto = array(
	
	
    );
}