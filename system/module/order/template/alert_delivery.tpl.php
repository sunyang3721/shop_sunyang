<?php include template('header','admin');?>

	<form name="form-validate" method="post">
	<div class="form-box border-bottom-none order-eidt-popup invoice-form-box clearfix">
		<?php echo form::input('radio','is_choise',1,'','',array('items' => array(1 => '选择物流公司' , 2 => '无需物流运输'),'colspan' => 2)) ?>
		<div id="shows">
			<?php $first = reset($deliverys); $first_key = key($deliverys); ?>
			<?php echo form::input('select','delivery_id',$first_key,'物流公司：','',array('items' => $deliverys)) ?>
			<?php echo form::input('text', 'delivery_sn', '','物流单号：','',array('datatype' => '*')); ?>
		</div>
		<?php echo form::input('textarea','msg','','确认订单发货备注：','',array('placeholder' => '请填写订单操作日志（选填）')); ?>
	</div>
	<div class="invoice-table-box">
		<div class="table high-table check-table invoice-table border">
			<div class="tr border-none">
				<div class="th check-option"><input id="check-all" type="checkbox"><span class="spacer"></span></div>
				<div class="th w55">商品信息<span class="spacer"></span></div>
				<div class="th w15">商品价格<span class="spacer"></span></div>
				<div class="th w15">购买数量<span class="spacer"></span></div>
				<div class="th w15">是否发货</div>
			</div>
			<div class="auto-box">
			<?php foreach ($o_skus as $key => $sku): ?>
				<div class="tr">
					<div class="td check-option"><input type="checkbox" name="o_sku_id[]" value="<?php echo $sku['id'];?>" <?php if ($sku['delivery_status']!=0){echo 'disabled="disabled" checked="checked"';} ?>/></div>
					<div class="td w55">
						<div class="td-con td-pic text-left">
							<span class="pic"><img src="<?php echo $sku['sku_thumb'] ?>"></span>
							<span class="title txt text-ellipsis"><?php echo $sku['sku_name'] ?></span>
							<span class="icon text-ellipsis"><?php echo $sku['_sku_spec'] ?></span>
						</div>
					</div>
					<div class="td w15">￥<?php echo $sku['sku_price']?></div>
					<div class="td w15"><?php echo $sku['buy_nums']?></div>
					<div class="td w15"><?php if ($sku['delivery_status'] == 0) {echo '否';}else{echo '是';} ?></div>
				</div>
			<?php endforeach ?>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="padding text-right ui-dialog-footer">
		<input type="hidden" name="sub_sn" value="<?php echo $_GET['sub_sn'];?>" />
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
		var $val=$("input[type=text]").eq(1).val();
		$("input[type=text]").eq(1).focus().val($val);
		dialog.title('确认发货');
		dialog.width(900);
		dialog.reset();     // 重置对话框位置
		var obj_validform = $("form[name='form-validate']").Validform({
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

		// 点击是否选择物流 && 表单验证
		$("input[type=radio]").live('click',function() {
			if ($("input[type=radio]:checked").val() == 2) {
				$("#shows .form-select-edit").addClass('disabled');
				$("#shows .form-select-edit input").removeAttr('readonly').prop('disabled',true);
				$("#shows").find('input').prop('disabled',true);

				$("[name=delivery_sn]").parents(".form-group").find(".validation-tips").hide();
				obj_validform.ignore("[name=delivery_sn]");
			} else {
				$("#shows .form-select-edit").removeClass('disabled');
				$("#shows .form-select-edit input").attr('readonly','readonly').prop('disabled',false);
				$("#shows").find('select').prop('disabled',false);
				$("#shows").find('input').prop('disabled',false);

				$("[name=delivery_sn]").parents(".form-group").find(".validation-tips").show();
				obj_validform.unignore("[name=delivery_sn]");
			}
		})

		$('#closebtn').on('click', function () {
			dialog.remove();
			return false;
		});
		
		function autoBox(){
			if($(".auto-box").find(".tr").length>3){
				$(".auto-box").prev(".tr").css({paddingRight:"17px",background:"#eee"});
			}
		}
		autoBox();
	})
</script>