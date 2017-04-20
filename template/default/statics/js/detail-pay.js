/* 支付订单 */
var hd_pay = (function() {

	var real_amount = 0;
	var member_money = 0;
	var balance_amount = 0;
	var order_need_amount = 0,pay_amount = 0;
	/* 点击余额付款框 */
	$(".balance,.pay-checkbox").live("click",function() { 
		hd_pay.balance();
	});

	/* 点击提交 */
	$("#subbtn").live("click",function() {
		hd_pay.dosubmit();
	})
	
	return{

		init : function() {
			if (typeof(order) != 'object') {
				$.dialogTip({content: '无法读取此订单信息！'});
				location.href = '/';
				return;
			}
			$(document).ready(function($) {
				$("div .pay-way .item:first-child").addClass('selected');	// 默认选中支付方式第一个
				real_amount = parseFloat(order.real_amount);		// 应付金额
				balance_amount = parseFloat(order.balance_amount);	// 余额支付金额
				member_money = parseFloat(member.money);			// 会员余额
				pay_amount = order_need_amount = real_amount - balance_amount;	// 订单还需支付金额
				balance=$(".balance").data("balance");
				if(balance == 0){
					$(".balance").addClass('hidden');
				}else{
					if (member_money == 0) {
						$(".balance [data-id='checkbox']").prop("disabled" ,true);
					} else {
						$(".balance [data-id='checkbox']").prop("checked" ,true);	// 选中余额支付
						if (member_money >= order_need_amount) {
							// 隐藏在线支付
							$(".pays").addClass('hidden');
							pay_amount = 0;
						} else {
							pay_amount = order_need_amount - member_money;
						}
					}
				}
				$("[data-id='pay_amount'] > em").text(pay_amount.toFixed(2));
			});
		},

		balance : function() {
			if (member_money == 0) {
				$.dialogTip({content: '余额不足，请先充值'});
				return false;
			}
			if ($(".balance [data-id='checkbox']").prop("checked") == true) {
				$(".balance").removeClass('border-main').find('input').prop("checked",false);
				$(".pays").removeClass('hidden');
				$("input[name=balance_checked]").val(0);
				pay_amount = order_need_amount;
				$("[data-id='pay_amount'] > em").text(pay_amount.toFixed(2));
			} else {
				$(".balance").addClass('border-main').find('input').prop("checked",true);
				$("input[name=balance_checked]").val(1);
				if (member_money >= order_need_amount) {
					$(".pays").addClass('hidden');
				} else {
					pay_amount = order_need_amount - member_money;
				}
				$("[data-id='pay_amount'] > em").text(pay_amount.toFixed(2));
			}
		},

		dosubmit : function() {
			// 是否勾选余额支付
			var balance_checked = $(".balance input[type=checkbox]").prop("checked");
			// 支付方式
			var pay_code = $("div .pay-way .selected").attr("data-code");
			// 网银bank
			var pay_bank = $("div .pay-way .selected").attr("data-defaultbank");
			if (balance_checked == false && pay_code == undefined) {
				$.dialogTip({content: '请选择支付方式'});
				return false;
			}
			if ((real_amount > member_money) && pay_code == undefined) {
				$.dialogTip({content: '请选择支付方式'});
				return false;
			}
			if (balance_checked == true && (member_money >= real_amount)) {
				$("form[name=dopay]").attr("target",'_self');
			} else {
				$("form[name=dopay]").attr("target",'_blank');
			}
			// 赋值参数
			$("input[name=pay_code]").val(pay_code);
			$("input[name=pay_bank]").val(pay_bank);
			$("form[name=dopay]").submit();
		}
	};

})();