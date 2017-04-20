<?php include template('header','admin');?>

	<form action="<?php echo url('order/admin_order/complete_parcel')?>" method="POST" name="parcel_form">
	<div class="form-box border-bottom-none order-eidt-popup clearfix">
		<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
		<?php echo form::input('radio', 'status', $parcelinfo['status'] ? $parcelinfo['status'] : 1, '是否确认配送：', '', array('items' => array('1'=>'配送中','2'=>'配送完成', '-1'=>'配送失败'), 'colspan' => 1,)); ?>
		<?php echo form::input('textarea', 'log', '', '备注：', '', array('datatype' => '*', 'nullmsg' => '请填写更改原因')); ?>
	</div>
	<div class="padding text-right ui-dialog-footer">
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
		dialog.title('确认配送');
		dialog.reset();     // 重置对话框位置


		var parcel_form = $("form[name='parcel_form']").Validform({
			ajaxPost:true,
			callback:function(ret) {
				dialog.title(ret.message);
				if(ret.status == 1) {
					setTimeout(function(){
						dialog.close(ret);
						dialog.remove();
					}, 1000);
				}
				return false;
			}
		})



		// $("#okbtn").live('click',function(){
		// 	if($("textarea[name=log]").val() == ''){
		// 		alert('请填写更改原因');
		// 		return false;
		// 	}
		// 	var url = "<?php echo url('order/admin_order/complete_parcel')?>";
		// 	var status = '' ;
		// 	$("input[name=status]").each(function(){
		// 		if($(this).attr('checked') == 'checked'){
		// 			status = $(this).val();
		// 		}
		// 	})
		// 	var date = {
		// 		"id":<?php echo $_GET['id']?>,
		// 		"log":$("textarea[name=log]").val(),
		// 		"status":status
		// 	}
		// 	$.post(url,date,function(data){
		// 		if(data.status == 1){
		// 			dialog.close(data);
		// 			dialog.remove();
		// 			return true;
		// 		}else{
		// 			alert('操作失败');
		// 			dialog.remove();
		// 			return false;
		// 		}
		// 	},'json')
		// })
		$('#closebtn').on('click', function () {
			dialog.remove();
			return false;
		});
	})
</script>
