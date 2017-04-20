<?php
/**
 * 		订单公共函数
 */

/**
 * 获取操作者信息
 * @return array
 */
function get_operator() {
	$operator = array();
	if (defined('IN_ADMIN')) {
		$admin_id = (int) ADMIN_ID;
		$operator = model("admin/admin_user")->where(array('id' => $admin_id))->find();
		$operator['operator_type'] = 1;	// 操作者类型：管理员
		$operator['_operator_type'] = '管理员';
	} else {
		$operator = model('member/member','service')->init();
		$operator['operator_type'] = 2;	// 操作者类型：会员
		$operator['_operator_type'] = '会员';
	}
	return $operator;
}

/**
 * 获取状态中文信息
 * @param  string $ident 标识
 * @return [string]
 */
function ch_status($ident) {
	$arr = array(
			'cancel'        => '已取消',
			'recycle'       => '已回收',
			'delete'        => '已删除',
			'create'        => '创建订单',
			'load_pay'      => '待付款',
			'pay'           => '已付款',
			'load_confirm'  => '待确认',
			'part_confirm'  => '部分确认',
			'all_confirm'   => '已确认',
			'load_delivery' => '待发货',
			'part_delivery' => '部分发货',
			'all_delivery'  => '已发货',
			'load_finish'   => '待收货',
			'part_finish'   => '部分完成',
			'all_finish'    => '已完成',
			'receive'       => '已收货',

			// 前台时间轴
			'time_cancel'   => '取消订单',
			'time_recycle'  => '回收订单',
			'time_create'   => '提交订单',
			'time_pay'      => '确认付款',
			'time_confirm'  => '确认订单',
			'time_delivery' => '商品发货',
			'time_finish'   => '确认收货',
		);
	return $arr[$ident];
}

function ch_prom($type) {
	$arr = array(
			'amount_discount' => '满额立减',
			'number_discount' => '满件立减',
			'amount_give'     => '满额赠礼',
			'number_give'     => '满件赠礼',
		);
	return $arr[$type];
}