/**
 * 		购物车
 */


// 更新当前页面总价
function update_total() {
	var total = 0,numbers = 0,checked_inputs = 0;
	var checked_inputs = $("input[name=check_sku]:not(:disabled):checked");	// 所有已选中input
	$(checked_inputs).each(function(k, v) {
		var price = 0,num = 0;
		price = parseFloat($(v).parents("dd").find(".price > em").text());
		num = parseInt($(v).parents("dd").find("input[data-id='buy-num']").val());
		max_nums = parseInt($(v).parents("dd").find("input[data-id='buy-num']").data("max"));
		num = (num < 1 || !num) ? 1 : ((num > max_nums) ? max_nums : num);
		total += price * num;
		numbers += num;
	});
	$("[data-id='totle']").text(total.toFixed(2));
	$("[data-id='sku-numbers']").text(parseInt(numbers));
	// 结算按钮
	if (checked_inputs.length > 0) {
		$("[data-id=sub-btn]").removeClass('disabled');
	} else {
		$("[data-id='chekced-all']").prop("checked" ,false);
		$("[data-id=sub-btn]").addClass('disabled');
	}
}

$(function(){

	/* 改变数量 加减按钮 */
	$("[data-event=number]").on('tap',function(){
		// 当前购买数
		now_nums = parseInt($(this).parent(".number").find("[data-id='buy-num']").val());
		// 最大购买数
		max_nums = parseInt($(this).parent(".number").find("[data-id='buy-num']").data("max"));
		now_nums = (now_nums < 1 || !now_nums) ? 1 : ((now_nums > max_nums) ? max_nums : now_nums);
		// 更改数据库或 cookie
		$.getJSON('?m=order&c=cart&a=set_nums', {sku_id:$(this).parents('dd').data("skuid"), nums:now_nums},function(ret){
			if (ret.status == 1) update_total();
		});
	});

	/* 改变数量 input文本 */
	$("input[data-id='buy-num']").on('keyup',function(){
		// 当前购买数
		now_nums = parseInt($(this).val());
		// 最大购买数
		max_nums = parseInt($(this).data("max"));
		now_nums = (now_nums < 1 || !now_nums) ? 1 : ((now_nums > max_nums) ? max_nums : now_nums);
		// 更改数据库或 cookie
		$.getJSON('?m=order&c=cart&a=set_nums', {sku_id:$(this).parents('dd').data("skuid"), nums:now_nums},function(ret){
			if (ret.status == 1) update_total();
		});
	});

	/* 勾选购买的商品 */
	$("[data-id='chekced-all'],input[name='check_sku']").on('click',function(){
		update_total();
	});

	/* 删除商品 */
	$("[data-id='del-sku']").on('click' ,function() {
		var $this = $(this)
		$.confirms("确定要删除该购物车商品吗？",function(){
			var sku_id = $this.parents('dd').data('skuid');
			$.getJSON('?m=order&c=cart&a=delpro', {
				sku_id : sku_id
			}, function(ret){
				if(ret.status == 1) {
					$('dd[data-skuid='+sku_id+']').fadeOut(400,function() {
						$('dd[data-skuid='+sku_id+']').remove();
						update_total();
					});
					return true;
				} else {
					$.tips({content:ret.message});
					return false;
				}
			});
   		});
	});

	$("[data-id='sub-btn']").on('tap',function(){
		if ($(this).hasClass('disabled')) return false;
		checked_inputs = $("input[name='check_sku']:not(:disabled):checked");	// 当前所有已选中input
		if (checked_inputs.length > 0) {
			var skuids = '';
			$(checked_inputs).each(function(k, v) {
				skuids += $(v).parents(".cart-list-item").data("skuid") + ';';
			});
			$(this).attr("href",'?m=order&c=order&a=settlement&skuids=' + skuids);
		} else {
			return false;
		}
	});
});
	