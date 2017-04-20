<?php
class database_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->backup_path = CACHE_PATH.'dbbak'.DIRECTORY_SEPARATOR;
	}

	/* 数据库管理 */
	public function index() {
		$dialog='';
		$type = isset($_GET['type'])?$_GET['type']:'export';
		switch ($type) {
			/* 数据还原 */
			case 'import':
				require_once(MODULE_PATH.'library'.DIRECTORY_SEPARATOR.'database.class.php');
				$dir = new dir();
				if(!file_exists($this->backup_path)) $dir->create($this->backup_path);
				//列出备份文件列表
				$path = realpath($this->backup_path);
				$flag = FilesystemIterator::KEY_AS_FILENAME;
				$glob = new FilesystemIterator($path, $flag);
				$list = array();
				foreach ($glob as $name => $file) {
					if (preg_match('/^\d{8,8}-\d{6,6}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
						$name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%2s%2s%2s-%d');

						$date = "{$name[0]}-{$name[1]}-{$name[2]}";
						$time = "{$name[3]}:{$name[4]}:{$name[5]}";
						$part = $name[6];

						if (isset($list["{$date} {$time}"])) {
							$info = $list["{$date} {$time}"];
							$info['part'] = max($info['part'], $part);
							$info['size'] = $info['size'] + $file->getSize();
						} else {
							$info['part'] = $part;
							$info['size'] = $file->getSize();
						}
						$extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
						$info['compress'] = ($extension === 'SQL') ? '-' : $extension;
						$info['time'] = strtotime("{$date} {$time}");

						$list["{$date} {$time}"] = $info;
					}
				}
				$title = '数据还原';
				break;

			/* 数据备份 */
			case 'export':
				$Db = Db::getInstance();
				$list = $Db->query('SHOW TABLE STATUS');
				$list = array_map('array_change_key_case', $list);
				$title = '数据备份';
				break;

			default:
				showmessage(lang('_param_error_'));
		}
		//渲染模板
		include $this->admin_tpl('database_'.$type);
	}
	/**
	 * 优化表
	 * @param  String $tables 表名
	 * @author
	 */
	public function optimize($tables = null){
		$tables = $_GET['tables'];
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		if($tables) {
			$Db   = Db::getInstance();
			if(is_array($tables)){
				$tables = implode('`,`', $tables);
				$list = $Db->query("OPTIMIZE TABLE `{$tables}`");
				if($list){
					showmessage(lang('optimize_data_success','admin/language'),'',1);
				} else {
					showmessage(lang('optimize_data_error','admin/language'),'',0);
				}
			} else {
				$list = $Db->query("OPTIMIZE TABLE `{$tables}`");
				if($list){
					showmessage("数据表'{$tables}'优化完成！",url('index'),1);
				} else {
					showmessage("数据表'{$tables}'优化出错请重试！",url('index'),0);
				}
			}
		} else {
			showmessage(lang('optimize_data_empty','admin/language'));
		}
	}

	/**
	 * 修复表
	 * @param  String $tables 表名
	 * @author
	 */
	public function repair($tables = null){
		$tables = $_GET['tables'];
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		if($tables) {
			$Db   = Db::getInstance();
			if(is_array($tables)){
				$tables = implode('`,`', $tables);
				$list = $Db->query("REPAIR TABLE `{$tables}`");
				if($list){
					showmessage(lang('repair_data_success','admin/language'));
				} else {
					showmessage(lang('repair_data_error','admin/language'));
				}
			} else {
				$list = $Db->query("REPAIR TABLE `{$tables}`");
				if($list){
					showmessage("数据表'{$tables}'修复完成！",url('index'));
				} else {
					showmessage("数据表'{$tables}'修复出错请重试！",url('index'));
				}
			}
		} else {
			showmessage(lang('repair_data_empty','admin/language'));
		}
	}

	/**
	 * 删除备份文件
	 * @param  Integer $time 备份时间
	 * @author
	 */
	public function del($time = 0){
		$time = $_GET['time'];
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		if($time){
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath($this->backup_path) . DIRECTORY_SEPARATOR . $name;
			array_map("unlink", glob($path));
			if(count(glob($path))){
				showmessage(lang('no_promission_delete','admin/language'));
			} else {
				showmessage(lang('delete_backups_success','admin/language'),url('index',array('type'=>'import')),1);
			}
		} else {
			showmessage(lang('_param_error_'));
		}
	}

	/**
	 * 备份数据库
	 * @param  String  $tables 表名
	 * @param  Integer $id	 表ID
	 * @param  Integer $start  起始行数
	 * @author
	 */
	public function export(){

		$tables = $_GET['tables'];
		$id = $_GET['id'];
		$start = $_GET['start'];

		require_once(MODULE_PATH.'library'.DIRECTORY_SEPARATOR.'database.class.php');
		$load = hd_load::getInstance();
		$dir = $load->librarys('dir');
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');

		if(!empty($tables) && is_array($tables)){ //初始化
			//读取备份配置
			$config = array(
				'path'	 => $this->backup_path. DIRECTORY_SEPARATOR,
				'part'	 => 20971520,
				'compress' => 1,
				'level'	=> 9,
			);

			//检查是否有正在执行的任务
			$lock = "{$config['path']}backup.lock";
			if(is_file($lock)){
				showmessage( $lock.'检测到有一个备份任务正在执行，请稍后再试！');
			} else {
				//创建锁文件
				file_put_contents($lock, NOW_TIME);
			}
			// 自动创建备份文件夹
			if(!file_exists($config['path']) || !is_dir($config['path'])) $dir->create($config['path']);
			//检查备份目录是否可写
			is_writeable($config['path']) || showmessage(lang('backup_not_exist_success','admin/language'));
			session('backup_config', $config);
			//生成备份文件信息
			$file = array(
				'name' => date('Ymd-His', time()).'-'.random(6,1),
				'part' => 1,
			);
			session('backup_file', $file);

			//缓存要备份的表
			session('backup_tables', $tables);

			//创建备份文件
			$database = new database($file, $config);
			if(false !== $database->create()){
				$tab = array('id' => 0, 'start' => 0);
				$data=array();
				$data['status']=1;
				$data['message']='初始化成功';
				$data['tables']=$tables;
				$data['tab']=$tab;
				echo json_encode($data);
			} else {
				showmessage(lang('backup_set_error','admin/language'));
			}
		} elseif (is_numeric($id) && is_numeric($start)) { //备份数据
			$tables = session('backup_tables');
			//备份指定表
			$database = new database(session('backup_file'), session('backup_config'));
			$start  = $database->backup($tables[$id], $start);
			if(false === $start){ //出错
				showmessage(lang('admin/backup_error'));
			} elseif (0 === $start) { //下一表
				if(isset($tables[++$id])){
					$tab = array('id' => $id,'table'=>$tables[$id],'start' => 0);
					$data=array();
					$data['rate'] = 100;
					$data['status']=1;
					$data['info']='备份完成！';
					$data['tab']=$tab;
					echo json_encode($data);
				} else { //备份完成，清空缓存
					unlink($this->backup_path.DIRECTORY_SEPARATOR.'backup.lock');
					session('backup_tables', null);
					session('backup_file', null);
					session('backup_config', null);
					showmessage(lang('_operation_success_'));
				}
			} else {
				$tab  = array('id' => $id,'table'=>$tables[$id],'start' => $start[0]);
				$rate = floor(100 * ($start[0] / $start[1]));
				$data=array();
				$data['status']=1;
				$data['rate'] = $rate;
				$data['info']="正在备份...({$rate}%)";
				$data['tab']=$tab;
				echo json_encode($data);
			}
		} else { //出错
			showmessage(lang('_param_error_'));
		}
	}

	/**
	 * 还原数据库
	 * @author
	 */
	public function import(){
		$time = $_GET['time'];
		$part = $_GET['part']=='null'?0:$_GET['part'];
		$start = $_GET['start']=='null'?0:$_GET['start'];

		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		require_once(MODULE_PATH.'library'.DIRECTORY_SEPARATOR.'database.class.php');

		if(is_numeric($time) && (is_null($part)||empty($part)) && (is_null($start)||empty($start))){ //初始化
			//获取备份文件信息
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath($this->backup_path) . DIRECTORY_SEPARATOR . $name;
			$files = glob($path);
			$list  = array();
			foreach($files as $name){
				$basename = basename($name);
				$match	= sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%2s%2s%2s-%d');
				$gz	   = preg_match('/^\d{8,8}-\d{6,6}-\d{6,6}-\d+\.sql.gz$/', $basename);
				$list[$match[9]] = array($match[9], $name, $gz);
			}
			ksort($list);
			//检测文件正确性
			$last = end($list);

			if(count($list) === $last[0]){
				session('backup_list', $list); //缓存备份列表
				showmessage(lang('init_ok','admin/language'), '',1, array('part' => 1, 'start' => 0));
			} else {
				showmessage(lang('backup_damage','admin/language'));
			}
		} elseif(is_numeric($part) && is_numeric($start)) {
			$list  = session('backup_list');
			$db = new Database($list[$part], array(
				'path'	 => realpath($this->backup_path) . DIRECTORY_SEPARATOR,
				'compress' => $list[$part][2]));

			$start = $db->import($start);
			if(false === $start){
				showmessage(lang('restore_data_error','admin/language'));
			} elseif(0 === $start) { //下一卷
				if(isset($list[++$part])){
					$data = array('part' => $part, 'start' => 0);
					showmessage("正在还原...#{$part}", '', $data);
				} else {
					session('backup_list', null);
					$g_config = file_get_contents(CONF_PATH.'config.php');
					$g_config = preg_replace('/(\'COOKIE_PREFIX\'.+?=>)(.+?),/', "$1'".$this->generate_password(5)."_'," ,$g_config);
					file_put_contents(CONF_PATH.'config.php',$g_config);
					showmessage(lang('restore_data_success','admin/language'));
				}
			} else {
				$data = array('part' => $part, 'start' => $start[0]);
				if($start[1]){
					$rate = floor(100 * ($start[0] / $start[1]));
					showmessage("正在还原...#{$part} ({$rate}%)", '',1, $data);
				} else {
					$data['gz'] = 1;
					showmessage("正在还原...#{$part}", '',1, $data);
				}
			}
		} else {
			showmessage(lang('_param_error_'));
		}
	}

	function generate_password( $length = 8 ) {
		$chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
		$password = '';
		for ($i = 0; $i < $length; $i++){
			$password .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $password;
	}

}
