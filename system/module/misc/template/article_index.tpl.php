<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">文章管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="tips margin-tb">
				<div class="tips-info border">
					<h6>温馨提示</h6>
					<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
				</div>
				<div class="tips-txt padding-small-top layout">
					<p>- 文章区别于站点帮助，可在文章列表页点击查看</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('add') ?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('delete')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table paging-table check-table treetable border clearfix">
				<div class="tr border-none">
					<div class="th check-option" data-resize="false">
						<input id="check-all" type="checkbox" />
					</div>
					<?php foreach ($lists['th'] AS $th) {?>
						<span class="th" data-width="<?php echo $th['length']?>">
							<span class="td-con"><?php echo $th['title']?></span>
						</span>
					<?php }?>
					<div class="th" data-width="10"><span class="td-con">操作</span></div>
				</div>
				<?php foreach ($lists['lists'] AS $list) {?>
				<div class="tr">
					<span class="td check-option"><input type="checkbox" name="id" value="<?php echo $list['id']?>" /></span>
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<span class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input class="input double-click-edit text-ellipsis" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ident') {?>
						<span class="td ident">
							<span class="ident-show">
								<em class="ico_pic_show"></em>
								<div class="ident-pic-wrap">
									<img src="<?php echo $list['logo'] ? $list['logo'] : '../images/default_no_upload.png'?>" />
								</div>
							</span>
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" type="text" value="<?php echo $value?>" />
							</div>
						</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
					<span class="td">
						<a class="ico_up_rack ico_up_recks <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rec') {?>
					<span class="td">
						<a class="ico_up_rack ico_up_rec <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'date') {?>
					<span class="td">
						<span class="td-con"><?php echo $value ?></span>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'hidden') {?>
						<input type="hidden" name="id" value="<?php echo $value?>" />
					<?php }else{?>
					<span class="td">
						<span class="td-con"><?php echo $value;?></span>
					</span>
					<?php }?>
					<?php }?>
					<?php }?>
					<span class="td">
						<span class="td-con">
						<a href="<?php echo url('edit',array('id'=>$list['id']))?>">编辑</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('delete', array('id[]' => $list['id'])); ?>">删除</a><?php echo $lists['option']?></span>
					</span>
				</div>
				<?php }?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $lists['pages'];?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".table").treetable();
				$(".paging-table").fixedPaging();
				$('.table .tr:last-child').addClass("border-none");

			})



			//ajax编辑文章信息
			var ajax_edit = "<?php echo url('ajax_edit')?>";
			$("input[name=title]").bind('blur',function(){
			   var title=$(this).val();
			   var id = $(this).data("id");
			   $.post(ajax_edit,{'id':id,'title':title},function(data){
				   if(data == 1){
					   return true;
				   }else{
					   return false;
				   }
			   });
			})
			$("input[name=sort]").bind('blur',function(){
				var sort = $(this).val();
				var id = $(this).data("id");
				$.post(ajax_edit,{'id':id,'sort':sort},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})



			$(".ico_up_rack").on('click',function(){
				var display=recommend=$(this).hasClass('cancel') ? 1:0;
				var id= $(this).data("id");
				var _this=$(this);
				if($(this).hasClass("ico_up_rec")){
					$.post(ajax_edit,{'id':id,'recommend':recommend},function(data){
					if(data.status == 1){
						if(!_this.hasClass("cancel")){
							_this.addClass("cancel");
							_this.attr("title","点击显示");
						}else{
							_this.removeClass("cancel");
							_this.attr("title","点击关闭");
						}
					}else{
						return false;
					}
				},'json')
				}
				else{
					$.post(ajax_edit,{'id':id,'display':display},function(data){
						if(data.status == 1){
							if(!_this.hasClass("cancel")){
								_this.addClass("cancel");
								_this.attr("title","点击显示");
							}else{
								_this.removeClass("cancel");
								_this.attr("title","点击关闭");
							}
						}else{
							return false;
						}
					},'json')

				}
			})

		</script>
	<?php include template('footer','admin');?>
