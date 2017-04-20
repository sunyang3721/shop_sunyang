<?php
class template
{
    protected $theme;
    protected $template;
    protected $replacecode = array('search' => array(), 'replace' => array());
    protected $config = array();

    public function __construct($theme = '') {
        $this->theme = $theme ? $theme : config('TPL_THEME');
        $this->config['taglib_begin']    = config('TAGLIB_BEGIN');
        $this->config['taglib_end']      = config('TAGLIB_END');
        $this->config['taglib_name']     = config('TAGLIB_NAME');
    }

    /**
     * 获取内容
     * @return string
     */
    public function fetch() {
        return $this->content;
    }

    /**
     * 显示模板
     * @return string
     */
    public function display() {
        return $this->cachefile;
    }

    /**
     * 编译模板
     * @return string
     */
    public function compile() {
        $template = @file_get_contents($this->template);
        $cache_dir = dirname($this->cachefile);
        if(!dir::create($cache_dir)) {
            hd_error::template_error('缓存目录('.$cache_dir.')不可写', $tplname);
        }
        if(!@$fp = fopen($this->cachefile, 'w')) {
            hd_error::template_error('模板缓存文件('.$this->cachefile.')不可写');
        }
        runhook('tmpl_compile',$template);
        $template = "<?php if(!defined('IN_APP')) exit('Access Denied');?>\n$template";
        /* 无用 */
        $template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
        /* 注释 */
        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        /* 语言包 */
        if(version_compare('5.4.0', phpversion()) > -1){
            $template = preg_replace("/\{lang\s+(.+?)\}/ies", "\$this->languagevar('\\1')", $template);
        }else{
            $template = preg_replace_callback("/\{lang\s+(.+?)\}/is",function($r){return $this->languagevar($r[1]);},$template);
        }

        if(version_compare('5.4.0', phpversion()) > -1){
             $template = preg_replace("/\{ad\s+([a-zA-Z0-9_\[\]]+)\}/ies", "\$this->adtags('\\1')", $template);
            $template = preg_replace("/\{ad\s+([a-zA-Z0-9_\[\]]+)\/(.+?)\}/ies", "\$this->adtags('\\1', '\\2')", $template);
            /* 插件钩子 */
            $template = preg_replace("/\{hook\/(\w+)}/ies", "\$this->hooktags('\\1')", $template);
            $template = preg_replace("/\{hook\/(\w+)\s+(\S+)}/ies", "\$this->hooktags('\\1','\\2')", $template);

             /* 引入模板 */
            $template = preg_replace("/\{template\s+(\S+)\}/ies", "\$this->stripvtags('<?php include template(\'\\1\'); ?>')", $template);
            $template = preg_replace("/\{template\s+(\S+)\s+(\S+)\}/ies", "\$this->stripvtags('<?php include template(\'\\1\', \'\\2\'); ?>')", $template);

            /* 条件 */
            $template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/ies", "\$this->stripvtags('\\1<?php if(\\2) { ?>\\3')", $template);
            $template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "\$this->stripvtags('\\1<?php } elseif(\\2) { ?>\\3')", $template);
        }else{
            /* 广告调用 */
            $template = preg_replace_callback("/\{ad\s+([a-zA-Z0-9_\[\]]+)\}/is",function($r){return $this->adtags($r[1]);},$template);
            $template = preg_replace_callback("/\{ad\s+([a-zA-Z0-9_\[\]]+)\/(.+?)\}/is",function($r){return $this->adtags($r[1],$r[2]);},$template);
            /* 插件钩子 */
            $template = preg_replace_callback("/\{hook\/(\w+)}/is",function($r){return $this->hooktags($r[1]);},$template);
            $template = preg_replace_callback("/\{hook\/(\w+)\s+(\S+)}/is",function($r){return $this->hooktags($r[1],$r[2]);},$template);

             /* 引入模板 */
            $template = preg_replace_callback("/\{template\s+(\S+)\}/is",function($r){return $this->stripvtags('<?php include template('. $r[1] .'); ?>');},$template);
            $template = preg_replace_callback("/\{template\s+(\S+)\s+(\S+)\}/is",function($r){return $this->stripvtags('<?php include template('.$r[1].','.$r[2].'); ?>');},$template);

            /* 条件 */
            $template = preg_replace_callback("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is",function($r){return $this->stripvtags($r[1].'<?php if('.$r[2].'){?>'.$r[3]);},$template);
            $template = preg_replace_callback("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is",function($r){return $this->stripvtags($r[1].'<?php }elseif('.$r[2].'){?>'.$r[3]);},$template);
        }
        $template = preg_replace("/\{else\}/i", "<?php } else { ?>", $template);
        $template = preg_replace("/\{\/if\}/i", "<?php } ?>", $template);

