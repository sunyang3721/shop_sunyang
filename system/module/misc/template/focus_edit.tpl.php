<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">幻灯片设置</li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;">首页幻灯片</a></li>
				<li><a href="<?php echo url('misc/navigation/index')?>">导航设置</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" name="focus_form" method="POST" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="id" value="<?php echo $info['id']?>">
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="form-box clearfix">
				<?php echo form::input('text', 'title', $info['title'], '幻灯片名称：', '请填写幻灯片名称', array('datatype' => '*', 'nullmsg' => '请填写幻灯片名称')); ?>
				<?php echo form::input('file', 'thumb',$info['thumb'], '幻灯片图片：','请上传幻灯片图片', array('datatype' => '*', 'nullmsg' => '请上传幻灯片图片'));?>
				<?php echo form::input('text', 'url', $info['url'], '幻灯片链接：', '请填写幻灯片点击后跳转的地址'); ?>
				<?php echo form::input('radio', 'target', isset($info['target']) ? $info['target']:1, '是否新窗口打开：', '点击幻灯片是否在新窗口打开页面', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
				<?php echo form::input('text', 'sort', $info['sort'] ? $info['sort'] : 100, '排序：', '请填写自然数，幻灯片将会根据排序进行由小到大排列显示'); ?>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</div>
		</form>
		<script type="text/javascript">
			$(function(){
				var site_base = $("[name=focus_form]").Validform({
					ajaxPost:false
				});
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
		</script>
	<?php include template('footer','admin');?>
