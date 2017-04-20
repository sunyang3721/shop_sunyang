<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">首页管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;">首页幻灯片</a></li>
				<li><a href="<?php echo url('misc/navigation/index')?>">导航设置</a></li>
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
					<p>- 首页幻灯片可添加多张，图片尺寸根据不同的模板，均不一样，具体尺寸以当前使用模板首页幻灯片尺寸为准。</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('add')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确认删除？" href="" data-ajax="id"><i class="ico_delete"></i>删除</a>
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
							<input class="input double-click-edit text-ellipsis <?php if($key == 'sort'){?>text-center<?php }?>" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ident') {?>
						<span class="td ident">
							<span class="ident-show">
								<em class="ico_pic_show"></em>
								<div class="ident-pic-wrap">
									<img src="<?php echo $list['thumb'] ? $list['thumb'] : '../images/default_no_upload.png'?>" />
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
			var ajax_edit = '<?php echo url("ajax_edit")?>';
			$("input[name=title]").live('blur',function(){
				var title = $(this).val();
				var id=$(this).parents(".tr").find("input[name=id]").val();
				$.post(ajax_edit,{"id":id,"title":title},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$("input[name=url]").live('blur',function(){
				var url = $(this).val();
				var id=$(this).parents(".tr").find("input[name=id]").val();
				$.post(ajax_edit,{"id":id,"url":url},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$("input[name=sort]").live('blur',function(){
				var sort = $(this).val();
				var id=$(this).parents(".tr").find("input[name=id]").val();
				$.post(ajax_edit,{"id":id,"sort":sort},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$(".ico_up_rack").live('click',function(){
				var target = $(this).attr('class') == 'ico_up_rack' ? 1:0;
				var id=$(this).parents(".tr").find("input[name=id]").val();
				$.post(ajax_edit,{"id":id,"target":target},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
			$(".table-work .border a:last").bind('click',function(){
				var ids = Array();
				var delete_navigation = '<?php echo url('delete')?>';
				$("input[type=checkbox]").each(function(){
					if($(this).attr("checked") == 'checked'){
						ids.push($(this).val());
					}
				})
				$(this).attr('href',delete_navigation+'&id[]='+ids);
			});
		</script>
	<?php include template('footer','admin');?>
