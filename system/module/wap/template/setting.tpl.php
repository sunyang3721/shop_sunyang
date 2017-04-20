<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">微店设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST">
		<div class="content padding-big have-fixed-nav">
			<div class="form-box clearfix">
				<?php echo form::input('radio', 'wap_enabled', $setting['wap_enabled'], '是否开启移动端：', '必须开启方可访问', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
				<?php echo form::input('radio', 'display_mode', $setting['display_mode'], '商品列表样式：', '设置商品在移动端展示样式', array('items' => array('1'=>'橱窗样式', '0'=>'列表样式'), 'colspan' => 2,)); ?>
				<?php echo form::input('radio', 'is_jump', $setting['is_jump'], '是否自动跳转：', '是否允许移动端设备自动识别会跳转到移动端域名', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
				<?php echo form::input('text', 'wap_domain',$setting['wap_domain'], '移动端域名：', '移动端专属域名,不带http及末尾的/(如：m.xxx.com)'); ?>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存"/>
			</div>
		</div>
		</form>
	<script>
		$(function(){
			var $val=$("input[type=text]").first().val();
			$("input[type=text]").first().focus().val($val);
		})
	</script>
<?php include template('footer','admin');?>