<?php include template('header','admin');?>
	<script type="text/javascript" src="./statics/js/goods/goods_list.js"></script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">分类管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 添加商品时可选择商品分类，用户可根据分类查询商品列表</p>
					<p>- 设置商品分类关联类型，可以让所属分类以下的商品设置商品类型属性</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('add')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<!--<a href=""><i class="ico_out"></i>导出</a>
					<div class="spacer-gray"></div>-->
				</div>
			</div>
			<div class="table resize-table treetable border clearfix">
				<div class="tr border-none">
					<?php foreach ($lists['th'] AS $th) {?>
					<span class="th" data-width="<?php echo $th['length']?>">
						<span class="td-con"><?php echo $th['title']?></span>
					</span>
					<?php }?>
					<div class="th" data-width="10"><span class="td-con">操作</span></div>
				</div>
				<?php foreach ($lists['lists'] AS $list) {?>
				<div class="tr" data-tree-id="<?php echo $list['id']?>" data-tree-parent-id='0'>
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<span class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'level_sort') {?>
						<span class="td">
							<div class="tree-indenter <?php if($list['level'] != 1){?>no-tree-status<?php }?>">
							<a class="<?php if($list['level']==1){?>tree-ind-status close<?php }?>" data-level='1' data-id="<?php echo $list['id']?>" data-open="false" href="javascript:;"></a>
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis" data-id="<?php echo $list['id']?>" name="sort" type="text" value="<?php echo $value?>" />
							</div>
						</div>
						</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'level_name') {?>
					<span class="td">
						<div class="tree-edit-input">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis" type="text" data-id="<?php echo $list['id']?>" name="name" value="<?php echo $value?>" />
							</div>
							<a class="tree-add-button" data-id="<?php echo $list['id']?>" href="javascript:;"><em class="ico_add"></em>添加下级分类</a>
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
						<span class="td-con text-left"><?php echo $value;?></span>
					</span>
					<?php }?>
					<?php }?>
					<?php }?>
					<span class="td">
						<span class="td-con">
						<a href="<?php echo url('edit',array('id'=>$list['id']))?>">编辑</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('ajax_del', array('id' => $list['id'])); ?>">删除</a><?php echo $lists['option']?></span>
					</span>
				</div>
				<?php }?>
			</div>
		</div>
		<script>
			$(function(){
				$(".del-confirm").live("click",function(){
					if(!confirm("是否确认删除？")) return false;
				})
			})
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".table").treetable();
				$('.table .tr:last-child').addClass("border-none");
				//启用与关闭
				$(".ico_up_rack").live('click',function(){
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
				var ajax_name = "<?php echo url('ajax_name')?>";
				$('input[name=name]').live('blur',function() {
					var name = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_name(ajax_name,id,name);
				});
				var ajax_sort = "<?php echo url('ajax_sort')?>";
				$('input[name=sort]').live('blur',function() {
					var sort = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_sort(ajax_sort,id,sort);
				});
				var add_url = "<?php echo url('add')?>";
				$('.tree-add-button').live('click',function(){
					window.location.href=add_url + '&pid='+$(this).attr('data-id');
				});
			})
				//ajax请求数据
			var ajax_category = '<?php echo url('ajax_category')?>';
			$(".tree-ind-status").live('click',function(){
				var tr = $(this).parents('.tr');
				var _this = $(this);
				var flog = true;
				$(".tr").each(function(){//这里用于判断它的下级是否已加载过
					if($(this).attr("data-tree-parent-id")==tr.attr("data-tree-id")){
						flog = false;
					}
				});
				if(flog){
					var level,classname,html;
					if($(this).attr("data-level")==1){
						level = 2;
						className = "tree-one";
					}else if($(this).attr("data-level")==2){
						level = 3;
						className = "tree-two";
					}else if($(this).attr("data-level")==3){
						level = 4;
						className = "tree-three";
					}

					/*
					 * 点击加号触发AJAX事件，加载下级分类
					 * 通过传递当前点击的地区的ID通过ajax获取它的下一级地区然后循环输出
					 *
					 * */
					$.hdLoad.start();
					$.ajax({
						type: "GET",
						url: ajax_category,
						data: {id:_this.attr("data-id")},
						dataType: "json",
						success: function(data){
							$.hdLoad.end();
							var datas = data.result;//通过获取的的json遍历添加
							var del_url = "<?php echo url('ajax_del')?>";
							var edit_url = "<?php echo url('edit')?>";
							$.each(datas,function(i){
								var text = '';
								if(datas[i].status == 0){
									text ='<div class="td"><a class="ico_up_rack cancel" data-id="'+ datas[i].id +'" href="javascript:;" title="点击开启"></a></div>';
								}else{
									text ='<div class="td"><a class="ico_up_rack" data-id="'+ datas[i].id +'" href="javascript:;" title="点击关闭"></a></div>';
								}
								html = '<div class="tr" data-tree-id="'+tr.attr("data-tree-id")+'-'+(i+1)+'" data-tree-parent-id="'+tr.attr("data-tree-id")+'" style="visibility: visible;">'
									+'	<div class="td">'
									+'		<div class="tree-indenter'+($(this).attr("data-level") == 3||datas[i].row == 0?' no-tree-status':'')+'">'
									+			(datas[i].row == 0 ? '' :($(this).attr("data-level")!=3?'<a class="tree-ind-status close" data-level="'+level+'" data-id="'+datas[i].id+'" href="javascript:;"></a>':''))
									+'			<div class="double-click">'
									+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
									+'				<input class="input double-click-edit text-ellipsis" data-id="'+datas[i].id+'" name="sort" type="text" value="'+datas[i].sort+'" />'
									+'			</div>'
									+'		</div>'
									+'	</div>'
									+'	<div class="td">'
									+'		<div class="tree-edit-input '+className+'">'
									+'			<span class="tree-input-status'+(datas[i].row == 0 ?' no':' can')+'"></span>'
									+'			<div class="double-click">'
									+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
									+'				<input class="input double-click-edit text-ellipsis" name="name" data-id="'+datas[i].id+'" type="text" value="'+datas[i].name+'" />'
									+'			</div>'
									+'			<a class="tree-add-button" data-id="'+datas[i].id+'" href="javascript:;"><em class="ico_add"></em>添加下级分类</a>'
									+'		</div>'
									+'	</div>'
									+'<div class="td"><span class="td-con text-left">'+ datas[i].type_name +'</span></div>'
									+ text +'	<div class="td">'
									+'		<a href="'+ edit_url + '&id=' + datas[i].id +'&formhash='+ formhash +'">编辑</a>&nbsp;&nbsp;&nbsp;<a class="del-confirm" href="'+ del_url + '&id=' + datas[i].id +'">删除</a>'
									+'	</div>'
									+'</div>';
								tr.after(html);
							})
							$(window).resize();
						}
					});
				}
			});
		</script>
<?php include template('footer','admin');?>