        /* 数据循环 */
        if(version_compare('5.4.0', phpversion()) > -1){
            $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->stripvtags('<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>')", $template);
            $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->stripvtags('<?php if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>')", $template);
        }else{
            $template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",function($r){return $this->stripvtags('<?php if(is_array('.$r[1].')) foreach('. $r[1] .' as '. $r[2] .'){?>');},$template);
            $template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",function($r){return $this->stripvtags('<?php if(is_array('.$r[1].')) foreach('. $r[1] .' as '. $r[2] .' => '. $r[3] .'){?>');},$template);
        }
        $template = preg_replace("/\{\/loop\}/i", "<?php } ?>", $template);

        /* 标签库 */
        if(version_compare('5.4.0', phpversion()) > -1){
             $template = preg_replace("/\{".$this->config['taglib_name'].":(\w+)\s+([^}]+)\}/ie", "\$this->begin_tag('$1','$2', '$0')", $template);
            $template = preg_replace("/\{\/".$this->config['taglib_name']."\}/ie", "\$this->end_tag()", $template);
        }else{
            $template = preg_replace_callback("/\{".$this->config['taglib_name'].":(\w+)\s+([^}]+)\}/i",function($r){return $this->begin_tag($r[1],$r[2],$r[0]);},$template);
            $template = preg_replace_callback("/\{\/".$this->config['taglib_name']."\}/i",function($r){return $this->end_tag();},$template);
        }

