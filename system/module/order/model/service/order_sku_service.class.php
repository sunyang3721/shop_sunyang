<?php
/**
 * 		订单商品服务层
 */
class order_sku_service extends service {

	protected $sqlmap = array();

	public function _initialize() {
		$this->table = $this->load->table('order/order_sku');
		$this->table_order = $this->load->table('order/order');
		$this->goods_sku = $this->load->service('goods/goods_sku');
	}

	/**
	 * 创建订单商品
	 * @param  array	$params 订单商品相关参数
	 * @return [boolean]
	 */
	public function create_all($params) {
		if (count(array_filter($params)) < 1) {
			$this->error = lang('order_goods_empty','order/language');
			return FALSE;
		}
		foreach ($params as $key => $val) {
            $goods = $this->goods_sku->fetch_by_id($val['sku_id']);
            $sku_info['sku_thumb'] = $val['sku_thumb'];
            $sku_info['sku_barcode'] = $val['sku_barcode'];
            $sku_info['sku_name'] = $val['sku_name'];
            $sku_info['sku_spec'] = $val['sku_spec'];
            $sku_info['sku_price'] = $val['sku_price'];
            $sku_info['real_price'] = $val['real_price'];
			$sku_info['content'] = $goods['content'];
            $sku_info['img_list'] = json_encode($goods['img_list']);
            $val['spu_id'] = $goods['spu_id'];
            $val['sku_info'] = json_encode($sku_info);
            $result = $this->table->update($val);
			if (!$result) {
				$this->error = $this->table->getError();
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * 根据主订单号获取订单商品
	 * @param 	$sn : 主订单号
	 * @return 	[result]
	 */
	public function get_by_order_sn($sn = '') {
		$sn = (string) trim($sn);
		if ($sn == '') {
			$this->error = lang('order_sn_not_null','order/language');
			return FALSE;
		}
		$sqlmap = $arr = array();
		$sqlmap['order_sn'] = $sn;
		$result = $this->table->where($sqlmap)->order('id ASC')->select();
		if (!$result) {
			$this->error = lang('order_goods_not_exist','order/language');
			return FALSE;
		}
		foreach ($result as $k => $val) {
			if (!empty($val['sku_spec'])) {
				$val['sku_spec'] = json_decode($val['sku_spec'], TRUE);
			}
			if (!empty($val['sku_spec'])) {
				$_spec = '';
				foreach ($val['sku_spec'] as $key => $v) {
					$_spec .= $v['name'].'：'.$v['value'].'&nbsp;&nbsp;';
 				}
 				$val['_sku_spec'] = $_spec;
			}
			$arr[$val['id']] = $val;
		}
		return $arr;
	}

	/**
	 * 获取订单商品详情
	 * @param 	$id : 订单商品主键ID
	 * @return 	[result]
	 */
	public function detail($id = 0) {
		$result = $this->table->find($id);
		if (!$result) {
			$this->error = lang('record_no_exist','order/language');
			return FALSE;
		}
		if (!empty($result['sku_spec'])) {
                $sku['sku_spec'] = json_decode($result['sku_spec'] ,TRUE);
                foreach ($result['sku_spec'] as $spec) {
                    $_spec .= $spec['name'].'：'.$spec['value'].'&nbsp;';
                }
            }
            $result['_sku_spec'] = $_spec;
		// 订单信息
		$result['_order'] = $this->table_order->detail($result['order_sn'])->output();
		return $result;
	}

	public function member_id($mid) {
		if((int) $mid > 0) {
			$this->sqlmap['member_id'] = $mid;
		}
		return $this;
	}

	/**
	 * 通用列表接口
	 * @param  array   $sqlmap 查询条件(默认空)
	 * @param  integer $limit  获取条数(默认取20条)
	 * @param  string  $order  排序(默认主键降序)
	 * @param  integer $page   分页页码(默认1)
	 * @return [result]
	 */
	public function lists($sqlmap = array(), $limit = 20, $order = 'id DESC', $page = 1) {
		$this->sqlmap = array_merge($this->sqlmap, $sqlmap);
        $DB = $this->table->where($this->sqlmap);
        $lists = &$DB->page($page)->limit($limit)->order($order)->select();
        foreach ($lists as $key => $value) {
        	$value['sku_spec'] = json_decode($value['sku_spec'], true);
        	$value['url'] = url("goods/index/detail",array('sku_id' => $value['sku_id']));
        	$lists[$key] = $value;
        }
        $count = $this->table->where($this->sqlmap)->count();
        return array('count' => $count, 'lists' => $lists);
	}

	/**
	 * 获取订单商品的成交记录
	 * @param int 	$sid 	商品sku_id||spu_id
	 * @param bool 	$isspu 	是否spu_id (当前为TRUE时，$all也必须为TRUE ,默认TURE)
	 * @param bool  $all 	是否查找所有sku的记录(默认TURE)
	 * @param array $options 附加条件($options['page'] => 分页页码、$options['limit'] => 获取条数,默认空)
	 * @return 	[result]
	 */
	public function records($sid = 0, $isspu = TRUE , $all = TRUE ,$options = array()) {
		if(!$sid) return FALSE;
		$all = ($isspu == TRUE) ? TRUE : $all;
		$sqlmap = $result = array();
		if ($all == FALSE) {
			$sqlmap['sku_id'] = $sid;
		} else {
			$sqlmap['spu_id'] = $sid;
		}
		$lists = array();
		$lists['count'] = $this->table->where($sqlmap)->count();
		$result = $this->table->where($sqlmap)->page($options['page'])->limit($options['limit'])->order('id DESC')->select();
		foreach ($result as $key => $value) {
			$name = $this->load->table('member/member')->where(array('id'=>$value['buyer_id']))->getfield('username');
			$lists['lists'][$key]['dateline'] = date('Y-m-d H:i:s',$value['dateline']);
			$lists['lists'][$key]['_sku_spec'] = $value['_sku_spec'];
			$lists['lists'][$key]['sku_price'] = $value['sku_price'];
			$lists['lists'][$key]['buy_nums'] = $value['buy_nums'];
			if(!$name) continue;
			$lists['lists'][$key]['username'] = cut_str($name, 1, 0).'**'.cut_str($name, 1, -1);
		}
		runhook('order_sku_records',$lists);
		return $lists;
	}

	public function add($params){
		$params['delivery_id'] = $params['delivery_id'] ? $params['delivery_id'] : 0;
		if($params['finish_status']){
			$params['delivery_status'] = 2;
		}elseif ($params['delivery_status'] == 0) {
			$params['delivery_status'] = 0;
		}else{
			$params['delivery_status'] = 1;
		}
		$data = $this->table->create($params);
		$this->table->add($data);
		return true;
	}

	/**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap = array()){
        $result = $this->table->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->table->getError();
            return false;
        }
        return $result;
    }
    /**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function fetch($sqlmap = array(), $limit = 20, $page = 1, $order = "") {
		$result = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		if($result===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	读取的字段
	 * @return [type]
	 */
	public function find($sqlmap = array(), $field = "") {
		$result = $this->table->where($sqlmap)->field($field)->find();
		if($result===false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
	/**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getField($field = '', $sqlmap = array()) {
		if(substr_count($field, ',') < 2){
			$result = $this->table->where($sqlmap)->getfield($field);
		}else{
			$result = $this->table->where($sqlmap)->field($field)->select();
		}
		if($result === false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}
}