<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">权限管理设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form action="" method="POST" data-reset="" data-validate='true' name="form">
			<div class="form-box clearfix">
				<div class="form-group"><span class="label">权限组名：</span><div class="box"><input type="text" tabindex="0" value="<?php echo $data['title']?>" name="title" class="input hd-input" data-validate="required;"></div><p class="desc">内容描述</p></div>
				<div class="form-group"><span class="label">权限描述：</span><div class="box"><textarea tabindex="0" placeholder="" name="description" class="textarea hd-input"><?php echo $data['description']?></textarea></div><p class="desc">内容描述信息</p></div>
			</div>
			<div class="author-wrapper padding">
				<div class="aut-all layout clearfix"><label>
					<div class="aut-all layout clearfix"><label><input id="check-aut-all" type="checkbox" name="" />设置权限</label></div>
				</label></div>
				<?php foreach($nodes as $k=>$v):?>
				<?php $checked=in_array($v['id'],$data['rules'])?'checked':''?>
				<dl class="aut-line clearfix">
					<dt><label><input class="aut-input aut-left-input" type="checkbox" name="rules[]" value="<?php echo $v['id']?>" <?php echo $checked?> /><?php echo $v['name']?></label></dt>
					<?php foreach($v['_child'] as $k1=>$v1):?>
					<?php $checked=in_array($v1['id'],$data['rules'])?'checked':''?>
					<dd>
						<label><input class="aut-input" type="checkbox" name="rules[]" value="<?php echo $v1['id']?>" <?php echo $checked?> /><?php echo $v1['name']?></label>
					</dd>
					<?php endforeach;?>
				</dl>
				<?php endforeach;?>
			</div>
			<div class="padding-top fl">
				<?php if(isset($data['id'])):?>
					<input type="hidden" name="id" id="id" value="<?php echo $data['id']?>" />
				<?php endif;?>
				<input type="submit" class="button bg-main" value="保存" />
				<a class="button margin-left bg-gray" href="<?php echo url('index')?>"/>返回</a>
			</div>
			</form>
		</div>
		<script>
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
			//总按钮
			$("#check-aut-all").click(function(){
				if($(this).is(":checked")){
					$(".aut-input").each(function(){
						$(this).attr("checked",true);
					});
				}else{
					$(".aut-input").each(function(){
						$(this).attr("checked",false);
					});
				}
			});
			//左边总按钮
			$(".aut-left-input").click(function(){
				if($(this).is(":checked")){
					$(this).parents(".aut-line").find(".aut-input").each(function(){
						$(this).attr("checked",true);
					});
				}else{
					$(this).parents(".aut-line").find(".aut-input").each(function(){
						$(this).attr("checked",false);
					});
				}
				selectAll();
			});
			//单个按钮
			$(".aut-input").click(function(){
				if($(this).is(":checked")){
					$("#check-aut-all").attr("checked",false);
				}
				var _parent = $(this).parent().parent().parent().find('dt input');
				var _current = $(this).parent().parent().parent().find('dd input');
				var _selectnum = $(this).parent().parent().parent().find('dd input:checked');
				if(_selectnum.length){
					_parent.attr('checked',true);
				}

				selectAll();
			});

			function selectAll(){
				var flog = true;
				$(".aut-input").each(function(){
					if(!$(this).is(":checked")){
						flog = false;
					}
				});
				$("#check-aut-all").attr("checked",flog);
			}
		</script>
<?php include template('footer','admin');?>
