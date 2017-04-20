<?php include template('header','admin');?>
	<form name="form-validate" method="post">
	<div class="form-box border-bottom-none order-eidt-popup clearfix">
		<?php if($order['pay_status'] == 1) : ?>
			<?php echo form::input('radio','isrefund',1,'','',array('items' => array(1 => '退款到账户余额'),'colspan' => 1)) ?>
			<div class="box margin-small-bottom"><span class="text-mix">温馨提示：该订单已支付￥<?php echo $order['real_price'];?></span></div>
		<?php else: ?>
			<div class="box margin-small-bottom margin-top"><span class="text-mix"> 温馨提示：该订单未支付，无需退款</span></div>
		<?php endif; ?>
		<?php echo form::input('textarea','msg','','是否确认取消订单：','',array('placeholder' => '请填写订单操作日志（选填）')) ?>
	</div>
	<div class="padding text-right ui-dialog-footer">
		<input type="hidden" name="sub_sn" value="<?php echo $_GET['sub_sn'] ?>" />
		<input type="submit" class="button bg-main" id="okbtn" value="确定" data-name="dosubmit" data-reset="false"/>
		<input type="button" class="button margin-left bg-gray" id="closebtn" value="取消"  data-reset="false"/>
	</div>
	</form>
<?php include template('footer','admin');?>

<script>
	$(function(){
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			return;
		}
		var $val=$("textarea").first().text();
		$("textarea").first().focus().text($val);
		dialog.title('取消订单');
		dialog.reset();     // 重置对话框位置
		$("form[name='form-validate']").Validform({
			ajaxPost:true,
			dragonfly:true,
			callback:function(ret) {
				message(ret.message);
				if(ret.status == 1) {
					setTimeout(function(){
						dialog.close();
						window.top.main_frame.location.reload();
					}, 1000);
				}
				return false;
			}
		})
		$('#closebtn').on('click', function () {
			dialog.remove();
			return false;
		});

	})
</script>
