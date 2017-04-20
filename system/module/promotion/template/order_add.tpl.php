<?php include template('header','admin');?>
<div class="fixed-nav layout">
	<ul>
		<li class="first">订单促销管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
		<li class="spacer-gray"></li>
		<li><a class="current" href="javascript:;"></a></li>
	</ul>
	<div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
	<form action="<?php echo url('add');?>" name="promotion_order_add" method="post">
	<div class="form-box clearfix">
		<?php echo form::input('text', 'name', '', '促销名称：', '请输入促销名称', array(
			'datatype' => '*',
			'nullmsg' => '请输入促销名称',
			)
		); ?>
		<?php echo form::input('calendar', 'start_time', '', '开始时间：', '促销开始时间', array('format' => 'YYYY-MM-DD hh:mm')); ?>
		<?php echo form::input('calendar', 'end_time', '', '结束时间：', '促销结束时间', array('format' => 'YYYY-MM-DD hh:mm')); ?>
		<?php echo form::input('select', 'type', '0', '促销类型：', '请选择促销类型', array(
			'items' => array('满额立减', '满额免邮', '满额赠礼'),
		)); ?>
		<?php echo form::input('text', 'price', '', '单笔订单满足金额：', '请填写单笔订单满足金额', array("datatype" => 'price')); ?>
		<div class="prom-list" data-type="0">
			<?php echo form::input('text', 'discount[0]', '', '单笔订单立减金额：', '单笔订单满足条件后，订单减少的金额'); ?>
		</div>
		<div class="prom-list hidden" data-type="1">
			<input type="hidden" name="discount[1]" value="0">
		</div>
        <div class="prom-list hidden" data-type="2">
    		<div class="form-group">
				<span class="label">赠送礼品：</span>
				<div class="box ">
					<input class="goods-class-text input input-readonly" value="<?php echo $info['_sku_info_']['sku_name'] ?>" readonly="readonly" type="text" data-title="" data-pic="" data-price="" data-spec="" data-number="" data-id="0" data-sku="true"/>
					<input type="hidden" name="discount[2]" value="" data-type="id">
					<input class="goods-class-btn" type="button" value="选择" />
				</div>
				<p class="desc">请选择要赠送的礼物</p>
			</div>
        </div>
	</div>
	<div class="padding">
		<input type="submit" name="dosubmit" class="button bg-main" value="保存" />
		<input type="button" class="button margin-left bg-gray" value="返回">
	</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		$(".form-select-edit").live('click',function(){
			var type = $(this).find('.form-select-name').val();
			$(".prom-list[data-type='"+ type +"']").removeClass("hidden").siblings(".prom-list").addClass("hidden");
		});

		$(".goods-class-btn").live('click',function() {
			var params = {
				id : $("input[data-sku]").attr('data-id'),
				title : $("input[data-sku]").attr('data-title'),
				pic : $("input[data-sku]").attr('data-pic'),
				spec : $("input[data-sku]").attr('data-spec'),
				price : $("input[data-sku]").attr('data-price'),
				number : $("input[data-sku]").attr('data-number')
			}
			top.dialog({
				url: "<?php echo url('goods/sku/select',array('multiple' => 0,'type' => 'give'))?>",
				title: '选择赠品',
				width: 980,
				selected:params,
				onclose: function () {
					if($.isEmptyObject(this.returnValue) === false){
						$("input[data-type='id']").attr("value", this.returnValue.id);
						$("input[data-sku]").attr("data-id", this.returnValue.id);
						$("input[data-sku]").attr("data-title", this.returnValue.title).attr("value", this.returnValue.title);
						$("input[data-sku]").attr("data-pic", this.returnValue.pic);
						$("input[data-sku]").attr("data-spec", this.returnValue.spec);
						$("input[data-sku]").attr("data-price", this.returnValue.price);
						$("input[data-sku]").attr("data-number", this.returnValue.number);
					}else{
						$("input[data-sku]").attr('data-id', 0).attr('data-title', '').attr('data-pic', '').attr('data-spec', '').attr('data-price', '').attr('data-number', '').attr('value', '');
						$("input[type='hidden']").attr('value', 0);
					}
				}
			})
			.showModal();
		})

		var promotion_order_add = $("form[name=promotion_order_add]").Validform({
			ajaxPost:false
		})

		document.title = '添加订单促销';
	})
</script>
<?php include template('footer','admin');?>
