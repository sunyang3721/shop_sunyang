<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">权限管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('add')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('del')?>" data-ajax='id'><i class="ico_delete "></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table check-table border clearfix">
				<div class="tr">
					<span class="th check-option" data-resize="false">
						<span><input id="check-all" type="checkbox" /></span>
					</span>
					<?php foreach ($lists['th'] AS $th) {?>
					<span class="th" data-width="<?php echo $th['length']?>">
						<span class="td-con"><?php echo $th['title']?></span>
					</span>
					<?php }?>
					<span class="th" data-width="20">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach ($lists['lists'] AS $list) {?>
				<div class="tr">
					<div class="td check-option">
						<?php if($list['id']==1):?>
							-
						<?php else:?>
							<input type="checkbox" name="id" value="<?php echo $list['id']?>" />
						<?php endif;?>
					</div>
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<span class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'status') {?>
						<span class="td">
							<?php if($list['id']!=1):?>
							<?php if($value == 1):?>
								<a class="ico_up_rack" href="javascript:;" title="点击启用/禁用角色" data-id="<?php echo $list['id'];?>"></a>
								<?php else:?>
								<a class="ico_up_rack cancel" href="javascript:;" title="点击启用/禁用角色" data-id="<?php echo $list['id'];?>"></a>
							<?php endif;?>
							<?php else:?>
								--
							<?php endif;?>
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
						<?php if($list['id']==1):?>
						<span class="td-con">--</span>
						<?php else:?>
						<span class="td-con"><a href="<?php echo url('edit',array('id'=>$list['id']))?>">编辑</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确定删除？" href="<?php echo url('del',array('id'=>$list['id']))?>">删除</a></span>
						<?php endif;?>
					</span>
				</div>
				<?php }?>
			</div>
		</div>
		<script>
			var status = true;
			var post_status_url="<?php echo url('ajax_status')?>";
			$(".table").resizableColumns();
			$(window).load(function(){
				$(".table .ico_up_rack").bind('click',function(){
					if(ajax_status($(this).attr('data-id'))=='true'){
						if(!$(this).hasClass("cancel")){
							$(this).addClass("cancel");
						}else{
							$(this).removeClass("cancel");
						}
					}
				});
				//改变状态
				function ajax_status(id){
					$.post(post_status_url,{'id':id,'formhash':formhash},function(data){
						if(data.status == 1){
							status =  true;
						}else{
							status =  false;
						}
					},'json');

					return status;
				}
			})
		</script>
<?php include template('footer','admin');?>
