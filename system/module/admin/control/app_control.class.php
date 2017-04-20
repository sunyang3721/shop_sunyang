<?php
defined('IN_PLUGIN') OR define('IN_PLUGIN', TRUE);
class app_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->app_db = $this->load->table('admin/app');
		$this->app_service = $this->load->service('admin/app');
		$this->plugin_path = APP_PATH.'plugin/';
	}
	/**
	 * [index 插件列表]
	 * @return [type] [description]
	 */
	public function plugin_index() {
		$_GET['status'] = isset($_GET['status']) ? $_GET['status'] : 1;
		$apps = $this->app_service->lists($_GET['status'],'plugin');
		$limit = $_GET['limit'] ? $_GET['limit'] : 20;
		$start = ($_GET['page']-1) * $limit;
		$lists = array_slice($apps,$start,$limit);
		$count = count($apps);
		$pages = $this->admin_pages($count, $limit);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('plugin_index');
	}
	/**
	 * [module_index 模块列表]
	 * @return [type] [description]
	 */
	public function module_index() {
		$_GET['status'] = isset($_GET['status']) ? $_GET['status'] : 1;
		$modules = $this->app_service->lists($_GET['status'],'module');
		$limit = $_GET['limit'] ? $_GET['limit'] : 20;
		$start = ($_GET['page']-1) * $limit;
		$lists = array_slice($modules,$start,$limit);
		$count = count($modules);
		$pages = $this->admin_pages($count, $limit);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('module_index');
	}
	/**
	 * [module 插件管理]
	 * @return [type] [description]
	 */
	public function module(){
		$method = explode(':',$_GET['mod']);
		$mod = $method[1] ? $method[1] : $method[0];
		$plugin = $this->app_db->where(array('identifier' => 'plugin.'.$method[0]))->find();
		$plugin['identifier'] = str_replace('plugin.','',$plugin['identifier']);
		$plugin['menu'] = unserialize($plugin['menu']);
		$nav_title = '插件配置';
		foreach ($plugin['menu'] as $v) {
			if(is_numeric($v['type']) && $v['name'] == $mod) {
				$nav_title = $v['menu'];
				break;
			}
		}
		if(!$plugin) showmessage('插件不存在');
		if(!$mod) showmessage('参数错误');
		$plugin_folder = $this->plugin_path.$plugin['identifier'];
		define('PLUGIN_ID', $plugin['identifier']);
		$modfile = $plugin_folder.'/'.$mod.'.inc.php';
		if(!file_exists($modfile)) {
			$this->error = '模块文件不存在';
			return false;
		}
		include $modfile;
	}
	/**
	 * [install 插件安装]
	 * @return [type] [description]
	 */
	public function install(){
		$result = $this->app_service->_install($_GET['identifier'],$_GET['type']);
		if($result){
			showmessage('安装成功！');
		}else{
			showmessage($this->app_service->error);
		}
	}
	/**
	 * [upgrade 升级]
	 * @return [type] [description]
	 */
	public function upgrade(){
		$result = $this->app_service->upgrade_app($_GET['identifier'],$_GET['type']);
		if($result){
			showmessage('更新成功！');
		}else{
			showmessage($this->app_service->error);
		}
	}
	/**
	 * [uninstall 卸载插件]
	 * @return [type] [description]
	 */
	public function uninstall(){
		$result = $this->app_service->uninstall($_GET['identifier'],$_GET['type']);
		if($result){
			showmessage('卸载成功！');
		}else{
			showmessage($this->app_service->error);
		}
	}
	/**
	 * [ajax_status 获取更新状态]
	 * @return [type] [description]
	 */
	public function ajax_upgrade(){
		$lists = $this->app_service->ajax_upgrade($_GET);
		if($lists){
			showmessage('获取成功','',1,$lists);
		}else{
			showmessage('获取失败','',0);
		}
	}
	/**
	 * [available 更改插件状态]
	 * @return [type] [description]
	 */
	public function available() {
		$result = $this->app_service->available($_GET['identifier'],$_GET['type']);
		if(!$result){
			showmessage($this->app_service->error);
		}
		showmessage('操作成功');
	}
	/**
	 * [app_develop 插件设计]
	 * @return [type] [description]
	 */
	public function app_develop(){
		if((int)$_GET['id'] > 0){
			$rs = $this->app_db->where(array('id' => $_GET['id']))->find();
			if(!$rs || strpos($rs['identifier'],'plugin.') === false){
				showmessage('插件不存在');
			}
			if($rs['branch_id'] > 0){
				showmessage('无法设计该插件');
			}
			$rs['menu'] = unserialize($rs['menu']);
			$rs['identifier'] = str_replace('plugin.', '', $rs['identifier']);
			$vars = $this->load->table('admin/appvar')->where(array('appid' => $_GET['id']))->order('displayorder ASC,id ASC')->select();
		}
		if(checksubmit('dosubmit')){
			$result = $this->app_service->develop($_GET);
			if(!$result){
				showmessage($this->app_service->error);
			}
			showmessage('操作成功');
		}else{
			$nodes = model('node')->where(array('parent_id' => '0', 'id' => array("NEQ", 8)))->order("sort ASC, id ASC")->select();
			$this->load->librarys('View')->assign('nodes',$nodes)->assign('rs',$rs)->assign('vars',$vars)->display('app_develop');
		}
	}
	/**
	 * [export 插件导出]
	 * @return [type] [description]
	 */
	public function export(){
		$result = $this->app_service->export($_GET['id']);
		echo $result;
	}
	/**
	 * [setting 插件设置]
	 * @return [type] [description]
	 */
	public function setting(){
		if(empty($_GET['id'])) {
			showmessage('插件不存在');
		}
		$plugin = $this->app_db->find($_GET['id']);
		if(!$plugin) {
			showmessage('插件不存在');
		}
		$plugin['identifier'] = str_replace('plugin.', '', $plugin['identifier']);
		$vars = model('admin/appvar')->where(array('appid' => $_GET['id']))->order("displayorder ASC, id ASC")->select();
		if(empty($vars) && $plugin['url']){
			redirect($plugin['url']);
		}
		if(checksubmit('dosubmit')) {
			$result = $this->app_service->setting($_GET);
			showmessage('插件设置成功', url('setting', array('id' => $_GET['id'])));
		} else {
			$this->load->librarys('View')->assign('plugin',$plugin)->assign('vars',$vars)->display('plugin_setting');
		}
	}
}