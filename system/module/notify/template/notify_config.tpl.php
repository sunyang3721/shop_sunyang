<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">通知系统设置</li>
				<!--<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;">邮件设置</a></li>
				<li><a href="email_test.html">发送测试</a></li>-->
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form action="<?php echo url('config') ?>" method="POST">
			<input type="hidden" name="code" value="<?php echo $notify['code'] ?>">
			<div class="form-box clearfix">
				<?php foreach ($notify['vars'] as $key => $var): ?>
					<?php echo form::input($var['type'], 'vars['.$key.']', $_config[$key], $var['name'], $var['tips']); ?>
				<?php endforeach ?>
			</div>
			<div class="padding">
				<input type="submit" name="dosubmit" class="button bg-main" value="保存" />
				<input type="reset" class="button margin-left bg-gray" value="返回" />
			</div>
			</form>
		</div>
	<script>
		$(function(){
			var $val=$("input[type=text]").first().val();
			$("input[type=text]").first().focus().val($val);
		})
	</script>
<?php include template('footer','admin');?>
