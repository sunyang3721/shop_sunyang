<?php include template('header','admin');?>
<style type="text/css">
	.check-table.table .input{display: inline-block;}
	.check-table.table select{height: 26px;}
</style>
<div class="fixed-nav layout">
    <ul>
        <li class="first">插件管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">插件设置</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">菜单设置</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">变量设置</a></li>
        <?php if($_GET['id'] > 0):?>
        <a href="<?php echo url('export', array('id' => $_GET['id'])) ?>" class="more">导出插件>></a>
	    <?php endif ?>
    </ul>
    <div class="hr-gray"></div>
</div>
<form action="<?php echo url('app_develop');?>" method="POST" enctype="multipart/form-data">
<div class="content padding-big have-fixed-nav">
	<div class="tips margin-tb">
		<div class="tips-info border">
			<h6>温馨提示</h6>
			<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
		</div>
		<div class="tips-txt padding-small-top layout">
			<p>- 本功能仅供插件开发者使用，如果您只是安装或使用插件，切勿修改本设置；警告：不正确的插件设计或安装可能危及整个站点正常使用。</p>
		</div>
	</div>
	<div class="hr-gray"></div>
    <div class="content-tabs">
        <div class="form-box clearfix">
		<?php echo form::input('text', 'setting[name]', $rs['name'], '插件名称(name):', '此插件的名称，中英文均可，最多 40 个字节'); ?>
		<?php echo form::input('text', 'setting[server_version]', $rs['server_version'], '插件版本名称(server_version):', '此插件的服务版本名称,如：收费版、免费版等，中英文均可，最多 40 个字节'); ?>
		<?php echo form::input('text', 'setting[version]', $rs['version'], '插件版本号(version)：', '此插件的版本，中英文均可，最多 20 个字节。版本号高于旧版本号时，安装给用户时将会提示更新'); ?>
		<?php echo form::input('text', 'setting[identifier]', $rs['identifier'], '唯一标识符(identifier)：', '插件的唯一英文标识，不能够与现有插件标识重复。可使用字母/数字/下划线命名，不能包含其他符号或特殊字符，最大 40 个字节'); ?>
		<?php echo form::input('text', 'setting[author]', $rs['author'], '插件作者(author)：', '插件的开发者名称，可选填'); ?>
		<?php echo form::input('text', 'setting[copyright]', $rs['copyright'], '版权信息(copyright)：', '插件的版权信息，可选填'); ?>
		 <?php echo form::input('textarea', 'setting[description]', $rs['description'], '插件描述(description)：','插件的简单描述，最多 100 个字节，可选填'); ?>
        </div>
    </div>
    <div class="content-tabs hidden margin-top">
       <div class="table border check-table paging-table clearfix">
			  <div class="tr">
			    <div class="th check-option"><input id="check-all" type="checkbox" /></div>
			    <div class="th w10">显示顺序</div>
			    <div class="th w30">模块类型</div>
			    <div class="th w20">程序模块（必填）</div>
			    <div class="th w10">链接名称</div>
			    <div class="th w20">链接URL</div>
			    <div class="th w10">操作</div>
			  </div>
			  <?php if(!empty($rs['menu'])){?>
			  <?php foreach ($rs['menu'] as $key => $module): ?>
			  	<div class="tr">
				    <div class="td check-option"><input type="checkbox" /></div>
				    <div class="td w10"><input class="input w50" type="text" name="module[<?php echo $key ?>][displayorder]" value="<?php echo $module['displayorder']?>" /></div>
				    <div class="td w30">
						<select class="w30" name="module[<?php echo $key ?>][type]">
							<optgroup label="后台菜单">
		                    <?php foreach ($nodes as $node): ?>
		                    	<option value="<?php echo $node['id'] ?>"<?php if ($module['type'] == $node['id']): ?> selected<?php endif ?>><?php echo $node['name'] ?></option>
		                    <?php endforeach ?>
		                    </optgroup>
		                </select>
					</div>
				    <div class="td w20"><input class="input w75 fl" type="text" name="module[<?php echo $key ?>][name]" value="<?php echo $module['name']?>" />.inc.php</div>
				    <div class="td w10"><input class="input w60" type="text" name="module[<?php echo $key ?>][menu]" value="<?php echo $module['menu']?>" /></div>
				    <div class="td w20"><input class="input w60" type="text" name="module[<?php echo $key ?>][url]" value="<?php echo $module['url']?>" /></div>
				    <div class="td w10"><a href="javascript:;" class="del_btn" data-confirm="您确认要删除？">删除</a></div>
			  	</div>
			  <?php endforeach ?>
			  <?php }else{?>
			  <div class="tr">
				    <div class="td check-option"><input type="checkbox" /></div>
				    <div class="td w10"><input class="input w50" type="text" name="module[0][displayorder]" value="100" /></div>
				    <div class="td w30">
						<select class="w30" name="module[0][type]">
							<optgroup label="后台菜单">
		                    <?php foreach ($nodes as $node): ?>
		                        <option value="<?php echo $node['id'] ?>"><?php echo $node['name'] ?></option>
		                    <?php endforeach ?>
		                    </optgroup>
		                </select>
					</div>
				    <div class="td w20"><input class="input w75 fl" type="text" name="module[0][name]" /><span>.inc.php</span></div>
				    <div class="td w10"><input class="input w60" type="text" name="module[0][menu]" /></div>
				    <div class="td w20"><input class="input w60" type="text" name="module[0][url]"/></div>
				    <div class="td w10"><a href="javascript:;" class="del_btn" data-confirm="您确认要删除？">删除</a></div>
			  	</div>
			  <?php }?>

		    <div class="td spec-add-button add-btn">
				<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个属性</a>
			</div>
		</div>
    </div>
    <div class="content-tabs hidden margin-top">
       <div class="table border check-table paging-table clearfix">
			  <div class="tr">
			    <div class="th check-option"><input id="check-all" type="checkbox" /></div>
			    <div class="th w15">显示顺序</div>
			    <div class="th w15">配置名称（必填）</div>
			    <div class="th w20">配置变量名（必填）</div>
			    <div class="th w30">配置类型（必填）</div>
			    <div class="th w10">变量默认值</div>
			    <div class="th w10">操作</div>
			  </div>
			<?php if($vars){?>
			<?php foreach ($vars as $var): ?>
			  <div class="tr">
			    <div class="td check-option"><input type="checkbox" /></div>
			    <input type="hidden" name="vars[edit_pluginvar][<?php echo $var['id']?>][id]" value="<?php echo $var['id']?>">
			    <div class="td w15"><input class="input w50 displayorders" type="text" name="vars[edit_pluginvar][<?php echo $var['id']?>][displayorder]" value="<?php echo $var['displayorder']?$var['displayorder']:200 ?>" /></div>
			    <div class="td w15"><input class="input w60 title" type="text" name="vars[edit_pluginvar][<?php echo $var['id']?>][title]" value="<?php echo $var['title'] ?>" />
				</div>
			    <div class="td w20"><input class="input w60 variable" type="text" name="vars[edit_pluginvar][<?php echo $var['id']?>][variable]" value="<?php echo $var['variable'] ?>" /></div>
			    <div class="td w30">
					<select class="w30 type" name="vars[edit_pluginvar][<?php echo $var['id']?>][type]" data-val="<?php echo $var['type'] ?>">
				    	<option value="text">字串(text)</option>
                        <option value="textarea">文本(textarea)</option>
                        <option value="enabled">开启/关闭(enabled)</option>
                        <option value="radio">单选(radio)</option>
                        <option value="checkbox">复选(checkbox)</option>
                        <option value="select">下拉框(select)</option>
                        <option value="calendar">日期/时间(calendar)</option>
                        <option value="color">颜色(color)</option>
                        <option value="editor">编辑器(editor)</option>
                        <option value="file">文件选择(file)</option>
			    	</select>
				</div>
				 <div class="td w10"><input class="input w60 value" type="text" name="vars[edit_pluginvar][<?php echo $var['id']?>][value]" value="<?php echo $var['value'] ?>" /></div>
				 <input type="hidden" class="description" name="vars[edit_pluginvar][<?php echo $var['id']?>][description]" value="<?php echo $var['description']?>">
				 <input type="hidden" class="extra" name="vars[edit_pluginvar][<?php echo $var['id']?>][extra]" value="<?php echo $var['extra']?>">
			    <div class="td w10"><a href="javascript:;" data-url="<?php echo url('appvar')?>" class="detail-btn">详情&nbsp;&nbsp;&nbsp;</a><a href="javascript:;" data-id="<?php echo $var['id']?>" class="del_btn" data-confirm="您确认要删除？">删除</a></div>
			  </div>
			<?php endforeach ?>
			<?php }else{?>
				<div class="tr">
			    <div class="td check-option"><input type="checkbox" /></div>
			    <div class="td w15"><input class="input w50 displayorder" type="text" name="vars[new_pluginvar][0][displayorder]" value="100" /></div>
			    <div class="td w15"><input class="input w60 title" type="text" name="vars[new_pluginvar][0][title]"/>
				</div>
			    <div class="td w20"><input class="input w60 variable" type="text" name="vars[new_pluginvar][0][variable]"/></div>
			    <div class="td w30">
			    	<select class="w30 type" name="vars[new_pluginvar][0][type]">
				    	<option value="text">字串(text)</option>
                        <option value="textarea">文本(textarea)</option>
                        <option value="enabled">开启/关闭(enabled)</option>
                        <option value="radio">单选(radio)</option>
                        <option value="checkbox">复选(checkbox)</option>
                        <option value="select">下拉框(select)</option>
                        <option value="calendar">日期/时间(calendar)</option>
                        <option value="color">颜色(color)</option>
                        <option value="editor">编辑器(editor)</option>
                        <option value="file">文件选择(file)</option>
					</select>
				</div>
				<div class="td w10"><input class="input w60 value" type="text" name="vars[new_pluginvar][0][value]" /></div>
				<input type="hidden" class="description" name="vars[new_pluginvar][0][description]">
				<input type="hidden" class="extra" name="vars[new_pluginvar][0][extra]">
			    <div class="td w10"><a href="javascript:;" data-url="<?php echo url('appvar')?>" class="detail-btn">详情&nbsp;&nbsp;&nbsp;</a><a href="javascript:;" class="del_btn" data-confirm="您确认要删除？">删除</a></div>
			  </div>
			<?php }?>
		    <div class="td spec-add-button variable-btn">
				<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个属性</a>
			</div>
		</div>
    </div>
    <div class="padding">
    	<input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
        <input type="submit" class="button bg-main" value="提交" />
    </div>
