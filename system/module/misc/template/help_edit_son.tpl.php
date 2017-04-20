<?php include template('header','admin');?>
		<script type="text/javascript" charset="utf-8" src="statics/js/ueditor/ueditor.config.js"></script>
   	 	<script type="text/javascript" charset="utf-8" src="statics/js/ueditor/ueditor.all.min.js"> </script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">站点帮助详情</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST">
		<div class="content padding-big have-fixed-nav">
			<div>
				<input type="hidden" name="id" value="<?php echo $info['id']?>" />
				<input type="hidden" name="parent_id" value="<?php echo $info['parent_id']?>" />
			</div>
			<div class="form-box clearfix">
				<?php echo form::input('text', 'title', $info['title'], '帮助标题：', '请填写站点帮助的名称'); ?>
				<?php echo form::input('select', 'parent_id',$info['parent_id'], '父分类：', '选择站点帮助所属上级', array('items' => $parent)); ?>
				<?php echo form::input('text', 'keywords', $info['keywords'], '帮助关键字：', '帮助关键字出现在页面头部的<Meta>标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔'); ?>
			</div>
			<div class="padding">
				<span class="margin-bottom show clearfix">帮助内容：</span>
				<?php echo form::editor('content', $info['content'], '', '', array('module'=>'common','mid' => $admin['id'], 'path' => 'common')); ?>
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
