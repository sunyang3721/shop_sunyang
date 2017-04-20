<?php
class unit {
	private function array_urlencode($params){
		if(is_array($params)) {
	        foreach($params as $key => $value) {
	            $params[urlencode($key)] = $this->array_urlencode($value);
	        }
	    } else {
	        $params = urlencode($params);
	    }
	    return $params;
		if(is_array($params)) {
			$arr = array();
	        foreach($params as $key => $value) {
	            $arr[urlencode($key)] = $this->array_urlencode($value);
	        }
	        $params = $arr;
	    } else {
	        $params = urlencode($params);
	    }
	    return $params;
	}
	public function array2json($params){
		return urldecode(json_encode($this->array_urlencode($params)));
	}

	static function json_encode($input) {
        if(defined('JSON_UNESCAPED_UNICODE')){
            return json_encode($input, JSON_UNESCAPED_UNICODE);
        }
        if(is_string($input) || is_int($input)){
            $text = $input;
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(array("\r", "\n", "\t", "\""),array('\r', '\n', '\t', '\"'),$text);
            return '"' . $text . '"';
        }else if(is_array($input) || is_object($input)){
            $arr = array();
            $is_obj = is_object($input) || (array_keys($input) !== range(0, count($input) - 1));
            foreach($input as $k=>$v){
                if($is_obj){
                    $arr[] = self::json_encode($k) . ':' . self::json_encode($v);
                }else{
                    $arr[] = self::json_encode($v);
                }
            }
            if($is_obj){
                return '{' . join(',', $arr) . '}';
            }else{
                return '[' . join(',', $arr) . ']';
            }
        }else{
            return $input . '';
        }
    }



}
