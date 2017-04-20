<?php include template('header','admin');?>
<link rel="stylesheet" href="./statics/js/dialog/ui-dialog.css" />
	<div class="attr-wrap">
			<div class="tab-con">
				<div class="layout clearfix" style="padding-bottom: 46px;">
					<div class="table check-table border-none clearfix" style="height: 327px; overflow: hidden; overflow-y: auto;">
						<div class="tr border-none attr-title">
							<div class="th bg-none bg-white check-option"><b>删除</b></div>
							<div class="th bg-none bg-white text-left padding-lr w85"><b>属性名称</b></div>
							<div class="th bg-none bg-white w15"><b>操作</b></div>
						</div>
					</div>
				</div>
				<div class="spec-add-button" style="position: fixed; left: 0; bottom: 55px; padding: 0 15px; width: 100%; height: 46px; line-height: 46px;  background-color: #fdfdfd;">
					<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个属性</a>
				</div>
			</div>
			<div class="tab-con attr-choose-spec" style="display: none;">
				<div class="title"><label><input class="margin-small-right" id="checkAll" type="checkbox" />全选</label></div>
				<div class="padding attr-choose-wrap">
					<a href="javascript:;">颜色</a>
					<a href="javascript:;">尺码</a>
					<a href="javascript:;">型号</a>
				</div>
			</div>
		</div>
		<div class="padding text-right ui-dialog-footer">
			<input type="button" class="button bg-main" id="okbtn" value="确定" />
			<input type="button" class="button margin-left bg-gray" id="closebtn" value="返回" />
		</div>
		<script>
			$(function(){
				
				$('.tabs').click(function(){
					$(this).addClass("current").siblings().removeClass("current");
					$('.tab-con').eq($(this).index()).show();
					$('.tab-con').not($('.tab-con').eq($(this).index())).hide();
				});
				$('.attr-choose-wrap a').click(function(){
					if($(this).hasClass('current')){
						$(this).removeClass('current');
					}else{
						$(this).addClass('current');
					}
					var flog = true;
					$('.attr-choose-wrap a').each(function(){
						if(!$(this).hasClass('current')){
							flog = false;
						}
					});
					$('#checkAll').attr("checked",flog)
				});
				$('#checkAll').click(function(){
					if($(this).is(":checked")){
						$('.attr-choose-wrap a').each(function(){
							$(this).addClass('current');
						});
					}else{
						$('.attr-choose-wrap a').each(function(){
							$(this).removeClass('current');
						});
					}
				});
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				
				dialog.title('编辑属性值');
				dialog.reset();     // 重置对话框位置
				if(dialog.data){
					var poparr = dialog.data.split(',');
					$.each(poparr,function(i,item){
					var popvalue = '<div class="tr">';
						popvalue += '<div class="td check-option"><input type="checkbox"/></div>';
						popvalue += '<div class="td padding-lr w85 text-left"><input class="input w50" name="attr_value[]" type="text" value="'+item+'" /></div>';
						popvalue += '<div class="td w15"><a href="javascript:" onclick="delNewAttr(this)">删除</a></div>';
					$('.attr-title').parent().append(popvalue);
					});
				}else{
					var html = '<div class="tr">';
					html += '<div class="td check-option"><input type="checkbox"/></div>';
					html += '<div class="td padding-lr w85 text-left"><input class="input w50" name="attr_value[]" type="text" value="" /></div>';
					html += '<div class="td w15"><a href="javascript:" onclick="delNewAttr(this)">删除</a></div>';
					$(".table").append(html);
				}

				$(".spec-add-button a").click(function(){
					var html = '<div class="tr">';
					html += '<div class="td check-option"><input type="checkbox"/></div>';
					html += '<div class="td padding-lr w85 text-left"><input class="input w50" name="attr_value[]" type="text" value="" /></div>';
					html += '<div class="td w15"><a href="javascript:" onclick="delNewAttr(this)">删除</a></div>';
					$(".table").append(html);
				});
				
				$('#okbtn').on('click', function () {
					var ids = [];
					$('input[name="attr_value[]"]').each(function(){
						if(this.value != ''){
							ids.push(this.value);
						}
					})
					var value = ids.toString();
					dialog.close(value); // 关闭（隐藏）对话框
					dialog.remove();	// 主动销毁对话框
					return false;
				});
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
			})
				function delNewAttr(self){
					if (!confirm("确认要删除么？")) {
                        return false;
                    }
					$(self).parent().parent('.tr').remove();
				}
		</script>
<?php include template('footer','admin');?>
