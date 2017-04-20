<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>后台管理登陆 - 云商系統</title>
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/haidao.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/admin.css" />
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/jquery-1.7.2.min.js" ></script>
		<!--<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.form.js" ></script>-->
	</head>
	<body>
		<div class="login-wrapper">
			<div class="left fl">
				<span class="logo"><img src="<?php echo __ROOT__;?>statics/images/login_logo.png" /></span>
				<p class="margin-big-top"><a class="text-sub" href="#" >云商系统</a></p>
			</div>
			<div class="right fr">
                <form action="<?php echo url('login');?>" onsubmit="return submit_check()" method="POST" data-layout="rank">
				<div class="form-box form-layout-rank border-bottom-none clearfix">
					<?php echo form::input('text', 'username', '', '用户名：', ''); ?>
					<?php echo Form::input('password', 'password', '', '密&emsp;码：'); ?>
				</div>
				<div class="login-btn margin-top">
                    <input type="submit" name="dosubmit" class="button bg-main" value="确定" />
				</div>
                </form>
			</div>
		</div>
		<script>
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
			$('.form-group:last-child').css({zIndex:"3"});
			function submit_check(){
				var $user=$("input[name=username]").val();
				var $pass=$("input[name=password]").val();
				if(!$user || !$pass){
					return false;
				}
			}
		</script>
<?php include template('footer','admin');?>
