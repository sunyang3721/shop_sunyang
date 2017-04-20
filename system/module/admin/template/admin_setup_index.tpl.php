<?php include template('header','admin');?>
<div class="fixed-nav layout">
    <ul>
        <li class="first">管理员设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
    </ul>
    <div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
	<form method="POST" action="<?php echo url('admin/admin_setup/edit')?>" enctype="multipart/form-data" name="form">
    <div class="content-tabs">
        <div class="form-box clearfix">
		  <?php echo form::input('password', 'oldpassword', '', '原密码：', '请填写密码', array(
			        	'datatype' => "*",
			        	'ajaxurl'  => url('admin/admin_setup/ajax_password'),
			        	'nullmsg'  => '请填写原密码', 
			        	'errormsg' => "原密码不正确，请重新填写",

		  )); ?>
		  <?php echo form::input('password', 'newpassword', '', '新密码：', '请填写新密码，为空则不修改', array(
			        	'datatype' => "/^\s*$/|*",

		  )); ?>
		  <?php echo form::input('password', 'pwpassword', '', '确认密码：', '请填写确认密码', array(
			        	'datatype' => "/^\s*$/|*",
						'recheck'  => "newpassword",
						'errormsg' => "两次输入的密码不一致",
		  )); ?>
		<?php echo form::input('file', 'portrait', $admin['avatar'], '头像：','显示于左上角');?>
        </div>
    </div>
    <div class="padding">
        <input type="submit" class="button bg-main" value="保存" />
    </div>
	</form>
</div>
<?php include template('footer','admin');?>
<script>
	$(function(){
		$("input[type=password]").first().focus();
		$("[name=form]").Validform();
	})
</script>
