<?php

/* 除非您特别了解以下配置项，否则不建议更改任何代码 */
return array(
    /* 默认设定 */
    'DEFAULT_H_LAYER'  =>  'module',
    'DEFAULT_M_LAYER'  =>  'model', // 默认的模型层名称
    'DEFAULT_C_LAYER'  =>  'control', // 默认的控制器层名称
    'DEFAULT_V_LAYER'  =>  'template', // 默认的视图层名称
    'DEFAULT_LANG'     =>  'zh-cn', // 默认语言
    'DEFAULT_THEME'    =>  'default',	// 默认模板主题名称
    'DEFAULT_MODULE'   =>  'goods',  // 默认模块
    'DEFAULT_CONTROL'  =>  'index', // 默认控制器名称
    'DEFAULT_METHOD'   =>  'index', // 默认操作名称
    'DEFAULT_CHARSET'  =>  'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE' =>  'PRC',	// 默认时区

    /* 系统变量名称设置 */
    'VAR_MODULE'        =>  'm',     // 默认模块获取变量
    'VAR_CONTROL'       =>  'c',    // 默认控制器获取变量
    'VAR_METHOD'        =>  'a',    // 默认操作获取变量
    'VAR_AJAX_SUBMIT'   =>  'inajax',  // 默认的AJAX提交变量
    'VAR_JSONP_HANDLER' =>  'callback',
    'VAR_TEMPLATE'      =>  't',    // 默认模板切换变量
    'VAR_LANG'          =>  'l',    // 默认语言切换变量
    'VAR_AUTO_STRING'   =>	false,	// 输入变量是否自动强制转换为字符串 如果开启则数组变量需要手动传入变量修饰符获取变量

	/* 数据库配置 */
	'DB_TYPE'   =>'mysql',
	'DB_HOST'   =>'192.168.1.100',
	'DB_PORT'   =>'3306',
	'DB_NAME'   =>'',
	'DB_USER'   =>'',
	'DB_PWD'    =>'',
	'DB_PREFIX' =>'',
	'DB_CHARSET' => 'utf8',
	'DB_FIELDS_CACHE' => TRUE,

    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => false,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => '',     // 缓存前缀
    'DATA_CACHE_TYPE'       => 'file',  // 数据缓存类型,支持:file|db|apc|memcache|shmop|sqlite|xcache|apachenote|eaccelerator
    'DATA_CACHE_PATH'       => CACHE_PATH,// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'     => false,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       => 1,        // 子目录缓存级别

    'AUTHKEY'               => 'fP5a_',

    /* Cookie设置 */
    'COOKIE_EXPIRE'         => 0,    // Coodie有效期
    'COOKIE_DOMAIN'         => '',      // Cookie有效域名
    'COOKIE_PATH'           => '/',     // Cookie路径
    'COOKIE_PREFIX'         => 'fP5a_',      // Cookie前缀 避免冲突

    /* SESSION设置 */
    'SESSION_AUTO_START'    => true,    // 是否自动开启Session
    'SESSION_OPTIONS'       => array(), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE'          => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX'        => 'fP5a_', // session 前缀
    //'VAR_SESSION_ID'      => 'session_id',     //sessionID的提交变量


    /* 模板相关配置 */
    'TMPL_CACHE_ON' => TRUE, // 开启模板缓存
    'TMPL_CACHE_COMPARE'    => 0, //缓存时间
    'TMPL_TEMPLATE_SUFFIX'  => '.html', //模板后缀



    /*自定义类前缀*/
    'SUBCLASS_PREFIX'   => 'MY_',


    'OUTPUT_ENCODE'      =>  false, // 页面压缩输出
    'HTTP_CACHE_CONTROL' =>  'private', // 网页缓存控制

);