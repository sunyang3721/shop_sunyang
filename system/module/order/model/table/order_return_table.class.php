<?php
/**
 * 		退货模型
 */
class order_return_table extends table {

	protected $_validate = array(
        /* array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]), */
        array('order_sn', 'require', '{order/order_not_empty}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buyer_id', 'require', '{order/buyer_id_not_null}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buyer_id', 'number', '{order/buyer_id_error}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 退款总额
        array('amount', 'currency', '{order/amount_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    //自动完成
    protected $_auto = array(
    	// array(完成字段1,完成规则,[完成条件,附加规则]),
        array('dateline','time',1,'function'), //新增数据时插入系统时间
        array('system_time','time',2,'function'), //更新数据时更新修改时间
    );
    
}