<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">团队管理设置</li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;"></a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form action="" method="POST" enctype="multipart/form-data">
			<div class="form-box clearfix">
				<?php if(isset($id)):?>
					<?php echo form::input('text', 'username', $username, '用户名：', '用户名不允许修改', array('validate'=>'required;','readonly'=>'')); ?>
				<?php else:?>
					<?php echo form::input('text', 'username', $username, '用户名：', '请输入用户名', array('validate'=>'required;')); ?>
				<?php endif;?>
				<?php echo form::input('password', 'password', '', '密码：', '为空则不进行修改', array('validate'=>'required;')); ?>
				<?php echo form::input('select', 'group_id', $group_id, '角色组', '', array('items' => $group)); ?>


			</div>
			<div class="padding">
				<?php if(isset($id)):?>
					<input type="hidden" name="id" value="<?php echo $id?>" />
				<?php endif;?>
				<input type="submit" class="button bg-main" value="保存" />
				<a href="<?php echo url('index')?>" class="button margin-left bg-gray" >返回</a>
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
