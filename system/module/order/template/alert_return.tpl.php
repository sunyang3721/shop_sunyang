<?php include template('header','admin');?>

<form id="alert-box">
	<div class="form-box border-bottom-none order-eidt-popup clearfix">
		<?php echo form::input('radio','status', 1, '是否通过退货申请？','',array('colspan' => 2,'items'=>array(1 => '同意',0 => '拒绝'))); ?>
		<?php echo form::input('textarea','msg', '', '操作备注(选填)：'); ?>
	</div>
	<div class="padding text-right ui-dialog-footer">
		<input type="button" class="button bg-main" id="okbtn" value="确定" data-name="dosubmit" data-reset="false"/>
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
		$('#okbtn').on('click', function () {
			var status = $('[name=status]').val();
			var msg = $('[name=msg]').val();
			$.post('<?php echo url("order/admin_server/detail_return") ?>', {
				id : dialog.data,
				status : $("#alert-box").find('input[name="status"]:checked').val(),
				msg : $("#alert-box div").find('textarea').val()
			}, function(ret) {
				if (ret.status == 1) {
					alert(ret.message);
					dialog.close();
				} else {
					alert(ret.message);
					window.top.main_frame.location.reload();
				}
			},'json');
		});
		$('#closebtn').on('click', function () {
			dialog.remove();
			return false;
		});

	})
</script>
