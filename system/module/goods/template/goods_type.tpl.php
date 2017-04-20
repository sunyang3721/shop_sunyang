			<div class="form-box clearfix" id="form">
			<div class="form-group" style="z-index: 2;">
				<span class="label">请选择商品属性：</span>
				<div class="box">
					<div class="form-select-edit" id="spu-typeid">
						<div class="form-buttonedit-popup">
							<input class="input" value="<?php echo $goods['extra']['attr']['types'][$goods['spu']['type_id']]?>" readonly="readonly" type="text">
							<span class="ico_buttonedit"></span>
						</div>
						<div class="listbox-items" style="display: none;">
						<?php foreach ($goods['extra']['attr']['types'] as $key => $types) {?>
							<span class="listbox-item" data-val="<?php echo $key;?>"><?php echo $types;?></span>
						<?php }?>
						</div>
						<input class="form-select-name" name="spu[type_id]" value="<?php echo $goods['spu']['type_id'] ? $goods['spu']['type_id'] : 0?>" type="hidden">
					</div>
				</div>
				<p class="desc">设置商品的类型属性，属性信息会在商品详情页显示，并且可以筛选本件商品</p>
			</div>
			</div>

			<div class="goods-attr-content margin border">
			</div>
			<script id="attr_select" type="text/html">
			<%for(var key in extra_attr){%>
			<%var attrs = extra_attr[key]%>
				<table data-id="<%=key%>" class="hidden">
					<%for(var i in attrs){%>
					<%var attr = attrs[i]%>
					<tr>
						<th width="120"><%=attr['name']%></th>
						<td>
							<% if (attr['type'] == 'input'){ %>
							<%var _select_type = []%>
								<%if(goods_type){%>
									<%=_select_type = goods_type[attr['id']] ? goods_type[attr['id']] : []%>
								<% }%>
							<div class="area-box" data-id="<%=attr['id']%>">
								<input class="input" type="text" name="attr[<%=attr['id']%>][]" value="<%=_select_type[0]%>" />
							</div>
							<%}else{%>
							<%for(var n in attr['value']){%>
							<%var val = attr['value'][n]%>
							<div class="area-box" data-id="<%=attr['id']%>">
								<%checked = false;%>
								<%if(attr['type'] == 'radio'){%>
									<%if(goods_type && goods_type[attr['id']] && val == goods_type[attr['id']][0]) checked = true;%>
								<%}else if(attr['type'] == 'checkbox'){%>
									<%if(goods_type && goods_type[attr['id']]){%>
										<%for(var j in goods_type[attr['id']]){%>
											<%goods_attr = goods_type[attr['id']][j]%>
											<%if(goods_attr == val){%>
												<%checked = true;%>
											<%}%>
										<%}%>
									<%}%>
								<%}%>
								<label class="select-wrap"><input class="select-btn" type="<%=attr['type']%>" name="attr[<%=attr['id']%>][]" value="<%=val%>" <%if(checked){%>checked='true'<%}%>><%=val%>
								</label>
							</div>
							<%}%>
							<%}%>
						</td>
					</tr>
					<%}%>
				</table>
			<%}%>
			</script>
		<script>
			var extra_attr = <?php echo json_encode($goods['extra']['attr']['attr'])?>,
				goods_type = <?php echo json_encode($goods['_type'])?>;
			var attr_select = template('attr_select', {'extra_attr': extra_attr,'goods_type': goods_type});
			$('.goods-attr-content').html(attr_select);

			$(function(){
				show();
				var $val=$("input[type=text]").eq(1).val();
				$("input[type=text]").eq(1).focus().val($val);
			})
			$('.box #spu-typeid .form-select-name').live('change',function(){
				show();
			})


			function show(){
				var type_id = $('.box #spu-typeid .form-select-name').val();
				$('table[data-id="' + type_id + '"]').removeClass('hidden').siblings().addClass('hidden');
				$('table[data-id="' + type_id + '"]').siblings().find('input').each(function() {
					$(this).removeAttr('checked');
				});
				if(type_id == 0){
					$('table').addClass('hidden');
				}
			}
			var attr_id = '<?php echo key($info['attr'])?>';
			$('div[data-id="'+ attr_id +'"]').parents('table').removeClass('hidden');
			$('span[data-val="' + $('div[data-id="'+ attr_id +'"]').parents('table').attr('data-id') + '"]').addClass('listbox-item-selected').siblings().removeClass('listbox-item-selected');
			$('span[data-val="' + $('div[data-id="'+ attr_id +'"]').parents('table').attr('data-id') + '"]').parent().siblings().find('.input').val($('.listbox-item-selected').html());
		</script>
