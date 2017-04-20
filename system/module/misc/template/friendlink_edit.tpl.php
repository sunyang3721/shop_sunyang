<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">友情链接设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST" enctype="multipart/form-data">
		<div class="content padding-big have-fixed-nav">
			<div>
			<input type="hidden" name="id" value="<?php echo $info['id']?>">
			</div>
			<div class="form-box clearfix">
			   <?php echo form::input('text', 'name', $info['name'], '链接名称：', '请填写友情链接的名称'); ?>
			   <?php echo form::input('radio', 'display', isset($info['display']) ? $info['display']:1, '是否显示：', '是否在前台显示', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
			   <?php echo form::input('radio', 'target', isset($info['target']) ? $info['target']:1, '是否新窗口打开：', '点击链接是否在新窗口打开', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
			   <?php echo form::input('file', 'logo',$info['logo'], '链接LOGO：','选择友情链接的LOGO');?>
			   <?php echo form::input('text', 'url', $info['url'], '链接地址：', '友情链接跳转地址'); ?>
			   <?php echo form::input('text', 'sort', $info['sort'] ? $info['sort'] : 100 , '排序：', '请填写自然数，友情链接会根据排序进行由小到大排列显示'); ?>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
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
