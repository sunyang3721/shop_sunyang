<?php
class upgrade_control extends init_control
{
	private $cloud_config;
	public function _initialize() {
		parent::_initialize();
		$this->service = model('admin/cloud','service');
		$this->cloud_config = $this->service->get_account_info();
		if(FALSE === $this->cloud_config) showmessage(lang('no_bind_site','admin/language'),url('admin/cloud/index'));
		if(FALSE === $this->service->getcloudstatus()) showmessage(lang('cloud_communication_error','admin/language'),url('index'));
		$this->update_path = CACHE_PATH.'uppack/';
		$this->update_back_path = CACHE_PATH.'upback/';
	}

	public function index() {
		$load = hd_load::getInstance();
		$load->librarys('View')->display('upgrade_index');
	}

	public function lists() {
		$load = hd_load::getInstance();
		$r = cache('version_detail');
		$load->librarys('View')->assign('r',$r)->display('upgrade_lists');
	}

	public function upgrade() {
		$this->init_uppack();	
		$version = '';
		extract($_GET,EXTR_IF_EXISTS);
		//取要升级的版本
		$upversion = array();
		foreach(cache('version_detail') as $k=>$v){
			if(version_compare($version, $v['version'])>=0){
				$upversion[$v['version']] = $v;
			}
		}
		cache('version_uppack',$upversion);
		showmessage(lang('start_upload','admin/language'),url('upgrade_setp',array('setp'=>0)));
		
	}
	
	public function upgrade_setp(){
		$setp = 0;
		extract($_GET,EXTR_IF_EXISTS);
		$caches = cache('version_uppack');
		$caches = array_values($caches);
		if (!array_key_exists($setp,$caches)){
			cache('version_detail',null);
			cache('version_uppack',null);
			showmessage(lang('versions_upload_success','admin/language'), url('index'), 1);
		}
		$this->downpack($caches[$setp]['version']);
		showmessage(lang('versions_uploading','admin/language').$caches[$setp]['version'].' ，请稍候...', url('upgrade_setp', array('setp' => $setp + 1)), 1);
	}
	
	//获取最新版本
	public function ajax_checkupgrade(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$r = $this->service->api_product_version();
		if($r['result']!=false)$r['url']=url('admin/upgrade/lists');
		cache('version_detail',$r['result']);
		echo json_encode($r);
	}
	//下载文件
	public function downpack($version){
		
		$file = $this -> update_path.random(8).'.zip';
		
		$down = $this->service->api_product_downpack();
		
		$ch = curl_init();
		//Initialize a cURL session.
		$fp = fopen($file, 'wb');
		curl_setopt($ch, CURLOPT_URL, $down['url']);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
		//curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 64000);
		curl_setopt($ch, CURLOPT_POST, FALSE); // post传输数据
		curl_setopt($ch, CURLOPT_POSTFIELDS,$down['params']);// post传输数据
		$res = curl_exec($ch);
		if (curl_errno($ch)){
			die(curl_error($ch));
		}else{
			curl_close($ch);
		}
		fclose($fp);
		$this->expfile($file, $version);
	}
	//执行升级
	public function expfile($file, $version) {
		$_version = cache('version_uppack');
		$md5file = md5_file($file);
		if($md5file != $_version[$version]['md5']){
			dir::del($this->update_path);
			showmessage(lang('download_again','admin/language'),url('index'),0);
		}
		$load = hd_load::getInstance();
		$archive = $load->librarys('pclzip',$file);
		//读取XML指定文件内容
		if(!is_dir(CACHE_PATH.'upgrade')) mkdir(CACHE_PATH.'upgrade');
		$upgrade_path = CACHE_PATH.'upgrade';
		$result = $archive->extract(PCLZIP_OPT_PATH, $upgrade_path, PCLZIP_OPT_REPLACE_NEWER);
		//读取成功
		if(!$result){
			showmessage('更新失败，请开启caches文件夹权限',url('index'),0);
		}

		$importtxt = @implode('', file($upgrade_path.'/upgrade.xml'));
		$xml_info = xml2array($importtxt);
		deldir($upgrade_path);
		
		$back_file = array_merge_recursive(
			(array)$xml_info['hd_files']['create'],
			(array)$xml_info['hd_files']['update'],
			(array)$xml_info['hd_files']['delete']
		);
		$back_file = array_unique($back_file);
		
		$back_dir = $this->update_back_path.$version.'/';
		if (!is_dir($back_dir)) dir::create($back_dir, 0777, true);
		//备份文件
		foreach ($back_file as $k => $v) {
			$file = $v;
			$_dir = $back_dir.dirname($file).'/';
			if (!is_dir($_dir)) dir::create($_dir, 0777, true);
			@copy($file,$_dir.basename($file));
		}
		//删除文件
		foreach ($xml_info['hd_files']['delete'] as $k => $v) {
			@unlink($v);
		}

		if ($archive -> extract(PCLZIP_OPT_PATH, './', PCLZIP_OPT_REPLACE_NEWER) == 0) {
			exit(showmessage(lang('upload_file_no_exist','admin/language'),'null',0));
		} else {
			$sqlfile = APP_ROOT . 'update.sql';
			$sql = file_get_contents($sqlfile);
			if ($sql) {
				$sql = str_replace("hd_", config('DB_PREFIX'), $sql);
				error_reporting(0);
				foreach (split(";[\r\n]+", $sql) as $v) {
					@mysql_query($v);
				}
			}
			@unlink($sqlfile);
			//删除文件
			$updatefile = $this -> DOC_ROOT . 'update.php';
			if (file_exists($updatefile)){
				require($updatefile);
			}
			@unlink($updatefile);
			$this->update_config($version);
		}
	}
	//写入配置文件
	public function update_config($version){
		$_version = cache('version_uppack');
		$versiondata =  <<<EOF
<?php
if(!defined('HD_VERSION')) {
	define('HD_VERSION', '{$version}');
	define('HD_RELEASE', '{$_version[$version][release]}');
	define('HD_BRANCH', '{$_version[$version][branch]}');
	define('HD_FIXBUG', '{$_version[$version][fixbug]}');
}
EOF;
		file_put_contents(CONF_PATH.'version.php',$versiondata);
		$this->clcache();
		
	}
	//更新缓存
	public function clcache(){
		$caches = array('setting', 'module', 'plugin', 'template', 'taglib', 'temp', 'extra');
		foreach($caches as $k=>$v){
			try{
 				model('admin/cache','service')->$v();
 			}catch(Exception $e){
 				showmessage($e->getMessage(),'null',0);
			}
		}
		dir::del($this->update_path);
		model('admin/cloud','service')->update_site_userinfo();
	}
	//初始更新
	public function init_uppack(){
		if (!is_dir($this->update_path)){
			dir::create($this->update_path, 0777, true);
		}
		if(!is_writable(CACHE_PATH)){
			showmessage(CACHE_PATH.'目录不可写,请重新设置权限','null',0);
		}
		//检查是否有正在执行的任务
		$lock = "{$this->update_path}backup.lock";
		if(is_file($lock)){
			showmessage( $lock.'检测到有一个升级任务正在执行，请手动删除此文件后重试！','null',0);
			exit();
		} else {
			//创建锁文件
			file_put_contents($lock, time());
		}
	}
}