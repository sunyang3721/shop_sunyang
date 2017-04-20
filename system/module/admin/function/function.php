<?php 
/**
 * 生成登陆密码
 * @param string $pwd 原始密码
 * @param string $encrypt 混淆字符
 * @return string
 */
function create_password($pwd, $encrypt = '') {
	if(empty($encrypt)) $encrypt = random (6);
	return md5($pwd.$encrypt);
}
/**
 * 格式化字节大小
 * @param  number $size		字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string				格式化后的带单位的大小
 * @author 
 */
function format_bytes($size, $delimiter = '') {
	 $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	 for ($i = 0; $size >= 1024 && $i < 5; $i++)
		  $size /= 1024;
	 return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 返回进度
 */
function showinfo($text, $status = 0) {
    echo '<script type="text/javascript">showInfo(\'' . $text . '\',' . $status . ');</script>';
    ob_flush();
    flush();

}
function progress($dltotal, $dlnow, $ultotal, $ulnow) {
    $now = date('Y-m-d H:i:s');
    //当前时间
    //刚开始下载或上传时，$dltotal和$ultotal为0，此处避免除0错误
    if (empty($dltotal)) {
        $percent = "0";
    } else {
        $percent = $dlnow / $dltotal;
    }
    echo "<script type='text/javascript'>\r\nupdateProgress('$percent','$dlnow','$dltotal')\r\n</script>\r\n";
    ob_flush();
    flush();
    return (0);
}
function get_url(){
    $url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $url = str_replace('/index.php','',$url);
    return $url;
}
function xCopy($source, $destination, $child = FALSE){
   if(!is_dir($source)){
        showmessage('目录不存在');
   }
   if(!is_dir($destination)){
        dir::create($destination,0777);
   }
   $hander = dir($source);
   while ($entry = $hander->read()) {
        if($entry != '.' && $entry != '..'){
            if(is_dir($source.'/'.$entry) && $child == TRUE){
                xCopy($source."/".$entry,$destination."/".$entry,$child);
            }else{
                copy($source."/".$entry,$destination."/".$entry);
            }
        }
   }
   return TRUE;
}