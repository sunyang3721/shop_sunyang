<?php
/**
 * 		订单服务层
 */
class order_service extends service {

	protected $where = array();

	public function _initialize() {
		/* 实例化数据层 */
		$this->table      = $this->load->table('order/order');
		$this->table_cart = $this->load->table('order/cart');
		$this->table_sub  = $this->load->table('order/order_sub');
		$this->table_delivery = $this->load->table('order/order_delivery');
		$this->table_return = $this->load->table('order/order_return');
		$this->table_refund = $this->load->table('order/order_refund');
		$this->table_return_log = $this->load->table('order/order_return_log');
		$this->table_refund_log = $this->load->table('order/order_refund_log');
		$this->table_delivery_template = $this->load->table('admin/delivery_template');
		$this->table_member = $this->load->table('member/member');
		/* 实例化服务层 */
		$this->service_cart   = $this->load->service('order/cart');
		$this->service_sku    = $this->load->service('order/order_sku');
		$this->service_track  = $this->load->service('order/order_track');
		$this->service_order_log = $this->load->service('order/order_log');
		$this->service_goods_sku = $this->load->service('goods/goods_sku');
		$this->service_payment = $this->load->service('pay/payment');
		$this->service_member = $this->load->service('member/member');
        $this->service_order_trade = $this->load->service('order/order_trade');
	}

