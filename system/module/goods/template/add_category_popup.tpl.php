<?php include template('header','admin');?>
	<script type="text/javascript" src="./statics/js/goods/goods_cat.js" ></script>
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
				<p class="layout fl margin-top goods-class-choose">您当前所选择的分类：<span></span><em class="margin-left text-mix"></em></p>
			</div>
			<div class="padding text-right ui-dialog-footer">
				<input type="button" class="button bg-main" id="okbtn" value="确定" />
				<input type="button" class="button margin-left bg-gray" id="closebtn" value="返回" />
			</div>
		</div>
		<script>
			$('.goods-add-class .root a,.goods-add-class .child a').live('click',function(){
				$('.text-mix').html('');
				var _this = $(this);
				$('.goods-class-choose span').html(classNameText());
				$('.goods-class-choose span').attr('data-id','')
				if(_this.parent().next('.child').children('a').length == 0){
					$('.goods-class-choose span').attr('data-id',classId());
				}
			});
			function classNameText(){
				var _txt = '';
				$('.goods-add-class div.focus').each(function(){
					if($(this).find("a.focus").html()!=null){
						if($(this).index()==0){
							_txt += $(this).find("a.focus").html();
						}else{
							_txt += '>'+$(this).find("a.focus").html();
						}
					}
				})
				return _txt;
			}
			function classId(){
				var _txt = '';
				$('.goods-add-class div.focus').each(function(){
					if($(this).find("a.focus").html()!=null){
						if($(this).index()==0){
							_txt += $(this).find("a.focus").attr('data-id');
						}else{
							_txt += ','+$(this).find("a.focus").attr('data-id');
						}
					}
				})
				return _txt;
			}
			$(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				 //格式化分类
				jsoncategory = <?php echo json_encode($category) ?> ;
			    nb_category(0, '.root'); 
			    $('.goods-class-choose span').text(dialog.data[1]);
			    $('.goods-class-choose span').attr('data-id',dialog.data[0]);
			    if(dialog.data[2] != undefined){
				    $.each(dialog.data[2].split(','),function(i,item){
				    	 $('#cat' + item).click();
				    })
				}
			    dialog.title('选择上级分类');
				dialog.reset();     // 重置对话框位置
				$('#okbtn').on('click', function () {
					var classname =  $('.goods-class-choose span');
					if(classname.attr('data-id') == ''){
						$('.text-mix').html('请选择该分类下的子分类')
				    	return false;
				    }else{
				    	dialog.close(classname);
				    }
					dialog.remove();	// 主动销毁对话框
					return false;
				});
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
			})
		</script>
<?php include template('footer','admin');?>
