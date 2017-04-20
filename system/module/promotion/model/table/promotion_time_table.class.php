<?php
/**
 *		显示营销数据层
 */

class promotion_time_table extends table {
	protected $_validate = array(
        array('name','require','{promotion/title_require}',table::MUST_VALIDATE),
    );
}