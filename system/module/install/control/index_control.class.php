<?php
define('INIT_INSTALL', TRUE);
require_cache(MODULE_PATH.'library/checkconfig.php');
class index_control extends control {
	public function _initialize() {
		parent::_initialize();
		if(file_exists(APP_ROOT.'caches/install.lock')){
			die('您已安装过云商系统，请勿重复执行安装操作！');
		}
	}

	public function index(){
		include template('index');
	}

	public function setup1(){
		include template('setup1');
	}
	public function setup2(){
		include template('setup2');
	}
	public function setup3(){
		include template('setup3');
	}
	public function setup4(){
		include template('setup4');
	}
	public function ajax_check_mysql(){
		check_mysql();
	}
	public function clear_cache(){
		//更新缓存
		$caches = array('setting', 'module', 'plugin', 'template', 'taglib', 'temp', 'extra');
		foreach($caches as $k=>$v){
			$this->load->service('admin/cache')->$v();
		}
	}
}