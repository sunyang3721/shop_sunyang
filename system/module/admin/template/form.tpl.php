<?php include template('header','admin');?>
<div class="fixed-nav layout">
    <ul>
        <li class="first">站点设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">基本表单</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">编辑器</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">交互示例</a></li>
    </ul>
    <div class="hr-gray"></div>
</div>

<div class="content padding-big have-fixed-nav">
    <div class="content-tabs">
        <div class="form-box clearfix">
        <?php echo form::input('text', 'text', '', '站点名称：', '内容描述'); ?>
        <?php echo form::input('password', 'password', '', '公司名称：', '内容描述'); ?>
        <?php echo form::input('enabled', 'enabled', 1, '开关', '开启与关闭'); ?>
        <?php echo form::input('calendar', 'calendar', '', '日历控件', '日历'); ?>
        <?php echo form::input('textarea', 'textarea', '', '商城关闭原因：'); ?>
        <?php echo form::input('file', 'site_logo', '', '商城LOGO：');?>
        <?php echo form::input('select', 'cart_jump', 1, '下拉菜单：', '下拉菜单', array('items' => array('菜单1', '菜单2', '菜单3', '菜单2', '菜单3', '菜单2', '菜单3'))); ?>
        <?php echo form::input('select', 'cart_jump', 1, '下拉菜单：', '下拉菜单', array('items' => array('菜单1', '菜单2', '菜单3', '菜单2', '菜单3', '菜单2', '菜单3'))); ?>
        <?php echo form::input('select', 'cart_jump', 1, '下拉菜单：', '下拉菜单', array('items' => array('菜单1', '菜单2', '菜单3', '菜单2', '菜单3', '菜单2', '菜单3'))); ?>
        <?php echo form::input('select', 'cart_jump', 1, '下拉菜单：', '下拉菜单', array('items' => array('菜单1', '菜单2', '菜单3', '菜单2', '菜单3', '菜单2', '菜单3'))); ?>
        <?php echo form::input('radio', 'radio', 0, '单项选择框', '表单描述', array('items' => array('选项1', '选项2', '选项3'), 'colspan' => 2, 'disabled' => '0,2')); ?>
        <?php echo form::input('checkbox', 'checkbox', '0, 1', '复选框', '复选框', array('items' => array('选项1', '选项2', '选项3', '选项4', '选项5'), 'colspan' => 3, 'disabled' => '2,3')); ?>
        <?php echo form::input('text', 'color', '', '颜色', '请选择标题颜色', array('color' => '1', 'key' => 'style[]')); ?>
        <?php echo form::input('text', 'color2', '', '颜色', '请选择标题颜色', array('color' => '2', 'key' => 'style[]')); ?>
        <?php echo form::input('text', 'color3', '', '颜色', '请选择标题颜色', array('color' => '3', 'key' => 'style[]')); ?>
        <!-- 简易颜色表单 -->
        <?php echo form::color('color[]', '#222'); ?>
        <?php echo form::color('color[]', 'red'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix">
	        <div class="padding">
				<span class="margin-bottom show clearfix">文章内容：</span>
				<?php echo form::editor('a[content]', 'funk！！'); ?>
			</div>

	        <div class="padding">
				<span class="margin-bottom show clearfix">文章内容：</span>
				<?php echo form::editor('b[editor]', 'shit！！'); ?>
			</div>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix">
	        <div class="form-group">
		        <span class="label">弹窗示例：</span>
		        <div class="box">
			        <a href="http://www.baidu.com" data-iframe="true">超链接弹窗</a><br /><br />
			        <input type="button" class="button bg-main" value="按钮弹窗" data-iframe="http://www.baidu.com"/>
		        </div>
		        <p class="desc">内容描述</p>
		    </div>

		    <form action="<?php echo url('update');?>" method="POST" name="site_base" data-ajax="true" data-validate='true' data-handkey="site_base">
			    <?php echo form::input('text', 'text', '', '手机：', '内容描述', array(
			        	'datatype' => "mobile",
			        	//'ajaxurl' => url('ajax_mobile'),//无刷新判断
			        	'nullmsg'	=> '信息不能为空', // 为空时的提示
			        	'errormsg' => "手机号格式不正确", // 错误的地址

			        )); ?>
			    <?php echo form::input('text', 'text', '', 'email：', '内容描述', array(
			        	'datatype' => "email",
			        	//'ajaxurl' => url('ajax_mobile'),//无刷新判断
			        	'nullmsg'	=> '信息不能为空', // 为空时的提示
			        	'errormsg' => "手机号格式不正确", // 错误的地址

			        )); ?>
			    <?php echo form::input('text', 'text', '', '邮编：', '内容描述', array(
			        	'datatype' => "zip",
			        	//'ajaxurl' => url('ajax_mobile'),//无刷新判断
			        	'nullmsg'	=> '信息不能为空', // 为空时的提示
			        	'errormsg' => "手机号格式不正确", // 错误的地址

			        )); ?>
		    <input type="submit" name="dosubmit" class="button bg-main" value="保存"/>
		    </form>
			<script type="text/javascript">
				$(function(){
					var $val=$("input[type=text]").first().val();
					$("input[type=text]").first().focus().val($val);
					var site_base = $("[name=site_base]").Validform({
						ajaxPost:true,
						callback:function(result) {
							/* ajax 回调函数 */
							console.log(result);
						}
					});
				})
			</script>





        </div>
    </div>
</div>

		<script>
			// $(function(){
			// 	$("#form-group-id5 .select-btn").live('click',function(){
			// 		if($(this).val()=='2'){
			// 			$("#form-group-id6").slideDown(100);
			// 		}else{
			// 			$("#form-group-id6").slideUp(100);
			// 		}
			// 	});
			// 	$("#form-group-id16 .select-btn").live('click',function(){
			// 		if($(this).val()=='2'){
			// 			$(".for-invoice").slideUp(100);
			// 		}else{
			// 			$(".for-invoice").slideDown(100);
			// 		}
			// 	});
			// 	$("#form-group-id14 .select-btn").live('click',function(){
			// 		if($(this).val()=='1'){
			// 			$("#form-group-id15").slideDown(100);
			// 		}else{
			// 			$("#form-group-id15").slideUp(100);
			// 		}
			// 	});
			// 	function loadAfterExecutive(){
			// 		var id_5 = $("#form-group-id5 .select-btn");
			// 		for(var i=0;i<id_5.length;i++){
			// 			if(id_5.eq(i).is(":checked")){
			// 				if(id_5.eq(i).val()=="1"){
			// 					$("#form-group-id6").hide();
			// 				}else{
			// 					$("#form-group-id6").show();
			// 				}
			// 			}
			// 		}
			// 		var id_14 = $("#form-group-id14 .select-btn");
			// 		for(var i=0;i<id_14.length;i++){
			// 			if(id_14.eq(i).is(":checked")){
			// 				if(id_14.eq(i).val()=="1"){
			// 					$("#form-group-id15").show();
			// 				}else{
			// 					$("#form-group-id15").hide();
			// 				}
			// 			}
			// 		}
			// 		var id_16 = $("#form-group-id16 .select-btn");
			// 		for(var i=0;i<id_16.length;i++){
			// 			if(id_16.eq(i).is(":checked")){
			// 				if(id_16.eq(i).val()=="1"){
			// 					$(".for-invoice").show();
			// 				}else{
			// 					$(".for-invoice").hide();
			// 				}
			// 			}
			// 		}
			// 	}
			// 	loadAfterExecutive();
			// });
		</script>
<?php include template('footer','admin');?>
