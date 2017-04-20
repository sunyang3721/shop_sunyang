<?php
	if($url == -1) {
		$url = 'javascript:history.back()';
	}
	if(!defined('MOBILE')){include template('toper','common');};
	include template('header','common');
?>

	<div class="layout border-top shop-maintain">
		<div class="container border-bottom maintain-tip">
			<div class="padding-left margin-tb text-gray border-left border-middle border-sub h4">温馨提示</div>
		</div>
		<div class="container">
			<div class="system-tip text-left">
				<h2><img src="<?php echo __ROOT__; ?>template/default/statics/images/icon_tip.png" /><?php echo $message; ?>！</h2>
				<div class="tip-text margin-top">
					<span>您可以尝试以下操作：</span><br />
					<a href="<?php echo __APP__ ?>">商城首页</a>|<a href="<?php echo url('member/order/index') ?>">我的订单</a>|<a href="<?php echo url('order/cart/index') ?>">我的购物车</a>
				</div>
			</div>
		</div>
	</div>
<?php if(!defined('MOBILE')){include template('toolbar','common');};?>
<?php include template('footer','common');?>

<script type="text/javascript">
	<?php if($url != NULL) : ?>
		setTimeout(function() {
			location.href = '<?php echo strip_tags($url) ?>';
		}, 3000);
	<?php endif;?>
</script>