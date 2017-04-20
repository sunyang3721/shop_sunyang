<?php
/**
 * 		退款模型
 */
class order_refund_log_table extends table {

	protected $_validate = array(
        /* array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]), */
    );

    //自动完成
    protected $_auto = array(
    	// array(完成字段1,完成规则,[完成条件,附加规则]),
        array('system_time','time',1,'function'), //新增数据时插入系统时间
        array('clientip','get_client_ip', 1, 'function'),
    );

    /* 查询后回调 */
    public function _after_select($result, $options) {
    	$type = array(1 => '后台管理员' , 2 => '会员' ,3 => '商家');	// 1:后台管理员,2:会员3:商家(预留)
    	foreach ($result as $k => $val) {
    		$result[$k]['_operator_type'] = $type[$val['operator_type']];
    	}
    	return $result;
    }
    
}