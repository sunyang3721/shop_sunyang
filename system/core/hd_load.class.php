<?php
class hd_load {
	protected $load;
	
	protected static $_instance;

	public static function getInstance(){
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	/**
	 * 获取和设置配置参数
	 * @param string $name 配置变量
	 * @param string $file 配置值
	 * @param string $default 默认值
	 * @return mixed
	 */
	public function config($name=null, $file=null, $default = null){
		static $_config = array();
	    $name = strtolower($name);
	    if($file && is_file(CONF_PATH.$file.'.php')) {
	        $array = include CONF_PATH.$file.'.php';
	    }
	    if($array) $_config = array_merge($_config, array_key_case($array));

	    if($default !== null) $_config[$name] = $default;

	    if(is_string($name)) {
	        return $_config[$name];
	    }
	    return null;
	}

	/**
	 * 缓存管理
	 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
	 * @param mixed $value 缓存值
	 * @param mixed $options 缓存参数
	 * @return mixed
	 */
	public function cache($name, $value='',$folder = 'common',$options=null) {
	    static $cache   =   '';
	    $options['folder'] = $folder;
	    $type       =   isset($options['type']) ? $options['type'] : '';
	    $cache      = cache::getInstance($type,$options);
	    if(''=== $value){ // 获取缓存
	        return $cache->get($name);
	    }elseif(is_null($value)) { // 删除缓存
	        return $cache->rm($name);
	    }else { // 缓存数据
	        if(is_array($options)) {
	            $expire     =   isset($options['expire'])? $options['expire']:NULL;
	        }else{
	            $expire     =   is_numeric($options) ? $options : NULL;
	        }
	        return $cache->set($name, $value, $expire);
	    }
	}

	/**
	 * [helper 加载公共函数]
	 * @param  array  $helpers [description]
	 * @return [type]          [description]
	 */
	public function helper($helper) {
		$files = array();
	    $module = MODULE_NAME;
	    $filename = $helper;
	    if(FALSE !== strpos($helper, '/')) {
	        list($module, $filename) = explode("/", $helper);
	    }
	    $helpers = array(
	        APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/function/'.$filename.'.php',
	        APP_PATH.'function/'.$filename.'.php',
	    );
	    return require_array($helpers, TRUE);
	}

	/**
	 * [librarys 加载类库]
	 * @param  [type] $library [description]
	 * @return [type]          [description]
	 */
	public function librarys($library,$params = null) {
		static $_librarys = array();
		if(!isset($_librarys[$library])){
			if($library == 'View'){
				$_library = hd_view::getInstance();
			}else{
				$_library = new $library($params);
			}
			$_librarys[$library] = $_library;
		}
		return $_librarys[$library];
	}
	
	/**
	 * [table 加载table]
	 * @param  string $name [description]
	 * @return [type]       [description]
	 */
	public function table($name = '') {
		return $this->_load_model($name,'table');
	}

	/**
	 * [service 加载service]
	 * @param  string $name [description]
	 * @return [type]       [description]
	 */
	public function service($name = '') {
		return $this->_load_model($name,'service');
	}

	private function _load_model($name,$layer) {
		static $_models = array();
		$module = MODULE_NAME;
		/* 插件模式 */
		if($name[0] == '#') {
			list(, $pluginid, $name) = explode("#", $name);
		}
		if(strpos($name, '/') !== FALSE) {
			list($module, $name) = explode("/", $name);
		}
		$file = $name.'_'.$layer;
		$class = $name."\\".$layer;
		if(!isset($_models[$class])) {
			$instance = FALSE;
			$files = array();
			if($pluginid) {
				$files[] = APP_PATH.'plugin/'.$pluginid.'/model/'.$layer.'/'.$file.EXT;
			} else {
				$files[] = APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/model/'.$layer.'/'.$file.EXT;
			}
			$files[] = APP_PATH.'model/'.$layer.'/'.$file.EXT;
			if(require_array($files, TRUE) && class_exists($file)) {
				$instance = new $file();
			}
			if(!$instance) {
				$instance = new $layer($name);
			}
			$_models[$class] = $instance;
		}
		return $_models[$class];
	}
	/*public function autoload($class) {*/
		/**
		$class 根据下划线拆分
			list(module, class, layer) = explode("_", $class);

			如果只有一个：进入 library
			如果是两个，则默认为控制器
			如果是三个，同上

			所有的类均通过
			$this->load 的子方法加载，包括 library  model  helpers ,可以多次重复载入，不必担心重复的问题。

				$this->load->config();
				$this->load->library();
				$this->load->helper();
				$this->load->cache();
				$this->load->model();
				$this->load->table(); {
					$this->load->table('member')		// 当前模块
					$this->load->table('admin/member')  // 指定模块
					$this->load->table('#admin#member') // 插件
				}
				
					
					


			类库尽可能通过连贯方式实现，例如：
				$this->load->table('admin_user')->fetch_all_by_id();

			嵌入开发架构：
				整站分为 model（服务层） 与 table（数据层） 、control (控制器)


			语言包
				调用：
					lang(module:file, key, value) ,若未找到，则返回大写的key
					语言包在加载时通过静态变量缓存，缓存名为 {模块：文件名}
				定义：语言包通过 return array() 方式定义，不区分大小写

			关于钩子
				钩子只预埋在前台模板里可用于拓展的，可以在插件中实现不修改源文件而动态改变数据结果

			配置文件
				获取&设置 	config([file:]key, value)
			缓存方法：
				获取&设置 	cache([file:]key, value, options) 当 value 为空，则标识设置

			插件：
				访问： plugin.php?id=mod:method
				{
					访问： {mod}.inc.php
					钩子： hooks.class.php
					缓存： cache.class.php
				}
		**/

	/*}*/
}

/**
所有的钩子写入 hooks 表并缓存
module
layer
name
**/