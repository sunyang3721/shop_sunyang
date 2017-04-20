<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">友情链接<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 友情链接可添加多个，部分模板不支持图片友链。</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('add')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('delete')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table paging-table check-table clearfix">
				<div class="tr">
					<span class="th check-option" data-resize="false">
						<span><input id="check-all" type="checkbox" /></span>
					</span>
					<?php foreach ($lists['th'] AS $th) {?>
						<span class="th" data-width="<?php echo $th['length']?>">
							<span class="td-con"><?php echo $th['title']?></span>
						</span>
					<?php }?>
					<span class="th" data-width="10">
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
			$(".table").resizableColumns();
			$(".paging-table").fixedPaging();
			//启用与关闭
			$(".table .ico_up_rack").bind('click',function(){
				if(!$(this).hasClass("cancel")){
					$(this).addClass("cancel");
					$(this).attr("title","点击打开");
				}else{
					$(this).removeClass("cancel");
					$(this).attr("title","点击取消");
				}
			});
			//ajax_edit编辑
			var ajax_edit = '<?php echo url('ajax_edit')?>';
			$("input[name=name]").live('blur',function(){
				var id=$(this).parents(".tr").find("input[type=checkbox]").val();
				var name = $(this).val();
				$.post(ajax_edit,{'id':id,'name':name},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$("input[name=url]").live('blur',function(){
				var id = $(this).parents(".tr").find("input[type=checkbox]").val();
				var url = $(this).val();
				$.post(ajax_edit,{'id':id,'url':url},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$("input[name=sort]").live('blur',function(){
				var id = $(this).parents(".tr").find("input[type=checkbox]").val();
				var sort = $(this).val();
				$.post(ajax_edit,{'id':id,'sort':sort},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$(".ico_up_rack").live('click',function(){
				var id = $(this).parents(".tr").find("input[type=checkbox]").val();
				var target = $(this).attr('class') == 'ico_up_rack' ? 1:0;
				$.post(ajax_edit,{'id':id,'target':target},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
		</script>
	<?php include template('footer','admin');?>
