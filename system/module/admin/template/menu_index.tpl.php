<?php include template('header','admin');?>
		<div class="relation-type layout bg-white padding-big clearfix">
			<div class="left fl">
				<p>已有菜单</p>
				<div class="type-selected-wrap border clearfix">
					<?php foreach ($menus as $menu): ?>
					 <span class="rtbox" data-menuid="<?php echo $menu['id'] ?>">
						<span class="txt"><?php echo $menu['title'] ?></span>
						<a class="delete" data-id="<?php echo $menu['id'] ?>" href="javascript:;">×</a>
					</span> 
					<?php endforeach ?>
				</div>
			</div>
			<div class="right">
				<p>待删除菜单</p>
				<div class="relation-optional-wrap layout border clearfix"></div>
			</div>
		</div>

		<script>
			var select_ids = new Array();
			if (top.dialog) {
				dialog = top.dialog.get(window);
			}
			dialog.title('选择自定义菜单');
			dialog.reset();
			
			$(function(){
				$('.relation-optional-wrap .rtbox').hover(function(){
					$(this).deleteClass("hover");
				},function(){
					$(this).removeClass("hover");
				});


				$('.type-selected-wrap .rtbox .delete').live('click',function(){
					id = $(this).attr('data-id');
					$('.relation-optional-wrap').append('<span class="rtbox"><span class="txt">'+$(this).prev().html()+'</span><a class="add" data-id='+id+' href="javascript:;">+</a></span>');
					$(this).parent().remove();
					getselect();
				});

				$('.relation-optional-wrap .rtbox .add').live('click',function(){
					id = $(this).attr('data-id');
					$('.type-selected-wrap').append('<span class="rtbox"><span class="txt">'+$(this).prev().html()+'</span><a class="delete" data-id='+id+' href="javascript:;">×</a></span>');
					$(this).parent().remove();
					getselect();
				});
				function getselect(){
					select_ids = new Array();
					$.each($('.relation-optional-wrap a'),function(index,data){
						select_ids.push($(this).attr('data-id'));
					})
					dialog.returnValue = select_ids.toString();
				}

			});
		</script>
<?php include template('footer','admin');?>
