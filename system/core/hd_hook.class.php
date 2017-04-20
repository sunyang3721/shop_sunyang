<?php
class hd_hook {

    static private  $hooks       =   array();

    /**
     * [add 注册hook]
     * @param [type]  $hook     [description]
     * @param [type]  $class [description]
     * @param boolean $first    [description]
     *
     *
     * hooks 格式 {
     *     key : {
     *         class 类型
     *     }
     *
     * }
     *
     *
     */
    static public function add($hook, $class = array()){
        if(!isset(self::$hooks[$hook])){
            self::$hooks[$hook] = array();
        }
        if(is_array($hook)) {
            self::$hooks = array_merge(self::$hooks, $hook);
        } else {
            if(is_array($class)){
                self::$hooks[$hook] = array_merge(self::$hooks[$hook], $class);
            }else {
                self::$hooks[$hook][] = $class;
            }
        }
    }
    /**
     * 执行钩子
     * @param string $hook 钩子名称
     * @param mixed $params 传入参数
     * @return void
     */
    static public function listen($hook,&$params = null) {
        if(!$hook) return FALSE;
        if(isset(self::$hooks[$hook])) {
            foreach (self::$hooks[$hook] as $_hook) {
                foreach ($_hook AS $class) {
                    list($type,$identify,$name) = explode('/',$class);
                    $classname = str_replace("/", "_", $class);
                    $filename = $name.EXT;

                    if($type == 'module'){
                        $path = APP_PATH.config('DEFAULT_H_LAYER').'/'.$identify.'/hooks/';
                    }elseif ($type == 'plugin') {
                        $path = APP_PATH.'plugin/'.$identify.'/hooks/';
                    }
                    if(require_cache($path.$filename)){
                        $result = self::class_exec($classname,$hook,$identify,$params);
                        if(is_array($result)){
                            $return[] = $result;
                        }elseif(is_string($result)){
                            $return .= $result;
                        }

                    }
                }
            }
            return $return;
        }
        return FALSE;
    }
    /**
     * [class_exec 执行类]
     * @param  [type] $class   [description]
     * @param  string $hook    [description]
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    public static function class_exec($class, $hook = '',$identify = '', &$params = null){
        $obj = new $class($identify);
        if(is_callable(array($obj, $hook))){
            return  $obj->$hook($params);
        }
    }
}