<?php include template('header','admin');?>
	<script type="text/javascript" src="./statics/js/goods/goods_list.js"></script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">组合营销<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;"></a></li>
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
					<p>- 可以自由搭配相关的商品进行组合推荐，提高客单价</p>
					<p>- 组合营销并不是组合促销，不支持组合套餐价格</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('edit')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('delete')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table paging-table check-table border clearfix">
				<div class="tr">
					<span class="th check-option" data-resize="false">
						<span><input id="check-all" type="checkbox" /></span>
					</span>
					<?php foreach ($lists['th'] AS $th) {?>
			<span class="th" data-width="<?php echo $th['length']?>">
				<span class="td-con"><?php echo $th['title']?></span>
			</span>
			<?php }?>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
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
							<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
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
						<a class="ico_up_rack <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'date') {?>
					<span class="td">
						<span class="td-con"><?php echo date('Y-m-d H:i' ,$value) ?></span>
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
			var ajax_name = "<?php echo url('ajax_name')?>";
			var ajax_subtitle = "<?php echo url('ajax_subtitle')?>";
			$(".table").resizableColumns();
			$(".table").fixedPaging();
			//启用与关闭
			$(".table .ico_up_rack").bind('click',function(){
				var url = "<?php echo url('ajax_status')?>";
				var id = $(this).attr('data-id');
				var row = $(this);
				$.post(url,{id:id},function(data){
						if(data.status==1){
							if(!row.hasClass("cancel")){
								row.addClass("cancel");
								row.attr("title","点击开启");
							}else{
								row.removeClass("cancel");
								row.attr("title","点击关闭");
							}
						}
					},'json');
				
				
			});

			$('input[name=title]').bind('blur',function() {
				var name = $(this).val();
				var id = $(this).attr('data-id');
				list_action.change_name(ajax_name,id,name);
			});
			$('input[name=subtitle]').bind('blur',function() {
				var name = $(this).val();
				var id = $(this).attr('data-id');
				list_action.change_name(ajax_subtitle,id,name);
			});
		</script>
<?php include template('footer','admin');?>
