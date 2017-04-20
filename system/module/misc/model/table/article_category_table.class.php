<?php
/**
 *	    文章分类数据层
 */

class article_category_table extends table {
    protected $_validate = array(
        array('name','require','{misc/classkit_name_require}',0),
		array('parent_id','number','{misc/parent_class_not_exist}',2),
		array('sort','number','{misc/sort_require}',2),
    );
    protected $_auto = array(
    	
    ); 
}