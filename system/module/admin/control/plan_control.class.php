<?php
hd_core::load_class('init', 'goods');
class plan_control extends init_control {
	public function __construct() {
		$this->app_service = $this->load->service('admin/app');
		$this->cloud_service = $this->load->service('admin/cloud');
	}
	/**
	 * [update description]
	 * @return [type] [description]
	 */
	public function app_update(){
		if(!cache('app_lock')){
			cache('app_lock',TIMESTAMP,'common',array('expire' => 7200));
			$this->app_service->get_plugins();
			$this->load->service('admin/module')->build_cache();
		}
		return true;
	}
	/**
	 * [synchro_lists description]
	 * @return [type] [description]
	 */
	public function synchro_lists(){
		$this->app_service->synchro_lists();
		return true;
	}
	/**
	 * [update description]
	 * @return [type] [description]
	 */
	public function cloud_update(){
		if(!cache('cloud_lock')){
            cache('cloud_lock',TIMESTAMP,'common',array('expire' => 7200));
			$r = $this->cloud_service->update_site_userinfo();
        }
		return TRUE;
	}

	/**
	 * 更新商品促销状态
	 */
	public function update_status(){
		$this->load->service('promotion/promotion_goods')->fetch_skuid_by_goods();
		$this->load->service('promotion/promotion_time')->fetch_skuid_by_timeed();
		return TRUE;
	}
}