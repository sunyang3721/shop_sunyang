<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">广告位设置</li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('index')?>">广告列表</a></li>
				<li><a class="current" href="javascript:;">广告位</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form name="form" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
			<div class="form-box clearfix">
				<?php echo form::input('text', 'name', $name, '广告位名称：', '请填写广告位名称，如：头部通栏广告位', array('datatype' => '*')); ?>
				<?php echo form::input('radio', 'status', $status, '是否启用', '是否启用广告位，如关闭，广告位下的所有广告将不展示', array('items' => array(1=>'开启',0=>'关闭'), 'colspan' => 2)); ?>	
				<?php echo form::input('radio', 'type', $type, '展示方式', '请选择广告位的展示方式', array('items' => array(0=>'图片',1=>'文字'), 'colspan' => 2)); ?>	
				<?php echo form::input('text', 'width', $width, '广告位宽度：', '设置图片广告位的宽度，单位为px', array('datatype' => 'n1-4','ignore'=>'ignore')); ?>
				<?php echo form::input('text', 'height', $height, '广告位高度：', '设置图片广告位的高度，单位为px', array('datatype' => 'n1-4','ignore'=>'ignore')); ?>
				<?php echo form::input('text', 'defaulttext', $defaulttext, '默认文字：', '当没有广告时广告位展示的文字内容'); ?>
				<?php echo form::input('file', 'defaultpic', $defaultpic, '广告图片：','当没有广告时广告位展示的图片',array('preview'=>$defaultpic));?>	
			</div>
			<div class="padding">
				<?php if(isset($id)):?>
					<input type="hidden" name="id" value="<?php echo $id?>" />
				<?php endif;?>
				<input type="submit" class="button bg-main" value="保存" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
			</form>
		</div>
		<script>
			$(window).otherEvent();
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				$("[name=form]").Validform();
				settypebox();
				$('input[name="type"]').on('change',function(){
					settypebox();
				})
				function settypebox(){
					var checked_type = $('input[name="type"]:checked').val();
					var text_box = $('.form-box .form-group:eq(5)');
					var pic_box = $('.form-box .form-group:eq(6)');
					var pic_width = $('.form-box .form-group:eq(3)');
					var pic_height = $('.form-box .form-group:eq(4)');
					if(checked_type == 1){
						text_box.show();
						pic_box.hide();
						pic_width.hide();
						pic_height.hide();
					}else{
						text_box.hide();
						pic_box.show();
						pic_width.show();
						pic_height.show();
					}
				}
			})
		</script>
<?php include template('footer','admin');?>
