/**
 * 		支付订单
 */

var hd_pay = (function() {

	var real_amount = 0;
	var member_money = 0;
	var balance_amount = 0;
	var order_need_amount = 0,pay_amount = 0;
	/* 点击余额付款框 */
	$("[data-id='balance_pay']").on("click",function() {
		hd_pay.balance($(this));
	});

	/* 点击提交 */
	$("[data-id='subbtn']").on("tap",function() {
		hd_pay.dosubmit();
	})
	
	return{

		init : function() {
			if (typeof(order) != 'object') {
				$.tips({content: '无法读取此订单信息！'});
				location.href = '/';
				return;
			}
			$(document).ready(function($) {
				$("[data-id='pay_method']:first").prop("checked" ,true);	// 默认选中支付方式第一个
				real_amount = parseFloat(order.real_amount);		// 应付金额
				balance_amount = parseFloat(order.balance_amount);	// 余额支付金额
				member_money = parseFloat(member.money);			// 会员余额
				pay_amount = order_need_amount = real_amount - balance_amount;	// 订单还需支付金额
				if (member_money == 0) {
					$("[data-id='balance_pay']").find("input[type='checkbox']").prop("disabled" ,true);
				} else {
					$("[data-id='balance_pay']").find("input[type='checkbox']").prop("checked" ,true);	// 选中余额支付
					if (member_money >= order_need_amount) {
						// 隐藏在线支付
						$("[data-id='pays']").hide();
						pay_amount = 0;
					} else {
						pay_amount = order_need_amount - member_money;
					}
				}
				$("[data-id='pay_amount'] em").text(pay_amount.toFixed(2));
			});
		},

		balance : function(obj) {
			if (member_money == 0) {
				$.tips({content: '余额不足，请先充值'});
				return false;
			}
			if ($(obj).find("input[type='checkbox']").prop("checked") == true) {
				$("input[name=balance_checked]").val(1);
				if (member_money >= order_need_amount) {
					$("[data-id='pays']").hide();
				} else {
					pay_amount = order_need_amount - member_money;
				}
				$("[data-id='pay_amount'] em").text(pay_amount.toFixed(2));
			} else {
				$("[data-id='pays']").show();
				$("input[name=balance_checked]").val(0);
				pay_amount = order_need_amount;
				$("[data-id='pay_amount'] em").text(pay_amount.toFixed(2));
			}
		},

		dosubmit : function() {
			// 是否勾选余额支付
			var balance_checked = $("[data-id='balance_pay']").find("input[type='checkbox']").prop("checked");
			// 支付方式
			var pay_code = $("[data-id='pay_method']:checked").data("code");
			// 支付银行
			var pay_bank = $("[data-id='pay_method']:checked").data("bank");
			if (pay_code == undefined && (balance_checked == false || (real_amount > member_money))) {
				$.tips({content: '请选择支付方式'});
				return false;
			}
			
			// 赋值参数
			$("input[name=pay_code]").val(pay_code);
			$("input[name=pay_bank]").val(pay_bank);
			$("form[name=dopay]").submit();
		}
	};

})();