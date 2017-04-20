<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">地区管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('add') ?>" data-iframe="true" data-iframe-width="320" data-iframe-title="添加地区"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a href="<?php echo url('delete') ?>" id="delete-area"><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table-wrap">
				<div class="table check-table resize-table treetable border clearfix">
					<div class="tr">
						<div class="th check-option" data-resize="false">
							<input id="check-all" type="checkbox" />
						</div>
						<?php foreach ($lists['th'] AS $th) {?>
						<span class="th" data-width="<?php echo $th['length']?>">
							<span class="td-con"><?php echo $th['title']?></span>
						</span>
						<?php }?>
						<div class="th" data-width="10">
							<div class="td-con">操作</div>
						</div>
					</div>
					<?php foreach ($lists['lists'] AS $list) {?>
					<div class="tr" data-tree-id="<?php echo $list['id'] ?>" data-tree-parent-id="<?php echo $list['parent_id'] ?>" data-id="<?php echo $list['id'] ?>">
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $list['id'] ?>" /></div>
						<div class="td">
							<div class="tree-indenter">
								<a class="tree-ind-status close" data-level="0" data-id="<?php echo $list['id'] ?>" href="javascript:;"></a>
								<div class="double-click">
									<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
									<input class="input double-click-edit text-ellipsis" type="text" value="<?php echo $list['sort'] ?>" />
								</div>
							</div>
						</div>
					<?php }elseif ($lists['th'][$key]['style'] == 'data') {?>
						<div class="td">
							<div class="tree-edit-input">
								<div class="double-click">
									<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
									<input class="input double-click-edit text-ellipsis" type="text" value="<?php echo $list['name'] ?>" />
								</div>
								<a class="tree-add-button" href="<?php echo url('add', array('parent_id' => $list['id'])) ?>" data-iframe="true" data-iframe-width="320"><em class="ico_add"></em>添加子地区</a>
							</div>
						</div>
					<?php }elseif ($lists['th'][$key]['style'] == 'hidden') {?>
						<input type="hidden" name="id" value="<?php echo $value?>" />
					<?php }else{?>
					<span class="td">
						<span class="td-con"><?php echo $value;?></span>
					</span>
					<?php }?>
					<?php }?>
					<?php }?>
					<div class="td">
							<a href="<?php echo url('edit', array("id" => $list['id']));?>" data-iframe="true" data-iframe-width="320">编辑</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo url('delete', array("id[]" => $list['id']));?>"  class="delete">删除</a>
						</div>
				</div>
				<?php }?>
				</div>
			</div>
		</div>
