<?php
class upload_local
{
	protected $error = '';

    public function __construct($config = null){

    }

	public function checkRootPath($root) {
        if(!(is_dir($root) && is_writable($root))){
            $this->error = '上传根目录不存在！请尝试手动创建:'.$root;
            return false;
        }
        $this->root = $root;
        return true;
	}

	public function checkSavePath($path) {
        /* 检测并创建目录 */
        if (!$this->mkdir($path)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($this->root . $path)) {
                $this->error = '上传目录 ' . $path . ' 不可写！';
                return false;
            } else {
                return true;
            }
        }
	}

	public function mkdir($path) {
        $dir = $this->root. $path;
        if(is_dir($dir)){
            return true;
        }
        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$savepath} 创建失败！";
            return false;
        }
		return true;
	}

	public function save($file, $replace=true) {
        $filename = $this->root . $file['savepath']. $file['savename'];
        /* 不覆盖同名文件 */
        if (!$replace && is_file($filename)) {
            $this->error = '存在同名文件' . $file['savename'];
            return false;
        }
        /* 移动文件 */
        if (!rename($file['tmp_name'], $filename)) {
            $this->error = '文件上传保存错误！';
            return false;
        }
		@chmod($filename,0777);
        @unlink($file['tmp_name']);
        return true;
	}
    /**
     * [remove_thumb 删除文件夹内缩略图]
     * @return [type] [description]
     */
    public function remove_thumb($filepath){
        $dir = dirname($filepath).'/';
        //获取不带后缀的文件名
        $file = basename($filepath);
        $sub = substr(strrchr($file, '.'), 1);
        $filename = basename($file,'.'.$sub);
        if(is_dir($dir)){
           $file_arr = glob($dir.$filename.'_thumb_*.*');
            foreach ($file_arr AS $file_name) {
                @unlink($file_name);
            }
        }
        return true;
    }

	public function getError() {
		return $this->error;
	}
}