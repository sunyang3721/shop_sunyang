var goods_detail = (function() {
    
    return {
        init : function() {
			this.getCartCount();
        },
        
        /* 获取购物车总数 */
		getCartCount : function () {
			$.getJSON('index.php?m=order&c=cart&a=get_carts',{format:'json'}, function(ret) {
				cart_nums = ret.sku_counts;
				$('.nums').text(cart_nums);
			});
		},
        
        /* 加入购物车 */
		cart_add : function() {
			if(goods.number < 1) return;
            var params = {},
                sku_id = goods.sku_id,
                num = $('.number .num-input').val();
            params[sku_id] = num;
			$.getJSON("index.php?m=order&c=cart&a=cart_add", {params:params}, function(ret) {
				if (ret.status == 1) {
                    $.tips({
                    	content : '添加购物车成功',
                    	callback:function() {
							$(".hd-cover").removeClass("show");
							$(".cover-decision").hide();
							window.location.href = '#';
							cart_nums++;
							$('.nums').text(cart_nums);
							setTimeout(function(){
								$(".hd-cover").hide();
							},200);
						}
                    });
				} else {
                    $.tips({content : ret.message});
					return false;
				}
			});
		},
		/* 立即购买 */
		buy_now : function(obj) {
			var skuids = obj.data("skuids");
			if (skuids == null || skuids == undefined) {
				$.tips({content: '请选择您要购买的商品'});
				return false;
			}
			var buy_nums = $('.number .num-input').val() ? $('.number .num-input').val() : 1;
			// 组装数据格式
			var params = {};
			params[skuids] = buy_nums;
			$.ajax({
				url: 'index.php?m=order&c=cart&a=cart_add',
				data: {params : params ,buynow : true},
				type: 'GET',
				dataType: 'json',
				success:function(ret) {
					if (ret.status == 0) {
						$.dialogTip({content: ret.message});
						return false;
					}
					// 直接跳转到结算页面
					window.location.href = 'index.php?m=order&c=order&a=settlement&skuids=' + skuids + ',' + buy_nums;
				}
			});
		},
    }
})();