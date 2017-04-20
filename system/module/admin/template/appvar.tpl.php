<?php include template('header','admin');?>
<style type="text/css">
	.form-group{border-bottom: 0;}
	.select-wrap{display: none;}
</style>
<form action="<?php echo url('update')?>" method="post" name="member_update">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="formhash" value="<?php echo FORMHASH ?>">

	<div class="form-box clearfix">
		<?php echo form::input('text', 'title', '', '配置名称:', '中英文均可，用于显示在插件配置的菜单中，最多 100 个字节。'); ?>
		<?php echo form::input('textarea', 'description', '', '配置说明:', '描述此项配置的用途和取值范围，详细的描述有利于插件使用者了解这个设置的作用，最多 255 个字节。此处和配置名称类似，也支持语言定义'); ?>
		<div class="select">
		<?php echo form::input('select', 'type','', '配置类型：', '设置此配置的数据类型，用于程序中检查和过滤相应配置值', array('items' => array('text'      => '字串(text)',
			'textarea'  => '文本(textarea)',
			'enabled'     => '开启/关闭(enabled)',
			'radio'     => '单选(radio)',
			'checkbox'  => '复选(checkbox)',
			'select'    => '下拉框(select)',
			'calendar'  => '日期/时间(calendar)',
			'color'  => '颜色(color)',
			'editor'  => '编辑器(editor)',
			'file'  => '文件选择(file)',))); ?>
		</div>
		<?php echo form::input('text', 'variable', '', '配置变量名：', '设置配置项目的变量名，用于插件程序中调用，可包含英文、数字和下划线，在同一个插件中需要保持变量名的唯一性，最多 40 个字节'); ?>
		<?php echo form::input('text', 'value', '', '变量默认值：', '设置变量默认值，用于插件设置中默认设置'); ?>
		<div class="select-wrap">
		<?php echo form::input('textarea', 'extra', '', '扩充设置：', '用于设定选项值。等号前面为选项索引(建议用数字)，后面为内容，例如:1 = 光电鼠标2 = 机械鼠标3 = 没有鼠标'); ?>
		</div>
	</div>


<div class="padding text-right ui-dialog-footer">
	<input type="submit" name="dosubmit" value="确定" class="button okbtn bg-main"/>
	<input type="button" name="button" value="取消" class="button closebtn margin-left bg-gray" data-back="false"/>
</div>
</form>

<script type="text/javascript">
	$(function(){
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			return;
		}
		var vars = dialog.data;
		$('[name=title').val(vars.title);
		$('[name=description]').val(vars.description);
		$('[name=variable]').val(vars.variable);
		$('[name=extra]').val(vars.extra);
		$('[name=type]').val(vars.type);
		$('[name=value]').val(vars.value);
		$('.form-buttonedit-popup input[type=text]').val($('.listbox-item[data-val='+vars.type+']').text());

		$('.okbtn').on('click', function () {
			var data = {};
			data.title = $('[name="title"]').val();
			data.description = $('[name="description"]').val();
			data.variable = $('[name="variable"]').val();
			data.extra = $('[name="extra"]').val();
			data.type = $('[name=type]').val();
			data.value = $('[name=value]').val();
			dialog.close(data); // 关闭（隐藏）对话框
			dialog.remove();	// 主动销毁对话框
			return false;
		});
		$('.closebtn').on('click', function () {
			dialog.remove();
			return false;
		});
		var select=$(".select").find(".form-select-name").val();
		if(select=="radio"||select=="checkbox"||select=="select"){
			$(".select-wrap").show();
			}else{
				$(".select-wrap").hide();
			}
		$(".select .form-select-name").change(function(event) {
			var select=$(this).val();
			if(select=="radio"||select=="checkbox"||select=="select"){
			$(".select-wrap").show();
			}
			else{
				$(".select-wrap").hide();
			}
		});

	})
</script>
<?php include template('footer','admin');?>
