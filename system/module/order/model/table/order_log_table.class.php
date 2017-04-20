<?php
/**
 * 		订单日志模型
 */
class order_log_table extends table {

	protected $_validate = array(
        /* array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]), */
        // 订单号
        array('order_sn', 'require', '{order/order_not_empty}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // 操作类型
        array('action','require','{order/type_not_empty}',self::MUST_VALIDATE,'regex',self::MODEL_INSERT),
        // 操作者ID
        array('operator_id', 'require', '{order/user_id_not_empty}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('operator_id', 'number', '{order/user_id_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 操作者名称
        array('operator_name', 'require', '{order/user_name_not_null}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // 操作者类型
        array('operator_type','require','{order/operator_type_not_null}',self::MUST_VALIDATE,'regex',self::MODEL_INSERT),
        array('operator_type','number','{order/operator_type_numbre}',self::VALUE_VALIDATE,'regex',self::MODEL_BOTH),
    );

    //自动完成
    protected $_auto = array(
    	// array(完成字段1,完成规则,[完成条件,附加规则]),
        array('system_time','time',1,'function'), //新增数据时插入系统时间
        array('clientip','get_client_ip', 1, 'function'),
    );
}