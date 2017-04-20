<?php
class cache_service extends service
{
	/**
	 * 更新设置
	 */
	public function setting(){
		return cache('setting',NULL);
	}
	/* 更新模块 */
	public function module() {
		return cache('module', NULL);
	}

	public function plugin() {
		return true;
	}

	/* 更新模板缓存 */
	public function template() {
		return dir::delDir(CACHE_PATH.'view/');
	}

	public function taglib() {
		return dir::delDir(CACHE_PATH.'taglib/');
	}

	public function temp() {
		return dir::delDir(CACHE_PATH.'temp/');
	}

	public function field() {
		return dir::delDir(CACHE_PATH.'common/fields/');
	}
    public function delgoods(){
        return dir::delDir(CACHE_PATH.'goods/');
    }
	public function extra() {
		/* 读取缓存 */
		$modules = model('admin/app','service')->get_module();
		if(!$modules) return;
		foreach($modules as $module => $name) {
			$file = APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/include/cache.inc.php';
			if(is_file($file)) @include $file;
		}
		return true;
	}
}