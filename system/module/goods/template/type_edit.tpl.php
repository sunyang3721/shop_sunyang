<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">商品类型设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		
		<div class="content padding-big have-fixed-nav">
		<form action="" method="POST" data-reset=false>
			<div class="padding-lr clearfix">
				<input type="hidden" value="<?php echo $type['id']?>" name="id">
				<div class="form-group">
					<span class="label">属性名称：</span>
					<div class="box">
						<input class="input hd-input" type="text" tabindex="0" value="<?php echo $type['name']?>" name="name">
					</div>
					<p class="desc">请填写常用的商品类型的名称；例如：服装；图书等</p>
				</div>
				<div class="form-group">
					<span class="label">属性排序：</span>
					<div class="box">
						<input class="input hd-input" type="text" tabindex="0" value="<?php echo $type['sort'] ? $type['sort'] : 100?>" name="sort">
					</div>
					<p class="desc">请填写自然数，商品类型列表将会根据排序进行由小到大排列显示</p>
				</div>
				<div class="form-group">
					<span class="label">是否启用：</span>
					<div class="box">
						<label class="select-wrap">
							<input class="select-btn" type="radio" <?php if($type['status'] == 0){?>checked=""<?php }?> value="0" name="status">关闭
						</label>
						<label class="select-wrap">
							<input class="select-btn" type="radio" <?php if($type['status'] == 1){?>checked=""<?php }?> value="1" name="status">开启
						</label>
					<br>
					</div>
					<p class="desc">请设置属性是否启用</p>
				</div>
			</div>
			<div class="padding-lr margin-top clearfix">
				<div class="border attr-choose-spec">
					<div class="table-add-top">
						<div class="th layout">
							<span class="text-sub text-left">关联规格</span>
						</div>
					</div>
					<div class="padding-top padding-left padding-right attr-choose-wrap clearfix">
					<?php foreach ($specs AS $spec):?>
						<a href="javascript:;" data-id="<?php echo $spec['id']?>" <?php if($type_spec[$spec['id']] == $spec['id']){?>class="current"<?php }?>><?php echo $spec['name']?></a>
						<?php if(!empty($type_spec[$spec['id']]) && $type_spec[$spec['id']] == $spec['id']){?>
						<input type="hidden" id="spec<?php echo $spec['id']?>" name="spec_id[]" value="<?php echo $spec['id']?>">
						<?php }?>
					<?php endforeach?>
					</div>
				</div>
			</div>
			<div class="padding">
				<div class="table resize-table paging-table check-table border clearfix">
					<div class="table-add-top">
						<div class="th layout">
							<span class="text-sub text-left">编辑属性</span>
						</div>
					</div>
					<div class="tr border-none">
						<div class="th check-option" data-resize='flase'>删除</div>
						<div class="th" data-width="5">
							<span class="td-con text-left">排序</span>
						</div>
						<div class="th" data-width="25">
							<span class="td-con text-left">属性名称</span>
						</div>
						<div class="th" data-width="15">
							<span class="td-con text-left">属性类型</span>
						</div>
						<div class="th" data-width="35">
							<span class="td-con text-left">属性值</span>
						</div>
						<div class="th" data-width="10">
							<span class="td-con">是否筛选</span>
						</div>
						<div class="th" data-width="10">
							<span class="td-con">操作</span>
						</div>
					</div>
					<?php foreach ($pop as $k => $v) {?>
					<div class="tr">
						<div class="td check-option">
							<input type="checkbox" />
							<input type="hidden"  name='attr_ids[]' value="<?php echo $v['id']?>">
						</div>
						<div class="td w5">
							<input class="input" type="text" name="attr_sort[]" value="<?php echo $v['sort'] ? $v['sort'] : '100'?>" />
						</div>
						<div class="td w25">
							<div class="td-con">
								<input class="input fl" style="width: 80px" type="text" name="attr_name[]" value="<?php echo $v['name']?>" />
								<?php if($v['type'] != 'input'){?>
								<span id="popinput<?php echo $v['id']?>" class="fl margin-left hidden">输入框无需编辑</span>
								<a class="fl margin-left" id="popedit<?php echo $v['id']?>" href="javascript:setAttr(<?php echo $v['id']?>);">编辑属性值</a>
								<?php }else{?>
								<span id="attrinput<?php echo $v['id']?>" class="fl margin-left">输入框无需编辑</span>
								<a class="fl margin-left hidden" id="attredit<?php echo $v['id']?>" href="javascript:setAttr(<?php echo $v['id']?>);">编辑属性值</a>
								<?php }?>
							</div>
						</div>
						<div class="td w15">
							<div class="td-con">
								<select class="layout fl choose" data-id="<?php echo $v['id']?>" name="type[]" style="height: 26px; margin-top: 7px;">
									<option value="radio" <?php if($v['type'] == 'radio'){?>selected='selected'<?php }?>>单选框</option>
									<option value="checkbox" <?php if($v['type'] == 'checkbox'){?>selected='selected'<?php }?>>复选框</option>
									<option value="input" <?php if($v['type'] == 'input'){?>selected='selected'<?php }?>>输入框</option>
								</select>
							</div>
						</div>
						<div class="td w35">
							<div class="td-con text-left" id="attr<?php echo $v['id']?>"><?php echo $v['value']?></div>
							<input type="hidden" id="attrhidden<?php echo $v['id']?>" name="attr_val[]" value="<?php echo $v['value']?>">
						</div>
						<div class="td w10">
						<?php if($v['search'] == 1){?>
							<span class="td-con">
								<a class="ico_up_rack" href="javascript:;" title="点击取消"></a>
								<input type="hidden" class="search" name="search[]" value="1">
							</span>
						<?php }else{?>
							<span class="td-con">
								<a class="ico_up_rack cancel" href="javascript:;" title="点击选择"></a>
								<input type="hidden" class="search" name="search[]" value="0">
							</span>
						<?php }?>
						</div>
						<div class="td w10">
							<a href="javascript:" onclick="delAttr(this,<?php echo $v['id']?>)">删除</a>
						</div>
					</div>
					<?php }?>
					<?php if(empty($pop)){?>
					<div class="tr" style="visibility: visible;">
						<div class="td check-option">
							<input type="checkbox"/>
						</div>
						<div class="td w5">
								<input class="input" type="text" name="attr_sort[]" value="100" />
						</div>
						<div class="td w25">
							<div class="td-con">
								<input class="input fl" style="width: 80px" type="text" name="attr_name[]" value="" />
								<a class="fl margin-left" id="popedit1" href="javascript:setAttr(1);">编辑属性值</a>
								<span id="popinput1" class="fl margin-left hidden">输入框无需编辑</span>
							</div>
						</div>
						<div class="td w15">
							<div class="td-con">
								<select class="layout fl" data-id="1" name="type[]" style="height: 26px; margin-top: 7px;">
								<option value="radio">单选框</option>
								<option value="checkbox">复选框</option>
								<option value="input">输入框</option>
								</select>
							</div>
						</div>
						<div class="td w35">
						<div class="td-con text-left" id="attr1"></div>
							<input type="hidden" id="attrhidden1" name="attr_val[]" value=""/>
						</div>
						<div class="td w10">
							<span class="td-con"><a class="ico_up_rack" href="javascript:;" title="点击取消"></a><input type="hidden" class="search" name="search[]" value="1"></span>
						</div>
						<div class="td w10">
							<a href="javascript:" onclick="delNewAttr(this)">删除</a>
						</div>
					</div>
					<?php }?>
					<div class="spec-add-button">
						<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个属性</a>
					</div>
				</div>
			</div>
			<div class="padding submit-data">
				<input type="submit" class="button bg-main" name="dosubmit" value="确定" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</form>
		</div>
		<script>
			$(window).load(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				$('.resize-table').resizableColumns();
				//增加新规格属性行
				$('.attr-choose-wrap a').click(function(){
					var dataid = $(this).attr('data-id');
					if($(this).hasClass('current')){
						$(this).removeClass('current');
						$('#spec'+dataid).remove();
					}else{
						$(this).addClass('current');
						var text = '<input type="hidden" id="spec'+ dataid +'" name="spec_id[]" value="'+ dataid +'">';
						$(this).parent().append(text);
					}
				});
				var newid = parseInt($('input[name="attr_ids[]"]:last').val())+1;
				var i = newid > 0 ? newid : 2;
				$(".spec-add-button a").click(function(){
					var html = '<div class="tr" style="visibility: visible;">'
								+'	<div class="td r0 check-option">'
								+'		<input type="checkbox"/>'
								+'	</div>'
								+'	<div class="td r1 w5">'
								+'		<input class="input" type="text" name="attr_sort[]" value="100" />'
								+'	</div>'
								+'	<div class="td r2 w25">'
								+'		<div class="td-con">'
								+'			<input class="input fl" style="width: 80px" type="text" name="attr_name[]" value="" />'
								+'			<a class="fl margin-left" id="popedit'+ i +'" href="javascript:setAttr('+ i +');">编辑属性值</a>'
								+'			<span id="popinput'+ i +'" class="fl margin-left hidden">输入框无需编辑</span>'
								+'		</div>'
								+'	</div>'
								+'	<div class="td r3 w15">'
								+'		<div class="td-con">'
								+'			<select class="layout fl" data-id="'+ i +'" name="type[]" style="height: 26px; margin-top: 7px;">'
								+'				<option value="radio">单选框</option>'
								+'				<option value="checkbox">复选框</option>'
								+'				<option value="input">输入框</option>'
								+'			</select>'
								+'		</div>'
								+'	</div>'
								+'	<div class="td r4 w35">'
								+'		<div class="td-con text-left" id="attr'+ i +'"></div>'
								+'		<input type="hidden" id="attrhidden'+ i +'" name="attr_val[]" value=""/>'
								+'	</div>'
								+'	<div class="td r6 w10">'
								+'		<span class="td-con"><a class="ico_up_rack" href="javascript:;" title="点击取消"></a><input type="hidden" class="search" name="search[]" value="1"></span>'
								+'	</div>'
								+'	<div class="td r7 w10">'
								+'		<a href="javascript:" onclick="delNewAttr(this)">删除</a>'
								+'	</div>'
								+'</div>';
					$(this).parent().before(html);
					i++;
				});
				
				$(".table .ico_up_rack").live('click',function(){
					if(!$(this).hasClass("cancel")){
						$(this).addClass("cancel");
						$(this).attr("title","点击选择");
						$(this).siblings('.search').val('0');
					}else{
						$(this).removeClass("cancel");
						$(this).attr("title","点击取消");
						$(this).siblings('.search').val('1');
					}
				});
			})
		</script>
		<script>
			$(function(){
				$('.layout').live('change', function() {
					var typeid = $(this).attr('data-id');
					if($(this).children('option:selected').val() == 'input'){
						$('#popinput'+typeid).removeClass('hidden');
						$('#popedit'+typeid).addClass('hidden');
						$('#attrinput'+typeid).removeClass('hidden');
						$('#attredit'+typeid).addClass('hidden');
						$('#attr'+typeid).text('');
						$('#attrhidden'+typeid).val('');
					}else{
						$('#popinput'+typeid).addClass('hidden');
						$('#popedit'+typeid).removeClass('hidden');
						$('#attrinput'+typeid).addClass('hidden');
						$('#attredit'+typeid).removeClass('hidden');
					}
				})
			})
			function setAttr(id){
				$('#popedit'+id).attr('data-id',$('#attr'+id).text());
				$('#attredit'+id).attr('data-id',$('#attr'+id).text());
				value = $('#popedit'+id).attr('data-id') ? $('#popedit'+id).attr('data-id') : $('#attredit'+id).attr('data-id');
				var url = "<?php echo url('edit_pop')?>"+'&id='+id;
				top.dialog({
					url: url,
					title: '加载中...',
					data: value,
					width: 681,
					onclose: function () {
						$('#attr'+id).text(this.returnValue);
						$('#attrhidden'+id).val(this.returnValue);
					}
				})
				.showModal();
			}
			function delAttr(self,id){
				if (!confirm("确认要删除么？")) {
                        return false;
                    }
				if(id > 0){
					$(".submit-data").append("<input name='delId[]' value='"+id+"' type='hidden'>");
				}
				$(self).parent().parent('.tr').remove();
			}
			function delNewAttr(self){
				if (!confirm("确认要删除么？")) {
                        return false;
                    }
				$(self).parent().parent('.tr').remove();
			}
		</script>
<?php include template('footer','admin');?>
