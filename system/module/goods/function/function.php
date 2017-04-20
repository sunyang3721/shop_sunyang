<?php
/**
 * 		商品公共函数
 */
	/**
	 * [more_array_unique 二维数组去重保留key值]
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	function more_array_unique($arr){
	    foreach($arr[0] as $k => $v){
	        $arr_inner_key[]= $k;
	    }
	    foreach ($arr as $k => $v){
	        $v =join(',',$v);
	        $temp[$k] =$v;
	    }
	    $temp =array_unique($temp);
	    foreach ($temp as $k => $v){
	        $a = explode(',',$v);
	        $arr_after[$k]= array_combine($arr_inner_key,$a);
	    }
	    return $arr_after;
	}
	/**
	 * [mult_unique 多维数组去重]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	function mult_unique($array){
		$return = array();
		foreach($array as $key=>$v){
		    if(!in_array($v, $return)){
		        $return[$key]=$v;
		    }
		 }
		return $return;
	}
	/**
	 * [restore_array 重新给数组赋key值]
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	function restore_array($arr){
	 	if (!is_array($arr)){
	  		return $arr;
	   	}
	 	$c = 0; $new = array();
	 	while (list($key, $value) = each($arr)){
	  		if (is_array($value)){
	   			$new[$c] = restore_array($value);
	  		}
	  		else { $new[$c] = $value; }
	  			$c++;
	 		}
	 	return $new;
	}
	/**
	 * [create_url 组织url地址，用于筛选页面]
	 * @param  [type] $k    [description]
	 * @param  [type] $v    [description]
	 * @param  [type] $attr [description]
	 * @return [type]       [description]
	 */
	function create_url($k, $v, $attr = array()) {
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $param);
		$param = dstripslashes($param);
		if(in_array($k, $attr)) {
			$v = base_encode($v);
			$param['attr'][$k] = $v;
		} else {
			$param[$k] = $v;
		}
		$param['page'] = 0;
		$param = array_filter($param);
		$param['attr'] = array_filter($param['attr']);
		return urldecode($url['path'].'?'.http_build_query($param));
	}
	/**
	 * [catpos 商品模块下的面包屑导航]
	 * @param  [type] $catid  [description]
	 * @param  string $symbol [description]
	 * @return [type]         [description]
	 */
	function catpos($catid, $symbol=' > ') {
		$categorys = model('goods/goods_category','service')->get();
		$cat_url = $categorys[$catid]['url'] ? $categorys[$catid]['url'] : url('goods/index/lists', array('id' => $catid));
		$pos = '';
		$parentids = model('goods_category','service')->get_parent($catid);
		sort($parentids);
		foreach ($parentids as $parentid) {
			$url = $categorys[$parentid]['url'] ? $categorys[$parentid]['url'] :url('goods/index/lists', array('id' => $parentid));
			$pos .= '<a href="'.$url.'">'.$categorys[$parentid]['name'].'</a>'.'<em>'.$symbol.'</em>';
		}
		$pos .= '<a href="'.$cat_url.'">'.$categorys[$catid]['name'].'</a>';
		return $pos;
	}

	function base_encode($str) {
        $src  = array("/","+","=");
        $dist = array("_a","_b","_c");
        $old  = base64_encode($str);
        $new  = str_replace($src,$dist,$old);
        return $new;
	}

	function base_decode($str) {
        $src = array("_a","_b","_c");
        $dist  = array("/","+","=");
        $old  = str_replace($src,$dist,$str);
        $new = base64_decode($old);
        return $new;
	}
