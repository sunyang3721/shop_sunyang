<?php
/**
 * 		购物车服务层
 */
class cart_service extends service {

	public function _initialize() {
		$this->table = $this->load->table('order/cart');
		$this->service_goods_sku = $this->load->service('goods/goods_sku');
	}

	/**
	 * 购物车添加商品(支持数组)
	 * @param  array	$params ：array('sku_id' => 'nums'[,'sku_id' => 'nums'...])
	 * @param  int 		$buyer_id ：会员id (游客为0，默认0)
	 * @param  boolean  $buynow ：是否立即购买(默认false)
	 * @return [boolean]
	 */
	public function cart_add($params ,$buyer_id = 0 ,$buynow = FALSE) {
		runhook('before_add_cart');
		if (empty($params)) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		foreach ($params as $skuid => $nums) {
			$skuid = (int) $skuid;
			$sku_info = $this->load->table('goods/goods_sku')->find($skuid);	// 获取商品信息
			unset($sku_info['content']);
			if (!$sku_info || $sku_info['status'] != 1) continue;
			$nums = ((int) $nums === 0) ? 1 : $nums;
    		$this->_add($skuid , $nums , $buyer_id ,$buynow);
		}
		if ($buyer_id > 0) { // 清空 购物车cookie
			cookie('cart_nums', 0);
        	cookie('cart', NULL);
		}
        return TRUE;
	}

	/* 执行添加购物车 */
	private function _add($sku_id ,$nums ,$buyer_id = 0 ,$buynow = FALSE) {
		$sqlmap = $data = array();
		if ($buyer_id > 0) {
			$data['buyer_id'] = $sqlmap['buyer_id'] = $buyer_id;
			$data['sku_id'] = $sqlmap['sku_id'] = $sku_id;
			$data['nums']   = $nums;
            $cart = $this->table->where($sqlmap)->find();
            if ($cart) {
            	if ($buynow == TRUE) {
            		$nums = max($nums ,$cart['nums']);
            	} else {
            		$nums = (int) $cart['nums'] + $nums;
            	}
            	return $this->set_nums($sku_id,$nums,$buyer_id);
            } else {
            	runhook('_add_empty_cart',$data);
            	return $this->table->update($data);
            }
		} else {
			$all_item = json_decode(cookie('cart'), TRUE);
        	$sku_ids = array_keys($all_item);
            if (count($all_item) > 0 && in_array($sku_id,$sku_ids)) {
        		$item = explode(',', $all_item[$sku_id]);
        		if ($buynow == TRUE) {
            		$nums = max($nums ,$item[1]);
            	} else {
            		$nums = (int) $item[1] + $nums;
            	}
            	return $this->set_nums($sku_id,$nums,$buyer_id);
            } else {
            	$all_item[$sku_id] = $sku_id.','.$nums;
            }
            cookie('cart_nums', count($all_item));
			cookie('cart', json_encode($all_item));
            return $all_item;
		}
	}

	/**
	 * 获取购物车列表
	 * @param  integer $buyer_id 会员id(游客为0)
	 * @param  string  $sku_ids   skuids (默认空,例：sku_id1[,number1][;sku_id2[,number2]])；多个sku用;分割。数量number可省略，代表购物车记录的件数。为空则获取购物车所有列表
	 * @param  boolean $isgroup   是否根据商家分组(默认false)
	 * @return [result]
	 */
	public function get_cart_lists($buyer_id = 0, $sku_ids = '', $isgroup = false) {
        $sqlmap = array();
		if ($sku_ids) {
			$sku_arr = array_filter(explode(';', $sku_ids));
			if (empty($sku_arr)) {
				$this->error = lang('_param_error_');
				return FALSE;
			}
			$sku_ids = $nums = $arr = array();
			foreach ($sku_arr as $k => $val) {
				$arr = explode(',', $val);
				$sku_ids[] = $arr[0];
				$nums[$arr[0]] = abs((int) $arr[1]);
			}
			$sqlmap['sku_id'] = array('IN',$sku_ids);
		}
		if ($buyer_id > 0) {
            $sqlmap['buyer_id'] = $buyer_id;
            runhook('get_cart_list_sqlmap',$sqlmap);
            $items = $this->table->where($sqlmap)->order("id DESC")->getField('sku_id,nums');
			foreach($items as $skuid => $num) {
				$num = ($nums[$skuid] == 0 || $nums[$skuid] > $num) ? $num : $nums[$skuid];
				$items[$skuid] = $skuid.','.$num;
			}
        } else {
            $items = json_decode(cookie('cart'), TRUE);
	        foreach ($items as $k => $v) {
	        	if ($sku_ids) {
	        		if (!in_array($k, $sku_ids)) unset($items[$k]);
	        	} else {
	        		$items[$k] = $v;
	        	}
	        }
        }
        $result = array();
        $all_prices = $numbers = $sold_count = 0;
        foreach ($items as $item) {
        	$val = array();
        	list($sku_id, $number) = explode(",", $item);
        	$sku_info = $this->service_goods_sku->goods_detail($sku_id);
        	runhook('cart_get_sku_info',$sku_info);
        	unset($sku_info['content']);
        	if($sku_info === false || ($sku_info['status'] == -1)) {
        		continue;
        	}
        	$number = min($sku_info['number'], $number);
        	$val['sku_id'] = $sku_id;
        	$val['number'] = $number;
        	$val['_sku_'] = $sku_info;
        	if($number == 0) {
        		$val['issold'] = true;
        		$sold_count++;
        	}
        	$sku_info['shop_price'] = $sku_info['prom_price'];
        	$val['prices'] = sprintf("%.2f",$sku_info['shop_price'] * $number);
        	$result['skus'][$sku_id] = $val;
        	$numbers += $number;
        	$all_prices += $val['prices'];
        }
		$result['all_prices']  = sprintf("%.2f",$all_prices);
		$result['sku_numbers'] = $numbers;
		$result['sku_counts']  = count($result['skus']);
		$result['sold_count']  = $sold_count;
        if($isgroup === true) {
        	$sku = array();
        	$sub_prices = $sub_counts = 0;
        	foreach ($result['skus'] as $sku_id => $v) {
        		$seller_id = (int) $v['_sku_']['seller_id'];
        		$sku[$seller_id]['sku_list'][$sku_id] = $v;
        		$sub_prices += $v['prices'];
        		$sub_numbers += $v['number'];
        		$sku[$seller_id]['sku_price'] = $sku[$seller_id]['sub_prices'] = $sub_prices;
        		$sku[$seller_id]['sub_numbers'] = $sub_numbers;
        	}
        	$result['skus'] = $sku;
        }
        runhook('cart_lists_extra',$result);
        return $result;
	}

