<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">导航设置</li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('misc/focus/index')?>">首页幻灯片</a></li>
				<li><a class="current" href="javascript:;">导航设置</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST">
		<div>
			<input type="hidden" name="id" value="<?php echo $info['id']?>" />
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="form-box clearfix">
				<?php echo form::input('text', 'name', $info['name'], '导航名称：', '请填写商城主导航的名称'); ?>
				<?php echo form::input('text', 'url', $info['url'], '导航链接：', '请填写主导航点击后跳转的地址'); ?>
				<?php echo form::input('radio', 'target', isset($info['target']) ? $info['target']:1, '是否新窗口打开：', '点击导航是否在新窗口打开页面', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
				<?php echo form::input('text', 'sort', $info['sort'] ? $info['sort'] : 100, '排序：', '请填写自然数，导航将会根据排序进行由小到大排列显示'); ?>
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
			$('.form-box').formPlug();
			var ue = UE.getEditor('editor');
			function setClass(){
				top.dialog({
					url: 'choose_class_popup.html',
					title: '加载中...',
					width: 930,
					onclose: function () {
						if(this.returnValue){
							console.log(this.returnValue)
						}
					}
				})
				.showModal();
			}
		</script>
	<?php include template('footer','admin');?>
