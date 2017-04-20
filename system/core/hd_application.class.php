<?php
class hd_application extends hd_base {

	var $var = array();
    var $inited = false;
	static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new self();
		}
		return $object;
	}


	public function __construct() {
		$this->_init_env();
		$this->_init_config();
		$this->_init_input();
		$this->_init_execute();
		$this->_init_output();
	}
	private function _init_env() {
		if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
		define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
		define('REQUEST_METHOD',    $_SERVER['REQUEST_METHOD']);
        define('IS_GET',            REQUEST_METHOD =='GET' ? TRUE : FALSE);
        define('IS_POST',           REQUEST_METHOD =='POST' ? TRUE : FALSE);
        define('IS_PUT',            REQUEST_METHOD =='PUT' ? TRUE : FALSE);
        define('IS_DELETE',         REQUEST_METHOD =='DELETE' ? TRUE : FALSE);
		define('TIMESTAMP', time());
		$this->timezone_set(8);
		if(!defined('CORE_FUNCTION') && !@include(APP_PATH.'function/function.php')) {
			exit('function.php is missing');
		}
		if(function_exists('ini_get')) {
			$memorylimit = @ini_get('memory_limit');
			if($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
				ini_set('memory_limit', '128m');
			}
		}
		@header('Content-Type: text/html; charset='.CHARSET);
	}



    private function _init_input() {
		if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
			hd_error::system_error('_request_tainting_');
		}
		//加载钩子
		$this->_hook_register();
		$this->_xss_check();
		$_GET = input::get();
		$_POST = input::post();
		$_COOKIE = input::cookie();
		if(MAGIC_QUOTES_GPC) {
			$_GET = dstripslashes($_GET);
			$_POST = dstripslashes($_POST);
			$_COOKIE = dstripslashes($_COOKIE);
		}
		if(IS_POST && !empty($_POST)) {
			$_GET = array_merge_multi($_GET, $_POST);
		}
		if(!isset($_GET['page'])) $_GET['page'] = max(1, intval($_GET['page']));
		define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[config('VAR_AJAX_SUBMIT')]) || !empty($_GET[config('VAR_AJAX_SUBMIT')])) ? true : false);
	}

	private function _init_config() {
		if(!is_file(CONF_PATH.'config.php')) {
			hd_error::system_error('_NO_EXISTS_CONFIG_');
		}
		/* 加载系统配置 */
		config(null, 'config');
		/* 自动加载文件 */
		if(is_file(CONF_PATH.'autoload.php')) {
			include CONF_PATH.'autoload.php';
			/* 配置文件 */
			if($_autoload['config']) {
				$_autoload['config'] = explode(",", $_autoload['config']);
				foreach($_autoload['config'] AS $conf) {
					config(null, $conf);
				}
			}
			/* 语言包 */
			if($_autoload['language']) {
				$_autoload['language'] = explode(",", $_autoload['language']);
				foreach ($_autoload['language'] AS $lang) {
					lang(null, $lang);
				}
			}
			if($_autoload['hooks']) {
				foreach ($_autoload['hooks'] AS $name => $val) {
					if(!is_array($val)) $hooks = explode(",", $val);
					foreach ($hooks AS $hook) {
						hd_hook::add($name, $hook);
					}
				}
			}
		}
		if(!IS_CLI){
            session(config('SESSION_OPTIONS'));
        }
		/* 加载系统版本 */
		@include CONF_PATH.'version.php';
	}

	private function _init_execute() {
		$module_name = $this->_getModule();
        $control_name = $this->_getControl();
        $method_name = $this->_getMethod();
        define('MODULE_NAME', $module_name);
        define('CONTROL_NAME', $control_name);
        define('METHOD_NAME', $method_name);
        define('MODULE_PATH', str_replace(DOC_ROOT, '', APP_PATH.config('DEFAULT_H_LAYER').'/'.$module_name.'/'));
        runhook('pre_system');
		$control = C::load_class($control_name, $module_name, true);
		if(!$control) {
		   hd_error::system_error('_controller_not_exist_');
		}
		/* 加载模块函数 */
		if(is_file(MODULE_PATH.'function/function.php')) {
			require_cache(MODULE_PATH.'function/function.php');
		}

		try {
		    if(!preg_match('/^[A-Za-z](\w)*$/',METHOD_NAME)){
		        // 非法操作
		        throw new ReflectionException();
		    }
		    $method =   new ReflectionMethod($control, METHOD_NAME);
		    /* 只允许访问 public 权限 */
		    if($method->isPublic()) {
		        $class  =   new ReflectionClass($control);
		        runhook('pre_control');
		        // 前置操作
		        if($class->hasMethod('_before_'.METHOD_NAME)) {
		            $before =   $class->getMethod('_before_'.METHOD_NAME);
		            if($before->isPublic()) {
		                $before->invoke('_before_'.METHOD_NAME);
		            }
		        }
		        $method->invoke($control);
		        // 后置操作
		        if($class->hasMethod('_after_'.METHOD_NAME)) {
		            $after =   $class->getMethod('_after_'.METHOD_NAME);
		            if($after->isPublic()) {
		                $after->invoke('_after_'.METHOD_NAME);
		            }
		        }
		        runhook('post_control');
		    } else {
		        throw new ReflectionException();
		    }
		} catch (ReflectionException $e) {
		    // 方法调用发生异常后 引导到__call方法处理
		    $method = new ReflectionMethod($control,'__call');
		    $method->invokeArgs($control,array(METHOD_NAME,''));
		}
	}

	private function _init_output() {
		//define('CHARSET', config['output']['charset']);
        //@header('Content-Type: text/html; charset=utf-8');
	}

	public function timezone_set($timeoffset = 0) {
		if(function_exists('date_default_timezone_set')) {
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}

	private function _xss_check() {
		static $check = array('"', '>', '<', '\'', '(', ')', 'CONTENT-TRANSFER-ENCODING');
		if($_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$temp = $_SERVER['REQUEST_URI'];
		} elseif(empty ($_GET['formhash'])) {
			$temp = $_SERVER['REQUEST_URI'].file_get_contents('php://input');
		} else {
			$temp = '';
		}
		$temp = $_SERVER['REQUEST_URI'];
		if(!empty($temp)) {
			$temp = strtoupper(urldecode(urldecode($temp)));
			foreach ($check as $str) {
				if(strpos($temp, $str) !== false) {
					hd_error::system_error('_request_tainting_');
				}
			}
		}
		return true;
	}

    static private function _getModule() {
        $var = config('VAR_MODULE');
        $module   = (!empty($_GET[$var]) ? $_GET[$var] : config('DEFAULT_MODULE'));
        unset($_GET[$var]);
        return strip_tags($module);
    }

    static private function _getControl() {
        $var = config('VAR_CONTROL');
        $control = (!empty($_GET[$var]) ? $_GET[$var]: config('DEFAULT_CONTROL'));
        unset($_GET[$var]);
        return strip_tags($control);
    }

    static private function _getMethod() {
        $var = config('VAR_METHOD');
        $method   = !empty($_POST[$var]) ? $_POST[$var] : (!empty($_GET[$var])?$_GET[$var]: config('DEFAULT_METHOD'));
        unset($_POST[$var],$_GET[$var]);
        return strip_tags($method);
    }

    private function _hook_register(){
    	if(file_exists(APP_ROOT.'caches/install.lock')){
	    	$apps = $this->load->service('admin/app')->get_apps();
	    	$hooks = array();
	    	foreach ($apps as $app) {
	    		list($type, $appname) = explode('.', $app);
	    		if($type == 'module'){
	    			$path = APP_PATH.config('DEFAULT_H_LAYER').'/'.$appname;
	    		} else {
	    			$path = APP_PATH.'plugin/'.$appname;
	    		}
	    		$file = $path.'/include/hook.inc.php';

	    		if(is_file($file) && file_exists($file)) {
	    			$file_hooks = include $file;
		    		if($file_hooks){
			    		foreach ($file_hooks as $hook => $class) {
			    			$_classes = (!is_array($class)) ? explode(",", $class) : $class;
			    			$_name = array();
			    			foreach ($_classes AS $_c) {
				    			$_name[] = $type.'/'.$appname.'/'.$_c;
			    			}
			    			$hooks[$hook][] = $_name;
			    		}
		    		}
	    		}
	    	}
	    	hd_hook::add($hooks);
    	}
    }
}