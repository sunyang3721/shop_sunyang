<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">注册与访问<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST">
		<div class="content padding-big have-fixed-nav">
			<div class="form-box clearfix">
				<?php echo runhook('set_reg_extra');?>
				<?php echo form::input('radio', 'reg_allow', $setting['reg_allow'], '是否允许注册：', '设置是否允许游客注册成为站点会员', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
				<?php echo form::input('textarea', 'reg_closedreason', $setting['reg_closedreason'], '请填写注册关闭原因，将在前台显示'); ?>
				<?php echo form::input('checkbox', 'reg_user_fields[]',$setting['reg_user_fields'], '注册信息填写：', '注册时是否需要填写邮箱及手机，填写后如配置邮件及短信，则需要进行验证后方能注册成功', array('items' => array('email'=>'邮箱注册','phone'=> '手机注册'), 'colspan' => 1,)); ?>
				<?php echo form::input('textarea', 'reg_user_censor', $setting['reg_user_censor'], '用户名保留关键字：','用户在其用户信息中无法使用这些关键字。每个关键字一行，可使用通配符 "*" 如 "*管理员*"(不含引号)'); ?>
				<?php echo form::input('text', 'reg_pass_lenght',$setting['reg_pass_lenght'], '密码最小长度：', '新用户注册时密码最小长度，0或不填为不限制'); ?>
				<?php echo form::input('checkbox', 'reg_pass_complex[]', $setting['reg_pass_complex'], '强制密码复杂度：', '新用户注册时密码中必须存在所选字符类型，不选则为无限制', array('items' => array('num'=>'数字','small'=> '小写字母', 'big'=>'大写字母','sym'=>'符号'), 'colspan' => 1,)); ?>
				<?php echo form::input('textarea', 'reg_agreement', $setting['reg_agreement'], '注册服务条款内容：','新用户注册时显示网站服务条款内容'); ?>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存"/>
			</div>
		</div>
		</form>
		<script>
			$(function(){
				$.each($("input[name=reg_allow]"),function(){
					if($(this).attr('checked') == 'checked'){
						if($(this).val() == '0'){
							$(this).parents(".form-group").next().show();
							var $val=$("textarea").first().text();
							$("textarea").first().focus().text($val);
						}else{
							$(this).parents(".form-group").next().hide();
							var $val=$("textarea").eq(1).text();
							$("textarea").eq(1).focus().text($val);
						}
					}
				})
			})
			$("input[name=reg_allow]").live('click',function(){
				if($(this).val() == 0){
					$(this).parents(".form-group").next().show();
				}else{
					$(this).parents(".form-group").next().hide();
				}
			})
		</script>
<?php include template('footer','admin');?>
