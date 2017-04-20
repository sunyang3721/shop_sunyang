<?php
class hd_core
{
	private static $_app;


	public static function app() {
		return self::$_app;
	}

	public static function run() {
		if(!is_object(self::$_app)) {
			self::$_app = hd_application::instance();
		}
		return self::$_app;
	}

	public static function handleException($exception) {
		hd_error::exception_error($exception);
	}

	public static function handleError($errno, $errstr, $errfile, $errline) {
		if($errno & APP_DEBUG) {
			hd_error::system_error($errstr, false, true, false);
		}
	}

	public static function handleShutdown() {
		if(($error = error_get_last()) && $error['type'] & APP_DEBUG) {
			hd_error::system_error($error['message'], false, true, false);
		}
	}

    public static function load_class($class, $module = '', $initialize = false) {
		static $_class = array();
		$module = (empty($module)) ? MODULE_NAME : $module;
		$class_name = $class.'_control';
		$class_dir = APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/control/';

		if(config('SUBCLASS_PREFIX') && is_file($class_dir.config('SUBCLASS_PREFIX').$class_name.EXT)) {
			$class_name = config('SUBCLASS_PREFIX').$class_name;
		}
		$class_file = $class_dir.$class_name.EXT;
		if(require_cache($class_file)) {
			$class = TRUE;
			if($initialize == TRUE && class_exists($class_name)) {
				$class = new $class_name();
			}
			$_class[$class_name] = $class;
			return $class;
		} else {
			hd_error::system_error('_class_not_exist_');
		}
		return FALSE;
	}

    public static function autoload($class) {
    	$class = strtolower($class);
    	if(strpos($class, 'hd_') === 0){
    		$path = CORE_PATH;
    	} else {
            if(strpos($class, 'control') > 1) {
            	$name = cut_str($class, strpos($class, 'control') - 1);
            	return self::load_class($name);
            } else {
    			$path = LIB_PATH;
            }
    	}
		try {
	    	$files = array(
	    		$path.'MY_'.$class.EXT,
	    		$path.$class.EXT
	    	);
	    	if(require_array($files) === false) {
	    		throw new Exception('Oops! System file lost: '.$class);
	    	}
	    	return true;
		} catch (Exception $exc) {
			$trace = $exc->getTrace();
			foreach ($trace as $log) {
				if(empty($log['class']) && $log['function'] == 'class_exists') {
					return false;
				}
			}
			hd_error::exception_error($exc);
		}
	}
}

class C extends hd_core {}