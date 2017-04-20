<?php include template('header','admin');?>
	<div class="fixed-nav layout">
		<ul>
			<li class="first">物流配送设置<a class="hidden" id="addHome" title="添加到首页快捷菜单">[+]</a></li>
			<li class="spacer-gray"></li>
		</ul>
		<div class="hr-gray"></div>
	</div>

	<form action="<?php url('order/admin_delivery/update'); ?>" method="post" enctype="multipart/form-data">
	<div class="content padding-big have-fixed-nav">
		<div class="form-box clearfix">
			<?php
			$def_enabled = (isset($delivery['enabled'])) ? $delivery['enabled'] : 1;
			echo form::input('enabled', 'enabled', $def_enabled, '是否开启该物流：', '设置是否开启当前配送方式，开启即可使用', array('itemrows' => 2));
			?>

			<?php echo form::input('text', 'name', $delivery['name'], '物流名称：', '设置配送方式名称，此处设置将在会员结算时显示，请根据实际情况填写，如顺丰快递'); ?>

			<?php echo form::input('text', 'identif', $delivery['identif'], '物流标识：', '请按照《标准快递公司及参数说明》设置'); ?>

			<?php echo form::input('file', 'logo', $delivery['logo'], '物流LOGO：', '设置配送方式LOGO'); ?>
		</div>

		<div class="form-box clearfix">
			<?php
			$def_sort = (isset($delivery['sort'])) ? $delivery['sort'] : 100;
			echo form::input('text', 'sort', $def_sort, '排序：', '请填写自然数，列表将会根据排序进行由小到大排列显示');
			?>
		</div>
		<div class="padding">
			<input type="hidden" name="id" value="<?php echo $delivery['id']; ?>" />
			<input type="submit" class="button bg-main" value="确定" name="dosubmit"/>
			<input type="button" class="button margin-left bg-gray" value="返回" />
		</div>
	</div>
	</form>

<script type="text/javascript">
$(function(){
	var $val=$("input[type=text]").first().val();
	$("input[type=text]").first().focus().val($val);
})
</script>