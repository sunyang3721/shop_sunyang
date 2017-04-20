<?php
define('IN_APP', true);
defined('APP_ROOT') 	OR 		define('APP_ROOT', str_replace("\\","/",substr(dirname(__FILE__), 0, -6)));
defined('LIB_PATH') 	OR 		define('LIB_PATH',  APP_PATH.'library/');
defined('CORE_PATH') 	OR 		define('CORE_PATH',  APP_PATH.'core/');
defined('CONF_PATH')    OR 		define('CONF_PATH',  DOC_ROOT.'config/');
defined('CACHE_PATH') 	OR 		define('CACHE_PATH',  DOC_ROOT.'caches/');
defined('TPL_PATH') 	OR 		define('TPL_PATH',  DOC_ROOT.'template/');
defined('LANG_PATH') 	OR 		define('LANG_PATH',  APP_PATH.'language/');

defined('APP_DEBUG') 	OR 		define('APP_DEBUG', false);
/* 入口文件 */
defined('__APP__') 	    OR 		define('__APP__', $_SERVER['SCRIPT_NAME']);
/* 安装目录 */
define('__ROOT__', str_replace(basename(__APP__), "", __APP__));
/* 插件目录 */
defined('PLUGIN_PATH') 	OR 		define('PLUGIN_PATH',  APP_PATH.'plugin/');

define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
define('EXT', '.class.php');

require CORE_PATH.'hd_core'.EXT;
// require CORE_PATH.'hd_load'.EXT;
require APP_PATH.'function/function.php';

set_exception_handler(array('C', 'handleException'));
set_error_handler(array('C', 'handleError'));
register_shutdown_function(array('C', 'handleShutdown'));

if(function_exists('spl_autoload_register')) {
	spl_autoload_register(array('C', 'autoload'));
} else {
	function __autoload($class) {
		return C::autoload($class);
	}
}
C::run();
