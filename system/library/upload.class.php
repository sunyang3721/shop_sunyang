<?php
helper('attachment');
class upload
{
    protected $instance;

    protected $config = array(
        /* 根目录 */
        'root'      => './uploadfile/',
        /* 子目录 */
        'path'        => 'common',
        'module'      => 'goods',
        /* 存在同名是否覆盖 */
        'md5'       => false,
        'hash'      => true,
        'saveName'  =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'allow_mimes' => '',//允许的mime类型

        /* 强制后缀名 */
        'save_ext'  => '',
        /* 上传前回调 */
        '_before_function'  => 'attachment_exists',
        /* 上传前回调 */
        '_after_function'  => false,
    );

    public function __construct($config = array(),$driver = '') {
        $setting = model('admin/setting','service')->get();
        unset($config['allow_exts']);
        $this->config['attach_enabled'] = isset($setting['attach_enabled']) ? $setting['attach_enabled'] : 1;
        $this->config['replace'] = isset($setting['attach_replace']) ? ($setting['attach_replace']==1 ? true : false) : false;
        $this->config['allow_exts'] = isset($setting['attach_ext']) ? explode(',', $setting['attach_ext']) : (isset($config['allow_exts']) ? $config['allow_exts'] : array('jpg','png','jpeg','bmp','gif'));
        $this->config['allow_size'] = isset($setting['attach_size']) ? $setting['attach_size'] : 0;
        // $driver = isset($driver) ? $driver : (isset($setting['upload_driver']) ? $setting['upload_driver'] : 'local');
        $driver = $driver != '' ? $driver : (isset($setting['attach_type']) ? $setting['attach_type'] : 'local');
        $this->config   =   array_merge($this->config, $config);
        $this->temp_dir = CACHE_PATH.'temp/';
        $this->initialize($driver, $this->config);
        return $this;
    }

    public function __isset($name){
        return isset($this->config[$name]);
    }

    public function __get($name) {
        return $this->config[$name];
    }

    public function __set($name,$value){
        $this->config[$name] = $value;
    }

    private function initialize($driver, $config) {
        $driver = 'upload_'.$driver;
        if(require_cache(LIB_PATH.'upload/'.$driver.EXT) !== TRUE) {
            hd_error::system_error('_no_exist_driver_');
        }
        if(!class_exists($driver)) {
            hd_error::system_error('_class_not_exist_');
        }
        $this->instance = new $driver($config);
    }

    public function upload($file = '') {


        $file = $_FILES[$file];
        if(empty($file)){
            $this->error = lang('upload_file_empty');
            return false;
        }
        /* 检测上传根目录 */
        if(!$this->instance->checkRootPath($this->root)){
            $this->error = $this->instance->getError();
            return false;
        }

        /* 检查上传目录 */
        if(!$this->instance->checkSavePath($this->path)){
            $this->error = $this->instance->getError();
            return false;
        }

        if(!is_dir($this->temp_dir) && !dir::create($this->temp_dir)) {
            $this->error = lang('temp_catalog_not_exist');
            return false;
        }
        if(function_exists('finfo_open')){
            $finfo   =  finfo_open ( FILEINFO_MIME_TYPE );
        }
        // 对上传文件数组信息处理
        $file['name']  = strip_tags($file['name']);
        /* 通过扩展获取文件类型，可解决FLASH上传$FILES数组返回文件类型错误的问题 */
        if(isset($finfo)){
            $file['type']   =   finfo_file ( $finfo ,  $file['tmp_name'] );
        }
        /* 获取上传文件后缀，允许上传无后缀文件 */
        $file['ext']    =   pathinfo($file['name'], PATHINFO_EXTENSION);
        /* 文件上传检测 */
        if (!$this->check($file)){
            return FALSE;
        }

        /* 获取文件hash */
        if($this->hash){
            $file['md5']  = md5_file($file['tmp_name']);
            $file['sha1'] = sha1_file($file['tmp_name']);
        }
        /* 移动临时目录 */
        $tmp_name = CACHE_PATH.'temp/'.basename($file['tmp_name']);
        if(!rename($file['tmp_name'], $tmp_name)) {
            $this->error = lang('temp_catalog_move_error');
            return false;
        }
        $file['tmp_name'] = $tmp_name;

        /* 调用回调函数检测文件是否存在 */
        $file = call_user_func($this->_before_function, $file);
        if(isset($file['aid']) && $file['aid'] > 0) {
            @unlink($tmp_name);
            runhook('before_upload_img',$file['url']);
            return $file;
        }
        /* 生成保存文件名 */
        $savename = $file['savename'] = $this->getSaveName($file);
        if(false == $savename){
            return FALSE;
        }

        /* 检测并创建子目录 */
        $subpath = $this->getSubPath($file['name']);
        if(false === $subpath){
            return FALSE;
        }
        $file['savepath'] = $this->md5 ? $this->path : $this->path . '/'. $subpath;
        /* 对图像文件进行严格检测 */
        $ext = strtolower($file['ext']);
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png','swf'))) {
            $imginfo = getimagesize($file['tmp_name']);
            if(empty($imginfo) || ($ext == 'gif' && empty($imginfo['bits']))){
                $this->error = lang('illegal_image_file');
                return FALSE;
            }
            $file['isimage'] = 1;
            $file['width'] = $imginfo[0];
            $file['height'] = $imginfo[1];
        }
        $file['url'] = $this->root.$file['savepath'].$file['savename'];
        /* 保存文件 并记录保存成功的文件 */
        $this->upload_watermark($file['tmp_name']);
        if (FALSE === $this->instance->save($file, $this->replace)) {
            $this->error = $this->instance->getError();
            return FALSE;
        }

