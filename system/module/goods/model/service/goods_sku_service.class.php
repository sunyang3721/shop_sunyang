<?php
/**
 *		子商品数据层
 */
class goods_sku_service extends service {
	public function _initialize() {
		$this->sku_db = $this->load->table('goods/goods_sku');
		$this->spu_db = $this->load->table('goods/goods_spu');
		$this->index_db = $this->load->table('goods/goods_index');
		$this->goodsattr_db = $this->load->table('goods/goods_attribute');
		$this->cate_service = $this->load->service('goods/goods_category');
	}
	/**
	 * [create_sku 处理子商品]
	 * @param  [type] $params [子商品信息]
	 * @return [type]         [boolean]
	 */
	public function create_sku($params){
		if(!empty($params['del'])){
			$map = array();
			$map['sku_id'] = array("IN", $params['del']);
			$this->sku_db->where($map)->delete();
		}
		//sku新增数据处理
		if(isset($params['new'])){
			foreach ($params['new'] as $key => $new_data) {
				$new_data['thumb'] =  $new_data['imgs'][0] ? $new_data['imgs'][0] : '';
				$new_data['imgs'] = $new_data['imgs'] ? json_encode($new_data['imgs']) : '';
				$new_data['spec'] = !empty($new_data['spec']) ? unit::json_encode($new_data['spec']) : '';
				$params['new'][$key]['sku_id'] = $this->sku_db->add($new_data);
			}
		}
		//sku编辑数据处理
		if(isset($params['edit'])){
			foreach ($params['edit'] AS $edit_data) {
				$edit_data['thumb'] = $edit_data['imgs'][0] ? $edit_data['imgs'][0] : '';
				$edit_data['imgs'] = $edit_data['imgs'] ? json_encode($edit_data['imgs']) : '';
				$edit_data['spec'] = !empty($edit_data['spec']) ? unit::json_encode($edit_data['spec']) : '';
				$this->sku_db->update($edit_data);

			}
		}
		runhook('create_sku',$params);
		return $params;
	}
	/**
	 * [sku_edit 编辑sku]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function sku_edit($params){
		if($params['sku_id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if(empty($params['sku_name'])){
			$this->error = lang('goods_goods_name_empty','goods/language');
			return FALSE;
		}
		$params['imgs'] = $params['images'] ? json_encode($params['images']) : '';
		$params['thumb'] = $params['images'][0] ? $params['images'][0] : '';
		runhook('before_sku_edit',$params);
		$result = $this->sku_db->update($params);
		$this->index_db->where(array('sku_id' => $params['sku_id']))->save(array('show_in_lists' => $params['show_in_lists']));
		if($result === FALSE){
			$this->error = $this->sku_db->getError();
			return FALSE;
		}else{
            model('goods_sku')->where(array('sku_id'=>$params['sku_id']))->setInc('edition',1);
			return TRUE;

		}
	}
	/**
	 * [create_sku_name 生成子商品名称]
	 * @param  [type] $params [商品参数]
	 * @return [array]         [子商品名称数组]
	 */
	public function create_sku_name($spec_array){
		$name = '';
		foreach ($spec_array as $k => $v) {
			$name .= $v['value'].' ';
		}
		return $name;
	}
	/**
	 * [get_lists 获取商品sku列表]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function get_lists($params){
		$spu = config("DB_PREFIX").'goods_spu';
		$sqlmap = array();
		if(!empty($params['catid'])){
			if($this->cate_service->has_child($params['catid'])){
				$catid = $this->cate_service->get_child($params['catid']);
			}else{
				$catid= array(0 => $params['catid']);
			}
			$sqlmap['catid'] = array('IN',$catid);
		}
		if(!empty($params['brand_id'])){
			$sqlmap['brand_id'] = $params['brand_id'];
		}
		$sqlmap[config("DB_PREFIX").'goods_sku.status'] = 1;
		if(!empty($params['keyword'])){
			 $sqlmap['sku_name|'.config("DB_PREFIX").'goods_sku.sn|barcode'] = array("LIKE", '%'.$params["keyword"].'%');
		}
		$sku_ids = $this->sku_db->join($spu.' on '.'id = spu_id')->where($sqlmap)->page($params['page'])->limit($params['limit'])->getField('sku_id',TRUE);
		$result['count'] =$this->sku_db->field('sku_id,'.config("DB_PREFIX").'goods_spu.sn AS osn')->join(config("DB_PREFIX").'goods_spu on '.'id = spu_id')->where($sqlmap)->count();
		foreach ($sku_ids AS $sku_id) {
			$result['lists'][] = $this->goods_detail($sku_id,'');
		}
		return $result;
	}
	/**
	 * 指定商品减少库存
	 * @param [int] $id 子商品ID
	 * @param [int] $number变更数量
	 * @return bool
	 */
	public function set_dec_number($id, $number) {
		$sku_id = (int) $id;
		$number = (int) $number;
		if($id < 1 || $number < 1) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$sqlmap = $map = array();
		$map['sku_id'] = $id;
		$sqlmap['id'] = $this->sku_db->where(array('sku_id'=>$id))->getField('spu_id');
		$result = $this->sku_db->where($map)->setDec('number', $number);
		$_result = $this->spu_db->where($sqlmap)->setDec('sku_total',$number);
		if(!$result){
			$this->error = $this->sku_db->getError();
			return FALSE;
		}else{
			return TRUE;
		}
	}
	/**
	 * 指定商品增加库存
	 * @param [int] $sku_id 子商品ID
	 * @param [int] $goods_num变更数量
	 * @return bool
	 */
	public function set_inc_number($id, $number) {
		$id = (int) $id;
		$number = (int) $number;
		if($id < 1 || $number < 1) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$sqlmap = $map = array();
		$map['sku_id'] = $id;
		$sqlmap['id'] = $this->sku_db->where(array('sku_id'=>$id))->getField('spu_id');
		$result = $this->sku_db->where($map)->setInc('number', $number);
		$_result = $this->spu_db->where($sqlmap)->setInc('sku_total',$number);
		if(!$result){
			$this->error = $this->sku_db->getError();
			return FALSE;
		}else{
			return TRUE;
		}
	}
	/**
	 * [is_favorite 判断商品是否已收藏]
	 * @param  [type]  $id [description]
	 * @return boolean     [description]
	 */
	public function is_favorite($mid,$id){
		if((int)$id < 1) return FALSE;
		$favorite = FALSE;
		if($mid > 0){
			$favorite = $this->load->service('member/member_favorite')->set_mid($mid)->is_exists($id);
		}
		return $favorite;
	}
	/**
	 * [detail 查询子商品详情]
	 * @param  [type]  $id    [子商品id]
	 * @return [type]         [description]
	 */
	public function detail($id,$flag = FALSE){
		if ((int) $id < 1) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$goods_info = array();
		$goods_info = $this->fetch_by_id($id,'spu,price,brand,cat_name,show_index');
		if (!$goods_info['sku_id']) {
			$this->error = lang('goods_goods_not_exist','goods/language');
			return FALSE;
		}
		if($flag == FALSE){
			$categorys = $this->cate_service->get_parent($goods_info['catid']);
			array_push($categorys,$goods_info['catid']);
			$goods_info['catname'] = $this->cate_service->create_cat_format($categorys);
		}
		$sku_list = $this->sku_db->where(array('spu_id'=>$goods_info['spu_id']))->select();
		if($sku_list){
			$sku = array();
			foreach ($sku_list AS $v) {
				$v['spec_array'] = $v['spec'];
				$sku[$v['sku_id']] = $v;
			}
		}
		if($sku){
			foreach ($sku as $k => $v) {
				$spec_md5 = '';
				$spec_md = array();
				foreach ($v['spec_array'] AS $value) {
					$spec_md[] = md5($value['id'].':'.$value['value']);
					$spec_md5 .= $value['id'].':'.$value['value'].';';
				}
				$sku[$k]['spec_md5'] = $spec_md5;
				$sku[$k]['spec_md'] = implode(";", $spec_md);
				$sku[$k]['url'] = url('goods/index/detail',array('sku_id' => $v['sku_id']));
				$sku_arr[$spec_md5] = $sku[$k];

			}
			$goods_info['sku_arr'] = $sku_arr;
		}
		$spec_str = '';
		foreach ($goods_info['spec'] AS $spec) {
			$spec_str .= $spec['id'].':'.$spec['value'].';';
			$spec_show .= $spec['name'].':'.$spec['value'].'&nbsp;&nbsp;';
		}
		$goods_info['spec_str'] = $spec_str;
		$goods_info['spec_show'] = $spec_show;
		$goods_info['spec'] = json_encode($goods_info['spec']);
		$goods_info['attrs'] = $this->attrs_detail($id);
		runhook('after_sku_detail',$goods_info);
		return $goods_info;
	}