	/**
	 * 设置购物车商品数量
	 * @param int $sku_id 	商品sku_id
	 * @param int $nums 	数量
	 * @param int $buyer_id 会员id(游客为0 ,默认0)
	 * @return [boolean]
	 */
	public function set_nums($sku_id ,$nums ,$buyer_id = 0) {
		$sku_id = (int) $sku_id;
		$nums = max(0, (int) $nums);
		$buyer_id = (int) $buyer_id;
		if ($sku_id < 1) {
			$this->error = lang('buy_number_require','order/language');
			return FALSE;
		}
		if ($buyer_id == 0) {	// 游客更新cookie
			$all_item = array();
			$all_item = json_decode(cookie('cart'),TRUE);
			if (empty($all_item[$sku_id])) {
				$this->error = lang('goods_not_exist','order/language');
				return FALSE;
			}
			if ($nums == 0) {	// 删除该记录
				unset($all_item[$sku_id]);
			} else {
				$set_info = explode(',', $all_item[$sku_id]);	// array('sku_id','nums')
				$set_info[1] = $nums;
				$all_item[$sku_id] = implode(',', array_values($set_info));
			}
			cookie('cart_nums', count($all_item));
        	cookie('cart', json_encode($all_item));
        	return TRUE;
		}
		$sqlmap = $data = array();
		$sqlmap['buyer_id'] = $buyer_id;
		$sqlmap['sku_id']   = $sku_id;
		$set_info = $this->table->where($sqlmap)->find();
		if (!$set_info) {
			$this->error = lang('goods_not_exist','order/language');
			return FALSE;
		}
		if ($nums == 0) {	// 删除该记录
			$result = $this->delpro($sku_id ,$buyer_id);
			if (!$result) return FALSE;
		} else {
			$data['nums']        = $nums;
			$data['system_time'] = time();
			$result = $this->table->where($sqlmap)->setField($data);
			if (!$result) {
				$this->error = $this->table->getError();
				return FALSE;
			}
		}
		return $result;
	}

	/**
	 * 删除购物车商品
	 * @param  int $sku_id 商品sku_id
	 * @param  int $buyer_id 会员id(游客为0 ,默认0)
	 * @return [boolean]
	 */
	public function delpro($sku_id ,$buyer_id = 0) {
		$sku_id = (int) trim($sku_id);
		$buyer_id = (int) $buyer_id;
		if (!$sku_id) {
			$this->error = lang('delete_parame_empty','order/language');
			return FALSE;
		}
		if ($buyer_id > 0) {
			$sqlmap = array();
			$sqlmap['buyer_id'] = $buyer_id;
			$sqlmap['sku_id']   = $sku_id;
			$result = $this->table->where($sqlmap)->delete();
			if ($result == FALSE) {
				$this->error = $this->table->getError();
				return FALSE;
			}
		} else {
			$all_item = json_decode(cookie('cart'), TRUE);
        	if (!$all_item) return FALSE;
            unset($all_item[$sku_id]);
            cookie('cart_nums', count($all_item));
            cookie('cart', json_encode($all_item));
		}
        return TRUE;
	}

