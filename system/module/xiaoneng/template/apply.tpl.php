<?php include template('header','admin');?>
		<style>
			.check-box input { margin-top: -3px; margin-right: 10px; vertical-align: middle; }
			.custom-service-wran { padding: 80px 120px; }
		</style>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">客服开通<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="hr-gray"></div>
			<form method="POST" action="<?php echo url('apply'); ?>" name="apply">
			<div class="form-box clearfix">
				<?php echo form::input('text', 'company_name', '', '公司名称：', '请输入公司名称',array('datatype' => '*', 'nullmsg' => '公司名称不能为空')); ?>
				<?php echo form::input('text', 'link_man', '', '公司联系人：', '请输入公司联系人',array('datatype' => '*', 'nullmsg' => '公司联系人不能为空')); ?>
				<?php echo form::input('text', 'mobile_phone_no', '', '公司联系人手机：', '请输入公司联系人手机',array('datatype' => '*', 'nullmsg' => '公司联系人手机不能为空')); ?>
				<?php echo form::input('text', 'email', '', '公司邮箱：', '请输入公司邮箱',array('datatype' => '*', 'nullmsg' => '公司邮箱不能为空')); ?>
				<?php echo form::input('text', 'addr', '', '公司地址：', '请输入公司地址',array('datatype' => '*', 'nullmsg' => '公司地址不能为空')); ?>
				<?php echo form::input('text', 'url', '', '公司网址：', '请输入公司网址',array('datatype' => '*', 'nullmsg' => '公司网址不能为空')); ?>
				<?php echo form::input('radio', 'version', 'PCWAP1', '坐席数量：', '选择小能账号的坐席数量', array('items' => array('PCWAP1'=>'3坐席', 'PCWAP2'=>'5坐席'), 'colspan' => 2,)); ?>
			</div>
			<div class="padding">
       	 		<input type="submit" class="button bg-main" value="保存" />
    		</div>
    		</form>
		</div>
	</body>
	<script type="text/javascript">
	var apply = $("[name=apply]").Validform({
            ajaxPost:false,
        });
	</script>
</html>
