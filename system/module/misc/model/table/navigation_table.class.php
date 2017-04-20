<?php
/**
 *	    友情链接数据层
 */

class navigation_table extends table {
    protected $_validate = array(
        array('name','require','{misc/article_name_require}',0),
		array('sort','number','{misc/sort_require}',2),
    );

    protected $_auto = array(
    	
    );
}