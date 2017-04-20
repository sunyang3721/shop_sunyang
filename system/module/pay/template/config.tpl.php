<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first"><?php echo $payment['pay_name']?>设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form name="form" action="<?php echo url('config') ?>" method="POST">
			<div class="form-box clearfix">
				<?php foreach ($payment['setup'] as $key => $var): ?>
					<?php if($var['type'] == 'radio'){?>
						<?php echo form::input('radio', 'config['.$key.']', $payment['config'][$key], '选择支付使用场景：', '选择京东支付的使用场景', array('items' => array('pc'=>'电脑'), 'colspan' => 1)); ?>
					<?php }else{?>
					<?php echo form::input($var['type'], 'config['.$key.']', $payment['config'][$key], $var['name'], $var['tips'],array('datatype' => "*")); ?>
					<?php }?>
				<?php endforeach ?>
				<?php unset($payment['setup']);unset($payment['config']);?>
				<?php foreach ($payment as $key => $var): ?>
					<?php echo form::input('hidden', $key, $var); ?>
				<?php endforeach ;?>
			</div>
			<div class="padding">
				<input type="submit" name="dosubmit" class="button bg-main" value="保存" />
				<a href="<?php echo(url('setting'))?>" class="button margin-left bg-gray">返回</a>
			</div>
			</form>
		</div>
		<script type="text/javascript">
			$(function(){		
				$("[name=form]").Validform();
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
		</script>
<?php include template('footer','admin');?>
