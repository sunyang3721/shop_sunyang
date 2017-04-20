<?php include template('header','admin');?>
<!--<div class="relation-type layout bg-white padding-big clearfix">-->
			<!--<div class="left fl">
				<p>已选区域</p>
				<div class="type-selected-wrap border clearfix">
				</div>
			</div>
			<div class="right">
				<p>可选区域</p>
				<div class="relation-optional-wrap layout border clearfix">
				<?php foreach ($type as $k => $v) {?>
					<span class="rtbox" data-id="<?php echo $k?>">
						<span class="txt" data-id="<?php echo $k?>"><?php echo $v?></span>
						<a class="add" href="javascript:;">+</a>
					</span>
				<?php }?>
				</div>
			</div>
		</div>-->
		<div class="relate-norm padding-big clearfix">
		<?php foreach ($type as $k => $v) {?>
			<a class="txt" data-id="<?php echo $k?>"><?php echo $v?></a>
		<?php }?>
		</div>
		<div class="padding text-right ui-dialog-footer">
			<input type="button" class="button bg-main" id="okbtn" value="确定" />
			<input type="button" class="button margin-left bg-gray" id="closebtn" value="返回" />
		</div>
		<script>
//			$(window).load(function(){
//				$('.relation-optional-wrap .rtbox').hover(function(){
//					if($('.type-selected-wrap').children('span').length == 0){
//						$(this).addClass("hover");
//					}
//				},function(){
//					$(this).removeClass("hover");
//				});
//				
//				$('.type-selected-wrap .rtbox .delete').live('click',function(){
//					$('.relation-optional-wrap').append('<span class="rtbox"><span class="txt" data-id="'+$(this).prev().attr('data-id')+'">'+$(this).prev().html()+'</span><a class="add" href="javascript:;">+</a></span>');
//					$(this).parent().remove();
//				});
//				$('.relation-optional-wrap .rtbox .add').live('click',function(){
//					if($('.type-selected-wrap').children('span').length == 0){
//						$('.type-selected-wrap').append('<span class="rtbox"><span class="txt" data-id="'+$(this).prev().attr('data-id')+'">'+$(this).prev().html()+'</span><a class="delete" href="javascript:;">×</a></span>');
//						$(this).parent().remove();
//					}
//				});
//			});
			$(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				dialog.title('请选择一项属性关联该分类');
				dialog.reset();     // 重置对话框位置
				if(dialog.data[0] != 0){
					$('.relate-norm').find('a[data-id="' + dialog.data[0] + '"]').addClass('current');
					/*$('.type-selected-wrap').append('<span class="rtbox" data-id="'+dialog.data[0]+'"><span class="txt" data-id="'+dialog.data[0]+'">'+dialog.data[1]+'</span><a class="delete" href="javascript:;">×</a></span>');
					$.each($('.relation-optional-wrap').children('span'),function(i,item){
						if($(item).attr('data-id') == dialog.data[0]){
							$(item).remove();
						}
					})*/
				}
				$('#okbtn').on('click', function () {
					var type = $('.current');
					dialog.close(type); // 关闭（隐藏）对话框
					dialog.remove();	// 主动销毁对话框
					return false;
				});
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
				$(".relate-norm a").live('click',function(){
					if(!$(this).hasClass('current')){
						$(this).addClass('current').siblings().removeClass('current');
					}else{
						$(this).removeClass('current');
					}
				});
			})
		</script>
<?php include template('footer','admin');?>
