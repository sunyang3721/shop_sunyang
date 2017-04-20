<?php
if($url == -1) {
	$url = 'javascript:history.back()';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__; ?>statics/css/haidao.css?v=<?php echo HD_VERSION ?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__; ?>statics/css/admin.css?v=<?php echo HD_VERSION ?>" />
		<script type="text/javascript" src="<?php echo __ROOT__; ?>statics/js/jquery-1.7.2.min.js?v=<?php echo HD_VERSION ?>"></script>
		<script type="text/javascript" src="<?php echo __ROOT__; ?>statics/js/haidao.plug.js?v=<?php echo HD_VERSION ?>"></script>
	</head>
	<body>
		<div class="padding-big layout">
			<div class="success-info layout border radius bg-white">
				<div class="title border-bottom text-white">
					<h6>温馨提示</h6>
				</div>
				<div class="text border-bottom">
					<span><?php echo $message; ?></span>
					<?php if($url != 'null') : ?>
					<p><a href="<?php echo $url ?>">如果您的浏览器没有自动跳转，请点击这里</a></p>
					<?php endif;?>
				</div>
			</div>
		</div>
<script type="text/javascript">
<?php if($url != 'null') : ?>
setTimeout(function() {
	location.href = '<?php echo strip_tags($url) ?>';
}, 3000);
<?php endif;?>
</script>
	</body>
</html>
