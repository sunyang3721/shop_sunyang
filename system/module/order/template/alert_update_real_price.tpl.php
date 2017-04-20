<?php include template('header','admin');?>

	<div class="table bg-white">
		<div class="tr">
			<div class="td w25"><span class="text-main">应付订单金额</span></div>
			<div class="td w10"></div>
			<div class="td w25"><span class="text-main">折扣率(%)</span></div>
			<div class="td w10"></div>
			<div class="td w25"><span class="text-main">实付订单金额</span></div>
		</div>
		<div class="tr">
			<div class="td w25"><span class="text-orange"><?php echo $order['real_price']; ?></span></div>
			<div class="td w10">×</div>
			<div class="td w25">
				<div class="td-con">
					<input class="input" type="text" name="discount" />
				</div>
			</div>
			<div class="td w10">=</div>
			<div class="td w25">
				<div class="td-con">
					<input class="input" type="text" name="real_price"/>
				</div>
			</div>
		</div>
		<p class="text-center line-height-40">小提示：您可以直接调整订单的最终付款价格，确认实付订单金额或折扣率均可</p>
	</div>
	<div class="padding text-right">
		<input type="button" class="button bg-main" data-btn="okbtn" value="确定" />
		<input type="button" class="button margin-left bg-gray" id="closebtn" value="取消" />
	</div>
<?php include template('footer','admin');?>

<script>
/* 自动计算折扣&价格 */
var real_price = <?php echo $order['real_price'];?>;
$('[name=discount]').on('keypress keyup blur', function(){
	var discount = $(this).val();
	var money = (real_price * discount / 100).toFixed(2);
	$("[name=real_price]").attr('value', money);
})
$('[name=real_price]').on('keypress keyup blur', function(){
	var money = $(this).val();
	var discount = ((money / real_price) * 100).toFixed(2);
	$("[name=discount]").attr('value', discount);
})

$(function(){
	try {
		var dialog = top.dialog.get(window);
	} catch (e) {
		return;
	}
	var $val=$("input[type=text]").first().val();
	$("input[type=text]").first().focus().val($val);
	dialog.title('修改订单应付总额');
	dialog.reset();     // 重置对话框位置
	$("[data-btn=okbtn]").on('click', function () {
		real_price = $('[name=real_price]').val();
		if (real_price == null || real_price == undefined) {
			alert('请填写实付订单金额');
			return false;
		}
		dialog.data.real_price = real_price;
		$.post(dialog.data.tpl_url, dialog.data , function(ret) {
			dialog.close();
			if (ret.status != 1) alert(ret.message);
			window.top.main_frame.location.reload();
		},'json');
	});
	$('#closebtn').on('click', function () {
		dialog.remove();
		return false;
	});
})
</script>