/**
 * 		购物车
 */

$(function(){

	/* 取消订单 */
	$("body").on('tap',"[data-action='cancel']",function(){
		var $this = $(this);
		$.confirms("确定取消订单吗？",function(){
			var sub_sn = $this.parent().attr('data-sub_sn');
			$.post('?m=member&c=order&a=cancel', {sub_sn: sub_sn}, function(ret) {
				$.tips({content:ret.message});
				if (ret.status == 1) window.location.reload();
			},'json');
		})
	});

	/* 确认收货 */
	$("body").on('tap',"[data-action='finish']",function(){
		var $this = $(this);
		$.confirms("确定已收货吗？",function(){
			var sub_sn = $this.parent().attr('data-sub_sn');
			var o_d_id = $this.attr('o-d-id');
			$.post('?m=member&c=order&a=finish', {sub_sn: sub_sn,o_d_id : o_d_id}, function(ret) {
				$.tips({content:ret.message});
				if (ret.status == 1) window.location.reload();
			},'json');
		})
	});

	/* 再次购买 */
	$("body").on('tap',"[data-action='again']",function(){
		var $this = $(this);
		$.confirms("确定再次购买吗？",function(){
			var $_box = $this.parent().prev(".order-pic");
			var params = {};
			$_box.find('[data-skuid]').each(function(){
				params[$(this).data('skuid')] = $(this).data('nums');
			})
			$.ajax({
				url: '?m=order&c=cart&a=cart_add&settlement=1',
				data: {params:params, buynow:true},
				type: 'GET',
				dataType: 'json',
				success:function(ret) {
					if (ret.status == 0) {
						$.tips({content: ret.message});
						return false;
					} else {
						window.location.href = ret.referer
					}
				}
			});
		});
	});
});
var m_order = (function() {
	return{};
})();