        /* 普通调用 */
        $template = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $template );
        $template = preg_replace ( "/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $template );
        $template = preg_replace ( "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $template );
        if(version_compare('5.4.0', phpversion()) > -1){
            $template = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')",$template);
        }else{
            $template = preg_replace_callback("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/s",function($r){return $this->addquote('<?php echo '.$r[1].';?>');},$template);
        }
        $template = preg_replace ( "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $template );

        /* 替代标签 */
        if(!empty($this->replacecode)) {
            $template = str_replace($this->replacecode['search'], $this->replacecode['replace'], $template);
        }
        $template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
        runhook('after_tmpl_compile',$template);
        flock($fp, 2);
        fwrite($fp, $template);
        fclose($fp);
        $this->content = $template;
        $this->template = $this->cachefile;
    }

    public function template($tplfile, $module = '') {
        if($tplfile[0] == '#'){
            $tplfile = str_replace('#', '', $tplfile);
            $fullfile = PLUGIN_PATH.PLUGIN_ID.'/template/'.$tplfile.'.tpl.php';
            if(!is_file($fullfile)) {
                $fullfile = PLUGIN_PATH.PLUGIN_ID.'/template/'.$tplfile.config('TMPL_TEMPLATE_SUFFIX');
            }
        }else{
            $tplfile = (!empty($tplfile)) ? $tplfile : CONTROL_NAME.'_'.METHOD_NAME;
            $module = (!empty($module)) ? $module : MODULE_NAME;
            $template = $fullfile = APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/template/'.$tplfile.config('TMPL_TEMPLATE_SUFFIX');
            if(!is_file($fullfile) && defined('IN_ADMIN')) {
                $fullfile = APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/template/'.$tplfile.'.tpl.php';
            }
            if(!is_file($fullfile)) {
                if(!defined('MOBILE')){
                    $fullfile =  APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/template/default/'.$tplfile.config('TMPL_TEMPLATE_SUFFIX');
                }else{
                    $fullfile =  APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/template/wap/'.$tplfile.config('TMPL_TEMPLATE_SUFFIX');
                }
            }

            if(!is_file($fullfile)) {
                $fullfile = TPL_PATH.$this->theme.'/'.$module.'/'.$tplfile.config('TMPL_TEMPLATE_SUFFIX');
            }

            if(!is_file($fullfile)) {
                hd_error::template_error(lang('_template_not_exist_').'：'.$template.','.$fullfile);

            }
        }
        $this->template = $fullfile;
        $this->cachefile = CACHE_PATH.'view/'.md5($this->template).'.inc.php';
        if(config('TMPL_CACHE_ON') && !defined('ADMIN_ID')) {
            if(!file_exists($this->cachefile) || TIMESTAMP - @filemtime($this->cachefile) > (int) config('TMPL_CACHE_COMPARE')) {
                $this->compile();
            }
        }else{
            $this->cachefile = $this->template;
        }
        return $this;
    }

    /**
     * 标签
     * @param string $op
     * @param string $data
     * @param string $html
     */
    public function begin_tag($op, $data, $html) {
        preg_match_all("/(\w+)\=[\"|']?([^\"|']+)[\"|']?/i", stripslashes($data), $matches, PREG_SET_ORDER);
        // 内置参数
        $keep_attrs = array('method', 'num', 'limit', 'cache', 'page', 'pagefunc', 'where', 'tagfile','return');
        $tags_attrs = array();
        $extract_tags = array();
        foreach ($matches as $k => $v) {
            if(in_array($v[1], $keep_attrs)) {
                $extract_tags[$v[1]] = $v[2];
                continue;
            }
            $tags_attrs[$v[1]] = $v[2];
        }
        if($where) $tags_attrs['_string'] = $where;
        extract($extract_tags, EXTR_OVERWRITE);

        /* 默认值 */
        $num = (isset($num) || is_numeric($num)) ? intval($num) : 20;
        $cache = (isset($cache) && is_numeric($cache)) ? intval($cache) : config('taglib_cache');
        $limit = (isset($limit)) ? $limit : $num;
        $return = (!empty($return)) ? $return : 'data';
        $pagefunc = (isset($pagefunc)) ? $pagefunc : 'pages';
        $where = (isset($where)) ? $where : '';

        if(!isset($method) || empty($method)) return false;

        $tags_args = array();
        $tagfile = (isset($tagfile)) ? $tagfile : $op;
        $replacecode = '';
        // if(!defined($op.'\\'.$tagfile)) {
            $replacecode .= "\r\n\t\$taglib_{$op}_{$tagfile} = new taglib('".$op."','".$tagfile."');";
            // define($op.'\\'.$tagfile, TRUE);
    //      }
        /* 数据条数 */
        $tags_args['limit'] = $limit;
        if($cache > 0) {
            $cache_key = md5($html);
            $tags_args['cache'] = $cache_key.','.$cache;
        }
        /* 分页处理 */
        if(isset($page)) {
            $tags_args['page'] = $page;
            $tags_args['pagefunc'] = $pagefunc;
        }

        $replacecode .= "\r\n\t\$".$return." = \$taglib_{$op}_{$tagfile}->".$method."(".$this->array2html($tags_attrs).", ".$this->array2html($tags_args).");";
        if(isset($page)) {
            $replacecode.= "\r\n\t\$pages = \$taglib_{$op}_{$tagfile}->pages;";
        }

        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!-- TAGlib_$i-->";
        $this->replacecode['replace'][$i] = "<?php".$replacecode."\r\n?>";
        return $search;
    }

    public function end_tag() {
        return;
    }

    public function addquote($var) {
        return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
    }

    /* 加载语言包 */
    public function languagevar($lang) {
        return lang($lang);
    }

    /* 标签替换 */
    public function stripvtags($expr, $statement = '') {
        $expr = str_replace("\\\"", "\"", preg_replace("/\<\? \=(\\\$.+?)\?\>/s", "\\1", $expr));
        $statement = str_replace("\\\"", "\"", $statement);
        return $expr.$statement;
    }

    function adtags($parameter, $varname = '') {
        $parameter = stripslashes($parameter);
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!--AD_TAG_$i-->";
        $this->replacecode['replace'][$i] = "<?php ".(!$varname ? 'echo ' : '$'.$varname.' = ')."adshow(\"$parameter\");?>";
        return $search;
    }

    function hooktags($hookid, $key = '') {
        $i = count($this->replacecode['search']);
        $this->replacecode['search'][$i] = $search = "<!--HOOK_TAG_$i-->";
        $dev = '';
        if(APP_DEBUG) {
            $dev = "echo '<hook>[".($key ? 'array' : 'string')." $hookid".($key ? '/\'.'.$key.'.\'' : '')."]</hook>';";
        }

        /*
        $key = $key !== '' ? "[$key]" : '';
        $this->replacecode['replace'][$i] = "<?php {$dev}if(!empty(\$_G['setting']['pluginhooks']['$hookid']$key)) echo \$_G['setting']['pluginhooks']['$hookid']$key;?>";

        */

        if(!$key){
            $this->replacecode['replace'][$i] = "<?php {$dev}echo runhook('$hookid');?>";
        }else{
            $this->replacecode['replace'][$i] = "<?php {$dev}\$$key = runhook('$hookid');?>";

        }
        return $search;
    }

    /**
     * 转换数据为HTML代码
     * @param array $data 数组
     */
    private function array2html($data) {
        if (is_array($data)) {
            $str = 'array(';
            foreach ($data as $key=>$val) {
                if (is_array($val)) {
                    $str .= "'$key'=>".$this->array2html($val).",";
                } else {
                    if (strpos($val, '$')===0) {
                        $str .= "'$key'=>$val,";
                    } else {
                        $str .= "'$key'=>'".daddslashes($val)."',";
                    }
                }
            }
            $str = trim($str, ',');
            return $str.')';
        }
        return false;
    }

}