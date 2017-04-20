<?php include template('header','admin');?>
	<script type="text/javascript" charset="utf-8" src="statics/js/goods/goods_cat.js"></script>
		<div class="goods-add-class-wrap layout bg-white">
			<div class="goods-add-class clearfix">
				<div class="root border focus">
				</div>
				<div class="child border focus">	
				</div>
				<div class="child border focus">
				</div>
				<div class="child border focus">
				</div>
				<p class="layout fl margin-top goods-class-choose">您当前所选择的分类：<span></span>&emsp;</p>
			</div>
			<div class="padding text-right ui-dialog-footer">
				<input type="button" class="button bg-main" id="okbtn" value="确定" />
				<input type="button" class="button margin-left bg-gray" id="closebtn" value="返回" />
			</div>
		</div>		
		<script>
			$('.goods-add-class .root a,.goods-add-class .child a').live('click',function(){
				$(this).addClass('focus').siblings().removeClass('focus');
				//在下方显示已选择分类
				$('.goods-add-class .goods-class-choose span').html(classnametext());
			});
			//分类名称
			function classnametext(){
				var _txt = '';
				$('.goods-add-class div.focus').each(function(i){
					if($(this).find("a.focus").attr('class') == 'focus'){
						if($(this).index() == 0){
							_txt += $(this).find("a.focus").html();
						}else{
							_txt += '>'+$(this).find("a.focus").html();
						}
					}
				})
				return _txt;
			}
			//分类ids
			function classnameids(){
				var category_ids = Array();
				$('.goods-add-class div.focus').each(function(i){
					if($(this).find("a.focus").attr('class') == 'focus'){
						if($(this).index() == 0){
							category_ids[i] = $(this).find("a.focus").attr('data-id');
						}else{
							category_ids[i] = $(this).find("a.focus").attr('data-id');
						}
					}
				})
				return category_ids;
			}				
			$(function(){
				var dialog = top.dialog.get(window);
				dialog.title('选择上级分类');
				dialog.reset();     // 重置对话框位置
				$('#okbtn').on('click', function () {	
					dialog.close($('.goods-add-class .goods-class-choose span').html()+'category_ids='+classnameids()); // 关闭（隐藏）对话框
					dialog.remove();	// 主动销毁对话框
					return false;
				});
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
			})
			 //格式化分类
			var type = '<?php echo $_GET['type']?>';
			var dialog = top.dialog.get(window);
			//为空默认顶级分类
			dialog.data = dialog.data == '' ? '0' : dialog.data;
			jsoncategory = <?php echo json_encode($category) ?> ;
			switch(type){
				case 'article_category':
				    nb_category(0, '.root'); 
					break;
				case 'category':
					dialog.data = '0,'+dialog.data;
					nb_p_category(-1, '.root'); 
					break;
			}
			$.each(dialog.data.split(','),function(i,item){
				$('#cat' + item).click();
			})
		</script>
	<?php include template('footer','admin');?>
