<?php include template('header','admin');?>

		<div class="goods-add-class-wrap layout bg-white" style="width:700px;">
			<div class="goods-add-class list-bg-none clearfix" id="district-box">
				<div class="child root border focus level-1" data-level="1">
					<div id="first_box">
					<?php foreach ($districts as $k => $district): ?>
						<a href="javascript:;" onclick="get_childs(this,<?php echo $district['id']; ?>)" data-id="" data-parentid='0'><input type="checkbox" name="district_id[]" value="<?php echo $district['id']; ?>" /><?php echo $district['fullname']; ?></a>
					<?php endforeach; ?>
					</div>
				</div>
				<div class="child border focus level-2" data-level="2"></div>
				<div class="child border focus level-3" data-level="3"></div>
			</div>
			<div class="padding text-right ui-dialog-footer">
				<input type="button" class="button bg-main" id="okbtn" value="确定" name="dosubmit"/>
				<input type="button" class="button margin-left bg-gray" id="closebtn" value="取消" />
			</div>
		</div>
<?php include template('footer','admin');?>


<script>

	$(function(){
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			return;
		}
		
		dialog.title('编辑地区');
		dialog.reset();     // 重置对话框位置
		$('#okbtn').on('click', function () {
			dialog.close($('.child ').find('input:checked'));
			return false;
		});
		$('#closebtn').on('click', function () {
			dialog.remove();
			return false;
		});

	});

	var _ischeck    = false;
	var _isdisabled = false;
	var _sub        = false;	// 是否提交该值

	/* 获取下级地区入口 */
	function get_childs(obj ,district_id){
		// 选择或勾选后的样式
		if($(obj).hasClass('focus')){
			// $(obj).removeClass('focus');
			if ($(obj).children('input').is(':checked') == true) {
				$(obj).children('input').prop("checked",false);
			} else {
				$(obj).children('input').prop("checked",true);
			}
		}else{
			$(obj).siblings().removeClass('focus');
			$(obj).addClass('focus');
			$(obj).children('input').prop("checked",true);
		}

		// 当前外层选择器
		var this_top = $(obj).parents('.child');

		// 当前层级
		var level = parseInt(this_top.attr('data-level'));

		if (level == 1) {
			$('.child:last').find('.search_box').hide();
		}

		//  设置默认值
		set_checked(obj ,district_id);

		// 小于第3级时执行
		if (level < 3) {
			// 下级选择器
			var next_top = $(this_top).next();
			var go_on = true;		
			var ob = next_top.find(".search_box");	// 下级选择器下的box框
			ob.each(function() {
				if ($(this).attr('data-parentid') == district_id) {
					$(this).show();
					go_on = false;
				} else {
					if (level == 1) {
						// 隐藏最后一级
						$('.child:last').find('.search_box').hide();
					}
					$(this).hide();
				}
			});
			if (go_on == false) return false;
			ajax_get_district(next_top ,$(obj).parent().attr('data-parentid'),district_id);			
		}
	}

	/* 勾选窗体并赋默认值 */
	function set_checked(obj ,district_id) {
		var this_checked = $(obj).find('input').is(':checked');
		var this_top    = $(obj).parents('.child');		// 当前外层选择器
		var level       = parseInt(this_top.attr('data-level'));
		var district_id = parseInt(district_id);

		if (level > 3) return false;

		// 循环上下级，进行赋值
		loop_before(level, obj ,this_checked);
		loop_next(level,district_id ,this_checked);
	}

	/* 循环上级 */
	function loop_before( level , obj ,this_checked) {
		if (level < 2) return false;
		var this_box = $(obj).parent();
		var parent_id = this_box.attr('data-parentid');
		if (level == 2) {
			var before_box = $("#first_box");
		} else {
			var before_box = $('#team_'+ this_box.attr('data-grandpa'));
		}
		// 当前要更改的input
		var this_input = '';
		$(before_box).find('input').each(function() {
			if ($(this).val() == parent_id) {
				this_input = $(this);
			}
		});

		//this_input = before_box.find("input[data-id="+this_box.attr("data-parentid")+"]")

		// 所有同级已勾选的input
		var _inputs = this_box.find('input:checked');

		if (_inputs.length === this_box.find('input').length) {
			this_input.prop('checked',true);				
		} else {
			this_input.prop('checked',false);
		}

		// 递归执行
		loop_before((level-1),this_input.parent());
	}

	/* 循环下级 */
	function loop_next( level ,district_id ,this_checked) {
		var next_level = level + 1;
		var next_top = $('.level-'+next_level);
		var next_boxs = next_top.find(".search_box");
		if (next_boxs.length == 0 && this_checked ==true) {
			_ischeck = true;
		}

		$("div[data-level="+ (level + 1) +"]").find("a[data-ident="+ident+"] input").prop("checked", this_checked);






		next_boxs.each(function() {
			if($(this).attr('data-parentid') == district_id) {
				$(this).children().find('input').prop('checked',this_checked);
			} else {
				_ischeck = this_checked;
			}
		});
	}

	/* ajax加载id下的地区 */
	function ajax_get_district(obj ,grandpa_id ,district_id) {
		var district_id = parseInt(district_id);
		var _checked    = (_ischeck == true) ? ' checked="checked"' : '';
		var _disabled   = (_isdisabled == true) ? ' disabled="disabled"' : '';
		var _issub		= (_sub == true) ? ' issub="true"' : ' issub="false"';
		var grandpa_id = ($(obj).attr('data-level') > 2) ? ' data-grandpa='+grandpa_id : '';

		$.ajax({
			url: '<?php echo url("order/admin_delivery/ajax_get_district_childs") ?>',
			type: 'get',
			dataType: 'json',
			data: {district_id: district_id},
			success: function (ret) {
				var _html = '<div class="search_box" id="team_'+ district_id +'"'+ grandpa_id +' data-parentid="'+district_id+'">';
				if (ret == null) {
					_html += '<a href="javascript:;" class="focus">无地区...</a></div>';
					$(obj).append(_html);
					return false;
				}
				$.each(ret, function(i, n) {
					_html += '<a href="javascript:;" onclick="get_childs(this,'+ n.id +')"><input type="checkbox" name="district_id[]" value="'+ n.id +'" '+ _checked + _disabled + _issub +'/>'+ n.fullname +'</a>';
				})
				_html += '</div>';
				$(obj).append(_html);
			},
		})
	}

</script>