	/**
	 * [fetch_by_id 获取一条子商品信息]
	 * @param  [type]  $id    [description]
	 * @param  boolean $field [description]
	 * @return [type]         [description]
	 */
	public function fetch_by_id($id = 0,$extra = ''){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$goods = $this->sku_db->detail($id);
		if(!($goods->result['sku'])){
			$this->error = lang('goods_goods_not_exist','goods/language');
			return FALSE;
		}
		if($extra) {
			$extra = explode(",", $extra);
			foreach ($extra AS $method) {
				if(method_exists($this->sku_db,$method)) {
					$goods = $goods->$method();
				}
			}
		}
		$sku = $goods->output();
		runhook('after_sku_fetch_by_id',$sku);
		return $sku;
	}
	/**
	 * [get_sku 根据主商品获取子商品]
	 * @param  [type] $id [主商品id]
	 * @return [type]     [description]
	 */
	public function get_sku($id){
		$sku_ids = $this->sku_db->where(array('spu_id' => $id,'status' => array('NEQ',-1)))->order('sku_id ASC')->getField('sku_id',TRUE);
		$result = array();
		foreach ($sku_ids as $key => $sku_id) {
			$sku = $this->sku_db->detail($sku_id)->output();
			$specs = $sku['spec'];
			unset($sku['spec']);
			$spec_str = '';
			foreach ($specs AS $id => $spec) {
				$sku['spec'][md5($id.$spec['value'])] = $spec;
				$spec_str .= $spec['name'].':'.$spec['value'].' ';
				$spec_md5 = md5($spec_str);
			}
			$sku['spec_md5'] = $spec_md5;
			$sku['spec_str'] = $spec_str;
			$result[$sku['sku_id']] = $sku;
		}
		if(!$result){
			$this->error = $this->sku_db->getError();
		}
		runhook('after_get_sku',$result);
		return $result;
	}
	/**
	 * [get_sku_ids 默认根据主商品获取子商品id，$flag为TRUE时查询子商品的同父级子商品]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_sku_ids($id,$isspu = TRUE){
		$spu_id = $id;
		if($isspu == FALSE){
			$spu_id = $this->sku_db->where(array('sku_id' => $id))->getField('spu_id');
		}
		$sku_ids = $this->sku_db->where(array('spu_id'=>$spu_id))->getfield('sku_id',TRUE);
		if(!$sku_ids){
			$this->error = $this->sku_db->getError();
		}
		return $sku_ids;
	}
	/**
	 * [_history 商品历史浏览记录]
	 * @param  integer $goods_id [description]
	 * @return [type]            [description]
	 */
	public function _history($id) {
		$id = (int) $id;
		if($id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$_history = cookie('_history');
		$_history = explode(',',$_history);
		if(empty($_history) || !in_array($id, $_history)) {
			array_unshift($_history, $id);
		}
		// 去除前20个为有效值
		$_history = array_slice($_history, 0, 20);
		cookie('_history', implode(',',$_history));
		return TRUE;
	}
	/**
	 * [clear_history 清除商品历史浏览记录]
	 * @return [type]            [description]
	 */
	public function clear_history() {
	    cookie('_history', null);
	    showmessage('success','',1,$result);
	}
	/**
	 * [get_sku_grades 根据分类和子商品价格生成子商品的商品价格范围]
	 * @param  [type] $id [description]
	 * @param  [type] $sku_price [子商品价格]
	 * @return [type]     [description]
	 */
	public function get_sku_grades($catid,$sku_price){
		if((int)$catid < 0){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if((int)$sku_price < 0){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$grades = $_grade_arr = $grade_arr = $_price_grade = $price_grade = array();
		$grades = $this->load->table('goods/goods_category')->where(array('id' => $catid))->getField('grade');
		$_grade_arr = explode(',',$grades);
		foreach ($_grade_arr as $value) {
			$grade_arr[] = explode('-',$value);
		}
		foreach ($grade_arr as $k => $v) {
			if($sku_price >= $v[0] && $sku_price <= $v[1]){
				$_price_grade = $v;
			}
		}
		$price_grade = implode('-', $_price_grade);
		return $price_grade;
	}
	/**
	 * [inc_hits 更新浏览记录]
	 * @param  [type] $id [商品Id]
	 * @return [type]     [description]
	 */
	public function inc_hits($id){
		if((int)$id < 0){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->index_db->where(array('sku_id' => $id))->setInc('hits');
		if(!$result){
			$this->error = $this->sku_db->getError();
		}
		return $result;
	}
	/**
	 * [attrs_detail 获取商品属性信息]
	 * @param  [type] $id [商品id]
	 * @return [type]     [description]
	 */
	public function attrs_detail($id){
		if((int)$id < 0){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$goods_attrs = $_goods_attrs = $attrs = array();
		$goods_attrs = $this->goodsattr_db->where(array('sku_id'=>$id,'type'=>1))->select();
		foreach ($goods_attrs as $key => $value) {
			$attrinfo[$key] = $this->load->table('goods/attribute')->where(array('id'=>$value['attribute_id']))->field('name,sort')->find();
			if($value['attribute_value'] && $attrinfo[$key]['name']){
				$attrs[$key]['name'] = $attrinfo[$key]['name'];
				$attrs[$key]['value'] = $value['attribute_value'];
				$attrs[$key]['sort'] = $attrinfo[$key]['sort'];
			}
		}
		$attrs = multi_array_sort($attrs,'sort',SORT_ASC);
		$temp = array();
		foreach($attrs as $item) {
		    list($n, $p) = array_values($item);
		    $temp[$n] =  array_key_exists($n, $temp) ? $temp[$n].','.$p : $p;
		}
		$arr = array();
		foreach($temp as $p => $n){
		    $arr[] = $p.'：'.$n;
		}
		return $arr;
	}
	/**
	 * [ajax_statusext ajax更改状态标签状态]
	 * @return [type] [description]
	 */
	public function ajax_statusext($params){
		$data = array();
		$statusext = $this->sku_db->where(array('sku_id'=>$params['sku_id']))->getField('status_ext');
		if($params['status_ext'] == $statusext){
			$data['status_ext'] = 0;
		}else{
			$data['status_ext'] = $params['status_ext'];
		}
		if(!is_null($data['status_ext'])){
			$result = $this->sku_db->where(array('sku_id'=>$params['sku_id']))->save($data);
			$this->index_db->where(array('sku_id'=>$params['sku_id']))->save($data);
		}
		if(!$result){
    		$this->error = lang('_operation_fail_');
    	}
    	return $result;
	}
	/**
	 * [goods_detail 外部调用商品详情接口,包含未处理的详细商品sku，spu信息]
	 * @param  [type] $ids   [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function goods_detail($ids,$extra = 'spu,price',$flag = TRUE){
		if(empty($ids)){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $goods = array();
		if(is_array($ids)){
				foreach ($ids AS $id) {
	    		$result = $this->fetch_by_id($id,$extra);
	    		if(!empty($result)){
	    			$goods[] = $result;
	    		}
	    	}
	    }else{
	    	$goods = $this->fetch_by_id($ids,$extra);
	    	if(!$flag){
	    		$categorys = $this->cate_service->get_parent($goods['catid']);
				array_push($categorys,$goods['catid']);
				$goods['catname'] = $this->cate_service->create_cat_format($categorys);
	    	}
    		foreach ($goods['spec'] AS $spec) {
				$goods['spec_show'] .= $spec['name'].':'.$spec['value'].'&nbsp;&nbsp;';
			}
	    }
    	if(empty($goods)){
    		$this->error = lang('goods_goods_not_exist','goods/language');
    		return FALSE;
    	}
    	return $goods;
    }
    /**
	 * [create_user_price 计算会员折扣价]
	 * @param  [type] $price [description]
	 * @return [type]        [description]
	 */
	public function create_user_price($price){
		return $price;
	}
    /**
	 * [goods_attr_screen 根据商品属性筛选]
	 * @param  [type] $attrs [选择的商品属性值]
	 * @return [arr]        [description]
	 */
	public function goods_attr_screen($attrs){
		$_sqlmap = $_join = $_goods_ids = array();
		$_count = 0;
		foreach ($attrs as $k => $attr) {
			list($_type, $_id) = explode("_", $k);
			$_id = (int) $_id;
			if($_id < 1 || empty($attr)) continue;
			$_count++;
			$type = ($_type == 'att') ? 1 : 2;
			$_join[] = "(`attribute_id` = '".$_id."' AND `attribute_value` = '".$attr."' AND `type` = '".$type."')";
		}
		if(!empty($_join)) $_sqlmap['_string'] = JOIN(" OR ", $_join);
		$_goods_ids = $this->goodsattr_db->where($_sqlmap)->group('sku_id')->having('count(sku_id) >= '.$_count)->getField('sku_id', TRUE);
		$_goods_ids = ($_goods_ids) ? implode(',',$_goods_ids) : -1;
		return $_goods_ids;
	}
	/**
	 * [lists 查询商品列表]
	 * @return [type] [description]
	 */
	public function lists($sqlmap = array(),$options = array()){
		$sqlmap = $this->build_goods_map($sqlmap);
		$map = array();
		if(!empty($sqlmap['status_ext'])){
			$map['status_ext'] = $sqlmap['status_ext'];
			unset($sqlmap['status_ext']);
		}
		$goods_ids = $this->build_goods_ids($sqlmap);
		if(isset($sqlmap['price'])){
			$map = $this->build_sku_map($sqlmap);
		}
		$map['sku_id'] = array('IN',$goods_ids);
		$map['status'] = array('EQ',1);
		$count = $this->index_db->where($map)->count();
		$sku_ids = $this->index_db->where($map)->page($options['page'])->order($sqlmap['order'])->limit($options['limit'])->getfield('sku_id',TRUE);
		foreach ($sku_ids AS $sku_id) {
			$result[] = $this->fetch_by_id($sku_id,'spu,price,brand,show_index');
		}
		return array('count' => $count,'lists' => $result);
	}
	/**
	 * [page 调用商品前台分页]
	 * @param  array  $sqlmap  [description]
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function page($sqlmap = array(),$options = array()){
		$sqlmap = $this->build_goods_map($sqlmap);
		$map = array();
		if(!empty($sqlmap['status_ext'])){
			$map['status_ext'] = $sqlmap['status_ext'];
			unset($sqlmap['status_ext']);
		}
		$goods_ids = $this->build_goods_ids($sqlmap);
		if(isset($sqlmap['price'])){
			$map = $this->build_sku_map($sqlmap);
		}
		$map['sku_id'] = array('IN',$goods_ids);
		$map['status'] = array('EQ',1);
		$lists['count'] = $this->index_db->where($map)->count();
		$totalPage = ceil($lists['count']/$options['limit']);
        $string = '';
		if($_GET['page'] > 1){
			$string .= '<a class="prev" href="'.page_url(array('page' => $_GET['page'] - 1)).'">上一页</a>';
		}else{
			$string .= '<a class="prev disabled">上一页</a>';
		}
        if($_GET['page'] < $totalPage){
			$string .= '<a class="next" href="'.page_url(array('page' => $_GET['page'] + 1)).'">下一页</a>';
		}else{
			$string .= '<a class="next disabled">下一页</a>';
		}
		$lists['page'] = $string;
		return $lists;
	}
	/**
	 * [build_goods_ids 获取子商品id]
	 * @param  [type] $sqlmap [description]
	 * @return [type]         [description]
	 */
	private function build_goods_ids($sqlmap){
		if($sqlmap['attribute_id'] && $sqlmap['attribute_value']){
			$map = array();
			$map['attribute_id'] = $sqlmap['attribute_id'];
			$map['attribute_value'] = $sqlmap['attribute_value'];
			$attr_goods_ids = $this->goodsattr_db->where($map)->getfield('sku_id',TRUE);
		}
		if($sqlmap['prom_type'] || $sqlmap['prom_id'] || $attr_goods_ids){
			$sqlmap['show_switch'] = TRUE;
		}
		if(!$sqlmap['show_switch']) $sqlmap['show_in_lists'] = 1;
		$_goods_ids = $this->index_db->where($sqlmap)->getfield('sku_id',TRUE);
		if(!empty($attr_goods_ids)){
			$_goods_ids = array_intersect($_goods_ids, $attr_goods_ids);
		}
		if(!is_null($sqlmap['goods_ids'])) {
			$goods_ids = explode(',',$sqlmap['goods_ids']);
			$goods_ids = array_intersect($_goods_ids, $goods_ids);
			$goods_ids = ($goods_ids) ? $goods_ids : -1;
		} else {
			$goods_ids = $_goods_ids;
		}
		return $goods_ids;
	}
	/**
	 * [build_goods_map 构造商品查询语句]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function build_goods_map($data){
		$map = $data;
		if(isset($data['statusext']) && is_numeric($data['statusext']) && $data['statusext'] > 0){
			$map['status_ext'] = $data['statusext'];
		}
		unset($map['brand_id']);
		if (isset($data['brand_id']) && is_numeric($data['brand_id']) && $data['brand_id'] > 0) {
			$map['brand_id'] = $data['brand_id'];
		}
		unset($map['catid']);
		if (isset($data['catid']) && is_numeric($data['catid'])) {
			$cat_ids = array();
			if($this->cate_service->has_child($data['catid'])){
				$cat_ids = $this->cate_service->get_child($data['catid']);
			}else{
				$cat_ids = array(0 => $data['catid']);
			}
			$map['catid'] = array('IN',$cat_ids);
		}
		if(empty($data['order'])){
           $map['order'] = 'sort asc,sku_id desc';
        }
		return $map;
	}
	/**
	 * [build_sku_map 构造子商品查询语句]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function build_sku_map($data){
		$map = array();
		$price = $data['price'];
		list($p_min, $p_max) = explode(',',$price);
		if($p_min > 0) {
			$map['shop_price'][] = array("EGT", $p_min);
		}
		if($p_max > 0) {
			$map['shop_price'][] = array("ELT", $p_max);
		}
		return $map;
	}
	/**
     * [create_sqlmap 组织筛选条件]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function create_sqlmap($params){
    	$result = $params;
    	$result['order'] = 'sort asc, sku_id desc';
    	list($_sort, $_by) = explode(',',$params['sort']);
		switch ($_sort) {
			case 'sale':
				$result['order'] = "`sales` desc";
				break;
			case 'hits':
				$result['order'] = "`hits` desc";
				break;
			case 'shop_price':
				$_by = ($_by == 'asc') ? 'desc' : 'asc';
				$result['order'] = "`shop_price` ".(($_by == 'asc') ? 'desc' : 'asc');
				break;
			case 'comper':
				$result['order'] = "`sales` desc,`favorites` desc,`hits` desc";
				break;
			default:
			 	$_by = ($_by == 'asc') ? 'desc' : 'asc';
			 	$result['order'] = $_sort ? $_sort.(($_by == 'asc') ? ' desc' : ' asc') : '';
			 	break;
		}
		if(empty($_sort)) $result['order'] = "`sales` desc,`favorites` desc,`hits` desc";
		$result['_by'] = $_by ? $_by : 'desc';
		$result['sort'] = $_sort ? $_sort : 'comper';
		if($params['attr']){
			foreach ($params['attr'] as $key => $value) {
				$params['attr'][$key] = base_decode($value);
			}
			$params['attr'] = daddslashes($params['attr']);
			$result['_goods_ids'] = $this->goods_attr_screen($params['attr']);
		}
		$result['show_switch'] = ($params['price'] || $params['attr']) ? 1 : 0;
    	return $result;
    }
    /**
     * [search 关键字查找商品]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function search($params){
		$keyword = remove_xss($params['keyword']);
		if(strlen($keyword) < 1 ) {
			showmessage(lang('_param_error_'));
		}
		$map = $sku_ids = array();
		$map['_string'] = "`sku_name` LIKE '%{$keyword}%'";
		$sku_ids = $this->sku_db->where($map)->getfield('sku_id',TRUE);
		$result['_goods_ids'] = $sku_ids ? implode(',',$sku_ids) : 0;
		list($_sort, $_by) = explode(',',$params['sort']);
			switch ($_sort) {
				case 'sale':
					$result['order'] = "`sales` desc";
					break;
				case 'hits':
					$result['order'] = "`hits` desc";
					break;
				case 'shop_price':
					$_by = ($_by == 'asc') ? 'desc' : 'asc';
					$result['order'] = "`shop_price` ".(($_by == 'asc') ? 'desc' : 'asc');
					break;
				default:
				$result['order'] = "`sales` desc,`favorites` desc,`hits` desc";
					break;
			}
			$result['_by'] = $_by ? $_by : 'desc';
			$result['sort'] = $_sort ? $_sort : 'comper';
			$result['show_switch'] = 1;
		if(!$result){
			$this->error = $this->logic->error;
		}
		return $result;
	}
	/**
	 * [sku_detail sku详情]
	 * @param  [type] $sku_ids [支持数组]
	 * @return [type]          [description]
	 */
	public function sku_detail($sku_ids){
		if(empty($sku_ids)){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if(is_array($sku_ids)){
			foreach ($sku_ids AS $sku_id) {
				$sku = $this->fetch_by_id($sku_id);
				$spec_str = '';
				foreach ($sku['spec'] AS $spec) {
					$spec_str .= $spec['name'].':'.$spec['value'].' ';
				}
				$sku['spec'] = $spec_str;
				$result[] = $sku;
			}
		}else{
			$result = $this->fetch_by_id($sku_ids);;
		}
		return $result;
	}
	/**
	 * [ajax_del_sku 删除子商品]
	 * @param  [type] $params [description]
	 * @return [type]      [description]
	 */
	public function ajax_del_sku($params){
		$id = $params['sku_id'];
		$label = $params['label'];
		$data = $sqlmap = $map = array();
		if($id){
			if($label == 4){
				$result = $this->delete_goods($id);
				return $result;
			}else{
				$sqlmap['sku_id'] = array('IN',$id);
				$data['status'] = -1;
				$result = $this->sku_db->where($sqlmap)->save($data);
				$this->index_db->where($sqlmap)->save($data);
				$spu_ids = $this->sku_db->where($sqlmap)->getField('spu_id',TRUE);
				$spu_ids = array_unique($spu_ids);
				foreach ($spu_ids AS $spu_id) {
					$sku_status = $this->sku_db->where(array('spu_id'=>$spu_id))->getField('sku_id,status',TRUE);
					$sku_status_num = 0;
					foreach ($sku_status AS $status) {
						if($status == -1){
							$sku_status_num++;
						}
					}
					if($sku_status_num == count($sku_status)){
						$this->spu_db->where(array('id'=>$spu_id))->save(array('status'=>-1));
					}
				}
				if($result === FALSE){
					$this->error = lang('_operation_fail_');
					return FALSE;
				}
				return TRUE;
			}
		}else{
			$this->error = lang('_param_error_');
			return FALSE;
		}
	}
	/**
	 * [delete_goods 删除商品,只有在回收站里进行此操作]
	 * @param  [array] $id [商品id]
	 * @return [type]     [description]
	 */
	private function delete_goods($id){
		$id = (array)$id;
		if(empty($id)) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$sqlmap = array();
		$sqlmap['sku_id'] = array('IN',$id);
		$infos = $this->sku_db->where($sqlmap)->getField('sku_id,imgs,content',true);
		foreach ($infos AS $info) {
			$this->load->service('attachment/attachment')->attachment('', json_decode($info['imgs'],true),false);
			$this->load->service('attachment/attachment')->attachment('', $info['content']);
		}
		$spu_ids = $this->sku_db->where($sqlmap)->getField('spu_id',TRUE);
		$result = $this->sku_db->where($sqlmap)->delete();
		$this->index_db->where($sqlmap)->delete();
		foreach ($spu_ids AS $spu_id) {
			$sku_ids_num = $this->sku_db->where(array('spu_id'=>$spu_id))->count();
			if($sku_ids_num == 0){
				$spu_info = $this->spu_db->where(array('id'=>$spu_id))->field('imgs,content')->find();
				$this->load->service('attachment/attachment')->attachment('', json_decode($spu_info['imgs'],true),false);
				$this->load->service('attachment/attachment')->attachment('', $spu_info['content']);
				$this->spu_db->where(array('id'=>$spu_id))->delete();
			}
		}
		return $result;
	}
	/**
	 * [ajax_show ajax]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function change_show_in_lists($id){
		if((int)$id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['show_in_lists'] = array('exp',' 1-show_in_lists ');
		$result = $this->sku_db->where(array('sku_id'=>$id))->save($data);
		$this->index_db->where(array('sku_id'=>$id))->save($data);
		if($result === FALSE){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
    	return TRUE;
    }
    /**
     * [change_sku_info ajax修改商品价格.库存]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function change_sku_info($params){
        if((int)$params['sku_id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$result = $this->sku_db->where(array('sku_id' => $params['sku_id']))->save($params);
		if(!$result){
    		$this->error = lang('_operation_fail_');
    		return FALSE;
    	}
    	if(!empty($params['shop_price'])){
			$min_max_price = $this->sku_db->field("min(shop_price) AS min_price, max(shop_price) AS max_price")->where(array("spu_id" => $params['spu_id']))->find();
			$this->spu_db->where(array('id' => $params['spu_id']))->save($min_max_price);
    	}
    	if(!empty($params['number'])){
			$list = $this->get_sku($params['spu_id']);
			$sku_total = 0;
			foreach ($list as $key=>$number){
			    $sku_total += $number['number'];
			}
			$this->spu_db->where(array('id' => $params['spu_id']))->save(array('sku_total' => $sku_total));
    	}
    	return $result;
    }
    /**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getField($field = '', $sqlmap = array()) {
		$exist = strpos($field, ',');
		if($exist === false){
			$result = $this->index_db->where($sqlmap)->getfield($field);
		}else{
			$result = $this->index_db->where($sqlmap)->field($field)->select();
		}
		if($result===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
	}
	/**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getBySkuid(){
		return $this->sku_db->getBySkuid();
	}
}