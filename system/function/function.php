<?php
define('CORE_FUNCTION', TRUE);

/**
 * 序列化
 * @param mixed $string 原始信息
 * @param intval $force
 * @return mixed
 */
function daddslashes($string, $force = 1) {
     if(is_array($string)) {
          $keys = array_keys($string);
          foreach($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = daddslashes($val, $force);
          }
     } else {
          $string = addslashes($string);
     }
     return $string;
}

function dstripslashes($string) {
    if(empty($string)) return $string;
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = dstripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }
    return $string;
}

function dhtmlspecialchars($string, $flags = null) {
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val, $flags);
        }
    } else {
        if($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if(strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if(PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if(strtolower(CHARSET) == 'utf-8') {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
    return $string;
}

/**
 * 批量导入文件 成功则返回
 * @param array $array 文件数组
 * @param boolean $return 加载成功后是否返回
 * @return boolean
 */
function require_array($array,$return=false){
    foreach ($array as $file){
        if (require_cache($file) && $return) return true;
    }
    if($return) return false;
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}


function array_key_case($array) {
	if(!is_array($array)) return $array;
	foreach( $array as $key => $value )
	{
		if(is_array($value)) {
			$array[$key] = array_key_case($value);
		} else {
			$array = array_change_key_case($array);
		}
	}
	return $array;
}

/**
 * 获取和设置配置参数
 * @param string $name 配置变量
 * @param string $file 配置值
 * @param string $default 默认值
 * @return mixed
 */
function config($name=null, $file=null, $default = null) {
    $load = hd_load::getInstance();
    return $load->config($name,$file,$default);
}


/**
 * 获取和设置语言定义(不区分大小写)
 * @param string $name 语言标识
 * @param mixed  $value 指定文件名
 * @return mixed
 */
function lang($name=null, $file = 'language', $vars = array(), $default = null) {
    static $_lang = array();
    $default_lang = config('DEFAULT_LANG');
    $path = LANG_PATH.$default_lang.'/';
    if(is_string($file)) {
        if(strpos($file,'/') !== false){
            list($folder, $file) = explode('/', $file);
            $path = APP_PATH.config('DEFAULT_H_LAYER').'/'.$folder.'/language/'.$default_lang.'/';
        }elseif (strpos($file,'#') !== false) {
            list($folder, $file) = explode('#', $file);
            $path = APP_PATH.'plugin/'.$folder.'/language/'.$default_lang.'/';
        } else {
            $path = LANG_PATH.$default_lang.'/';
        }
        $fullname = $path.$file.'.php';
        if(!isset($_lang[md5($fullname)]) && file_exists_case($fullname)) {
            $lang = include $fullname;
            $_lang[md5($fullname)] = array_change_key_case(array_merge($lang, (array) $_lang[md5($fullname)]));
        }

        if(!empty($name)) {
            $return = $_lang[md5($fullname)][$name] ? $_lang[md5($fullname)][$name] : ($default ? $default : $name);
            $searchs = $replaces = array();
            if($vars && is_array($vars)) {
                foreach($vars as $k => $v) {
                    $searchs[] = '{'.$k.'}';
                    $replaces[] = $v;
                }
            }
            return str_replace($searchs, $replaces, $return);
        }
    }
    return null;
}

/* 模型实例化 */
function model($name, $layer = 'table') {
    $load = hd_load::getInstance();
    return $load->$layer($name);
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $options cookie参数
 * @return mixed
 */
function cookie($name, $value='', $option=null) {
    // 默认设置
    $config = array(
        'prefix'    => config('cookie_prefix'), // cookie 名称前缀
        'expire'    => config('cookie_expire'), // cookie 保存时间
        'path'      => config('cookie_path'), // cookie 保存路径
        'domain'    => config('cookie_domain'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config     = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        if(isset($_COOKIE[$name])){
            $value =    $_COOKIE[$name];
            if(0===strpos($value,'cookie:')){
                $value  =   substr($value,6);
                return array_map('urldecode',json_decode(MAGIC_QUOTES_GPC?stripslashes($value):$value,true));
            }else{
                return $value;
            }
        }else{
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            if(is_array($value)){
                $value  = 'cookie:'.json_encode(array_map('urlencode',$value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function session($name,$value='') {
    $prefix   = config('SESSION_PREFIX');
    if(is_array($name)) { // session初始化 在session_start 之前调用
        if(isset($name['prefix'])) config('SESSION_PREFIX',$name['prefix']);
        if(config('VAR_SESSION_ID') && isset($_REQUEST[config('VAR_SESSION_ID')])){
            session_id($_REQUEST[config('VAR_SESSION_ID')]);
        }elseif(isset($name['id'])) {
            session_id($name['id']);
        }
        ini_set('session.auto_start', 0);
        if(isset($name['name']))            session_name($name['name']);
        if(isset($name['path']))            session_save_path($name['path']);
        if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
        if(isset($name['expire']))          ini_set('session.gc_maxlifetime', $name['expire']);
        if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
        if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
        if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
        if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
        // 启动session
        if(config('SESSION_AUTO_START'))  session_start();
    }elseif('' === $value){
        if(0===strpos($name,'[')) { // session 操作
            if('[pause]'==$name){ // 暂停session
                session_write_close();
            }elseif('[start]'==$name){ // 启动session
                session_start();
            }elseif('[destroy]'==$name){ // 销毁session
                $_SESSION =  array();
                session_unset();
                session_destroy();
            }elseif('[regenerate]'==$name){ // 重新生成id
                session_regenerate_id();
            }
        }elseif(0===strpos($name,'?')){ // 检查session
            $name   =  substr($name,1);
            if(strpos($name,'.')){ // 支持数组
                list($name1,$name2) =   explode('.',$name);
                return $prefix?isset($_SESSION[$prefix][$name1][$name2]):isset($_SESSION[$name1][$name2]);
            }else{
                return $prefix?isset($_SESSION[$prefix][$name]):isset($_SESSION[$name]);
            }
        }elseif(is_null($name)){ // 清空session
            if($prefix) {
                unset($_SESSION[$prefix]);
            }else{
                $_SESSION = array();
            }
        }elseif($prefix){ // 获取session
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;
            }else{
                return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;
            }
        }else{
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:null;
            }
        }
    }elseif(is_null($value)){ // 删除session
        if($prefix){
            unset($_SESSION[$prefix][$name]);
        }else{
            unset($_SESSION[$name]);
        }
    }else{ // 设置session
        if($prefix){
            if (!is_array($_SESSION[$prefix])) {
                $_SESSION[$prefix] = array();
            }
            $_SESSION[$prefix][$name]   =  $value;
        }else{
            $_SESSION[$name]  =  $value;
        }
    }
}


/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function cache($name, $value='',$folder = 'common',$options=null) {
    $load = hd_load::getInstance();
    return $load->cache($name, $value,$folder,$options);
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
	$type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


// 不区分大小写的in_array实现
function in_array_case($value,$array){
    return in_array(strtolower($value),array_map('strtolower',$array));
}

/**
 * 取得对象实例 支持调用类的静态方法
 * @param string $name 类名
 * @param string $method 方法名，如果为空则返回实例化对象
 * @param array $args 调用参数
 * @return object
 */
function get_instance_of($name, $method='', $args=array()) {
    static $_instance = array();
    $identify = empty($args) ? $name . $method : $name . $method . to_guid_string($args);
    if (!isset($_instance[$identify])) {
        if (class_exists($name)) {
            $o = new $name();
            if (method_exists($o, $method)) {
                if (!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                } else {
                    $_instance[$identify] = $o->$method();
                }
            }
            else
                $_instance[$identify] = $o;
        }
        else
            halt(lang('_class_not_exist_') . ':' . $name);
    }
    return $_instance[$identify];
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * 页面地址跳转
 * @param type $url 目标地址
 * @param type $name 倒计时
 * @return type
 */
function redirect($url, $time = 0) {
    if (!headers_sent()) {
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        exit($str);
    }
}

/**
 * 通用提示页
 * @param  string  $msg    提示消息（支持语言包变量）
 * @param  integer $status 状态（0：失败；1：成功）
 * @param  string  $extra  附加数据
 * @param  string  $format 返回类型
 * @return mixed
 */
function showmessage($msg, $url = '-1', $status = 0, $extra = '', $format = '') {
    if(empty($format)) {
        $format = IS_AJAX ? 'json' : 'html';
    }
    $message = lang($msg);
    switch ($format) {
        case 'html':
            if(!defined('IN_ADMIN')) {
                if($url == '-1'){
                    echo "<script>history.go(-1);</script>";
                }else{
                    redirect($url);
                }
                 // include TPL_PATH.'showmessage.tpl';
            } else {
                include TPL_PATH.'showmessage.tpl';
            }
            break;
        case 'json':
            $result = array(
                'status'  => $status,
                'referer' => $url,
                'message' => $message,
                'result'  => $extra
            );
            echo json_encode($result);
            exit;
            break;
        default:
            # code...
            break;
    }
    exit;
}

function checksubmit($name) {
	if(IS_POST) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) {
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);

	for ($i = 0; $i < sizeof($parm); $i++) {
		$pattern = '/';
		for ($j = 0; $j < strlen($parm[$i]); $j++) {
			if ($j > 0) {
				$pattern .= '(';
				$pattern .= '(&#[x|X]0([9][a][b]);?)?';
				$pattern .= '|(&#0([9][10][13]);?)?';
				$pattern .= ')?';
			}
			$pattern .= $parm[$i][$j];
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, '', $string);
	}
	return $string;
}


function template($tplfile, $module = '') {
    $load = hd_load::getInstance();
    $template = $load->librarys('template');
    return $template->template($tplfile, $module)->display();
}

/**
 * 模块是否存在
 * @param string 模块名
 * @return boolean
 */
function module_exists($name) {
	if(empty($name)) return false;
	$modules = model('admin/app','service')->get_module();
	return isset($modules[$name]);
}

/**
 * 加载函数文件
 * 支持如下两种方式：
 *     helper('until');
 *     helper('admin/until');
 * 加载顺序：当前模块->指定模块->全局助手
 * @param  string $helper 文件名
 * @return boolean
 */
function helper($helper) {
    $load = hd_load::getInstance();
    return $load->helper($helper);
}


/**
 * 字符串加解密
 * @param  string  $string	 原始字符串
 * @param  string  $operation 加解密类型
 * @param  string  $key		 密钥
 * @param  integer $expiry	 有效期

 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : config('authkey'));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}


/**
 * 远程请求数据
 * @return string
 */
function dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCODE', $allowcurl = TRUE, $position = 0) {
    $return = '';
    $matches = parse_url($url);
    $scheme = $matches['scheme'];
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;

    if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
        $ch = curl_init();
        $ip && curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ".$host));
        curl_setopt($ch, CURLOPT_URL, $scheme.'://'.($ip ? $ip : $host).':'.$port.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if($encodetype == 'URLENCODE') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            } else {
                parse_str($post, $postarray);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postarray);
            }
        }
        if($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if($errno || $status['http_code'] != 200) {
            return;
        } else {
            return !$limit ? $data : substr($data, 0, $limit);
        }
    }

    if($post) {
        $out = "POST $path HTTP/1.0\r\n";
        $header = "Accept: */*\r\n";
        $header .= "Accept-Language: zh-cn\r\n";
        $boundary = $encodetype == 'URLENCODE' ? '' : '; boundary='.trim(substr(trim($post), 2, strpos(trim($post), "\n") - 2));
        $header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data$boundary\r\n";
        $header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $header .= "Host: $host:$port\r\n";
        $header .= 'Content-Length: '.strlen($post)."\r\n";
        $header .= "Connection: Close\r\n";
        $header .= "Cache-Control: no-cache\r\n";
        $header .= "Cookie: $cookie\r\n\r\n";
        $out .= $header.$post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $header = "Accept: */*\r\n";
        $header .= "Accept-Language: zh-cn\r\n";
        $header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $header .= "Host: $host:$port\r\n";
        $header .= "Connection: Close\r\n";
        $header .= "Cookie: $cookie\r\n\r\n";
        $out .= $header;
    }

    $fpflag = 0;
    if(!$fp = @fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
        $context = array(
            'http' => array(
                'method' => $post ? 'POST' : 'GET',
                'header' => $header,
                'content' => $post,
                'timeout' => $timeout,
            ),
        );
        $context = stream_context_create($context);
        $fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
        $fpflag = 1;
    }

    if(!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if(!$status['timed_out']) {
            while (!feof($fp) && !$fpflag) {
                if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }

            if($position) {
                for($i=0; $i<$position; $i++) {
                    $char = fgetc($fp);
                    if($char == "\n" && $oldchar != "\r") {
                        $i++;
                    }
                    $oldchar = $char;
                }
            }

            if($limit) {
                $return = stream_get_contents($fp, $limit);
            } else {
                $return = stream_get_contents($fp);
            }
        }
        @fclose($fp);
        return $return;
    }
}

/**
 * URL生成
 * @param string $name 路由地址（[模块名]/[控制器名]/方法名）
 * @param mixed $param 附加参数
 * @param mixed  $domain 指定域名，TRUE 代表当前系统地址
 * @return string
 */

function url($name, $param = '', $domain = FALSE) {
    if($domain !== FALSE) {
        if($domain === TRUE) $domain = $_SERVER['HTTP_HOST'];
        $url = (is_ssl() ? 'https://' : 'http://').$domain;
    }
    //var_dump($url);exit;
    if($name[0] == '#'){
        $name = ltrim($name,'#');
        $data = array();
        if(strpos($name,'#')){
            $name_arr = explode('#', $name);
            $pluginid = $name_arr[0];
            $name = $name_arr[1];
        }else{
            $pluginid = PLUGIN_ID;
        }
        $name_arr = explode('#', $name);
        $data[config('VAR_METHOD')] = 'module';
        $data[config('VAR_CONTROL')] = 'app';
        $data[config('VAR_MODULE')] = 'admin';
        $data['mod'] = $pluginid.':'.$name;
        if($param) $data = array_merge($data,$param);
        return $url.__APP__.'?'.http_build_query($data);
    }else{
        $vars = explode("/", $name);
        $params[config('VAR_METHOD')] = (is_array($vars) && !empty($vars)) ? array_pop($vars) : METHOD_NAME;
        $params[config('VAR_CONTROL')] = (is_array($vars) && !empty($vars)) ? array_pop($vars) : CONTROL_NAME;
        $params[config('VAR_MODULE')] = (is_array($vars) && !empty($vars)) ? array_pop($vars) : MODULE_NAME;
        krsort($params);
        if ($param && is_string($param)) parse_str($param,$param);
        /* 检测伪静态 */
        $name = implode('#', $params);
        $rewrites = @include CONF_PATH.'rewrite.php';
        if ($rewrites[$name] && $rewrites[$name]['show'] == 1) {
            $showurl = $rewrites[$name]['showurl'];
            foreach ($param as $k => $v) {
                $showurl = str_replace('{'.$k.'}',$v,$showurl);
            }
            if ($domain !== FALSE) {
                return $url.$showurl;
            } else {
                return __ROOT__.$showurl;
            }
        }
        if ($param) $params = array_merge($params, $param);
        runhook('before_url_output',$params);
        return $url.__APP__.'?'.http_build_query($params);
    }
}


/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

function adshow($name) {
    return $name;
}
/**
 * 后台页面调用
 * @param int $totalrow 总记录数
 * @param int $pagesize 每页记录数
 * @param int $pagenum 	页码数量
 */
function pages($totalrow, $pagesize = 10, $pagenum = 5) {
	$_GET['page'] = (int) $_GET['page'];
    $totalPage = ceil($totalrow/$pagesize);
    $rollPage = floor($pagenum/2);
    $StartPage = $_GET['page'] - $rollPage;
    $EndPage = $_GET['page'] + $rollPage;
    if($StartPage < 1) $StartPage = 1;
    if($EndPage < $pagenum) $EndPage = $pagenum;

    if($EndPage >= $totalPage) {
        $EndPage = $totalPage;
        $StartPage = max(1, $totalPage - $pagenum + 1);
    }
    $string = '<ul class="fr">';
	if($_GET['page'] > 1){
		$string .= '<li><a href="'.page_url(array('page' => $_GET['page'] - 1)).'">上一页</a></li>';
	}else{
		$string .= '<li class="prev disabled"><a>上一页</a></li>';
	}
    for ($page = $StartPage; $page <= $EndPage; $page++) {
		$string .= $page == $_GET['page'] ? '<li class="current"><span>'.$page.'</span></li>' : '<li><a href="'.page_url(array('page' => $page)).'">'.$page.'</a></li>';
    }
	if($_GET['page'] < $totalPage){
		$string .= '<li><a href="'.page_url(array('page' => $_GET['page'] + 1)).'">下一页</a></li>';
	}else{
		$string .= '<li class="prev disabled"><a>下一页</a></li>';
	}
	$string .= '<li class="last">共<b>'.$totalPage.'</b>页&nbsp;到第<input name="page" class="input" type="text" value="'.$_GET['page'].'">页&nbsp;<a class="button bg-gray-white" href="#">确定</a></li>';
    $string .= '</ul>';
    runhook('before_pages_compile',$string);
    return $string;
}

/**
 * 随机字符串
 * @param int $length 长度
 * @param int $numeric 类型(0：混合；1：纯数字)
 * @return string
 */
function random($length, $numeric = 0) {
	 $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	 $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	 if($numeric) {
		  $hash = '';
	 } else {
		  $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		  $length--;
	 }
	 $max = strlen($seed) - 1;
	 for($i = 0; $i < $length; $i++) {
		  $hash .= $seed{mt_rand(0, $max)};
	 }
	 return $hash;
}


/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array		  返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	 if (is_array($tree)) {
		  $refer = array();
		  foreach ($tree as $key => $value) {
				$reffer = $value;
				if (isset($reffer[$child])) {
					 unset($reffer[$child]);
					 tree_to_list($value[$child], $child, $order, $list);
				}
				$list[] = $reffer;
		  }
		  $list = list_sort_by($list, $order, $sortby = 'asc');
	 }
	 return $list;
}


/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author
 */
function list_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0) {
	 $tree = array();
	 if (is_array($list)) {
		  // 创建基于主键的数组引用
		  $refer = array();
		  foreach ($list as $key => $data) {
				$refer[$data[$pk]] = & $list[$key];
		  }
		  foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = $data[$pid];
				if ($root == $parentId) {
					 $tree[$data[$pk]] = & $list[$key];
				} else {
					 if (isset($refer[$parentId])) {
						  $parent = & $refer[$parentId];
						  $parent[$child][$data[$pk]] = & $list[$key];
					 }
				}
		  }
	 }
	 return $tree;
}


/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	 if (is_array($list)) {
		  $refer = $resultSet = array();
		  foreach ($list as $i => $data)
				$refer[$i] = &$data[$field];
		  switch ($sortby) {
				case 'asc': // 正向排序
					 asort($refer);
					 break;
				case 'desc':// 逆向排序
					 arsort($refer);
					 break;
				case 'nat': // 自然排序
					 natcasesort($refer);
					 break;
		  }
		  foreach ($refer as $key => $val)
				$resultSet[] = &$list[$key];
		  return $resultSet;
	 }
	 return false;
}

/**
 * 格式化金额
 * @param type $money
 * @return type
 */
function money($money, $str = ',') {
    return number_format($money, 2, '.', $str);
}

/**
 * 获取用户头像
 * @param type $uid
 * @return string
 */
function getavatar($uid, $default = true) {
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $avatar = './uploadfile/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'.jpg';
    if(!is_file($avatar) && $default === true) {
        $avatar = './template/default/statics/images/member/default_head.png';
    }
    return $avatar;
}


function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function page_url($param = array(), $url = '') {
    $url = (empty($url)) ? $_SERVER['REQUEST_URI'] : $url;
    $urls = parse_url($url);
    $_url = $urls['path'];
    parse_str($urls['query'], $_param);
    $params = array_merge($_param, $param);
    return $_url.'?'.http_build_query($params);
}

function runhook($hookid, &$params = null) {
    return hd_hook::listen($hookid,$params);
    // if(!empty($result)){
    //     return $result;
    // }
}

function addhook($hookid,$class){
    hd_hook::add($hookid,$class);
}

/**
 * XML转数组
 * @param string $arr
 * @param boolean $isnormal
 * @return array
 */
function xml2array(&$xml, $isnormal = FALSE) {
    $xml_parser = new xml($isnormal);
    $data = $xml_parser->parse($xml);
    $xml_parser->destruct();
    return $data;
}

/**
 * 数组转XML
 * @param array $arr
 * @param boolean $htmlon
 * @param boolean $isnormal
 * @param intval $level
 * @return type
 */
function array2xml($arr, $htmlon = TRUE, $isnormal = FALSE, $level = 1) {
	$s = $level == 1 ? "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
	$space = str_repeat("\t", $level);
	foreach($arr as $k => $v) {
		if(!is_array($v)) {
			$s .= $space."<item id=\"$k\">".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</item>\r\n";
		} else {
			$s .= $space."<item id=\"$k\">\r\n".array2xml($v, $htmlon, $isnormal, $level + 1).$space."</item>\r\n";
		}
	}
	$s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
	return $level == 1 ? $s."</root>" : $s;
}

/**
 * 电子邮箱格式判断
 * @param  string $email 字符串
 * @return boolean
 */
function is_email($email) {
	 if (!empty($email)) {
		  return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email);
	 }
	 return FALSE;
}

/**
 * 手机号码格式判断
 * @param string $string
 * @return boolean
 */
function is_mobile($string){
	 if (!empty($string)) {
		  return preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $string);
	 }
	 return FALSE;
}

/**
 * 邮政编码格式判断
 * @param string $string
 * @return boolean
 */
function is_zipcode($string){
     if (!empty($string)) {
          return preg_match('/^[0-9][0-9]{5}$/', $string);
     }
     return FALSE;
}

/**
 * 缩略图生成
 * @param sting $src
 * @param intval $width
 * @param intval $height
 * @param boolean $replace
 * @return string
 */
function thumb($src = '', $width = 500, $height = 500, $replace = false) {
    if(is_file($src) && file_exists($src)) {
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $name = basename($src, '.'.$ext);
        $dir = dirname($src);
        $setting = model('admin/setting','service')->get();
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png'))) {
            $name = $name.'_thumb_'.$width.'_'.$height.'.'.$ext;
            $file = $dir.'/'.$name;
            if(!file_exists($file) || $replace == TRUE) {
                $image = new image($src);
                $image->thumb($width, $height, isset($setting['attach_thumb'])?$setting['attach_thumb']:1);
                $image->save($file);
            }
            return $file;
        }
    }
    return $src;
}

/**
 * 多维数组合并（支持多数组）
 * @return array
 */
function array_merge_multi () {
    $args = func_get_args();
    $array = array();
    foreach ( $args as $arg ) {
        if ( is_array($arg) ) {
            foreach ( $arg as $k => $v ) {
                if ( is_array($v) ) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : array();
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }
    return $array;
}

function sizecount($size) {
    if($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = intval($size) . ' Bytes';
    }
    return $size;
}

function fileext($filename) {
    return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * @param $title
 * @param $keywords
 * @param $description
 * @param $other
 * @param bool $flag
 * @return array
 */
function seo($title, $keywords='', $description='', $other='', $flag = false) {
    $setting = model('admin/setting','service')->get();
    $site_title = $setting['site_name'];
    $site_keywords = $setting['site_keywords'];
    $site_description = $setting['site_description'];
	$tpl_theme = config('TPL_THEME');
    $SEO = array();
    if($flag == false && $tpl_theme !== 'wap'){
         $SEO['title'] = ($title ? $title.' - ' : '').$site_title;
     }else{
        $SEO['title'] = $title;
     }
	if(0 === SITE_AUTHORIZE && $tpl_theme !== 'wap')$SEO['title'] .= ' - '.pack("H*","506f77657265642062792048616964616f");
    $SEO['keywords'] = ($keywords ? $keywords : '').$site_keywords;
    $SEO['description'] = ($description ? $description : '').$site_description;
    $SEO['other'] = $other ? $other : '';
    return $SEO;
}

/**
 * [is_favorite 判断商品是否已经收藏]
 * @param  [type]  $mid [description]
 * @param  [type]  $id  [description]
 * @return boolean      [description]
 */
function is_favorite($mid,$id){
    return model('goods/goods_sku','service')->is_favorite($mid,$id);
}

/*截取字符串*/
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8'){
    if($code == 'UTF-8'){
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);
        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen));
        return join('', array_slice($t_string[0], $start, $sublen));
    }else{
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';
        for($i=0; $i< $strlen; $i++){
            if($i>=$start && $i< ($start+$sublen)){
                if(ord(substr($string, $i, 1))>129){
                    $tmpstr.= substr($string, $i, 2);
                }
                else{
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        return $tmpstr;
    }
}


function pinyin($string = '') {
	if(CHARSET != 'gbk') {
		$string = iconv(CHARSET,'GBK',$string);
	}
	$l = strlen($string);
	$i = 0;
	$pyarr = array();
	$py = array();
	$filename = CACHE_PATH.'data/pinyin.table';
	$fp = fopen($filename,'r');
	while(!feof($fp)) {
		$p = explode("-",fgets($fp,32));
		$pyarr[intval($p[1])] = trim($p[0]);
	}
	fclose($fp);
	ksort($pyarr);
	while($i<$l) {
		$tmp = ord($string[$i]);
		if($tmp>=128) {
			$asc = abs($tmp*256+ord($string[$i+1])-65536);
			$i = $i+1;
		} else $asc = $tmp;
		$py[] = asc_to_pinyin($asc,$pyarr);
		$i++;
	}
	return $py;
}

/**
 * Ascii转拼音
 * @param $asc
 * @param $pyarr
 */
function asc_to_pinyin($asc,&$pyarr) {
	if($asc < 128)return chr($asc);
	elseif(isset($pyarr[$asc]))return $pyarr[$asc];
	else {
		foreach($pyarr as $id => $p) {
			if($id >= $asc)return $p;
		}
	}
}

/**
 * 生成订单号
 */
function build_order_no($suffix = 'o') {
    return $suffix.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 对多位数组进行排序
 * @param $multi_array 数组
 * @param $sort_key需要传入的键名
 * @param $sort排序类型
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC) {
	if (is_array($multi_array)) {
		foreach ($multi_array as $row_array) {
			if (is_array($row_array)) {
				$key_array[] = $row_array[$sort_key];
			} else {
				return FALSE;
			}
		}
	} else {
		return FALSE;
	}
		array_multisort($key_array, $sort, $multi_array);
		return $multi_array;
}

/* 获取加密后的签名 */
function get_sign($params, $key, $sign_type = 'md5') {
    ksort($params, SORT_STRING);
    $arg = "";
    while (list ($k, $val) = each($params)) {
        if (empty($val) || $k == 'sign'|| in_array($k, array('m', 'c', 'a')))
            continue;
        $arg.=$k . "=" . urldecode(htmlspecialchars_decode($val)) . "&";
    }
    $arg = substr($arg, 0, count($arg) - 2);
    return $sign_type($arg . $key);
}

/**
 * [deldir 删除文件夹]
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */
function deldir($dir){
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
               deldir($fullpath);
            }
        }
    }
    closedir($dh);
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}
/**
 * 加密字符串
 * @param string $str 字符串
 * @param string $key 加密key
 * @return string
 */
function encrypt($data,$key = '',$expire = 0) {
    $expire = sprintf('%010d', $expire ? $expire + time():0);
    $key = md5($key != '' ? $key : config('authkey'));
    $data = base64_encode($expire.$data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str    =   '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 解密字符串
 * @param string $str 字符串
 * @param string $key 加密key
 * @return string
 */
function decrypt($data,$key = '') {
    $key = md5($key != '' ? $key : config('authkey'));
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);

    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    $data   = base64_decode($str);
    $expire = substr($data,0,10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $data   = substr($data,10);
    return $data;
}