var _batch_vals = new Array();

$(function(){
	
	//顶部标签选项卡切换
	$(".fixed-nav-tab").click(function(){
		$(this).children("a").addClass("current");
		$(this).siblings().children().removeClass("current");
		$(".content-tabs").eq($(this).index(".fixed-nav-tab")).removeClass("hidden");
		$(".content-tabs").not($(".content-tabs").eq($(this).index(".fixed-nav-tab"))).addClass("hidden");
	});
	
	//表格tr行鼠标hover事件
	$(".table .tr").live('mouseover',function(){
		$(this).addClass("tr-hover");
	}).live('mouseout',function(){
		$(this).removeClass("tr-hover");
	});
	
	//显示与隐藏提示信息
	$("#show-tip").click(function(){
		if($(this).attr("data-open")=='true'){
			$(".tips .tips-txt").stop(true,true).slideUp(200);
			$(this).html("展示操作提示");
			$(this).attr("data-open","false")
		}else{
			$(".tips .tips-txt").stop(true,true).slideDown(200);
			$(this).html("关闭操作提示");
			$(this).attr("data-open","true")
		}
	});
	
	//可编辑框鼠标移上边框变色
	$("input[type = text], select, textarea").hover(function(){
		$(this).addClass("bd-main");
	},function(){
		$(this).removeClass("bd-main");
	});
	
	//表单鼠标移上提示信息变色
	$(".form-line").hover(function(){
		$(this).find("p").eq(0).addClass('text-sub');
	},function(){
		$(this).find("p").eq(0).removeClass('text-sub');
	});

	//表格操作区域
	/*$(".table-wrap .tr").hover(function(){
		$(this).addClass("bg-white");
	},function(){
		$(this).removeClass("bg-white");
	});*/
	
	//全选中与全不选中
	$("#check-all").live("click", function () {
		if($(this).is(":checked")){
			$(".check-option input:not(:disabled)").attr("checked", true);
			$(".check-option input:not(:disabled)").parents(".tr").addClass('selected');
		}else{
			$(".check-option input:not(:disabled)").attr("checked", false);
			$(".check-option input:not(:disabled)").parents(".tr").removeClass('selected');
		}
		_batch_vals = [];
        $(".check-option").each(function(){
        	if($(this).children("input").is(":checked")){
        		_batch_vals.push($(this).children("input").val());
        	}
        })
    });
    
    //全选状态下点击单个取消选中则取消全选状态
    $(".check-option input").live("click", function () {
		if(!$(this).is(":checked")){
			$("#check-all").attr("checked", false);
			$(this).parents(".tr").removeClass('selected');
		}else{
			$(this).parents(".tr").addClass('selected');
		}
		_batch_vals = [];
        $(".check-option").each(function(){
        	if($(this).children("input").is(":checked")){
        		_batch_vals.push($(this).children("input").val());
        	}
        })
    });
	
	//工具条批量操作
    $("[data-ajax]").live("click", function(){
		var $key = $(this).data("ajax");
    	if(_batch_vals.length < 1) {
			alert("请选择您要操作的数据！");
			return false;
		}
    	var message = $(this).data("message") || "您是否确定进行此操作？";
    	if(!confirm(message)) return false;
		var data = {};
		data[$key] = _batch_vals;
		$.post($(this).attr("href"), data, function(ret){
			alert(ret.message);
			if(ret.status==1){
				window.location.href=window.location.href;
			}
		},'json');
		return false;
	})
    
    //文本框双击可编辑
    $(".double-click .double-click-button").live('dblclick',function(){
    	$(this).addClass('hidden');
    	$(this).next(".double-click-edit").focus();
    });
    $(".double-click .double-click-edit").live('focus',function(){
    	var dclick = $(this);
    	$(this).addClass('focus').select();
    	$(this).attr("initial-val",$(this).val());
    	$(this).parents('.tr').removeClass('tr-hover');
    	$(window).bind('keyup',function(e){
    		if(e.keyCode=="13"&&dclick.hasClass("focus")){
    			dclick.blur();
    		}
    	});
    });
    $(".double-click .double-click-edit").live('blur',function(){
    	$(this).val($(this).val().replace(/(^\s*)|(\s*$)/g, ""));//当用户输入空格时默认为没有输入字符
    	if(!$(this).val()){
    		$(this).val($(this).attr("initial-val"));//如果值为空则替换成修改前的值 
    	}
    	$(this).removeAttr('initial-val');
    	$(this).removeClass('focus');
    	$(this).prev('.double-click-button').removeClass('hidden');
    });
	
	$(".ident-show em").hover(function(){
		$(this).next().show();
	},function(){
		$(this).next().hide();
	})
	
	//警告窗口关闭
	$('.warn-info .close').click(function(){
		$(this).parent().addClass('hidden');
	})
    
});
