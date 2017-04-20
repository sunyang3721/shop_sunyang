<?php
/**
 * 		订单模型
 */
class order_table extends table {

    protected $where = array();

    protected $counts = array();

    protected $result = array();

	protected $_validate = array(
        /* array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]), */
        // 订单号
        array('sn', 'require', '{ORDER_SN_NOT_NULL}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('sn', '', '{ORDER_SN_ALREADY_EXIST}', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        // 买家ID
        array('buyer_id', 'require', '{order/buyer_id_not_null}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buyer_id', 'number', '{order/buyer_id_error}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 订单来源
        array('source', 'require', '{ORDER_SOURCE_NOT_NULL}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('source', 'number', '{ORDER_SOURCE_ERROR}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 支付类型
        array('pay_type', 'require', '{order/pay_type_require}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('pay_type', 'number', '{order/pay_type_error}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 支付状态
        array('pay_status', 'number', '{order/pay_status_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 确认状态
        array('confirm_status', 'number', '{order/confirm_status}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 发货状态
        array('delivery_status', 'number', '{order/delivery_status_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 完成状态
        array('finish_status', 'number', '{order/finish_status_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 商品总额
        array('sku_amount', 'require', '{order/sku_amount_require}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('sku_amount', 'currency', '{order/sku_amount_currency}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 应付金额
        array('real_amount', 'require', '{ORDER_REAL_AMOUNT_NOT_NULL}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('real_amount', 'currency', '{ORDER_REAL_AMOUNT_ERROR}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 配送总额
        array('delivery_amount', 'currency', '{order/delivery_amount_currency}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 余额付款总额
        array('balance_amount', 'currency', '{order/balance_amount_currency}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 收货人姓名
        array('address_name', 'require', '{ORDER_ADDRESS_NAME_NOT_NULL}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // 收货人手机
        array('address_mobile', 'require', '{ORDER_ADDRESS_MOBILE_NOT_NULL}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // 收货人 详细地址
        array('address_detail', 'require', '{order/address_detail_require}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );

    //自动完成
    protected $_auto = array(
    	// array(完成字段1,完成规则,[完成条件,附加规则]),
        array('system_time','time',1,'function'), //新增数据时插入系统时间
    );

    public function _initialize() {
        // $this->service_sub = model('order/order_sub','service');
    }

    public function _after_select($orders, $options) {
        $_subs = array();
        foreach ($orders as $k => $order) {
            // 获取子订单
            $_subs = $this->detail($order['sn'])->subs(TRUE ,TRUE , TRUE)->output();
            $orders[$k] = $_subs;
            // 买家信息
            $orders[$k]['_buyer'] = $this->load->table('member/member')->field('username')->find($order['buyer_id']);
            // 是否显示子订单号信息
            $orders[$k]['_showsubs'] = (count($_subs['_subs']) > 1) ? TRUE : FALSE;
        }
        return $orders;
    }

    /**
     * 获取订单当前状态 [now:当前状态，wait：待操作状态]
     * @param  $order    : 订单信息
     * @return [result]
     */
    public function get_status($order = array()) {
        if (empty($order)) {
            $this->error = lang('order/order_parame_empty');
            return FALSE;
        }
        $arr = array();
        switch ($order['status']) {
            case 2: // 已取消
                $arr['now']  = 'cancel';    // 已取消
                $arr['wait'] = 'recycle';   // 已回收
                break;
            case 3: // 已回收
                $arr['now']  = 'recycle';
                $arr['wait'] = 'delete';
                break;
            case 4: // 前台用户已删除
                $arr['now']  = 'delete';
                $arr['wait'] = '';
                break;
            default:    // 正常状态
                if ($order['pay_type'] == 1 && $order['pay_status'] == 0) {
                    $arr['now']  = 'create';
                    $arr['wait'] = 'pay';
                } else if ($order['finish_status'] == 2){
                    $arr['now']  = 'all_finish';
                    $arr['wait'] = 'aftermarket';
                } else if (($order['pay_type'] == 1 && $order['pay_status'] == 1 && $order['confirm_status'] == 0) || ($order['pay_type'] == 2 && $order['confirm_status'] == 0)) {
                    $arr['now']  = ($order['pay_type'] == 1) ? 'pay' :'create';
                    $arr['wait'] = 'load_confirm';  // 待确认
                }else if ($order['confirm_status'] == 1) {
                    $arr['now']  = 'part_confirm';  // 部分确认
                    $arr['wait'] = 'all_confirm';   // 已确认
                }else if ($order['finish_status'] == 0 && $order['confirm_status'] == 2) {
                    if ($order['delivery_status'] == 1) {
                        $arr['now']  = 'part_delivery'; // 部分发货
                        $arr['wait'] = 'all_delivery';  // 已发货
                    } else if ($order['delivery_status'] == 2) {
                        $arr['now']  = 'all_delivery';  // 已发货
                        $arr['wait'] = 'load_finish';        // 确认完成
                    } else {
                        $arr['now']  = 'all_confirm';
                        $arr['wait'] = 'load_delivery';
                    }
                } else if ($order['finish_status'] == 1) {
                    $arr['now']  = 'part_finish';   // 部分完成
                    $arr['wait'] = 'all_finish';    //  已完成
                }
                break;
        }
        return $arr;
    }

    /* 根据订单号获取订单信息(连贯操作) */
    // 主订单信息
    public function detail($sn){
        $this->result = $this->where(array('sn'=>$sn))->find();
        if (!empty($this->result)){
            // 状态标识
            $this->result['_status'] = $this->load->table('order/order_sub')->get_status($this->result);
            $this->result['_pay_type'] = ($this->result['pay_type'] == 2) ? '货到付款' : '在线支付';
        }
        return $this;
    }
    // 子订单信息(skus : 获取订单skus信息，track ：订单跟踪信息 ,group : 对订单商品分组)
    public function subs($skus = FALSE ,$track = FALSE ,$group = FALSE) {
        if ($this->result['sn']) {
            $this->result['_subs'] = $this->load->service('order/order_sub')->get_subs($this->result['sn'] ,$skus , $track ,$group);
        }
        return $this;
    }
    // 买家信息
    public function buyer() {
        if ($this->result['buyer_id']){
            $this->result['_buyer'] = $this->load->service('member/member')->fetch_by_id($this->result['buyer_id']);
        }
        return $this;
    }
    // 售后信息
    public function after() {
        if ($this->result['sn']) {
            $this->result['_after'] = $this->load->service('order/order_server')->get_after_by_sn($this->result['sn']);
        }
        return $this;
    }
    // 输出信息,(默认只输出订单信息,$field='all'时，输出所有信息,$field为其他值时输出该参数的方法值)
    public function output($field = '') {
        if ($field) {
            if ($field == 'all') {
                $this->subs(TRUE,TRUE)->buyer()->after();
                return $this->result;
            } else {
                return $this->result['_'.$field];
            }
        } else {
            return $this->result;
        }
    }


// ------------------------------  统计条数 (连贯操作) ------------------------------- 
    public function buyer_id($buyer_id) {
        if(!empty($buyer_id) && is_numeric($buyer_id)) {
            $this->where['buyer_id'] = $buyer_id;
        }
        return $this;
    }

    /* 所有订单 */
    public function all() {
        $this->counts['all'] = $this->where($this->where)->count();
        return $this;
    }

    /* 已取消 */
    public function cancel() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 2;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['cancel'] = $this->where($map)->count();
        return $this;
    }

    /* 已回收 */
    public function recycle() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 3;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['recycle'] = $this->where($map)->count();
        return $this;
    }

    /* (会员)已删除 */
    public function deletes() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 4;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['deletes'] = $this->where($map)->count();
        return $this;
    }

    /* 待支付 */
    public function pay() {
        $map = $sqlmap = array();
        $sqlmap['pay_type']   = 1;
        $sqlmap['status']     = 1;
        $sqlmap['pay_status'] = 0;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['pay'] = $this->where($map)->count();
        return $this;
    }

    /* 待确认 */
    public function confirm() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['_string'] = '(`pay_type` = 1 and `pay_status` = 1) or (`pay_type` = 2)';
        $sqlmap['confirm_status'] = 0;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['confirm'] = $this->where($map)->count();
        return $this;
    }

    /* 待发货 */
    public function delivery() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['confirm_status'] = array('IN',array(1,2));
        $sqlmap['delivery_status'] = array('IN',array(0,1));
        $map = array_merge($sqlmap,$this->where);
        $this->counts['delivery'] = $this->where($map)->count();
        return $this;
    }

    /* 待收货 */
    public function receipt() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['delivery_status'] = array('GT' ,0);
        $map = array_merge($sqlmap,$this->where);
        $order_sns = $this->where($map)->getField('sn' ,TRUE);
        $map = array();
        $map['order_sn'] = array('IN',$order_sns);
        $sub_sns = $this->load->table('order/order_sub')->where($map)->getField('sub_sn' ,TRUE);
        $map = array();
        $map['isreceive'] = 0;
        $map['sub_sn'] = array('IN',$sub_sns);
        $this->counts['receipt'] = $this->load->table('order/order_delivery')->where($map)->count();
        return $this;
    }

    /* 已完成 */
    public function finish() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['finish_status'] = 2;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['finish'] = $this->where($map)->count();
        return $this;
    }

    /* 待评价商品 */
    public function load_comment() {
        $map = $sqlmap = array();
        $sqlmap['delivery_status'] = 2;
        $sqlmap['iscomment'] = 0;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['load_comment'] = $this->load->table('order/order_sku')->where($map)->count();
        return $this;
    }

    /* 进行中的订单 */
    public function going() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['finish_status'] = array('IN',array(0,1));
        $map = array_merge($sqlmap,$this->where);
        $this->counts['going'] = $this->where($map)->count();
        return $this;
    }

    /* 待退货商品 */
    public function load_return() {
        $map = $sqlmap = array();
        $sqlmap['status'] = array('EQ',0);
        $map = array_merge($sqlmap,$this->where);
        $this->counts['load_return'] = $this->load->table('order/order_return')->where($map)->count();
        return $this;
    }

    /* 待退款商品 */
    public function load_refund() {
        $map = $sqlmap = array();
        $sqlmap['status'] = 0;
        $map = array_merge($sqlmap,$this->where);
        $this->counts['load_refund'] = $this->load->table('order/order_refund')->where($map)->count();
        return $this;
    }

    /**
     * 输出统计结果
     * @param  string $fun_name 要统计的方法名，默认统计所有结果
     * @return [result]
     */
    public function out_counts($fun_name = '') {
        if (empty($fun_name)) {
            $this->all()->cancel()->recycle()->deletes()->pay()->confirm()->delivery()->receipt()->finish()->load_comment()->going()->load_return()->load_refund();
        } else {
            $this->$fun_name();
        }
        return $this->counts;
    }

}