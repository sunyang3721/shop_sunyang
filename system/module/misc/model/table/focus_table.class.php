<?php
/**
 *	    友情链接数据层
 */

class focus_table extends table {
    protected $_validate = array(
        array('title','require','{misc/title_require}',0),
		array('sort','number','{misc/sort_require}',2),
    );
}