</div>
</form>
<?php include template('footer','admin');?>

<script>
	$(function(){
		$('.type').each(function(i, item) {
 			$(item).find("option[value="+$(item).attr('data-val')+"]").attr('selected',true);
		});
		$('.del_btn').live('click',function(){
			if($(this).attr('data-id') != undefined){
				$('[name=id]').after('<input type="hidden" name="vars[del_pluginvar][]" value="'+$(this).attr('data-id')+'">');
			}
			$(this).parents('.tr').remove();
		})
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		var j = <?php echo max(array_keys($rs['menu']))+1;?>;
		$(".spec-add-button.add-btn a").click(function(){
			var nodes = <?php echo json_encode($nodes)?>;
			var option = '';
			$.each(nodes,function(i,item){
				option += '<option value="'+item['id']+'">'+ item['name'] +'</option>';
			});
			var html = '<div class="tr">';
			html += '<div class="td check-option"><input type="checkbox" /></div>';
			html += '<div class="td w10"><input class="input w50" type="text" name="module['+j+'][displayorder]" value="100" /></div>';
			html += '<div class="td w30">';
			html += '<select class="w30" name="module['+j+'][type]">';
			html += '<optgroup label="后台菜单">';
			html += option;
			html += '</optgroup>';
			html +='</select></div>';
			html += '<div class="td w20"><input class="input w75 fl" type="text" name="module['+j+'][name]"/><span>.inc.php</span></div>';
			html += '<div class="td w10"><input class="input w60" type="text" name="module['+j+'][menu]"/></div>';
			html += '<div class="td w20"><input class="input w60" type="text" name="module['+j+'][url]"/></div>';
			html += '<div class="td w10"><a href="javascript:;" class="del_btn" data-confirm="您确认要删除？">删除</a></div>';
			html += '</div>';
			$(this).parent().before(html);
			j++;
		});
		var i = <?php echo max(array_keys($vars))+1;?>;
		var url = "<?php echo url('appvar')?>";
		$(".spec-add-button.variable-btn a").click(function(){
			var html = '<div class="tr">';
			html += '<div class="td check-option"><input type="checkbox" /></div>';
			html += '<div class="td w15 displayorder"><input class="input w50" type="text" name="vars[new_pluginvar]['+i+'][displayorder]" value="100"/></div>';
			html += '<div class="td w15">';
			html += '<input class="input w60 title" type="text" name="vars[new_pluginvar]['+i+'][title]"/>'
			html +=	'</div>';
			html +='<div class="td w20"><input class="input w60 variable" type="text" name="vars[new_pluginvar]['+i+'][variable]"/></div>';
			html +='<div class="td w30">';
			html +='<select class="w30 type" name="vars[new_pluginvar]['+i+'][type]">';
		    html +='            <option value="text">字串(text)</option>'
            html +='            <option value="textarea">文本(textarea)</option>'
            html +='            <option value="enabled">开启/关闭(enabled)</option>'
            html +='            <option value="radio">单选(radio)</option>'
            html +='            <option value="checkbox">复选(checkbox)</option>'
            html +='            <option value="select">下拉框(select)</option>'
            html +='            <option value="calendar">日期/时间(calendar)</option>'
            html +='            <option value="color">颜色(color)</option>'
            html +='            <option value="editor">编辑器(editor)</option>'
            html +='            <option value="file">文件选择(file)</option>'
			html +=    	'</select>';
			html +=	'</div>';
			html += '<div class="td w10"><input class="input w60 value" type="text" name="vars[new_pluginvar]['+i+'][value]"/></div>';
			html += '<input type="hidden" class="description" name="vars[new_pluginvar]['+i+'][description]">';
			html += '<input type="hidden" class="extra" name="vars[new_pluginvar]['+i+'][extra]">'
			html +=    '<div class="td w10"><a href="javascript:;" data-url="'+ url +'" class="detail-btn">详情&nbsp;&nbsp;&nbsp;</a><a href="javascript:;" class="del_btn" data-confirm="您确认要删除？">删除</a></div>';
			html += '</div>';
			$(this).parent().before(html);
			i++;
		});
		$(".form-buttonedit-popup").each(function(index, el) {
			$(this).live('click',function(){
				$(this).addClass('buttonedit-popup-hover');
				$(this).parents(".form-select-edit").find(".listbox-items").show();
			});

		});
		$("a.detail-btn").live('click',function() {
			var detail = this;
			var data = {};
			data.title = $(this).parents('.tr').find('.title').val();
			data.variable = $(this).parents('.tr').find('.variable').val();
			data.type = $(this).parents('.tr').find('.type').val();
			data.value = $(this).parents('.tr').find('.value').val();
			data.description = $(this).parents('.tr').find('.description').val();
			data.extra = $(this).parents('.tr').find('.extra').val();
			top.dialog({
				url: $(this).attr("data-url"),
				title: '修改会员信息',
				width: 800,
				height:650,
				data : data,
				onclose:function() {
					if(this.returnValue) {
						$(detail).parents('.tr').find('.title').val(this.returnValue.title);
						$(detail).parents('.tr').find('.variable').val(this.returnValue.variable);
						$(detail).parents('.tr').find('.type option[value='+this.returnValue.type+']').attr('selected',true);
						$(detail).parents('.tr').find('.value').val(this.returnValue.value);
						$(detail).parents('.tr').find('.description').val(this.returnValue.description);
						$(detail).parents('.tr').find('.extra').val(this.returnValue.extra);
					}
				}
			})
			.showModal();
		});
		return false;


	})
</script>
