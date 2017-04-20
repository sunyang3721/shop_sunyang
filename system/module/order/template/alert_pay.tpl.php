<?php include template('header','admin');?>

	<form name="pay_form" method="post">
	<div class="form-box border-bottom-none order-eidt-popup clearfix">
		<?php echo form::input('calendar','pay_status',date('Y-m-d H:i:s',time()),'付款时间：','',array('format' => 'YYYY-MM-DD hh:mm:ss','datatype' => '*')); ?>
		<?php echo form::input('text','paid_amount',$order['real_amount'],'付款金额：','请填写付款金额（必填）',array('datatype' => 'price')); ?>
		<?php  $pay_keys = array_keys($pays);$first_key = current($pay_keys);?>
		<?php echo form::input('select','pay_method',$first_key,'付款方式：','',array('items' => $pays)); ?>
		<?php echo form::input('text','pay_sn','','支付交易号：','',array('placeholder' => '请输入第三方交易凭证号（其它时选填）','datatype' => '*')); ?>
		<?php echo form::input('textarea','msg','','确认付款备注：','',array('placeholder' => '线下付款或其它支付方式建议填写')); ?>
	</div>
	<div class="padding text-right ui-dialog-footer">
		<input type="submit" class="button bg-main" id="okbtn" value="确定" name="dosubmit" data-reset="false"/>
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
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		dialog.title('确认付款');
		var obj_validform = $("form[name='pay_form']").Validform({
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
		// 当选择为 其它支付方式时禁止输入支付交易号 && 表单验证
		$('[name=pay_method]').bind("change" ,function(){
			if ($(this).val() == 'other') {
				$("input[name=pay_sn]").prop('disabled',true);
				$("[name=pay_sn]").parents(".form-group").find(".validation-tips").hide();
				obj_validform.ignore("[name=pay_sn]");
			} else {
				$("input[name=pay_sn]").prop('disabled',false);
				$("[name=pay_sn]").parents(".form-group").find(".validation-tips").show();
				obj_validform.unignore("[name=pay_sn]");
			}
		})
	})
</script>