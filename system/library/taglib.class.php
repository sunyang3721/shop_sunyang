<?php
class taglib
{
	public function __construct($module, $tagfile) {
		$this->getInstance($module, $tagfile);
	}

	public function getInstance($module, $tagfile = '') {
		$tagfile = (empty($tagfile)) ? $module : $tagfile;
		$tagfile = 'taglib_'.$tagfile;
		$files = array (
			APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/taglib/'.$tagfile.EXT,
			LIB_PATH.'driver/taglib/'.$tagfile.EXT
		);
		if(require_array($files, TRUE)) {
			if(class_exists($tagfile)) {
				$this->instance = new $tagfile();
				return $this;
			}
		}
		return FALSE;
	}

	public function __get($name) {
		return (isset($this->instance->$name)) ? $this->instance->$name : NULL;
	}

	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name)) {
			return call_user_func_array(array(& $this, $method_name), $method_args);
		} elseif (!empty($this->instance) && method_exists($this->instance, $method_name)) {
            return call_user_func_array(array(& $this->instance, $method_name), $method_args);
		} else {
			return false;
		}
	}
}