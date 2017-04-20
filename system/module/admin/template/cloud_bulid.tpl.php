<?php include template('header','admin');?>

		<div class="agree padding-none bg-white clearfix">
	       	<div class="form-group padding-none border-none clearfix">
		       	<div class="box margin-none" style="width: 100%;">
		       		<textarea class="layout textarea border-none" name="Area" style="height: 400px;"> 演示授权协议（孙洋论文设计需要，请忽略）
		       		</textarea>
		       	</div>
	       	</div>
	       	<div class="padding layout fl text-right ui-dialog-footer">
				<input type="button" class="button bg-main" id="agreement" value="同意并绑定" />
			</div>
		</div>
		<div class="login hidden">
			<form name="form" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" >
			<div class="padding-big">
				<p class="border-bottom">输入云商官方账号完成绑定<a class="text-main fr" href="javascript:" onClick="window.open('#')" >立即注册</a></p>
				<div class="login-con order-eidt-popup border-bottom-none clearfix">
					<?php echo form::input('text', 'account', '', '用户名：', '', array('datatype'=>'*')); ?>

					<?php echo form::input('password', 'password', '', '密码：', '', array('datatype'=>'*')); ?>
				</div>
			</div>
			<div class="padding text-right ui-dialog-footer">
				<input type="submit" class="button bg-main" id="okbtn" value="立即绑定" />
			</div>
			</form>
		</div>
		<div class="bind hidden">
			<div class="padding clearfix">
				<p class="fl padding text-mix text-lh-small">更改绑定站点，可能会导致丢失云市场授权，请谨慎操作！</p>
				<a class="fr button bg-main new_site" href="javascript:;">添加新站点</a>
			</div>
			<div class="table bg-white border-top clearfix" style="height: 247px; overflow: hidden; overflow-y: auto;">
				<div class="tr">
					<div class="th w25">站点名称</div>
					<div class="th w25">通信密钥</div>
					<div class="th w20">绑定时间</div>
					<div class="th w15">认证状态</div>
					<div class="th w15">操作</div>
				</div>

			</div>
			<div class="padding text-right ui-dialog-footer">
				<input type="button" class="button bg-main" id="bind" value="立即绑定" />
				<input type="button" class="button margin-left bg-gray" id="cancelbind" value="取消" />
			</div>
		</div>
		<script>
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				if(window.cloud == 0){
					$('#okbtn').val('立即绑定');
				}else{
					$('#okbtn').val('重新绑定');
				}

				var dialog = top.dialog.get(window);

				$("#agreement").click(function(){
					$(".agree").addClass("hidden");
					$(".login").removeClass("hidden");
					dialog.width(320);
					dialog.height(263);
					dialog.reset();
				});

				$(".table").find(".button").live('click',function(){
					$.each($(".table").find(".button"), function() {
						if($(this).hasClass('bg-main')){
							$(this).removeClass("bg-main").addClass("bg-gray");
						}
					});
					$(this).addClass("bg-main").removeClass("bg-gray");
					return false;
				});

				

				$("form").Validform({
					ajaxPost:true,
					callback:function(data){
						if(data.status == 0){
							alert(data.message);
							return false;
						}else if(data.status == 1 && data.result.site!=undefined){
							$(".login").addClass("hidden");
							$(".bind").removeClass("hidden");
							dialog.title('站点绑定');
							dialog.width(896);
							dialog.height(360);
							dialog.reset(); 
							var lists = data.result.site;
							var html = '';
							$.each(lists,function(i,item){
								var auth = text = '';
								if(item.current == 1){
									text = '当前绑定';
								}else{
									text = '<a class="button bg-gray" href="">选择</a>'
								}
								if(item.authentication_status == 0){
									auth = '<a class="text-main" target="_blank" href="http://www.xxxx.com/index.php?m=console&c=site&a=manage">立即认证</a>'
								}else{
									auth = '已认证';
								}
								html += 	'<div class="tr" data-identifier='+item.identifier+'>'
									 +		'<div class="td w25">'+item.site_name+'</div>'
									 +		'<div class="td w25">'+item.key+'</div>'
									 +		'<div class="td w20">'+item._install_time+'</div>'
									 +		'<div class="td w15">'+auth+'</div>'
									 +		'<div class="td w15">'+text+'</div>'
									 +	'</div>';
							})
							$('.table').append(html);
							return false;
						}
						dialog.close('1'); // 关闭（隐藏）对话框
						dialog.remove();	// 主动销毁对话框
					}
				});
				$('#bind').bind('click',function(){
					var identifier = $('.bg-main').parents('.tr').attr('data-identifier');
					$.post('<?php echo url("bind")?>',{identifier:identifier},function(ret){
						if(ret.status == 1){
							dialog.close(ret.result); // 关闭（隐藏）对话框
							dialog.remove();	// 主动销毁对话框
						}else{
							alert(ret.message);
						}
					},'json');
				})
				$('.new_site').bind('click',function(){
					if(confirm('进行此操作会新生成站点，确认该操作吗？')){
						$.post('<?php echo url("bind")?>',{identifier:''},function(ret){
							if(ret.status == 1){
								dialog.close(ret.result); // 关闭（隐藏）对话框
								dialog.remove();	// 主动销毁对话框
							}else{
								alert(ret.message);
							}
						},'json');
					}
				})
				$('#cancelbind').bind('click',function(){
					dialog.close('1'); // 关闭（隐藏）对话框
					dialog.remove();	// 主动销毁对话框
				})
			})
		</script>
<?php include template('footer','admin');?>