        if($this->_after_function) {
            call_user_func($this->_after_function, $file);
        }

        if(isset($finfo)){
            finfo_close($finfo);
        }
        unset($file['error'], $file['tmp_name']);
        runhook('before_upload_img',$file['url']);
        return empty($file) ? false : $file;
    }

    /**
     * 图片远程化
     */
    public function remote($file = array()) {
        if(empty($file)){
            $this->error = lang('upload_file_empty');
            return false;
        }
        /* 检测上传根目录 */
        if(!$this->instance->checkRootPath($this->root)){
            $this->error = $this->instance->getError();
            return false;
        }

        /* 检查上传目录 */
        if(!$this->instance->checkSavePath($this->path)){
            $this->error = $this->instance->getError();
            return false;
        }

        /* 检查临时目录 */
        if(!is_dir($this->temp_dir) && !dir::create($this->temp_dir)) {
            $this->error = lang('temp_catalog_not_exist');
            return false;
        }

        /* 检测是否是http */
        if (strpos($file, "http") !== 0) {
            // $this->error = '链接不是http链接';
            // return false;
        }

        /* 检测地址合法 */
        if(preg_match("/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/",$file) !== 1) {
            $this->error = lang('link_error');
            return false;
        }

        $fileType = strtolower(strrchr($file, '.'));
        if (!in_array($fileType, array('.gif','.jpg','.jpeg','.bmp','.png')) || stristr($heads['Content-Type'], "image")) {
            $this->error = '链接contentType不正确';
            return;
        }
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $file, $m);
        //获取远程图片
        $context = http::getRequest($file);
        /* 写入临时目录 */
        $tmp_name = $this->getSaveName($img);
        if($fp = @fopen($this->temp_dir.$tmp_name,'a')) {
            fwrite($fp,$context);
            fclose($fp);
        } else {
            $this->error = lang('temp_file_writer_error');
            return FALSE;
        }
        $info = array();
        $info['tmp_name'] = $this->temp_dir.$tmp_name;
        $info['name'] = $m ? $m[1] : "";
        $info['size'] = strlen($img);
        $info['ext'] = pathinfo($info['name'], PATHINFO_EXTENSION);
        if($this->hash){
            $info['md5']  = md5_file($info['tmp_name']);
            $info['sha1'] = sha1_file($info['tmp_name']);
        }
        if(function_exists('finfo_open')){
            $finfo   =  finfo_open ( FILEINFO_MIME_TYPE );
        }
        if(isset($finfo)){
            $info['type']   =   finfo_file ( $finfo ,  $info['tmp_name'] );
        }
        /* 生成保存文件名 */
        $info['savename'] = $this->getSaveName($info);
        if(false == $info['savename']){
            return FALSE;
        }

        /* 检测并创建子目录 */
        $subpath = $this->getSubPath($file['name']);
        if(false === $subpath){
            return FALSE;
        }
        $info['savepath'] = $this->path . '/'. $subpath;

        $imginfo = getimagesize($info['tmp_name']);
        if(empty($imginfo) || ($ext == 'gif' && empty($imginfo['bits']))){
            $this->error = lang('illegal_image_file');
            return FALSE;
        }
        $info['isimage'] = 1;
        $info['width'] = $imginfo[0];
        $info['height'] = $imginfo[1];
        $info['url'] = $this->root.$info['savepath'].$info['savename'];
        /* 保存文件 并记录保存成功的文件 */
        $this->upload_watermark($info['tmp_name']);
        if (FALSE === $this->instance->save($info, $this->replace)) {
            $this->error = $this->instance->getError();
            return FALSE;
        }
        if($this->_after_function) {
            call_user_func($this->_after_function, $info);
        }
        if(isset($finfo)){
            finfo_close($finfo);
        }
        unset($info['error'], $info['tmp_name']);
        runhook('before_upload_img',$info['url']);
        return empty($info) ? false : $info;

    }

    /**
     * 检查上传的文件
     * @param array $file 文件信息
     */
    private function check($file) {
        /*是否开启附件上传*/
        if($this->config['attach_enabled'] == 0){
            $this->error = lang('未开启附件上传');
            return false;
        }
        /* 文件上传失败，捕获错误代码 */
        if ($file['error']) {
            $this->error($file['error']);
            return false;
        }

        /* 无效上传 */
        if (empty($file['name'])){
            $this->error = lang('unknown_upload_error');
            return false;
        }

        /* 检查是否合法上传 */
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = lang('illegal_upload_file');
            return false;
        }

        /* 检查文件大小 */
        if (!$this->checkSize($file['size'])) {
            $this->error = lang('upload_file_size_error');
            return false;
        }

        /* 检查文件Mime类型 */
        //TODO:FLASH上传的文件获取到的mime类型都为application/octet-stream
        if (!$this->checkMime($file['type'])) {
            $this->error = lang('upload_file_type_error');
            return false;
        }

        /* 检查文件后缀 */
        if (!$this->checkExt($file['ext'])) {
            $this->error = lang('upload_file_suffix_error');
            return false;
        }

        /* 通过检测 */
        return true;
    }

    /**
     * 获取错误代码信息
     * @param string $errorNo  错误号
     */
    private function error($errorNo) {
        switch ($errorNo) {
            case 1:
                $this->error = lang('ini_upload_file_size_limit');
                break;
            case 2:
                $this->error = lang('html_upload_file_size_limit');
                break;
            case 3:
                $this->error = lang('file_part_upload');
                break;
            case 4:
                $this->error = lang('upload_file_empty');
                break;
            case 6:
                $this->error = lang('not_found_temp_file');
                break;
            case 7:
                $this->error = lang('file_writer_error');
                break;
            default:
                $this->error = lang('unknown_upload_error');
        }
    }

    /**
     * 检查文件大小是否合法
     * @param integer $size 数据
     */
    private function checkSize($size) {
        return !($size > $this->allow_size) || (0 == $this->allow_size);
    }

    /**
     * 检查上传的文件MIME类型是否合法
     * @param string $mime 数据
     */
    private function checkMime($mime) {
        return empty($this->allow_mimes) ? true : in_array(strtolower($mime), $this->allow_mimes);
    }

    /**
     * 检查上传的文件后缀是否合法
     * @param string $ext 后缀
     */
    private function checkExt($ext) {
        return empty($this->allow_exts) ? false : in_array(strtolower($ext), $this->allow_exts);
    }

    /**
     * 获取子目录的名称
     * @param array $file  上传的文件信息
     */
    private function getSubPath($filename) {
        if($this->md5) return '';
        $dir = array_slice(str_split(md5($filename), 2), 0, 4);
        $subpath = implode($dir, '/').'/';
        if(!empty($subpath) && !$this->instance->mkdir($this->path . '/' .$subpath)){
            $this->error = $this->instance->getError();
            return false;
        }
        return $subpath;
    }

    /**
     * 根据上传文件命名规则取得保存文件名
     * @param string $file 文件信息
     */
    private function getSaveName($file) {
        $rule = $this->saveName;
        if (empty($rule)) { //保持文件名不变
            /* 解决pathinfo中文文件名BUG */
            $filename = substr(pathinfo("_{$file['name']}", PATHINFO_FILENAME), 1);
            $savename = $filename;
        } else {
            $savename = $this->getName($rule, $file['name']);
            if(empty($savename)){
                $this->error = lang('file_name_error');
                return false;
            }
        }
        /* 文件保存后缀，支持强制更改文件后缀 */
        $ext = empty($this->config['save_ext']) ? $file['ext'] : $this->save_ext;
        return $savename . '.' . $ext;
    }

    /**
     * 根据指定的规则获取文件或目录名称
     * @param  array  $rule     规则
     * @param  string $filename 原文件名
     * @return string           文件或目录名称
     */
    private function getName($rule, $filename){
        $name = '';
        if(is_array($rule)){ //数组规则
            $func     = $rule[0];
            $param    = (array)$rule[1];
            foreach ($param as &$value) {
               $value = str_replace('__FILE__', $filename, $value);
            }
            $name = call_user_func_array($func, $param);
        } elseif (is_string($rule)){ //字符串规则
            if(function_exists($rule)){
                $name = call_user_func($rule);
            } else {
                $name = $rule;
            }
        }
        return $name;
    }
    /**
     * [remove_thumb 删除缩略图]
     * @param  [type] $filepath [description]
     * @return [type]           [description]
     */
    public function remove_thumb($filepath){
        return $this->instance->remove_thumb($filepath);
    }

    public function getError(){
        return $this->error;
    }

    public function upload_watermark($url)
    {
        $setting = model('admin/setting','service')->get();
        if(!in_array($this->config['module'], $setting['attach_module'])) return FALSE;
        if(!$setting['attach_watermark']) return FALSE;
        $image = new image();
        switch ($setting['attach_watermark']) {
            case '1':
             $image->open($url)->water($setting['attach_logo'], $setting['attach_position'], $setting['attach_alpha'])->save($url);
                break;
            default:
                break;
        }
    }

}