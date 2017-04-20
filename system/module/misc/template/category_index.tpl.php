<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">文章分类<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 新增文章时，可选择文章分类，文章分类将在前台文章列表页显示</p>
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
					<div class="th" data-width="10"><span class="td-con">排序</span></div>
					<div class="th" data-width="70"><span class="td-con">名称</span></div>
					<div class="th" data-width="10"><span class="td-con">启用</span></div>
					<div class="th" data-width="10"><span class="td-con">操作</span></div>
				</div>
				<?php foreach($category as $key => $value){?>
				<div class="tr" data-tree-id="<?php echo $value['id']?>" data-tree-parent-id="0"  >
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $value['id']?>" /></div>
					<div class="td">
						<div class="tree-indenter">
							<a class="tree-ind-status close" data-level='1' data-id="<?php echo $value['id']?>" data-open="false" href="javascript:;"></a>
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input name="sort" class="input double-click-edit text-ellipsis" type="text" value="<?php echo $value['sort']?>" />
							</div>
						</div>
					</div>
					<div class="td">
						<div class="tree-edit-input">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input name="name" class="input double-click-edit text-ellipsis" type="text" value="<?php echo $value['name']?>" />
							</div>
							<a class="tree-add-button" href="<?php echo url('misc/category/add',array('parent_id'=>$value['id']))?>"><em class="ico_add"></em>添加下级分类</a>
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
						<span class="td-con"><span class="td-con">
						 <a href="<?php echo url('edit',array('id'=>$value['id']))?>">编辑</a>&nbsp;&nbsp;&nbsp;
						 <a href="<?php echo url('delete',array('id[]'=>$value['id']))?>" data-confirm="是否确认删除？">删除</a></span>
						 </span>
					</div>
				</div>
				<?php }?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $pages;?>
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
				//启用与关闭
				$(".table .ico_up_rack").live('click',function(){
					if(!$(this).hasClass("cancel")){
						$(this).addClass("cancel");
						$(this).attr("title","点击开启");
					}else{
						$(this).removeClass("cancel");
						$(this).attr("title","点击关闭");
					}
				});

			})
			//ajax修改分类信息
			var ajax_edit = "<?php echo url('ajax_edit')?>";
			$("input[name=name]").live('blur',function(){
				var name = $(this).val();
				var id = $(this).parents('.tr').children().children().val();
				$.post(ajax_edit,{'id':id,'name':name},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			});
			$("input[name=sort]").live('blur',function(){
				var sort = $(this).val();
				var id = $(this).parents('.tr').children().children().val();
				$.post(ajax_edit,{'id':id,'sort':sort},function(data){
					console.log(data);
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			});
			//ajax请求数据
			var ajax_son_class = '<?php echo url('ajax_son_class')?>';
			var edit = '<?php echo url('edit')?>';
			var add = '<?php echo url('misc/category/add')?>'
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
					$.ajax({
						type: "GET",
						url: ajax_son_class,
						data: {id:_this.attr("data-id")},
						dataType: "json",
						success: function(data){
							var datas = data.result;//通过获取的的json遍历添加
							$.each(datas,function(i){
								var delete_url = "<?php echo url('delete')?>";
								html = '<div class="tr" data-tree-id="'+tr.attr("data-tree-id")+'-'+(i+1)+'" data-tree-parent-id="'+tr.attr("data-tree-id")+'" style="visibility: visible;">'
									+'	<div class="td check-option"><input type="checkbox" name="id" value="'+datas[i].id+'"/></div>'
									+'	<div class="td">'
									+'		<div class="tree-indenter'+($(this).attr("data-level")==3||datas[i].row==0?' no-tree-status':'')+'">'
									+			(datas[i].row==0?'':($(this).attr("data-level")!=3?'<a class="tree-ind-status close" data-level="'+level+'" data-id="'+datas[i].id+'" href="javascript:;"></a>':''))//
									//+			($(this).attr("data-level")!=3?'<a class="tree-ind-status close" data-level="'+level+'" data-id="'+datas[i].id+'" href="javascript:;"></a>':'')
									+'			'
									+'			<div class="double-click">'
									+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
									+'				<input name="sort" class="input double-click-edit text-ellipsis" type="text" value="'+datas[i].sort+'" />'
									+'			</div>'
									+'		</div>'
									+'	</div>'
									+'	<div class="td">'
									+'		<div class=" tree-edit-input '+className+'">'
									+	'			<span class="tree-input-status'+(datas[i].row == 0 ?' no':' can')+'"></span>'
									+'			<div class="double-click">'
									+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
									+'				<input name="name" class="input double-click-edit text-ellipsis" type="text" value="'+datas[i].name+'" />'
									+'			</div>'
									+'			<a class="tree-add-button" href="'+add+'&parent_id='+datas[i].id+'"><em class="ico_add"></em>添加下级分类</a>'
									+'		</div>'
									+'	</div>'
									+'<div class="td"><a class="'+(datas[i].display == 0 ?'ico_up_rack cancel':'ico_up_rack')+'" href="javascript:;" title="点击关闭"></a></div>'
									+'	<div class="td">'
									+'		<a href="'+edit+'&id='+datas[i].id+'">编辑</a>&nbsp;&nbsp;&nbsp;<a href="'+delete_url+'&id[]='+datas[i].id+'" data-confirm="是否确认删除？">删除</a>'
									+'	</div>'
									+'</div>';
								tr.after(html);
							})
							$(window).resize();
						}
					});
				}
			});
			$(".ico_up_rack").live('click',function(){
				var display = $(this).attr('class') == 'ico_up_rack' ? 0:1;
				var id = $(this).parents('.tr').children().children().val();
				$.post(ajax_edit,{'id':id,'display':display},function(data){
					if(data == 1){
						return true;
					}else{
						return false;
					}
				})
			})
		</script>
	<?php include template('footer','admin');?>