<script>
$(window).load(function(){
	$(".table").resizableColumns();
	$(".table").treetable();
	//启用与关闭
	$(".table .ico_up_rack").bind('click',function(){
		if(!$(this).hasClass("cancel")){
			$(this).addClass("cancel");
			$(this).attr("title","点击开启");
		}else{
			$(this).removeClass("cancel");
			$(this).attr("title","点击关闭");
		}
	});

	$(".tree-ind-status").live('click',function(){
		var tr = $(this).parents('.tr');
		var _this = $(this);
		var flog = $(this).parents(".tr").find("input[name=id]").is(":checked");
		if(_this.attr("data-id")==undefined) return false;
		if($(this).data("loaded")==undefined){
			$.hdLoad.start();
			var classname,html;
			var dlevel = $(this).attr("data-level");
			if(dlevel==0){
				className = "tree-one";
			}else if(dlevel==1){
				className = "tree-two";
			}else if(dlevel==2){
				className = "tree-three";
			}else if(dlevel==3){
				className = "tree-four";
			}
			$.ajax({
	            type: "GET",
	            url: "<?php echo url('ajax_district');?>",
	            data: {id : _this.attr("data-id")},
	            dataType: "json",
	            success: function(data){
	            	$.hdLoad.end();
	            	_this.attr("data-loaded","true");
	            	html = '';
	            	$.each(data, function(i,obj) {
	            		html += '<div class="tr'+(flog?' selected':'')+'" data-tree-id="'+tr.attr("data-tree-id")+'-'+(i+1)+'" data-tree-parent-id="'+tr.attr("data-tree-id")+'" data-id="'+obj.id+'" style="visibility: visible;">'
							+'	<div class="td check-option"><input type="checkbox" name="id" value="'+obj.id+'"'+(flog?'checked="checked"':'')+' /></div>'
							+'	<div class="td">'
							+'		<div class="tree-indenter'+(obj._child==0?' no-tree-status':'')+'">'
							+(obj._child!=0?'<a class="tree-ind-status close" data-level="'+obj.level+'" data-id="'+obj.id+'" href="javascript:;"></a>':'')
							+'			'
							+'			<div class="double-click">'
							+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
							+'				<input class="input double-click-edit text-ellipsis" type="text" value="'+(this.sort==''||this.sort==undefined?'255':this.sort)+'" />'
							+'			</div>'
							+'		</div>'
							+'	</div>'
							+'	<div class="td">'
							+'		<div class="tree-edit-input '+className+'">'
							+'			<span class="tree-input-status'+(obj._child==0?' no':' can')+'"></span>'
							+'			<div class="double-click">'
							+'				<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>'
							+'				<input class="input double-click-edit text-ellipsis" type="text" value="'+obj.name+'" />'
							+'			</div>'
							+(obj.level==4?'':'<a class="tree-add-button" href="?m=admin&c=district&a=add&parent_id='+ obj.id +'" data-iframe="true" data-iframe-width="320"><em class="ico_add"></em>添加子地区</a>')
							+'		</div>'
							+'	</div>'
							+'	<div class="td">'
							+'		<a href="?m=admin&c=district&a=edit&id='+ obj.id +'" data-iframe="true" data-iframe-width="320">编辑</a>&nbsp;&nbsp;&nbsp;<a href="?m=admin&c=district&a=delete&ids[]='+ obj.id +'" class="delete">删除</a>'
							+'	</div>'
							+'</div>';

	            	});
	            	_this.parents('.tr').after(html);
					$(window).resize();
	            }
	        });
		}
	});
	
	$(".tree-indenter .input").live('change',function(){
		if(isNaN($(this).val())){
			message('排序修改失败，请填写数字！');
			return false;
		}
		var data = {id:$(this).parents(".tr").data("id"),sort:$(this).val()};
		$.post("index.php?m=admin&c=district&a=ajax_sort",data,function(ret){
			message(ret.message);
		},'json');
	});
	
	$(".tree-edit-input .input").live('change',function(){
		var data = {id:$(this).parents(".tr").data("id"),name:$(this).val()};
		$.post("index.php?m=admin&c=district&a=ajax_name",data,function(ret){
			message(ret.message);
		},'json');
	});
	
	$(".tr .delete").live('click',function(){
		var data = {'ids[]':[$(this).parents(".tr").data("id")]};
		var d = dialog({
		    title: '提示',
		    content: '<p class="layout text-center padding-big bg-white">确认删除所选地区？删除选择地区，其所有下属地区也将会同步删除且不可恢复。</p>',
		    width: "300",
		    okValue: '确定',
		    ok: function () {
		        $.post("index.php?m=admin&c=district&a=delete",data,function(ret){
					message(ret.message);
				},'json');
		    },
		    cancelValue: '取消',
		    cancel: function () {}
		});
		d.show();
		return false;
	});
	
	$("#delete-area").click(function(){
		
		var data = new Array();
		$(".td input:checked").each(function(){
			data.push($(this).val());
		});
		var d = dialog({
		    title: '提示',
		    content: '<p class="layout text-center padding-big bg-white">确认删除所选地区？删除选择地区，其所有下属地区也将会同步删除且不可恢复。</p>',
		    width: "300",
		    okValue: '确定',
		    ok: function () {
		        $.post("index.php?m=admin&c=district&a=delete",{'ids[]':data},function(ret){
					message(ret.message);
				},'json');
		    },
		    cancelValue: '取消',
		    cancel: function () {}
		});
		d.show();
		return false;
		
	});	
	
})
</script>
<?php include template('footer','admin');?>