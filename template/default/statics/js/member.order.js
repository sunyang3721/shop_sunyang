/* 会员中心订单类 */
var m_order = (function() {

	/* 取消订单 */
	$(".action_cancel").live('click' ,function() {
		var obj = $(this);
		$.dialogConfirm({
			content: '确定取消订单？',
			callback: function(){
				var sub_sn = $(obj).parent().attr('sub-sn');
				$.post('?m=member&c=order&a=cancel', {sub_sn: sub_sn}, function(ret) {
					$.tips({content: ret.message});
					if (ret.status == 1) window.location.reload();
				},'json');
			}
		});
	});

	/* 确认收货 */
	$(".action_finish").live('click' ,function() {
		var obj = $(this);
		$.dialogConfirm({
			content: '确定已收货？',
			callback: function(){
				var sub_sn = $(obj).parent().attr('sub-sn');
				var o_d_id = $(obj).attr('o-d-id');
				$.post('?m=member&c=order&a=finish', {sub_sn: sub_sn,o_d_id : o_d_id}, function(ret) {
					$.tips({content: ret.message});
					if (ret.status == 1) window.location.reload();
				},'json');
			}
		});
	});

	/* 再次购买 */
	$(".action_again").live('click' ,function() {
		var $_box = $(this).parents('.line').find(".order-table-info");
		var params = {};

		$_box.find('[data-skuid]').each(function(){
			params[$(this).data('skuid')] = $(this).data('number');
		})

		$.ajax({
			url: '?m=order&c=cart&a=cart_add&settlement=1',
			data: {params:params, buynow:true},
			type: 'GET',
			dataType: 'json',
			success:function(ret) {
				if (ret.status == 0) {
					$.dialogTip({content: ret.message});
					return false;
				} else {
					window.location.href = ret.referer
				}
			}
		});
	});

	/* 放入回收站 */
	// $("#recycle").live('click' ,function() {
	// 	if (confirm('确定放入回收站？')) {
	// 		var sn = $(this).parents('.finish-order').attr('data-sn');
	// 		$.post('?m=member&c=order&a=recycle', {sn: sn}, function(ret) {
	// 			alert(ret.message);
	// 			if (ret.status == 1) window.location.reload();
	// 		},'json');
	// 	}
	// });

	/* 删除订单 */
	// $("#delete_sn").live('click' ,function() {
	// 	if (confirm('确定删除该订单？该操作不可逆')) {
	// 		var sn = $(this).parent().attr('data-sn');
	// 		$.post('?m=member&c=order&a=delete_sn', {sn: sn}, function(ret) {
	// 			alert(ret.message);
	// 			if (ret.status == 1) window.location.reload();
	// 		},'json');
	// 	}
	// });

	/* 确认收货 */
	// $("#completion").live('click' ,function() {
	// 	if (confirm('确定已收到商品？该操作不可逆')) {
	// 		var sn = $(this).parent().attr('data-sn');
	// 		$.post('?m=member&c=order&a=completion', {sn: sn}, function(ret) {
	// 			alert(ret.message);
	// 			if (ret.status == 1) window.location.reload();
	// 		},'json');
	// 	}
	// });

	return{
		init : function() {}
	};
})();