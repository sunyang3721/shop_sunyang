<?php
class init_control extends control
{
	public function _initialize() {
		defined('IN_PLUGIN') OR define('IN_PLUGIN', TRUE);
		parent::_initialize();
		$this->member = $this->load->service('member/member')->init();
		$this->load->librarys('View')->assign('member',$this->member);
		define('SKIN_PATH', __ROOT__.(str_replace(DOC_ROOT, '', TPL_PATH)).config('TPL_THEME').'/');
		$cloud =  unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
		define('SITE_AUTHORIZE', (int)$cloud['authorize']);
		define('COPYRIGHT', 'Powered by 孙洋论文演示'.HD_VERSION.'<br/>© 2017 Dmibox Inc.');
		/* 检测商城运营状态 */
		runhook('site_isclosed');
	}
}