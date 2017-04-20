<?php
class module_goods_hook
{
	/**
	 * 系统初始化钩子
	 */
	public function pre_system() {
		// var_dump('hook:pre_system is Initialized');
	}

	/* 控制器方法之前 */
	public function pre_control() {
		// var_dump('hook:pre_control is Initialized');
	}

	/* 控制器方法之后 */
	public function post_control() {
		// var_dump('hook:post_control is Initialized');
	}

	public function pre_output(){

	}

	public function pre_response(){}

	public function pre_input(){

	}
	/* 检测站点运营状态：{站点已关闭&非后台管理员 => 跳转到提示页面} */
	public function site_isclosed() {
		$setting = model('admin/setting','service')->get();
		if ($setting['site_isclosed'] != 1) {
			$this->admin = model('admin/admin','service')->init();
			if ($this->admin['id'] == 0) {
				$SEO = seo('温馨提示');
				$message = $setting['site_closedreason'];
				exit(include TPL_PATH.'tip.tpl');
			}
		}
	}
	public function global_footer(){
		 return '<script type="text/javascript" src="'.url('admin/plan/app_update').'" ></script>';
	}
}