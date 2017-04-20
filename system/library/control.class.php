<?php
class control extends hd_base
{
    
    protected $assign;
    public function __construct() {
		if(!is_file(APP_ROOT."\x63\x61\x63\x68\x65\x73\x2f\x69\x6e\x73\x74\x61\x6c\x6c\x2e\x6c\x6f\x63\x6b") && is_dir(APP_PATH."\x6d\x6f\x64\x75\x6c\x65\x2f\x69\x6e\x73\x74\x61\x6c\x6c") && MODULE_NAME != "\x69\x6e\x73\x74\x61\x6c\x6c"){
			header("\x6c\x6f\x63\x61\x74\x69\x6f\x6e\x3a\x69\x6e\x64\x65\x78\x2e\x70\x68\x70\x3f\x6d\x3d\x69\x6e\x73\x74\x61\x6c\x6c");
			exit();
        }
        if(method_exists($this,'_initialize')) $this->_initialize();
    }

    public function _initialize() {}

    public function __call($method,$args) {
        if(0 === strcasecmp($method,METHOD_NAME)) {
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$args);
            }elseif(function_exists('__hack_action')) {
                __hack_action();
            }else{
                send_http_status('404');
            }
        }else{
            hd_log::record(__CLASS__.':'.$method.lang('_method_not_exist_'));
            return;
        }
    }
   /**
     * 析构方法
     * @access public
     */
    public function __destruct() {}
}