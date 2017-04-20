<?php
/**
 *		捆绑营销数据层
 */

class promotion_group_table extends table {
	protected $_validate = array(
        array('title','require','{promotion/title_require}',table::MUST_VALIDATE),
        array('subtitle','require','{promotion/subtitle_require}',table::MUST_VALIDATE),
    );
}