	/**
	 * 创建订单
	 * @param  integer $buyer_id    会员id
	 * @param  integer $skuids      商品id及数量 (string , 必传参数, 多个sku用;分割。数量number可省略，代表购物车记录的件数。整个参数为空则获取购物车所有列表) eg ：sku_id1[,number1][;sku_id2[,number2]]
	 * @param  integer $district_id 物流配置地区id
	 * @param  integer $pay_type    支付方式 (1：在线支付 2：货到付款)
	 * @param  array   $deliverys   物流详细 eg : array('seller_id1' => 'delivery_id1' [,'seller_id2' => 'delivery_id2'])
	 * @param  array   $order_prom  订单促销 eg : array('seller_id1' => 'order_prom_id1'[,'seller_id2' => 'order_prom_id2'])
	 * @param  array   $sku_prom    商品促销 eg : array('sku_id1' => 'sku_prom1'[,'sku_id2' => 'sku_prom2'])
	 * @param  array   $remarks     订单留言 eg : array('seller_id1' => '内容1'[,'seller_id2' => '内容2'])
	 * @param  array   $invoices    发票信息 eg : array('invoice' => '是否开发票 - 布尔值','title' => '发票抬头' , 'content' => '发票内容')
	 * @param  boolean $submit      是否创建 (为false时 获取订单结算信息，为true时 创建订单)
	 * @return mixed
	 */
	public function create($buyer_id = 0, $skuids = 0, $district_id, $pay_type = 1,$deliverys = array(), $order_prom = array(), $sku_prom = array(), $remarks = array(), $invoices = array(), $submit = false) {
		/* 定义默认值 */
		$sub_total = 0;			//商品总价
		$deliverys_total = 0;	// 总运费
		$invoice_tax = 0;		// 总发票费
		$promot_total = 0;		// 总优惠金额

		$setting = $this->load->service("admin/setting")->get();
		/* 第一步：获取购物车数据 */
		$carts = $this->service_cart->get_cart_lists($buyer_id, $skuids, TRUE);
		$carts['deliverys'] = true;
		if(empty($carts["skus"])) {
			$this->error = lang('shopping_cart_empty','order/language');
			return false;
		}
		/* 第二步：处理商品 */
		foreach ($carts['skus'] as $seller_id => $value) {
			/* 商家订单赠品 */
			$value['_give'] = array();
			$value['_promos'] = array();
			$deliverys = array(); //物流模板数据
			foreach ($value['sku_list'] as $sku_id => $sku) {
				// 获取默认运费模板（默认运费模板若不存在，说明用户未设置运费模板）
				$deliverys_default = $this->table_delivery_template->where(array('isdefault' => 1))->find();
				if($deliverys_default === FALSE){
					$this->error = lang('deliverys_template_empty', 'order/language');
					return FALSE;
				}
				//校验delivery_template_id 若false，等于默认运费模板id。若true，校验该运费模板是否存在，不存在等于默认运费模板id，存在则不变
				$sku['_sku_']['delivery_template_id'] = $sku['_sku_']['delivery_template_id'] ? ($this->table_delivery_template->find($sku['_sku_']['delivery_template_id']) ? $sku['_sku_']['delivery_template_id'] : $deliverys_default['id']) : $deliverys_default['id'];

				/* 组装运费模板商品数据 */
				$deliverys[$sku['_sku_']['delivery_template_id']][] = array(
					'number' => $sku['number'],
					'weight' => $sku['_sku_']['weight'],
					'volume' => $sku['_sku_']['volume']
				);

				/* 获取商品促销信息 */
				$sku['_promos'] = array();
				$sku['_give'] = array();
				$pro_map = array();
				$pro_map['_string'] = "FIND_IN_SET($sku[sku_id], `sku_ids`)";
				$pro_map['start_time'] = array(array("EQ", 0), array("ELT", time()), "OR");
				$pro_map['end_time'] = array(array("EQ", 0), array("EGT", time()), "OR");
				$pro_map['_logic'] = "AND";
				$_sku_promos = $this->load->table('promotion/promotion_goods')->where($pro_map)->find();
				if ($_sku_promos['rules']) {
                    foreach ($_sku_promos['rules'] as $key => $row) {
                        $discount[$key]  = $row['discount'];
                    }
                    array_multisort($discount, SORT_DESC, $_sku_promos['rules']);
                    foreach ($_sku_promos['rules'] as $k => $rule) {
                        if($rule['type'] == 'number_give' || $rule['type'] == 'amount_give'){
                            $rule['selected'] = 1;
                        }elseif ($k == 0) $rule['selected'] = 1;
						switch ($rule['type']) {
							case 'amount_discount':	// 满额立减
							case 'amount_give':		// 满额赠礼
								if($sku['prices'] >= $rule['condition']) {
									$sku['_promos'][$k] = $rule;
								}
								break;
							default :
								if ($sku['number'] >= $rule['condition']) {
									$sku['_promos'][$k] = $rule;
								}
								break;
						}
					}
					/* 叠加订单促销 */
					/* 是否同时享受订单促销*/
					if(!$_sku_promos['share_order']){
                        $sku_prom_price = $sku['prices'];
						if(isset($sku_prom[$sku_id]) && $_sku_promos['rules'][$sku_prom[$sku_id]]) {
							$sku_prom_price += $sku['prices'];
							if($_sku_promos['rules'][$sku_prom[$sku_id]]['type'] == 'amount_discount' || $_sku_promos['rules'][$sku_prom[$sku_id]]['type'] == 'number_discount') {
								$promot_total += $_sku_promos['rules'][$sku_prom[$sku_id]]['discount'];
								$sku['prices'] -= $_sku_promos['rules'][$sku_prom[$sku_id]]['discount'];
								$value['sku_price'] -= $_sku_promos['rules'][$sku_prom[$sku_id]]['discount'];	// 子订单skus总额
								$value['sub_prices'] -= $_sku_promos['rules'][$sku_prom[$sku_id]]['discount'];	// 子订单优惠后的总额
							} else {
								$sku['_give'] = $_sku_promos['rules'][$sku_prom[$sku_id]];
							}
						}
					}else{
						$sku_prom_price = 0;
						if($_sku_promos['rules'][$sku_prom[$sku_id]]['type'] == 'amount_discount' || $_sku_promos['rules'][$sku_prom[$sku_id]]['type'] == 'number_discount') {
							$promot_total += $_sku_promos['rules'][$sku_prom[$sku_id]]['discount'];
							$sku['prices'] -= $_sku_promos['rules'][$sku_prom[$sku_id]]['discount'];
						} else {
							$sku['_give'] = $_sku_promos['rules'][$sku_prom[$sku_id]];
						}
					}
				}
				runhook('order_create_sku_extra',$data = array('value' => &$value,'sku' => &$sku));
				$value['sku_list'][$sku_id] = $sku;
			}
			/* 运费计算 */
			$delivery_price = $this->_delivery_fee($deliverys,$district_id);
			if($delivery_price === FALSE) $carts['deliverys'] = FALSE;
			$value['delivery_price'] = $delivery_price ? sprintf('%.2f',$delivery_price) : '0.00';
			/* 参与订单促销的价格 */
			$order_prom_price = $value['sub_prices'] - $sku_prom_price;
			/* 查找所有订单促销 */
			$_order_promos = $this->load->service('promotion/promotion_order')->fetch_all($order_prom_price);
			if(isset($order_prom[$seller_id]) && $_order_promos[$order_prom[$seller_id]]) {
				$_order_promos_info = $_order_promos[$order_prom[$seller_id]];
				if($_order_promos_info['type'] == 0) {
					$promot_total += $_order_promos_info['discount'];
				} elseif($_order_promos_info['type'] == 1) {
					$value['delivery_price'] = 0;
				} else {
					$_give = $this->load->table('goods_sku')->where(array('sku_id' => $_order_promos_info['discount']))->field('sku_id, sku_name')->find();
					$value['_give'] = array('sku_id' => $_give['sku_id'], 'sku_name' => $_give['sku_name'], 'title' => $_order_promos[$order_prom[$seller_id]]['name']);
				}
			}
			$value['_promos'] = $_order_promos;
			$value['remarks'] = $remarks[$seller_id];
			$deliverys_total += $value['delivery_price'];
			$sub_total += $value['sub_prices'];
			runhook('order_create_seller_extra',$params = array('carts' => &$carts,'value' => &$value));
			$carts['skus'][$seller_id] = $value;
		}
		/* 商品总原价 */
		$carts['sku_total'] = sprintf("%.2f",$carts['all_prices']);
		/* 订单总运费 */
		$carts['deliverys_total'] = sprintf("%.2f", $deliverys_total);
		/* 订单发票 */
		$carts['invoice_tax'] = $invoice_tax;
		if($setting['invoice_enabled'] == 1 && $invoices['invoice'] == 1) {
			$carts['invoice_tax'] = (($sub_total + $carts['deliverys_total']) * $setting['invoice_tax']) / 100;
		}
		$carts['invoice_tax'] = sprintf("%.2f", $carts['invoice_tax']);
		/* 活动优惠总额*/
	    $promot_total = $promot_total;
	    $carts['promot_total'] = sprintf("%.2f", -$promot_total);
		/* 订单应付总额 */
		$carts['real_amount'] = sprintf("%.2f", max(0,$carts['sku_total'] + $carts['deliverys_total'] + $carts['invoice_tax'] - $promot_total));
		runhook('carts_extra',$carts);
		if($submit === true) { // 写入订单表
			/* 创建主订单 */
			if ($carts['deliverys'] === FALSE) {
				$this->error = lang('delivery_template_error','order/language');
				return FALSE;
			}
			// 读取收货人信息
			$member_address = $this->load->table("member/member_address")->where(array('id' => $_GET['address_id']))->find();
			if (!$member_address) {
				$this->error = lang('shipping_address_empty','order/language');
				return FALSE;
			}
			$invoice_enabled = $invoices['invoice'];
			$invoice_title = remove_xss($invoices['title']);
			$invoice_content = ($invoice_enabled == 0) ? '' : remove_xss($invoices['content']);
			if ($invoice_enabled == 1 && empty($invoice_title)) {
				$this->error = lang('invoice_head_empty','order/language');
				return FALSE;
			}
			if (!in_array($pay_type, array(1,2))) {
				$this->error = lang('pay_way_empty','order/language');
				return FALSE;
			}
			/* 组装收货地址 && 收货地址地区ids*/
			$districts = $this->load->service('admin/district')->fetch_parents($member_address['district_id']);
			krsort($districts);
			$address_name = $address_district_ids = '';
			foreach ($districts as $district) {
				$address_name .= $district['name'].' ';
				$address_district_ids .= $district['id'].',';
			}
			/* 组装主订单信息 */
			$order_sn = $this->_build_order_sn();
			$source = defined('MOBILE') ? (defined('IS_WECHAT') ? 3 : 2) : 1;
			$order_data = array(
				'sn'              => $order_sn,
				'buyer_id'        => $buyer_id,
				'seller_ids'      => 0,
				'source'          => $source,	// 订单来源(1：标准，2：移动端)
				'pay_type'        => $pay_type, 		// 支付类型(1:在线支付 , 2:货到付款)
				'sku_amount'      => $carts['sku_total'], // 商品总额
				'real_amount'     => $carts['real_amount'], 		// 应付总额
				'delivery_amount' => $carts['deliverys_total'] ,	// 总运费
				'promot_amount'   => $promot_total ,	// 所有优惠总额
				'invoice_tax'     => $carts['invoice_tax'], 	// 发票税额
				'invoice_title'   => $invoice_title, 	// 发票抬头
				'invoice_content' => $invoice_content,  // 发票内容
				'address_name'    => $member_address['name'],
				'address_mobile'  => $member_address['mobile'],
				'address_detail'  => $address_name.' '.$member_address['address'],
				'address_district_ids' => $address_district_ids,
			);
			runhook('build_order_info',$order_data);
			$oid = $this->table->update($order_data);
			if (!$oid) {
				$this->error = $this->table->getError();
				return FALSE;
			}
			/* 生成子订单 */
			$result = $this->_create_sub($carts ,$order_sn ,$oid ,$pay_type ,$buyer_id);
			if ($result == FALSE) {
				// 回滚删除之前的订单信息
				$this->table->where(array('id' => $oid))->delete();
				return FALSE;
			}
			// 钩子：下单成功
			$member = array();
			$member['member'] = $this->load->table('member/member')->where(array('id' => $buyer_id))->find();
			$member['order_sn'] = $order_sn;
			runhook('create_order',$member);
			return $order_sn;
		} else {
			return $carts;

		}
	}
	/**
	 * 计算运费
	 * @param  int $deliverys 商品物流信息 array('delivery_id1' => array('number' =>,'weight' => ,'volume' => ), 'delivery_id2' => array('number' =>,'weight' => ,'volume' => ))
	 * @param  int $district_id  地区id
	 * @return [decimal]
	 */
	private function _delivery_fee($deliverys = array(),$district_id = 0){
		if((empty($deliverys)) || (int) $district_id < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$total_fee = 0; //配送费
		foreach ($deliverys as $delivery_id => $delivery) {
			$value = 0;  //商品重量或体积或数量不定
			$delivery_template = $this->table_delivery_template->find($delivery_id);
			if($delivery_template === FALSE){
				$this->error = lang('delivery_template_not_exist', 'order/language');
				return FALSE;
			}
			foreach ($delivery AS $sku_info) {
				if($delivery_template['type'] == 'weight'){
					$value += $sku_info['weight'] * $sku_info['number'];
				}elseif ($delivery_template['type'] == 'volume') {
					$value += $sku_info['volume'] * $sku_info['number'];
				}else{
					$value += $sku_info['number'];
				}
			}
			$template = array(); //运费模板数据
			$parent_district_ids = $this->load->service('admin/district')->fetch_position($district_id,'id');
			foreach (json_decode($delivery_template['delivery_info'],TRUE) AS $_delivery) {
				foreach ($parent_district_ids as $district_id) {
					if(in_array($district_id, explode(',', $_delivery['district_ids']))){
						$template = $_delivery['template'];
						break;
					}
				}
			}
			if(empty($template)){
				$this->error = lang('_not_delivery_','order/language');
				return FALSE;
			}
			if($value > $template['first_value']){
				$total_fee += $template['first_fee'] + ceil(($value - $template['first_value'])/$template['follow_value']) * $template['follow_fee'];
			}else{
				$total_fee += $template['first_fee'];
			}

		}
		return $total_fee;
	}
	/**
	 * 创建子订单
	 * @param  array  $cart_skus 购物车分组信息
	 * @param  string $order_sn  主订单号
	 * @param  int 	  $id 		 主订单id
	 * @param  int 	  $pay_type  支付方式
	 * @param  int 	  $mid  用户id
	 * @return [boolean]
	 */
	private function _create_sub($cart_skus ,$order_sn ,$id ,$pay_type ,$mid = 0) {
		if(!$mid) return FALSE;
		if (count($cart_skus['skus']) == 0) return FALSE;
		/* 读取后台配置 (是否减库存) */
		$stock_change = $this->load->service('admin/setting')->get('stock_change');
		$operator = get_operator();	// 获取操作者信息
		$data = array();
		foreach ($cart_skus['skus'] as $k => $val) {
			$sub_sn = $this->_build_order_sn(TRUE);
			$data['sub_sn']         = $sub_sn;
			$data['order_id']       = $id;
			$data['order_sn']       = $order_sn;
			$data['pay_type']       = $pay_type;
			$data['buyer_id']       = $mid;
			$data['seller_id']      = $k;
			$data['remark']         = (string) $val['remarks'];
			$data['delivery_name']  = '';
			$data['sku_price']      = $val['sku_price'];
			$data['delivery_price'] = $val['delivery_price'];
			$data['real_price']     = $val['sub_prices'] + $val['delivery_price'];
			if ($val['_promos'][$_GET['order_prom'][$k]]) {
				$order_prom = $val['_promos'][$_GET['order_prom'][$k]];
				$order_prom['title'] = $order_prom['name'].' , '.$val['_give']['sku_name'];
				$order_prom['sku_info'] = $val['_give'];
			}
			$data['promotion']      = unit::json_encode($order_prom);
			$data['system_time']    = time();
			runhook('order_create_sub',$data);
			$result = $this->table_sub->update($data);
			if (!$result) {
				$this->error = $this->table_sub->getError();
				return FALSE;
				break;
			}
			// 创建订单商品
			$skus = $result = $load_decs = array();
			foreach ($val['sku_list'] as $v) {
				$_data = array();
				$_data['order_sn']    = $order_sn;
				$_data['sub_sn']      = $sub_sn;
				$_data['buyer_id']    = $mid;
				$_data['seller_id']   = $k;
				$_data['sku_id']      = $v['sku_id'];
				$_data['sku_thumb']   = $v['_sku_']['thumb'];
				$_data['sku_barcode'] = $v['_sku_']['barcode'];
				$_data['sku_name']    = $v['_sku_']['name'];
				$_data['sku_spec']    = unit::json_encode($v['_sku_']['spec']);
				$_data['sku_price']   = $v['_sku_']['shop_price'];
				$_data['real_price']  = $v['prices'];
				$_data['buy_nums']    = $v['number'];
                $_data['sku_edition'] = $v['_sku_']['edition'];
				$_data['promotion']   = unit::json_encode($v['_promos'][$_GET['sku_prom'][$v['sku_id']]]);
				$_data['delivery_template_id']    = $v['_sku_']['delivery_template_id'];
				$skus[]               = $_data;
				/* 商品促销的赠品 */
				if($v['_give']) {
					$sku_info = $this->service_goods_sku->goods_detail($v['_give']['discount']);
					if($sku_info) {
						$_data                = array();
						$_data['order_sn']    = $order_sn;
						$_data['sub_sn']      = $sub_sn;
						$_data['buyer_id']    = $mid;
						$_data['seller_id']   = $k;
						$_data['sku_id']      = $sku_info['sku_id'];
						$_data['sku_thumb']   = $sku_info['thumb'];
						$_data['sku_barcode'] = $sku_info['barcode'];
						$_data['sku_name']    = $sku_info['name'];
                        $_data['sku_edition'] = $sku_info['edition'];
						$_data['sku_spec']    = unit::json_encode($v['_sku_']['spec']);
						$_data['sku_price']   = 0;
						$_data['real_price']  = 0;
						$_data['buy_nums']    = 1;
						$_data['is_give']     = 1;
						$skus[]               = $_data;
					}
				}
				// 待减除购物车记录数量 组装  array('sku_id1' => number1 [,'sku_id2' => number2])
				$load_decs[$v['sku_id']] = $v['number'];
			}
			/* 创建订单赠品 */
			if($val['_give']) {
				$sku_info = $this->service_goods_sku->goods_detail($val['_give']['sku_id']);
				if($sku_info) {
					$_data                = array();
					$_data['order_sn']    = $order_sn;
					$_data['sub_sn']      = $sub_sn;
					$_data['buyer_id']    = $mid;
					$_data['seller_id']   = $k;
					$_data['sku_id']      = $sku_info['sku_id'];
					$_data['sku_thumb']   = $sku_info['thumb'];
					$_data['sku_barcode'] = $sku_info['barcode'];
					$_data['sku_name']    = $sku_info['name'];
					$_data['sku_edition'] = $sku_info['edition'];
					$_data['sku_spec']    = unit::json_encode($v['_sku_']['spec']);
					$_data['sku_price']   = 0;
					$_data['real_price']  = 0;
					$_data['buy_nums']    = 1;
					$_data['is_give']     = 1;
					$skus[]               = $_data;
				}
			}
			runhook('order_create_skus',$skus);
			$result = $this->service_sku->create_all($skus);
			if (!$result) {
				$this->error = $this->service_sku->error;
				return FALSE;
				break;
			}
			/* 减库存 */
			if (($stock_change != NULL && $stock_change == 0) || ($stock_change == 1 && $pay_type == 2)) {
				foreach ($val['sku_list'] as $k => $cart) {
					$this->load->service('goods/goods_sku')->set_dec_number($k,$cart['number']);
				}
			}
			/* 清除购物车已购买数据 */
			$this->service_cart->dec_nums($load_decs ,$mid);
			// 订单日志
			$data = array();
			$data['order_sn']      = $order_sn;
			$data['sub_sn']        = $sub_sn;
			$data['action']        = '创建订单';
			$data['operator_id']   = $operator['id'];
			$data['operator_name'] = $operator['username'];
			$data['operator_type'] = $operator['operator_type'];
			$data['msg']           = '提交购买商品并生成订单';
			$this->service_order_log->add($data);
			// 订单跟踪
			$track_msg = $pay_type == 1 ? '系统正在等待付款': '请等待系统确认';
			$this->service_track->add($order_sn ,$sub_sn , '您提交了订单，'.$track_msg);
		}
		return TRUE;
	}

	/**
	 * 根据日期生成唯一订单号
	 * @param boolean $refresh 	是否刷新再生成
	 * @return string
	 */
	private function _build_order_sn($refresh = FALSE) {
		if ($refresh == TRUE) {
			return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
		}
		return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
	}

	/**
	 * 获取订单列表
	 * @param  array  $sqlmap 查询条件，具体参数见 build_sqlmap()方法
	 * @return [result]
	 */
	public function get_lists($sqlmap = array()) {
		$sqlmap = $this->build_sqlmap($sqlmap);
		$result = $this->table->where($sqlmap)->order('id DESC')->select();
		return $result;
	}

	/**
	 * 生成查询条件
	 * @param  $options['type'] (1:待付款|2:待确认|3:待发货|4:待收货|5:已完成|6:已取消|7:已回收|8:已删除)
	 * @param  $options['keyword'] 	关键词(订单号|收货人姓名|收货人手机)
	 * @return [$sqlmap]
	 */
	public function build_sqlmap($options) {
		if(empty($options['type'])){
       		$options['type'] = $options['map']['type'];
     	}
		extract($options);
		$sqlmap = array();
		if (isset($type) && $type > 0) {
			switch ($type) {
				// 待付款
				case 1:
					$sqlmap['pay_type']   = 1;
					$sqlmap['status']     = 1;
					$sqlmap['pay_status'] = 0;
					break;
				// 待确认
				case 2:
					$sqlmap['status'] = 1;
					$sqlmap['_string'] = '(pay_type=2 or pay_type=1 and pay_status=1) and confirm_status<>2';
					break;
				// 待发货
				case 3:
					$sqlmap['status'] = 1;
					$sqlmap['confirm_status'] = array('IN',array(1,2));
   					$sqlmap['delivery_status'] = array('IN',array(0,1));
					break;
				// 待收货
				case 4:
					$sqlmap['status'] = 1;
					// 获取所有待收货的主订单号
					$sub_sns = $this->load->table('order/order_delivery')->where(array('isreceive' => 0))->getField('sub_sn' ,TRUE);
					$map = array();
					$map['sub_sn'] = array('IN' ,$sub_sns);
					$order_sns = $this->load->table('order/order_sub')->where($map)->getField('order_sn' ,TRUE);
					$sqlmap['sn'] = array('IN' ,$order_sns);
					break;
				// 已完成
				case 5:
					$sqlmap['status'] = 1;
					$sqlmap['finish_status'] = 2;
					break;
				// 已取消
				case 6:
					$sqlmap['status'] = 2;
					break;
				// 已作废
				case 7:
					$sqlmap['status'] = (defined('IN_ADMIN')) ? array('GT', 2) : 3;
					break;
				// 前台已删除
				case 8:
					$sqlmap['status'] = 4;
			}
		}
		if (isset($keyword) && !empty($keyword)) {
			$buyer_ids = $this->load->table('member/member')->where(array('username' => array('LIKE','%'.$keyword.'%')))->getField('id',TRUE);
			$sn = $this->load->table('order/order_trade')->where(array('trade_no' => array('LIKE','%'.$keyword.'%')))->getField('order_sn',TRUE);
			if($buyer_ids){
				$sqlmap['buyer_id'] = array('IN',$buyer_ids);
			}
			if($sn){
				$sqlmap['sn'] = array('IN',$sn);
			}
			if (!$buyer_ids && !$sn) {
				$sqlmap['sn|address_name|address_mobile|pay_sn'] = array('LIKE','%'.$keyword.'%');
			}
		}
		return $sqlmap;
	}

	/**
	 * 修改订单应付总额
	 * @param  string  $sub_sn 		子订单号
	 * @param  float   $real_price  修改后的价格
	 * @return [boolean]
	 */
	public function update_real_price($sub_sn = '',$real_price = 0) {
		$real_price = sprintf('%.2f' , max(0,(float) $real_price));
		$order = $this->table_sub->where(array('sub_sn' => $sub_sn))->find();
		if ($order['pay_type'] == 2 || $order['pay_status'] ==1) {
			$this->error = lang('dont_edit_order_amount','order/language');
			return FALSE;
		}
		$result = $this->table_sub->where(array('sub_sn' => $sub_sn))->setField('real_price' ,$real_price);
		if (!$result) {
			$this->error = $this->table_sub->getError();
			return FALSE;
		}
		// 重新计算主订单应付总额
		$new_prices = $this->table_sub->where(array('order_sn' => $order['order_sn']))->sum('real_price');
		$this->table->where(array('sn' => $order['order_sn']))->setField('real_amount' ,$new_prices);
		// 订单操作日志
		$operator = get_operator();	// 获取操作者信息
		$data = array();
		$data['order_sn']      = $order['order_sn'];
		$data['sub_sn']        = $sub_sn;
		$data['action']        = '修改订单应付总额';
		$data['operator_id']   = $operator['id'];
		$data['operator_name'] = $operator['username'];
		$data['operator_type'] = $operator['operator_type'];
		$data['msg']           = '原应付总额「'.$order['real_price'].'」修改为「'.$real_price.'」';
		$this->service_order_log->add($data);
		return $result;
	}

	/**
	 * 提交订单支付
	 * @param  string  $sn        主订单号
	 * @param  integer $isbalance 是否余额支付
	 * @param  string  $pay_code  支付方式code
	 * @param  string  $pay_bank  网银支付bank($pay_code = 'bank'时必填)
	 * @param  string  $mid  用户id
	 * @return [result]
	 */
	public function detail_payment($sn = '' ,$isbalance = 0,$pay_code = '' ,$pay_bank = '' ,$mid = 0) {
		$sn = (string) $sn;
		$isbalance = (int) $isbalance;
		/* 读取后台配置 (是否减库存) */
		$stock_change = $this->load->service('admin/setting')->get('stock_change');
		$order = $this->table->detail($sn)->output();
		if ($order['buyer_id'] != $mid || $order['status'] != 1) {
			$this->error = lang('_valid_access_');
			return FALSE;
		}
		if ($order['pay_status'] == 1) {
			$this->error = lang('order_paid','pay/language');
			return FALSE;
		}
		if ($pay_code == 'bank' && empty($pay_bank)) {
			$this->error = lang('pay_ebanks_error','order/language');
			return FALSE;
		}
		$money = $this->load->table('member/member')->where(array('id' => $mid))->getfield('money');
		// 后台余额支付开关
		$balance_pay = $this->load->service('admin/setting')->get('balance_pay');
		// 还需支付总额
		$total_fee = round($order['real_amount'] - $order['balance_amount'], 2);
		/* 含有余额支付的 */
		if ($balance_pay == 1 && $isbalance == 1 && $money > 0) {
			$balance_amount = $total_fee;	// 本次余额支付的金额
			if ($money < $total_fee) {
				$balance_amount = $money;
				$total_fee = abs(round($total_fee - $money,2));
			} else {
				$total_fee = 0;
			}
			// 扣除会员余额($balance_amount),并写入 冻结资金
			$result = $this->service_member->action_frozen($mid ,$balance_amount , true ,'余额支付订单,主订单号:'.$sn);
			if (!$result) {
				$this->error = $this->service_member->error;
				return FALSE;
			}
			// 把余额支付金额写入订单余额支付总额里
			$_balance_amount = $order['balance_amount'] + $balance_amount;
			$result = $this->table->where(array('sn' => $sn))->setField('balance_amount' ,$_balance_amount);
			if (!$result) {
				$this->error = $this->table->getError();
				return FALSE;
			}
		}
		/* 需要再用网银支付的 */
		$result = array();
		if ($total_fee > 0) {
            $trade_no = $this->service_order_trade->get_trade_no($sn,$total_fee,$pay_code);

            $pay_info = array();
            $pay_info['trade_sn']  = $trade_no;
            $pay_info['total_fee'] = $total_fee;
            $pay_info['subject']   = '订单支付号：'.$trade_no;
            $pay_info['pay_bank']  = $pay_bank;
			/* 请求支付 */
			$gateway = $this->service_payment->gateway($pay_code,$pay_info);
			if($gateway === false) showmessage(lang('pay_set_error','pay/language'));
			$gateway['order_sn'] = $sn;
            $gateway['trade_no'] = $trade_no;
			$gateway['total_fee'] = $total_fee;
		} else {
			// 设置订单为支付状态
			$data = array();
			$order = $this->table->detail($sn)->output();
			if($order['balance_amount'] == $order['real_amount']  && $balance_pay==1){
				$data['pay_method'] = '余额支付';
			}elseif($order['balance_amount'] == 0){
				$data['pay_method'] = $pay_code;
			}else{
				$data['pay_method'] ='余额支付'. '+' . $pay_code;
			}
			$data['paid_amount'] = $order['real_amount'];
			$data['msg'] = '您的订单已支付成功';
			$this->load->service('order/order_sub')->set_order($sn ,'pay','',$data);
			$result['pay_success'] = 1;
		}
		//支付成功后减少库存
		if ($stock_change != NULL && $stock_change == 1) {
			$goods = $this->service_sku->get_by_order_sn($sn);
			foreach ($goods as  $sku) {
				$this->load->service('goods/goods_sku')->set_dec_number($sku['sku_id'],$sku['buy_nums']);
			}
		}
		/* 支付后的跳转地址 */
		$gateway['url_forward'] = url('order/order/pay_success',array('order_sn' => $sn));
		$result['gateway'] = $gateway;
		return $result;
	}
	/**
	 * 商品导入
	 */
	public function order_import($params){
		$params['sku_amount'] = $params['shop_price'][0] * $params['shop_number'][0];
		$sub_data = array();
		$sub_data['order_id'] = $params['id'];
		$sub_data['order_sn'] = $params['sn'];
		$sub_data['sub_sn'] = substr($params['sn'],0,10).random(10,1);
		$sub_data['pay_type'] = $params['pay_type'];
		$sub_data['buyer_id'] = $params['buyer_id'];
		$sub_data['seller_id'] = $params['seller_id'];
		$sub_data['delivery_name'] = $params['delivery_name'] ? $params['delivery_name'] : '';
		$sub_data['sku_price'] = $params['sku_amount'];
		$sub_data['delivery_price'] = $params['delivery_amount'];
		$sub_data['real_price'] = $params['paid_amount'];
		$sub_data['status'] = $params['status'];
		$sub_data['pay_status'] = $params['pay_status'];
		$sub_data['confirm_status'] = $params['confirm_status'];
		$sub_data['delivery_status'] = $params['delivery_status'];
		$sub_data['finish_status'] = $params['finish_status'];
		$sub_data['pay_time'] = $params['pay_time'];
		$sub_data['confirm_time'] = $params['confirm_time'];
		$sub_data['delivery_time'] = $params['delivery_time'];
		$sub_data['finish_time'] = $params['finish_time'];
		$sub_data['system_time'] = $params['system_time'];
		$sub_data['remark'] = $params['remark'];
		$sub_data['promotion'] = $params['promotion'];
		//加入订单
		if($params['pay_method'] == 0){
			$params['pay_method'] = '在线支付';
		}elseif ($params['pay_method'] == 1) {
			$params['pay_method'] = '货到付款';
		}else{
			$params['pay_method'] = $params['pay_method'];
		}
		$params['promot_amount'] = $params['sku_amount'] + $params['delivery_amount'] - $params['paid_amount'];
		$order_result = $this->table->add($params);
		//加入子订单
		$sub_result = $this->table_sub->add($sub_data);
		//物流
		if($params['delivery_time']){
			$delivery = array();
			$delivery['o_sku_ids'] = $params['o_sku_ids'];
			$delivery['sub_sn'] = $sub_data['sub_sn'];
			$delivery['delivery_id'] = $params['delivery_id'];
			$delivery['delivery_name'] = $params['delivery_name'];
			$delivery['delivery_sn'] = $params['delivery_sn'];
			$delivery['delivery_time'] = $params['delivery_time'];
			$delivery['isreceive'] = $params['isreceive'];
			$delivery['receive_time'] = $params['receive_time'];
			$delivery['print_time'] = $params['print_time'];
			$delivery_data = $this->table_delivery->create($delivery);
			$this->table_delivery->add($delivery_data);
		}
		return TRUE;
	}
	public function get_order_lists($sqlmap,$page,$limit){
		$orders = $this->table->page($page)->where($sqlmap)->limit($limit)->order('id DESC')->select();
		$lists = array();
		foreach ($orders AS $order) {
			$lists[] = array('id'=>$order['id'],'sn'=>$order['sn'],'username'=>$order['_buyer']['username'],'address_name'=>$order['address_name'],'address_mobile'=>$order['address_mobile'],'system_time'=>$order['system_time'],'real_amount'=>$order['real_amount'],'_pay_type'=>$order['_pay_type'],'source'=>$order['source'],'seller_ids'=>$order['seller_ids'],'_status'=>$order['_status']['now'],'_showsubs'=>$order['_showsubs'],'sub_sn'=>$order['_subs']['0']['sub_sn']);
		}
		return $lists;
	}

	/*
	 *订单变更收货地址
	 */
	public function edit_address($sqlmap){
		$districts = $this->load->service('admin/district')->fetch_parents($sqlmap['district_id']);
			krsort($districts);
			$address_name = $address_district_ids = '';
			foreach ($districts as $district) {
				$address_name .= $district['name'].' ';
				$address_district_ids .= $district['id'].',';
			}
		$data['address_detail'] = $address_name." ".$sqlmap['address'];
		$data['address_district_ids'] =$address_district_ids;
		$data['address_name'] = $sqlmap['name'];
		$data['address_mobile'] = $sqlmap['mobile'];
		$order = $this->table->where(array('sn' =>$sqlmap['order_sn'] ))->save($data);
		if(!$order){
			return false;
		}
		// 订单操作日志
		$sub_sn = $this->table_sub->where(array('order_sn' => $sqlmap['order_sn']))->getField('sub_sn');
		$operator = get_operator();	// 获取操作者信息
		$data = array();
		$data['order_sn']      = $sqlmap['order_sn'];
		$data['sub_sn']        = $sub_sn;
		$data['action']        = '修改订单收货信息';
		$data['operator_id']   = $operator['id'];
		$data['operator_name'] = $operator['username'];
		$data['operator_type'] = $operator['operator_type'];
		$data['msg']           = $sqlmap['remark'];
		$this->service_order_log->add($data);
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
    public function member_table_detail($order_sn){
		return $this->table->detail($order_sn)->subs(FALSE ,FALSE, FALSE)->output();
	}
	public function order_table_detail($order_sn){
		return $this->table->detail($order_sn)->output();
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function fetch($sqlmap = array(), $limit = 20, $page = 1, $order = "", $field = "") {
		$result = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->field($field)->select();
		if($result===false){
			$this->error = $this->table->getError();
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
	 * @param  array 	sql条件
	 * @param  integer 	读取的字段
	 * @return [type]
	 */
	public function order_return_find($sqlmap = array(), $field = "") {
		$result = $this->table_return->where($sqlmap)->field($field)->find();
		if($result===false){
			$this->error = $this->table_return->getError();
			return false;
		}
		return $result;
	}
	public function order_refund_find($sqlmap = array(), $field = "") {
		$result = $this->table_refund->where($sqlmap)->field($field)->find();
		if($result===false){
			$this->error = $this->table_refund->getError();
			return false;
		}
		return $result;
	}
	/**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function order_return_field($field = '', $sqlmap = array()) {
		if(substr_count($field, ',') < 2){
			$result = $this->table_return->where($sqlmap)->getfield($field);
		}else{
			$result = $this->table_return->where($sqlmap)->field($field)->select();
		}
		if($result === false){
			$this->error = $this->table_return->getError();
			return false;
		}
		return $result;
	}
	/*修改*/
	public function order_return_update($data){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table_return->update($data, FALSE);
		if($result === false){
			$this->error = $this->table_return->getError();
			return false;
		}
		return $result;
	}
	public function order_return_log_update($data, $sqlmap = array()){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table_return_log->where($sqlmap)->update($data);
		if($result === false){
			$this->error = $this->table_return_log->getError();
			return false;
		}
		return $result;
	}
	public function order_refund_update($data){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table_refund->update($data, FALSE);
		if($result === false){
			$this->error = $this->table_refund->getError();
			return false;
		}
		return $result;
	}
	public function order_refund_log_update($data, $sqlmap = array()){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table_refund_log->where($sqlmap)->update($data);
		if($result === false){
			$this->error = $this->table_refund_log->getError();
			return false;
		}
		return $result;
	}
	/**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function order_delivery_count($sqlmap = array()){
        $result = $this->table_delivery->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->table_delivery->getError();
            return false;
        }
        return $result;
    }
    /*修改*/
	public function order_delivery_update($data, $sqlmap = array()){
		if(empty($data)){
			$this->error = lang('_param_error_');
			return false;
		}
		$result = $this->table_delivery->where($sqlmap)->update($data);
		if($result === false){
			$this->error = $this->table_delivery->getError();
			return false;
		}
		return $result;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	读取的字段
	 * @return [type]
	 */
	public function order_delivery_find($sqlmap = array(), $field = "") {
		$result = $this->table_delivery->where($sqlmap)->field($field)->find();
		if($result === false){
			$this->error = $this->table_delivery->getError();
			return false;
		}
		return $result;
	}

	//获取用户资料
	public function member_data($sqlmap){
		$result = $this->load->table('member/member')->find($sqlmap);
		return $result;
	}

	public function order_return_cancel($id, $mid){
		if((int)$mid < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$return = $this->order_return_find(array('id'=>$id));
		if($return === false){
			return false;
		}
		if($return['status'] == -1){
			$this->error = lang('已经成功取消，请勿重复提交');
			return false;
		}
		$this->table_return->where(array('id'=>$id))->setField('status', -1);
		$this->load->table('order/order_server')->where(array('order_sn' => $return['order_sn']))->setField('status', -1);
		$username = $this->table_member->where(array('id' => $mid))->getField('username');
		// 写入退货日志
		$log['return_id']     = $return['id'];
		$log['order_sn']      = $return['order_sn'];
		$log['sub_sn']        = $return['sub_sn'];
		$log['o_sku_id']      = $return['o_sku_id'];
		$log['action']        = '用户取消退货退款申请';
		$log['operator_id']   = $mid;
		$log['operator_name'] = $username;
		$log['operator_type'] = 2;	
		$this->order_return_log_update($log);
		return true;
	}

	public function order_refund_cancel($id, $mid){
		if((int)$mid < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$refund = $this->order_refund_find(array('id'=>$id));
		if($refund === false){
			return false;
		}
		if($refund['status'] == -1){
			$this->error = lang('已经成功取消，请勿重复提交');
			return false;
		}
		$this->table_refund->where(array('id'=>$id))->setField('status', -1);
		$this->load->table('order/order_server')->where(array('order_sn' => $refund['order_sn']))->setField('status', -1);
		$username = $this->table_member->where(array('id' => $mid))->getField('username');
		// 写入退货日志
		$log['refund_id']     = $refund['id'];
		$log['order_sn']      = $refund['order_sn'];
		$log['sub_sn']        = $refund['sub_sn'];
		$log['o_sku_id']      = $refund['o_sku_id'];
		$log['action']        = '用户取消退款申请';
		$log['operator_id']   = $mid;
		$log['operator_name'] = $username;
		$log['operator_type'] = 2;
		$this->order_refund_log_update($log);
		return true;
	}
}