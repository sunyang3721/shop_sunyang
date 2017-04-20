<?php

/**
 *        订单交易服务层
 */
class order_trade_service extends service
{


    public function _initialize()
    {
        $this->table = model('order/order_trade');
    }

    /**
     * 对订单发起支付时判断订单交易表里是否有该订单的支付记录，
     * 如果没有，生成一条支付记录，并创建支付单号，写入支付金额，以创建的支付单号作为第三方支付需要的订单号发起支付；
     * 如果有该订单的支付记录，需判断传过来的订单号和金额是否与订单交易表中“未支付”状态的订单号和金额一致；
     * 如果一致，则用该订单的支付单号作为第三方支付需要的订单号发起支付；
     * 如果不一致，则需再生成一条新的支付记录，并重新生成支付单号作为第三方支付需要的订单号发起支付。
     *
     * @param string $order_sn 订单号
     * @param string $total_fee 金额
     * @param string $method 交易方式
     * @return string 交易单号
     */
    public function get_trade_no($order_sn = '', $total_fee = '', $method = '')
    {
        $map['order_sn'] = $order_sn;
        $map['status'] = 0;
        $result = $this->table->where($map)->find();
        if (!$result) {
            $trade_info['order_sn'] = $order_sn;
            $trade_info['trade_no'] = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
            $trade_info['total_fee'] = $total_fee;
            $trade_info['method'] = $method;
            $trade_info['time'] = time();
            $trade_info['status'] = 0;
            $this->table->add($trade_info);
            return $trade_info['trade_no'];
        }

        if($method == 'alipay' && $result['method'] == $method && $result['total_fee'] != $total_fee){
            $result['status'] = '-1';
            $this->table->update($result);

            $trade_info['order_sn'] = $order_sn;
            $trade_info['trade_no'] = $result['trade_no'];
            $trade_info['total_fee'] = $total_fee;
            $trade_info['method'] = $method;
            $trade_info['time'] = time();
            $trade_info['status'] = 0;
            $this->table->add($trade_info);
            return $trade_info['trade_no'];
        }
        if ($result['total_fee'] != $total_fee || $result['method'] != $method) {
            $result['status'] = '-1';
            $this->table->update($result);

            $trade_info['order_sn'] = $order_sn;
            $trade_info['trade_no'] = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
            $trade_info['total_fee'] = $total_fee;
            $trade_info['method'] = $method;
            $trade_info['time'] = time();
            $trade_info['status'] = 0;
            $this->table->add($trade_info);
            return $trade_info['trade_no'];
        }
        return $result['trade_no'];
    }

    /**
     * @param  array    sql条件
     * @param  integer  条数
     * @param  integer  页数
     * @param  string   排序
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
    /*修改*/
    public function setField($data, $sqlmap = array()){
        if(empty($data)){
            $this->error = lang('_param_error_');
            return false;
        }
        $result = $this->table->where($sqlmap)->save($data);
        if($result === false){
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
}