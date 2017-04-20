<?php
/**
 *	    文章数据层
 */

class help_table extends table {
    protected $_validate = array(
        array('title','require','{misc/article_name_require}',0),
		array('fpid','require','{misc/article_classify_require}',0),
		array('sort','number','{misc/sort_require}',2),
    );
    protected $_auto = array(
    	
    );
}