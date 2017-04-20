<?php
if(!function_exists('attachment_exists')) {
    /**
     * 附件是否存在
     * @param array $file
     * @return array
     */
	function attachment_exists($file = array()) {
		if(empty($file)) return FALSE;
        $attachment = model('attachment')->where(array('md5' => $file['md5']))->find();
		return ($attachment) ? $attachment : $file;
	}
}

if(!function_exists('attachment_init')) {
    /**
     * 初始化附件权限
     * @param array $config
        {
            'mid'              => '用户ID(若为后台上传，此处为管理员ID)',
            'path'             => '目录',
            'replace'          => '同名覆盖',
            'allow_exts'       => '允许后缀',
            'allow_size'       => '允许文件大小',
            'allow_mimes'      => '允许的mime类型',
            'save_ext'         => '允许的文件大小',
            '_before_function' => '上传前执行',
            '_after_function'  => '上传结束回调函数',
        }
     * @return string
     */
	function attachment_init($config = array()) {
		return authcode(serialize($config), 'ENCODE');
	}
}