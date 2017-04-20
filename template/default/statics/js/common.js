$(function(){
	//商品分类的展示与隐藏
	$(".menu").hover(function(){
		$("#leftsidebar").show();
	},function(){
		$("#leftsidebar").hide();
	});

	//商品分类
	$(".category").hover(function(){
		$(this).children(".dd").show();
	},function(){
		$(this).children(".dd").hide();
		$('.dd-inner li').removeClass("current");
    	$(".dropdown-layer").hide();
	});
	$(".category .dd").mouseenter(function(){
		$(".dropdown-layer").show();
	});
    $('.dd-inner li').mouseenter(function(){
    	$(this).addClass("current").siblings().removeClass("current");
    	$(".droplayer-text .item-sub").eq($(this).index()).show().siblings().hide();
    	$(this).find(".arrow").stop().animate({right:"5px"},100);
    }).mouseleave(function(){
    	$(this).find(".arrow").stop().animate({right:"10px"},100);
    });

	$(".droplayer-text .item-sub").each(function(){
		$(this).find(".item-sub-box").each(function(i){
			if(i%2==0){
				$(this).addClass("odd");
			}else{
				$(this).addClass("even");
			}
		})
	})
	//可取消的选择
	$(".item-check .item").live('click',function(){
		if(!$(this).hasClass("disabled")){
			if($(this).hasClass("selected")){
				$(this).removeClass("selected");
			}else{
				$(this).addClass("selected").siblings().removeClass("selected");
			}
		}
	});
	//不可取消的选择
	$(".item-checked .item").live('click',function(){
		if(!$(this).hasClass("disabled")){
			$(this).addClass("selected").siblings().removeClass("selected");
		}
	});
	//多选
	$(".item-checks .item").live('click',function(){
		if(!$(this).hasClass("disabled")){
			if(!$(this).hasClass("selected")){
				$(this).addClass("selected")
			}else{
				$(this).removeClass("selected")
			}
		}
	});

	//加减数量
	$(".adjust .adjust-reduce").live('click',function(){
		adjust($(this));
	});
	$(".adjust .adjust-add").live('click',function(){
		adjust($(this));
	});
	$(".adjust .adjust-input").live('keyup',function(){
		var val = $(this).val();
        var val2 = val.replace(/[^\d]/g, '');
        var min = parseInt($(this).attr("data-min"));
		var max = parseInt($(this).attr("data-max"));
		if($(this).attr("data-min")==undefined){
			min = 0
		}else if($(this).attr("data-max")==undefined){
			min = 999
		}
        if(val2<=min){
        	val2 = min;
        }else if(val2>=max){
        	val2 = max;
        }
        $(this).val(val2);
        disabledAdjustBtn($(this).parents(".adjust"),min,max);
	});
	function adjust(o){
		var _input = o.parents(".adjust").find(".adjust-input");
		var min = parseInt(_input.attr("data-min"));
		var max = parseInt(_input.attr("data-max"));
		if(_input.attr("data-min")==undefined){
			min = 0
		}else if(_input.attr("data-max")==undefined){
			min = 999
		}
		if(o.hasClass('adjust-reduce')&&_input.val()>min){
			_input.val(_input.val()-1)
		}else if(o.hasClass('adjust-add')&&_input.val()<max){
			_input.val(parseInt(_input.val())+1)
		}
		disabledAdjustBtn(o.parents(".adjust"),min,max);
	}
	function disabledAdjustBtn(p,min,max){
		if(p.find(".adjust-input").val()==min){
			p.find(".adjust-reduce").addClass("disabled");
			p.find(".adjust-add").removeClass("disabled");
		}else if(p.find(".adjust-input").val()==max){
			p.find(".adjust-add").addClass("disabled");
			p.find(".adjust-reduce").removeClass("disabled");
		}else{
			p.find(".adjust-add").removeClass("disabled");
			p.find(".adjust-reduce").removeClass("disabled");
		}
		return false;
	}

	//左侧分类
	$(".sp-category dl dt").live('click',function(){
		if(!$(this).parents("dl").hasClass("open")){
			$(this).parents("dl").addClass("open");
		}else{
			$(this).parents("dl").removeClass("open");
		}
	});

	//商品列表鼠标指上效果
	$(".list-wrap .list-h li").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});

	//全选与取消全选
	$(".check-fun .check-switch").live('click',function(){
		var child = $(this).parents(".check-fun").find(".check-child").children("input");
		if($(this).is(":checked")){
			child.attr("checked",true);
		}else{
			child.attr("checked",false);
		}
	});
	$(".check-fun .check-child").live('click',function(){
		var flog = true;
		var _parent = $(this).parents(".check-fun");
		_parent.find(".check-child").each(function(){
			if(!$(this).children("input").is(":checked")){
				flog = false
			}
		});
		if(flog){
			_parent.find(".check-switch").attr("checked",true);
		}else{
			_parent.find(".check-switch").attr("checked",false);
		}
	});

	//优惠券
	$(".sales-promotion").live('click',function(){
		$(this).next(".sales-promotion-info").removeClass("hidden")
	});
	$(".sales-promotion-info .promotion-tit").live('click',function(){
		var _parent = $(this).parents(".sales-promotion-info");
		_parent.addClass("hidden");
	});
	//取消
	$(".sales-promotion-info .cancel-promotion").live('click',function(){
		$(this).parents(".sales-promotion-info").addClass('hidden');
	});


	$(window).scroll(function(){

		var _h = $(document).scrollTop();
		if(_h>80){
			$(".hd-toolbar-footer").show();
		}else{
			$(".hd-toolbar-footer").hide();
		}

	})

	$(window).resize(function(){
		//脚部工具条显示位置
		var winWidth = $('body,html').width();
		if(winWidth>1284){
			$(".hd-toolbar-footer").css({right:(winWidth-1284)/2+"px"});
		}else{
			$(".hd-toolbar-footer").css({right:"0"});
		}
	});
	$(window).resize();
	//加入收藏
	$(".collect-btn").live('click',function(){
		var _this = $(this);
		var _parent = _this.parent();
		var url = "?m=member&c=favorite&a=add";
		var sku_id = _this.attr('data-skuid');
		var sku_price = _this.attr('data-price');
		var url_forward =  _this.data('url');
		var sku_name = _this.attr('data-name');
		$.post(url,{sku_id:sku_id,sku_price:sku_price,url_forward:url_forward,sku_name:sku_name},function(data){
			if(data.status == 1){
				top.dialog({
					content: '<div class="padding-large text-center">商品已成功加入收藏夹</div>',
					title: '收藏成功',
					width: 280,
					okValue: '确定',
					ok: function(){
						_this.html('<em class="text-gray">已收藏</em>');
						_this.removeClass('collect-btn');
					}
				})
				.showModal();
				return false;
			}else{
				$.tips({
					icon:'error',
					content:data.message,
					callback:function() {}
				});
			}
		},'json');
	});



})

function jump_to_page(that){
	var $parent=$(that).closest(".paging");
	var $page=parseInt($parent.find("input[name='page']").val());
	var $current=$parent.data('page');
	var $max_page=parseInt($parent.find('b').html());
	var url=window.location.href;
	if($page>$max_page){
		$page=$max_page;
	}

	if($page && $page>0 && url){
		if(url.indexOf("page")!=-1){
			var page="page="+$page;
			var page1="page="+$current;
			url=url.replace(page1,page);
			window.location.href=url;
		}else{
			if(url.indexOf("?")!=-1){
				window.location.href=url+"&page="+$page;
			}else{
				window.location.href=url+"?page="+$page;
			}
		}

	}
}

