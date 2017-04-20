<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">商品规格设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
		 <form action="" method="POST">
			<div class="form-box clearfix" id="form">
			<input type="hidden" name="id" value="<?php echo $spec['id']?>">
			<?php echo form::input('text', 'name', $spec['name'],'规格名称：','请填写常用的商品规格的名称；例如：颜色；尺寸等',array('validate' => 'required')); ?>
			<?php echo form::input('text', 'sort', $spec['sort'] ? $spec['sort'] : 100, '规格排序：','请填写自然数，商品规格列表将会根据排序进行由小到大排列显示'); ?>
			<?php echo form::input('enabled','status',isset($spec['status']) ? $spec['status'] : '1', '是否启用：','请设置规格是否启用',array('colspan' => 2)); ?>	
			</div>
			<div class="padding">
				<div class="table check-table paging-table border clearfix">
					<div class="tr padding-none border-none">
						<div class="th layout text-left">
							<span class="padding-left text-normal text-sub">添加属性</span>
						</div>
					</div>
					<div class="tr border-none">
						<div class="th bg-none bg-white check-option"><b>删除</b></div>
						<div class="th bg-none bg-white text-left padding-lr w85"><b>属性名称</b></div>
						<div class="th bg-none bg-white w15"><b>操作</b></div>
					</div>
					<?php foreach ($spec['spec_array'] as $value) {?>
					<div class="tr">
						<div class="td check-option">
							<input type="checkbox"/>
						</div>
						<div class="td padding-lr w85">
							<input class="input w25" type="text" name="value[]" value="<?php echo $value?>" />
						</div>
						<div class="td w15">
							<a href="javascript:;" onclick="delNewAttr(this)">删除</a>
						</div>
					</div>
					<?php }?>
					<?php if(empty($spec['spec_array'])){?>
					<div class="tr">
						<div class="td check-option">
							<input type="checkbox"/>
						</div>
						<div class="td padding-lr w85">
							<input class="input w25" type="text" name="value[]" value="<?php echo $value?>" />
						</div>
						<div class="td w15">
							<a href="javascript:;" onclick="delNewAttr(this)">删除</a>
						</div>
					</div>
					<?php }?>
					<div class="spec-add-button">
						<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个属性</a>
					</div>
				</div>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" name="dosubmit" value="确定" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</form>
		</div>
		<script>
			$(window).load(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				$('.table .tr:last-child').addClass("border-none");
				//增加新规格属性行
				$(".spec-add-button a").click(function(){
					var html = '<div class="tr">';
					html += '<div class="td check-option"><input type="checkbox"/></div>';
					html += '<div class="td padding-lr w85 text-left"><input class="input w25" type="text" name="value[]" value="" /></div>';
					html += '<div class="td w15"><a href="javascript:;" onclick="delNewAttr(this)">删除</a></div>';
					$(this).parent().before(html);
				});
			})
			function delNewAttr(self){
				if (!confirm("确认要删除么？")) {
                        return false;
                    }
				$(self).parent().parent('.tr').remove();
			}
		</script>
<?php include template('footer','admin');?>
