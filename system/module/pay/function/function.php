<?php
/**
 * 返回响应地址
 */
function return_url($code, $method = 'notify') {
    return (is_ssl() ? 'https://':'http://').$_SERVER['HTTP_HOST'].__ROOT__.'api/trade/api.'.$method.'.'.$code.'.php';
}

/**
 * 生成签名结果
 * @param $array要加密的数组
 * @param return 签名结果字符串
*/
function build_mysign($sort_array,$security_code,$sign_type = "MD5", $issort = TRUE) {
    if($issort == TRUE) {
	    $sort_array = arg_sort($sort_array);
	}
    $security_code = ltrim($security_code);
    $prestr = create_linkstring($sort_array);
    $prestr = $prestr.$security_code;
    $mysgin = sign($prestr,$sign_type);
    return $mysgin;
}


/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $array 需要拼接的数组
 * @param return 拼接完成以后的字符串
*/
function create_linkstring($array, $encode = FALSE) {
    $arg  = "";
    while (list ($key, $val) = each ($array)) {
        if($encode === TRUE) $val = urlencode($val);
        $arg.=$key."=".$val."&";
    }
    $arg = substr($arg,0,count($arg)-2);//去掉最后一个&字符
    return $arg;
}

/********************************************************************************/

/**除去数组中的空值和签名参数
 * @param $parameter 加密参数组
 * @param return 去掉空值与签名参数后的新加密参数组
 */
function para_filter($parameter) {
    $para = array();
    while (list ($key, $val) = each ($parameter)) {
        if($key == "sign" || $key == "sign_type" || $val == "")continue;
        else    $para[$key] = $parameter[$key];
    }
    return $para;
}

/********************************************************************************/

/**对数组排序
 * @param $array 排序前的数组
 * @param return 排序后的数组
 */
function arg_sort($array) {
    $array = para_filter($array);
    ksort($array);
    reset($array);
    return $array;
}

/********************************************************************************/

/**加密字符串
 * @param $prestr 需要加密的字符串
 * @param return 加密结果
 */
function sign($prestr,$sign_type) {
    return md5($prestr);
}

/*
*xml to array
*/
function xmlToArray($xml) {
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}

/*
*array to xml
*/
function arrayToXml($arr) {
    $xml = "<xml>";
    foreach ($arr as $key=>$val) {
         $xml.="<".$key.">".$val."</".$key.">";
    }
    $xml.="</xml>";
    return $xml;
}


function getHttpResponsePOST($url, $para, $input_charset = 'utf-8') {
    if (trim($input_charset) != '') {
        $url = $url."_input_charset=".$input_charset;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//严格认证
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    return $responseText;
}

/*
*生成32位随机字符串
*/
function createNoncestr( $length = 32 ) 
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {  
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
    }  
    return $str;
}