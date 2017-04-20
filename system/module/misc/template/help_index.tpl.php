<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">站点帮助<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 站点帮助最多仅支持两级，仅有下级帮助才可进行详细内容的编辑。</p>
				</div>
			</div>
			<div class="hr-gray  margin-bottom"></div>
			<form action="<?php echo url('batch')?>" method="POST">
			<div class="table resize-table check-table treetable border clearfix">
				<div class="tr border-none">
					<div class="th check-option" data-resize="false">
						<input id="check-all" type="checkbox" />
					</div>
					<div class="th" data-width="10"><span class="td-con">排序</span></div>
					<div class="th" data-width="60"><span class="td-con">名称</span></div>
					<div class="th" data-width="10"><span class="td-con">显示</span></div>
					<div class="th" data-width="20"><span class="td-con">操作</span></div>
				</div>
				<?php foreach($help as $key => $value){?>
				<div class="tr" id="firstlevel">
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $value['id']?>" /></div>
					<div class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input name="edit[<?php echo $value['id']?>][sort]" class="input double-click-edit text-ellipsis text-center" type="text" value="<?php echo $value['sort']?>" />
						</div>
					</div>
					<div class="td td-con">
						<div class="tree-edit-input">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input name="edit[<?php echo $value['id']?>][title]" class="input double-click-edit text-ellipsis" type="text" value="<?php echo $value['title']?>" />
							</div>
						</div>
					</div>
					<div class="td">
					<?php if($value['display'] == 0){?>
						<a class="ico_up_rack cancel" href="javascript:;" title="点击关闭"></a>
					<?php }else{?>
					    <a class="ico_up_rack" href="javascript:;" title="点击关闭"></a>
					<?php }?>
					</div>
					<div class="td">
						<span class="td-con"><span class="td-con"><a href="<?php echo url('edit',array('id' =>$value['id']))?>">详情</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo url('delete',array('id[]'=>$value['id']))?>" data-confirm="是否确认删除？">删除</a></span></span>
					</div>
				</div>
				<?php foreach($value['son_help'] as $son_value){?>
				<div class="tr">				
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $son_value['id']?>" /></div>
					<div class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input name="edit[<?php echo $son_value['id']?>][sort]" class="input double-click-edit text-ellipsis text-center" type="text" value="<?php echo $son_value['sort']?>" />
						</div>
					</div>
					<div class="td td-con">
						<div class="tree-edit-input tree-one">
							<span class="tree-input-status can"></span>
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input name="edit[<?php echo $son_value['id']?>][title]" class="input double-click-edit text-ellipsis" type="text" value="<?php echo $son_value['title']?>" />
							</div>
						</div>
					</div>
					<div class="td">
					<?php if($son_value['display']==0){?>
						<a class="ico_up_rack cancel" href="javascript:;" title="点击关闭"></a>
					<?php }else{?>
					    <a class="ico_up_rack" href="javascript:;" title="点击关闭"></a>
					<?php }?>
					</div>
					<div class="td">
						<span class="td-con"><span class="td-con"><a href="<?php echo url('edit',array('id' =>$son_value['id']))?>">详情</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo url('delete',array('id[]'=>$son_value['id']))?>" data-confirm="是否确认删除？">删除</a></span></span>
					</div>
				</div>
				<?php }?>
				<div class="tr">
					<div class="td check-option"></div>
					<div class="td"></div>
					<div class="td td-con">
						<div class="tree-edit-input tree-one">
							<span class="tree-input-status no"></span>
							<a class="tree-add-button add-theme margin-small-left" href="javascript:;"><em class="ico_add"></em>添加新帮助主题</a>
						</div>
					</div>
					<div class="td"></div>
					<div class="td"></div>
				</div>
				<?php }?>
				<div class="tr">
					<div class="td check-option"></div>
					<div class="td"></div>
					<div class="td td-con">
						<div class="tree-edit-input">
							<a id="add-class" class="tree-add-button margin-small-left" href="javascript:;"><em class="ico_add"></em>添加新帮助分类</a>
						</div>
					</div>
					<div class="td"></div>
					<div class="td"></div>
				</div>
			</div>
			<div class="padding-tb">
				<input type="submit" id="submit" class="button bg-main" value="保存" />
			</div>
			</form>
		</div>
		<script>
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".table").treetable();
				$('.table .tr:last-child').addClass("border-none");
				//启用与关闭
				$(".table .ico_up_rack").bind('click',function(){
					if(!$(this).hasClass("cancel")){
						$(this).addClass("cancel");
						$(this).attr("title","点击显示");
					}else{
						$(this).removeClass("cancel");
						$(this).attr("title","点击关闭");
					}
				});
				var id = 0;
				$(".add-theme").live('click',function(){
					var parent_id = $(this).parents(".tr").prevAll("#firstlevel").find("input[name=id]").val();
					id++;
					var theme = '<div class="tr" id="add_help">'
								+'	<div class="td check-option"><input type="checkbox" name="id" value="1" /></div>'
								+'	<div class="td">'
								+'		<div class="double-click">'
								+'			<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
								+'			<input name=add_son['+parent_id+']['+id+'][sort]'+' class="input double-click-edit text-ellipsis text-center" type="text" value="100" />'
								+'		</div>'
								+'	</div>'
								+'	<div class="td td-con">'
								+'		<div class="tree-edit-input tree-one">'
								+'			<span class="tree-input-status can"></span>'
								+'			<div class="double-click">'
								+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
								+'				<input name=add_son['+parent_id+']['+id+'][title]'+' class="input double-click-edit text-ellipsis" type="text" value="" placeholder="双击编辑站点帮助" />'
								+'			</div>'
								+'		</div>'
								+'	</div>'
								+'	<div class="td"></div>'
								+'	<div class="td"></div>'
								+'</div>';
					$(this).parents('.tr').before(theme);
					$(window).resize();
					$(window).resize();
				});
				$("#add-class").click(function(){
					id++;
					var classHtml = '<div class="tr" id="add_class">'
									+'	<div class="td check-option"><input type="checkbox" name="id" value="1" /></div>'
									+'	<div class="td">'
									+'		<div class="double-click">'
									+'			<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
									+'			<input name=add_parent['+id+'][sort]'+' class="input double-click-edit text-ellipsis text-center" type="text" value="100" />'
									+'		</div>'
									+'	</div>'
									+'	<div class="td td-con">'
									+'		<div class="tree-edit-input">'
									+'			<div class="double-click">'
									+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
									+'				<input name=add_parent['+id+'][title]'+' class="input double-click-edit text-ellipsis" type="text" value="" placeholder="双击编辑站点帮助" />'
									+'			</div>'
									+'		</div>'
									+'	</div>'
									+'	<div class="td"></div>'
									+'	<div class="td"></div>'
									+'</div>';
					$(this).parents('.tr').before(classHtml);
					$(window).resize();
					$(window).resize();
				});
				
			})
			//ajax修改帮助信息
			var ajax_edit = '<?php echo url('ajax_edit')?>';
			$(".ico_up_rack").live('click',function(){
				var display = $(this).attr('class') == 'ico_up_rack' ? 1:0;
				var id = $(this).parents(".tr").find("input[name=id]").val();
				$.post(ajax_edit,{'id':id,'display':display},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				});
			});
		</script>
	<?php include template('footer','admin');?>