	/**
	 * 清空购物车
	 * @param  int $buyer_id 会员id(游客为0 ,默认0)
	 * @return [boolean]
	 */
	public function clear($buyer_id = 0) {
		$buyer_id = (int) $buyer_id;
		if ($buyer_id == 0) {
			cookie('cart_nums', 0);
        	cookie('cart', NULL);
        	return TRUE;
		}
		$sqlmap = array();
		$sqlmap['buyer_id'] = $buyer_id;
		$_result = $this->table->where($sqlmap)->count();
		if ($_result == 0) {
			$this->error = lang('shopping_cart_empty','order/language');
			return FALSE;
		}
		$result = $this->table->where($sqlmap)->delete();
		if (!$result) {
			$this->error = $this->table->getError();
			return FALSE;
		}
		return $result;
	}

	/**
	 * 更换购物车商品规格
	 * @param  int $old_skuid 更换前的skuid
	 * @param  int $new_skuid 更换后的skuid
	 * @param  int $buyer_id 会员id(游客为0 ,默认0)
	 * @return [boolean]
	 */
	public function change_skuid($old_skuid ,$new_skuid ,$buyer_id = 0) {
		$old_skuid = (int) $old_skuid;
		$new_skuid = (int) $new_skuid;
		if (!$old_skuid || !$new_skuid) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$buyer_id = (int) $buyer_id;
		if ($buyer_id > 0) {
			$sqlmap = array();
			$sqlmap['buyer_id'] = $buyer_id;
			$sqlmap['sku_id'] = $old_skuid;
			$result = $this->table->where($sqlmap)->find();
			if (!$result) {
				$this->error = lang('goods_not_exist','order/language');
				return FALSE;
			}
			$data = array();
			$data['sku_id'] = $new_skuid;
			$data['nums'] = 1;
			$result = $this->table->where($sqlmap)->setField($data);
			if (!$result) {
				$this->error = $this->table->getError();
				return FALSE;
			}
			return $result;
		} else {
			$all_item = json_decode(cookie('cart'),TRUE);
			$sku_ids = array_keys($all_item);
			if (!in_array($old_skuid, $sku_ids)) {
				$this->error = lang('goods_not_exist','order/language');
				return FALSE;
			}
			unset($all_item[$old_skuid]);
			$all_item[$new_skuid] = $new_skuid.',1';
			cookie('cart_nums' , count($all_item));
			cookie('cart' , json_encode($all_item));
			return TRUE;
		}
	}

	/**
	 * 清除已售罄商品
	 * @param  int $buyer_id 会员id(游客为0)
	 * @return [boolean]
	 */
	public function clear_sold_out($buyer_id) {
		$buyer_id = (int) $buyer_id;
		$result = $this->get_cart_lists($buyer_id ,'',false);
		if ($result['sold_count'] > 0) {
			foreach ($result['skus'] as $cart) {
				if ($cart['_sku_']['number'] == 0) {
					$this->delpro($cart['sku_id'] ,$buyer_id);
				}
			}
		}
		return TRUE;
	}

	/**
	 * 登录成功后回调：把之前购物车的cookie存到cart表里
	 * @param  int $buyer_id 会员id
	 * @return [boolean]
	 */
	public function cart_sync($buyer_id) {
		$buyer_id = (int) $buyer_id;
		if ($buyer_id < 1) {
			$this->error = lang('no_login','misc/language');
			return FALSE;
		}
		$all_item = json_decode(cookie('cart'), TRUE);
		if($all_item) {
			$info = array();
			foreach($all_item as $key => $item) {
				list($sku_id,$num) = explode(',',$item);
				$data = array();
				$data['buyer_id'] = $buyer_id;
				$data['sku_id'] = $sku_id;
				$result = $this->table->where($data)->find();
				if($result) {
					$this->table->where($data)->setInc('nums', $num);
				} else {
					$data['nums'] = $num;
					$this->table->update($data);
				}
			}
		}
        cookie('cart', NULL);
		cookie('cart_nums', 0);
		return TRUE;
	}

	/**
	 * 减除相应购买数量
	 * @param  array $params  参数 ：array('sku_id1' => number1 [,'sku_id2' => number2])
	 * @param  int   $buyer_id 会员id(游客为0)
	 * @return [boolean]
	 */
	public function dec_nums($params = array() ,$buyer_id = 0) {
		$sku_ids = array();
		foreach ($params as $skuid => $num) {
			if ((int) $num < 1) unset($params[$skuid]);
			$sku_ids[] = $skuid;
		}
		if (empty($params) || empty($sku_ids)) {
			$this->error = lang('parameter_empty','order/language');
			return FALSE;
		}
		$sqlmap = array();
		$sqlmap['buyer_id'] = (int) $buyer_id;
		$sqlmap['sku_id'] = array('IN' ,$sku_ids);
		$carts = $this->table->where($sqlmap)->getField('sku_id,nums');
		foreach ($carts as $skuid => $cart_num) {
			$this->set_nums($skuid , ($cart_num - $params[$skuid]), $buyer_id);
		}
		return TRUE;
	}
}