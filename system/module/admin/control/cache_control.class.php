<?php
class cache_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('admin/cache');
	}

    /**
     * 更新缓存
     * 1、站点缓存
     * 2、模块缓存
     * 3、插件缓存
     * 4、模板缓存
     * 5、标签缓存
     * 6、临时文件
     * 7、自定义缓存
     */
	public function clear() {
		$caches = array(
			'setting' => '设置',
			'module' => '模块',
			'plugin' => '插件',
			'template' => '模板',
			'taglib' => '标签',
			'field' => '字段',
			'temp' => '临时文件',
			'extra' => '拓展',
		    'delgoods'=>'商品'
		);
		
        $step = intval($_GET['step']);
		$keys = array_keys($caches);
		$cache = $keys[$step];
        if($step >= count($caches)) {
	        showmessage(lang('cache_upload_success','admin/language'), "null", 1);
        }
        if(!isset($caches[$cache])) {
	        showmessage(lang('_param_error_'));
        }
        $this->service->$cache();
        showmessage($caches[$cache].'更新完成，请稍后...', url('clear', array('step' => $step + 1)), 1);
	}
}