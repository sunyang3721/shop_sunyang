/* 购物车 */

// 更新当前页面总价
function update_total() {
	var total = 0;
	checked_inputs = $(".check-child input:checked");	// 当前所有已选中input
	$(checked_inputs).each(function(k, v) {
		total += parseFloat($(v).parents(".tr").find(".cart-total span").text());
	});
	$("[data-id=totle]").text(total.toFixed(2));
	// 结算按钮
	if (checked_inputs.length > 0) {
		$("[data-id=settlement]").removeClass('gray-btn');
	} else {
		$("[data-id=settlement]").addClass('gray-btn');
	}
}

$(function() {

	/* 改变数量 加减按钮 */
	$("[data-event=submit]").live("click" ,function(){
		var now_nums = $(this).parent('.adjust').find("input").val(); 		// 当前购买数
		var min_nums = $(this).parent('.adjust').find("input").data("min");	// 最小购买数
		var max_nums = $(this).parent('.adjust').find("input").data("max");	// 最大购买数
		now_nums = (now_nums < min_nums || isNaN(now_nums)) ? 1 : now_nums;
		now_nums = (now_nums > max_nums) ? max_nums : now_nums;
		var sku_id = $(this).parents('.tr').data("skuid");
		// 更改数据库或 cookie
		$.getJSON('index.php?m=order&c=cart&a=set_nums', {sku_id:$(this).parents('.tr').data("skuid"), nums:now_nums},function(ret){
			if (ret.status == 1) hd_cart._lists();
		});
	});

	/* 改变数量 input文本 */
	$(".adjust-input").live('keyup',function(){
		var min = parseInt($(this).data("min"));	// 最小购买数
		var max = parseInt($(this).data("max"));	// 最大购买数
		var now_nums = parseInt($(this).val());			// 当前购买数
		now_nums = (now_nums < min || isNaN(now_nums)) ? 1 : now_nums;
		now_nums = (now_nums > max) ? max : now_nums;
		var sku_id = $(this).parents('.tr').data("skuid");
		// 更改数据库或 cookie
		$.getJSON('index.php?m=order&c=cart&a=set_nums', {sku_id:$(this).parents('.tr').data("skuid"), nums:now_nums},function(ret){
			if (ret.status == 1) hd_cart._lists();
		});
	});

	/* 购物车页面[单条删除] */
	$(".delpro").live("click",function() {
		var sku_id = $(this).parents(('.tr')).data("skuid");
		$.dialogConfirm({
			content: '确定删除吗？',
			callback: function(){
				$.getJSON('index.php?m=order&c=cart&a=delpro', {
					sku_id:sku_id,
				}, function(ret) {
				   	if(ret.status == 1) {
				   		hd_cart._lists();
						return true;
					}else {
						$.tips({icon: 'error',content: ret.message});
						return false;
					}
			   });
			}
		});
	})

	/* 购物车页面[清空购物车] */
	$("#clears").live("click" ,function(){
		$.dialogConfirm({
			content: '确定清空购物车？',
			callback: function(){
				$.getJSON('index.php?m=order&c=cart&a=clear', {}, function(ret) {
				   	if(ret.status == 1) {
						location.reload();
						hd_cart._counts();
					}else {
						$.tips({icon: 'error',content: ret.message});
						return false;
					}
			   	});
			}
		});
	})

	/* 购物车页面[清除已售馨商品] */
	$("#clear_sold_out").live("click" ,function() {
		$.dialogConfirm({
			content: '确定清除已售馨商品？',
			callback: function(){
				$.getJSON('index.php?m=order&c=cart&a=clear_sold_out', {}, function(ret) {
				   	if(ret.status == 1) {
						hd_cart._lists();
						hd_cart._counts();
						$(".sold-box").addClass('hidden');
					}else {
						$.tips({icon: 'error',content: ret.message});
						return false;
					}
			   	});
			}
		});
	})

	/* 购物车页面，商家组全选||当前商家的子复选框 */
	$(".th .seller_box, .check-child input").live('click',function(){
		// 当前组卖家id
		var sellerid = $(this).parents(".main-box").data("sellerid");
		// 当前组全选框
		var select = $(".main-box[data-sellerid="+ sellerid +"] .seller_box");
		// 当前组所有input(除去disabled的)
		var inputs = $(".main-box[data-sellerid="+ sellerid +"] .check-child input:enabled");
		// 当前组已选中input
		var checked_inputs = $(".main-box[data-sellerid="+ sellerid +"] .check-child input:enabled:checked");
		if ($(this).hasClass('seller_box')) {
			if ($(this).prop("checked") == true) {
				$(inputs).prop("checked", true);
				$(".cell-wrap").find(".tr-child").addClass("tr-bg");
			} else {
				$(inputs).prop("checked", false);
				$(".cell-wrap").find(".tr-child").removeClass("tr-bg");
			}
		} else {
			if (inputs.length == checked_inputs.length) {
				$(select).prop("checked", true);
			} else {
				$(select).prop("checked", false);
			}
		}
		seller_selected();
		// 更新总价
		update_total();
	})

	/* 购物车页面 全选 */
	$("#all_check").live("click" ,function() {
		// 所有商家组全选框
		var seller_selects = $(".main-box .seller_box");
		// 所有商家组下的子复选框(除去disabled的)
		var child_selects = $(".main-box .check-child input:enabled");
		if ($(this).prop("checked") == true) {
			$(seller_selects).prop("checked",true);
			$(child_selects).prop("checked",true);
			$(".cell-wrap").find(".tr-child").addClass("tr-bg");
		} else {
			$(seller_selects).prop("checked",false);
			$(child_selects).prop("checked",false);
			$(".cell-wrap").find(".tr-child").removeClass("tr-bg");
		}
		// 更新总价
		update_total();
	})

	// 检测所有商家是否全选中
	function seller_selected() {
		all_checkboxs = $(".main-box .th .seller_box");
		checkeds = $(".main-box .th .seller_box:checked");
		if (all_checkboxs.length != 0 && all_checkboxs.length == checkeds.length) {
			$(".check_all").prop("checked" ,true);
		} else {
			$(".check_all").prop("checked" ,false);
		}
	}

	/* 结算 */
	$("[data-id=settlement]").live("click",function() {
		checked_inputs = $(".check-child input:checked");	// 当前所有已选中input
		if (checked_inputs.length > 0) {
			var skuids = '';
			$(checked_inputs).each(function(k, v) {
				skuids += $(v).parents(".tr").data("skuid") + ';';
			});
			$(this).attr("href",'index.php?m=order&c=order&a=settlement&skuids=' + skuids);
		} else {
			return false;
		}
	})

// -------------------------------	toper	--------------------------------
	// 显示 购物车
	$("#carts").hover(function() {
		$("#carts .cart").removeClass('hidden').addClass("show");
		hd_cart._carts();
	}, function() {
		$("#carts .cart").removeClass('show').addClass("hidden");
	});

	// 清空购车
	$("#carts .cart").find("a").eq(1).click(function(event) {
		hd_cart._clear();
	});

	// 加入购物车成功页面 清空购物车
	$("#carts_c .cart").find("a").eq(1).click(function(event) {
		hd_cart._clear();
		$(".list").fadeOut(600);
	});

	// 删除购物车
	$(".cart-goods-fun a").live('click', function(event) {
		hd_cart._delpro($(this).parents('.list').data("skuid"));
	});

	// 加入购物车
	$("[data-event='cart_add']").live("click", function(){
		hd_cart.cart_add($(this));
	});

	// 立即购买(支持数组)
	$("[data-event='buy_now']").live("click", function(){
		hd_cart.buy_now($(this));
	});
});
var d;
var hd_cart = (function() {

	return {

		/* 加入购物车 */
		cart_add : function(obj) {
			var params = {};
			sku_ids = $(obj).data("skuids").toString();
			if(sku_ids.indexOf(',') > 0){
				sku_ids = sku_ids.split(',');
				$.each(sku_ids,function(i, sku_id) {
					params[sku_id] = 1;
				});
			}else{
				nums = parseInt($("#adjust .adjust-wrap input").val());
				if (nums < 1 || isNaN(nums)) nums = 1;
				if (sku_ids == null || sku_ids == undefined) {
					$.dialogTip({content: '请选择您要购买的商品'});
					return false;
				}
				params[sku_ids] = nums;
			}
			var d;
			$.ajax({
				url: 'index.php?m=order&c=cart&a=cart_add',
				data: {params : params},
				type: 'GET',
				dataType: 'json',
				success:function(ret) {
					var success_url = 'index.php?m=order&c=cart&a=success';
					var cart_url = 'index.php?m=order&c=cart&a=index';
					if(ret.status == 1){
						hd_cart._counts();
						if(cart_jump == 0){
							window.location = success_url;
						}else if(cart_jump == 1){
							window.location = cart_url;
						}else{
							d = dialog({
								content: '<div class="join-cart-tips join-cart-success"><h2 class="margin-bottom text-sub text-center"><b class="margin-right ico-right va-m"></b>商品已成功加入购物车</h2><div class="join-cart-box"><a class="button radius-none margin-right goon">继续购物</a><a class="item-btn button border-none text-center sett" href="' + cart_url + '">去购物车结算 <span class="margin-small-left strong songti">&gt;</span></a></div></div>',
								title: '加入购物车成功',
								width: 350
							});
							d.showModal();
							return false;
						}
					}else{
						$.dialogTip({content: '加入购物车失败'});
						return false;
					}
				}
			});
			$(".goon").live('click',function(){
				d.close();
				return false;
			});
		},

		/* 获取购物车总数 */
		_counts : function () {
			$.getJSON('index.php?m=order&c=cart&a=get_carts',{format:'json'}, function(ret){
				$("#count").text(ret.sku_counts);
			});
			return;
		},

		/* 清空购物车 */
		_clear:function(){
			$.getJSON('index.php?m=order&c=cart&a=clear', function(ret){
				if(ret.status == 1) {
					$("#carts .none").fadeOut(600,function() {
						hd_cart._carts();
					});
					return true;
				} else {
					$.dialogTip({content: ret.message});
					return false;
				}
			});
		},

		/* 头部 获取购物车列表 */
		_carts: function () {
			var _html = '';
			$.getJSON('index.php?m=order&c=cart&a=get_carts',{format:'json'}, function(ret){
				$("#count").text(ret.sku_counts);
				$(".cart-info p .g_c").text(ret.sku_counts);
				$(".cart-info p .m_c").text(ret.sku_numbers);
				$(".cart-info p .p_c").text("￥"+ret.all_prices);
				if (ret.sku_counts == 0) {
					$("#carts .none").addClass('hidden');
					$("#carts .cart-tips").removeClass('hidden');
					$("#carts .cart-tips p").text('购物车中还没有商品，赶紧选购吧！');
					return false;
				} else {
					$("#carts .cart-tips").addClass('hidden');
					$("#carts .user-con-top,.cart-info p").removeClass('hidden').show();
					$("#lists").removeClass('hidden').show();
				}
				$.each(ret.skus,function(k, v) {
					var	prom = v['_sku_'].prom_time > 0 ?  '<span class="padding-small-left padding-small-right radius-small bg-mix text-white margin-small-right">限时促销</span>' : '';
					var spec_html = v['_sku_']._spec ? v['_sku_']._spec : '';
					_html += '<div class="list" data-skuid="'+ v.sku_id +'">';
					_html += '	<div class="pic margin-none"><a href="?m=goods&c=index&a=detail&sku_id=' + v.sku_id +'"><img src="'+ v['_sku_'].thumb +'" /></a></div>';
					_html += '	<div class="cart-goods-title">';
					_html += '		<h6>' + prom + '<a href="?m=goods&c=index&a=detail&sku_id='+ v.sku_id +'">' + v['_sku_'].name +'</a></h6>';
					_html += '		<p class="text-ellipsis">'+ spec_html +'</p>';
					_html += '			</div>';
					_html += '			<div class="cart-goods-fun">';
					_html += '				<span>￥'+ v['_sku_'].prom_price +' x<em>'+ v.number +'</em></span>&nbsp;';
					_html += '				<p class="text-right"><a href="javascript:;" class="text-main">删除</a></p>';
					if(v._sku_.status == 0){
						_html += '				<p class="text-right shelves"><em class="icon icon-shelves margin-small-right"></em>已下架</p>';
					}
					_html += '			</div>';
					_html += '		</div>';
				});
				$("#lists").html(_html);
			});
			return;
		},

		/* 删除单个商品 */
		_delpro:function(sku_id) {
			$.getJSON('index.php?m=order&c=cart&a=delpro', {
				sku_id:sku_id
			}, function(ret){
				if(ret.status == 1) {
					$('.cart-goods div[data-skuid='+sku_id+']').fadeOut(600,function() {
						hd_cart._carts();
						hd_cart._lists();
					});
					return true;
				} else {
					$.dialogTip({content: ret.message});
					return false;
				}
			});
		},

		/* 购物车页面 获取购物车列表 */
		_lists: function () {
			var _html = '';
			$.getJSON('index.php?m=order&c=cart&a=get_carts',{group:'TRUE',format:'json'}, function(ret){
				if (ret.sku_counts > 0) {
					if (ret.sold_count > 0) {
						$(".sold-box").removeClass('hidden');
					} else {
						$(".sold-box").addClass('hidden');
					}
					$.each(ret.skus,function(k, v) {
						_html += '<div class="margin-big-bottom border border-gray-white main-box" data-sellerid="'+ k +'">';
						_html += '<div class="th">';
						_html += '	<span class="th-check"><label><input class="seller_box" type="checkbox" checked="checked" />商店名称：官方自营</label></span>';
						_html += '	<span class="th-price text-center">单价</span>';
						_html += '	<span class="th-num text-center">数量</span>';
						_html += '	<span class="th-subtotal text-center">小计</span>';
						_html += '	<span class="th-hand">操作</span></div>';
						$.each(v.sku_list,function(key ,cart){
							var	prom = cart._sku_.prom_time > 0 ?  '<em class="padding-small-left padding-small-right radius-small bg-mix text-white margin-small-right">限时促销</em>' : '';
							_html += '<div class="tr ';
									if(parseInt(cart._sku_.number) == 0){
										_html += 'tr-disabled';
									}
							_html +=		'" data-skuid="'+cart.sku_id+'"><!--当商品没有库存是加上tr-disabled-->';
							_html += '	<div class="laoyout tr-con">';
							_html += '		<div class="cell cart-check check-child"><input type="checkbox" name="id" value="'+ cart.id +'" ';
									if (parseInt(cart._sku_.number) == 0 || parseInt(cart._sku_.status) == 0) {
										_html += '	disabled="disabled"';
									} else {
										_html += '	checked="checked"';
									}
							_html += '	/></div><!--当商品没有库存是加上disabled="disabled"不可点击-->';
							_html += '		<div class="cell cart-pic"><img src="'+ cart._sku_.thumb +'" /></div>';
							_html += '		<div class="cell cart-info">';
							_html += '			<div class="title clearfix">';
							_html += '				<p class="text-ellipsis"><a href="?m=goods&c=index&a=detail&sku_id='+ cart._sku_.sku_id +'" title="'+ cart._sku_.name +'" target="_blank">'+ prom + cart._sku_.name +'</a></p>';
									if (parseInt(cart._sku_.number) == 0) {
										_html += '	<span>已售罄</span>';
									}
							_html += '			</div>';
									if (cart._sku_.spec == '' || cart._sku_.spec == undefined) {
										cart._sku_.spec = 0;
									}
									if (cart._sku_.spec.length > 0) {
										_html += '	<p class="spec text-ellipsis">';
											$.each(cart._sku_.spec,function(ke ,val){
												_html += '	<span class="text-main">'+ val.name +'：</span>'+ val.value;
											})
										_html += '		<span class="spec-hand">';
										_html += '			<b></b>';
										_html += '			<em>修改</em>';
										_html += '		</span>';
										_html += '	</p>';
									}

							_html += '		</div>';
							_html += '		<div class="cell cart-price text-center">';
							_html += '			<p class="shop_price">'+ cart._sku_.prom_price +'</p>';
							_html += '		</div>';
							_html += '		<div class="cell cart-nums text-center">';
							_html += '			<div class="adjust">';
							_html += '				<a class="adjust-control adjust-reduce border ';
									if (parseInt(cart.number) == 1) {
										_html += 'disabled';
									}
							_html += '" data-event="submit" href="javascript:;">-</a>';
							_html += '				<input class="adjust-input input border bg-white padding-none" type="text" value="'+ cart.number +'" data-min="1" data-max="'+ cart._sku_.number +'" />';
							_html += '				<a class="adjust-control adjust-add border ';
									if (parseInt(cart.number) >= parseInt(cart._sku_.number)) {
										_html += 'disabled';
									}
							_html += '" data-event="submit" href="javascript:;">+</a>';
							_html += '				<span class="adjust-disabled"></span>';
							_html += '			</div>';
							_html += '		</div>';
							_html += '		<div class="cell cart-total text-center">';
							_html += '			<span class="text-mix">'+ cart.prices +'</span>';
							_html += '		</div>';
							_html += '		<div class="cell cart-hand text-center">';
							_html += '			<a class="delpro" href="javascript:;">删除</a>';
							if(parseInt(cart._sku_.status) == 0){
								_html += '			<p class="shelves margin-small-top"><em class="icon icon-shelves margin-small-right"></em>商品已下架</p>';
							}
							_html += '		</div>';
							_html += '	</div>';
							_html += '</div>';
						});
					});
				} else {
					_html += '<div class="margin-big-bottom border border-gray-white">';
					_html += '	<div class="tr"><div class="text-center lh-heigth">';
					_html += '		你的购物车还是空的,<a href="/">赶紧行动</a>吧！</div></div></div>';
				}
				$("#lists-box").html(_html);
				//套装
				if($(".cell-wrap input[type=checkbox][checked]")){
					$(".cell-wrap").find(".tr-child").addClass("tr-bg");
				}
				$(".cell-wrap input[type=checkbox]").change(function(){
					if($(this).attr("checked")){
						$(".cell-wrap").find(".tr-child").addClass("tr-bg");
					}
					else{
						$(".cell-wrap").find(".tr-child").removeClass("tr-bg");
					}
				});
				// 更新总价
				update_total();
			});
			return;
		},

		/* 立即购买 */
		buy_now : function(obj) {
			var skuids = obj.data("skuids");
			if (skuids == null || skuids == undefined) {
				$.dialogTip({content: '请选择您要购买的商品'});
				return false;
			}
			var buy_nums = parseInt($(".adjust-input").val()) > 0 ? parseInt($(".adjust-input").val()) : 1;
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

		/*  初始化头部购物车 */
		init : function() {
			hd_cart._counts();
		}
	};
})();
