<?php include template('header','admin');?>
<div class="logistics-area layout bg-white clearfix" id="edit_district">
	<ul class="logistics-area">
		<?php foreach ($districts as $key => $value): ?>
		<li class="area-box" data-level="1">
			<label class="top-label"><input type="checkbox" data-id="<?php echo $value['id']?>" data-group="" data-name="<?php echo $value['name'] ?>"/><?php echo $value['name'] ?></label>
			<ul class="top-floor">
				<?php foreach($value['_child'] AS $child) : ?>
				<li class="top-label" data-level="2">
					<label><input type="checkbox" data-id="<?php echo $child['id']?>" data-group="" data-name="<?php echo $child['name'] ?>"/><?php echo $child['name']; ?></label>
					<span class="nums">(0)</span><b class="ico_triangle area-hand"></b>
					<ul class="last-floor">
						<span class="spacer"></span>
						<?php foreach($child['_child'] AS $v) : ?>
						<li data-level="3">
							<label><input type="checkbox" data-id="<?php echo $v['id']?>" data-group="" data-name="<?php echo $v['name'] ?>"/><?php echo $v['name']; ?></label>
						</li>
						<?php endforeach ?>
					</ul>
				</li>
				<?php endforeach ?>
			</ul>
		</li>
		<?php endforeach ?>
	</ul>
</div>
<div class="padding text-right ui-dialog-footer">
	<input type="submit" class="button bg-main" value="确定"/>
	<input type="reset" class="button margin-left bg-gray" value="取消"/>
</div>

<script type="text/javascript">/*
 * 2015-12-1
 * 修改物流设置中的编辑地区弹窗
 */
var $group = '<?php echo $_GET['id']; ?>';

$(function() {
	try {
		var dialog = top.dialog.get(window);
		dialog.reset();		
	} catch (e) {
		alert('请勿非法访问');
		return false;
	}	
	var deliverys = dialog.deliverys;
	/* ======赋值！！！fuck====== */
	if (deliverys) {		
		$.each(deliverys, function(i, delivery) {
			$.each(delivery, function(k, v) {
				$("input[data-id='"+ v +"']").attr('checked', true);
				if(i != $group) {
					$("input[data-id='"+ v +"']").attr('disabled', true).attr("data-group", i);
				}
				_loop_after(v, i);
				_loop_before(v, i);
			});
		});
		selected_num();
	}

	$("input[type=submit]").live("click", function() {			
		dialog.close(getDelivery());
		dialog.remove();
		return true;
	})
	
	$("input[type=reset]").live("click", function() {
		dialog.close();
		dialog.remove();
		return false;
	})	
	
	/* 获取当前选中数据 */
	function getDelivery() {
		/* 取得本次所有选中的数据 */
		var tmp = new Array(), _txt = new Array();
		$("input:checked[disabled!=disabled]").each(function(i, n){
			tmp.push($(this).data('id'));
		})		
		$.each(tmp, function(i, n){
			var dels = _delivery(n);			
			$.each(dels, function(k, del){
				tmp.splice($.inArray(del, tmp),1);
			})
		})
		deliverys[$group] = tmp;		
		$.each(tmp, function(i, n){
			_txt.push($("input[data-id='"+ n +"']").data('name'));
		})
		var dreturn = {
			ids : deliverys[$group],
			txt : _txt
		}
		return dreturn;
	}
	
	function _delivery(id) {
		var $_this  = $("input[data-id='"+ id +"']");
		var $_parent = $_this.parent();
		var _ids = new Array();
		if($_parent.siblings("ul").find('input:checked[disabled!=disabled]').length == $_parent.siblings("ul").find('input').length) {
			$_parent.siblings("ul").find('input:checked[disabled!=disabled]').each(function(i ,n){
				_ids.push($(this).data('id'));
			})
		} else {
			_ids.push(id);
		}
		return _ids;
	}
		
	$("input[type=checkbox]").live("click", function(){
		_loop_after($(this).data('id'), $group);
		_loop_before($(this).data('id'), $group);
		selected_num();
	})
})

/* 循环下级 */
function _loop_after(id, group) {	
	var $_this  = $("input[data-id='"+ id +"']");
	var $_parent = $_this.parent().parent('li');
	var $_level = $_parent.data("level");
	if($_level == 3) return false;
	var flag = $_this.attr("checked") ? true : false;	
	// 赋值下级选框
	$_parent.find("input[data-group=''][disabled!=disabled]").attr("checked", flag);
	if(group && group != $group) {
		$_parent.find("input").attr("data-group", group).attr("disabled", true);
	}
}


/* 循环上级 */
function _loop_before(id, group) {
	var $_this  = $("input[data-id='"+ id +"']");
	var $_parent = $_this.parent().parent('li');
	var $_level = $_parent.data("level");
	if($_level == 1) return false;
	// 定义同级元素
	var $this  = $_this.parents("li[data-level='"+ ($_level - 1) +"']");
	if($this.find("ul input:checked").length == $this.find("ul input").length) {
		$this.find("input:eq(0)").attr("checked", true);
		if(group && group != $group && $this.find("ul input[data-group='']").length == 0) {
			$this.find("input:eq(0)").attr("disabled", true);
		}
	} else {
		$this.find("input:eq(0)").attr("checked", false);
	}
	_loop_before($this.find("input:eq(0)").data("id"), group);
}

/* 浮动下级 */
$(".area-box .area-hand").live('click', function(e) {
	if (!$(this).parent(".top-label").hasClass("area-selected")) {
		$(".top-floor .top-label").removeClass("area-selected");
		$(".last-floor").hide();
		$(this).parent(".top-label").addClass("area-selected");
		$(this).next().show();
	} else {
		$(this).parent(".top-label").removeClass("area-selected");
		$(this).next().hide();
	}
});

/* 停止委派 */
$(".top-floor .top-label").live('click', function(e) {
	e.stopPropagation();
})

/* 关闭浮动 */
$("body").live('click', function() {
	$(".last-floor").hide();
	$(".logistics-area .area-selected").removeClass("area-selected");
});

/* 智能识别定位 */
$("#edit_district .last-floor").each(function() {
	var $p_height = $(this).parents(".logistics-area").height();
	var $p_width = $(this).parents(".logistics-area").width();
	var $width = 360;
	var $height = $(this).height() - 10;
	var $thisTop = $(this).parent().offset().top;
	var $thisLeft = $(this).parent().offset().left;
	if($p_width - $thisLeft < $width){
		$(this).addClass("right");
	}else{
		$(this).addClass("left");
	}
	if($height > $p_height - $thisTop){
		$(this).addClass("bottom");
	}else{
		$(this).addClass("top");
	}
})

/* 统计已选择 */
function selected_num() {
	setTimeout(function() {
		$("li[data-level=2]").each(function(i ,n){
			$_num = $(this).find("ul input:checked[data-group='']").length;
			$_dom = $(this).find("span.nums");			
			$_dom.text("(" + $_num + ")");			
			if ($_num > 0) {
				$_dom.css('display', 'inline');
			} else {
				$_dom.css('display', 'none');
			}
		})
	}, 10);
}

</script>