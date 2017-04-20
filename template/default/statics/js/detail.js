$(window).load(function(){
	
	//详情内容导航条
	itemContentScroll();
	itemBar();
	widgetCarousel();
	
	//商品图左右滚动
	$(".slider").sliderRoll({
		time: 300,
		mouseover: true
	});
	
	//选项卡切换
	$(".consult-tab").tabs({
		tag: ".consult-tag"
	});
	
	//大图放大镜效果
	$(".jqzoom").jqueryzoom({xzoom:338,yzoom:338});
	//修改预览图地址
	$("#spec-list").find("li").live('mouseover',function(){
		$(".jqzoom").children("img").attr("src",$(this).children("img").attr("src"));
	});
	
})

function itemContentScroll(){
	
	$("#item-bar").find("li").click(function(){
		if($(this).index()==0){
			$('html,body').animate({scrollTop:$("#pro-detail-right").offset().top-4},300);
		}else if($(this).index()==1){
			$('html,body').animate({scrollTop:$(".product-detail-panel").eq(0).offset().top-36},300);
		}else if($(this).index()==2){
			$('html,body').animate({scrollTop:$(".product-detail-panel").eq(2).offset().top-36},300);
		}else if($(this).index()==3){
			$('html,body').animate({scrollTop:$(".product-detail-panel").eq(4).offset().top-36},300);
		}
	});
	
}

function itemBar(){
	$(window).scroll(function(){
		
		var doctop = $(document).scrollTop();
		var _head = $("#pro-detail-right").offset().top;
		var pro1 = $(".product-detail-panel").eq(0).offset().top-40;
		var pro2 = $(".product-detail-panel").eq(2).offset().top-40;
		var pro3 = $(".product-detail-panel").eq(4).offset().top-40;
		var num = 0;

		if(doctop > _head){
			$('#item-bar').addClass('item-bar');
			
		}else{
			$('#item-bar').removeClass('item-bar');
		}
		if(doctop > _head){
			num = 0;
		}
		if(doctop > pro1) num = 1;
		if(doctop > pro2) num = 2;
		if(doctop > pro3) num = 3;
		$("#item-bar").find("li").eq(num).addClass("current").siblings().removeClass("current");
	
	});
	
	$(".item-info .cart-btn").click(function(){
    	if($(this).hasClass("disabled")){
    		return false;
    	}
   });
	$(".item-info .item-btn").click(function(){
    	if($(this).hasClass("disabled")){
    		return false;
    	}
   });
	
	$(".item-check .item").live('click',function(){
		var specKey = '';
	    $('#choose .dl').each(function() {
	    	var _select = $(this).find('.selected');
	       	if (_select.length <= 0) {
	            return false;
	        }
	        var spec_id = _select.data('id');
	        var spec_value = _select.data('value');
	        specKey += spec_id+':'+spec_value+';' ;
	    });
	    changeStatus();
	    if($('#choose .dl').length > $('#choose .selected').length){
	    	$('#choose').addClass('error');
	    	$('.item-info .item-btn').addClass('disabled').removeClass('cart_add');
	    	$(".item-info .cart-btn").addClass("disabled");
	    	var _this = $('#choose .selected');
	    	if($('#choose .selected').data('id') == spec_id){
	    		var spec_reg = _this.data('id') + ':' + _this.data('value') + ';';
	    		$.each(product_json,function(index,skus){
	    			if(index.indexOf(spec_reg) >= 0){
	    				var imgList = '';
	    				var imgs = eval(skus.imgs);
	    				$.each(imgs,function(i, img) {
	    					imgList += '<li><img src="'+ img +'" alt="" width="50" height="50" /></li>';
	    				});
	    				$('#spec-list .lh').html(imgList);
	    				$(".slider").sliderRoll({
							time: 300,
							mouseover: true
						});
	    				$('#spec-n').find('img').attr('src',imgs[0]);
	    			}
	    		})
	    	}
	    }else{
	    	$('#choose').removeClass('error');
	    	$('.item-info .item-btn').removeClass('disabled').addClass('cart_add');
	    	$(".item-info .cart-btn").removeClass("disabled");
	    }
	    if($('#choose .dl').length == $('#choose .selected').length){
			if(product_json[specKey] != undefined){
	            var item = product_json[specKey];
	            if(item.spec_md5 != sku_json){
	           		location.href = item.url;
	       		}
	        }
	    }	    
	});
	function autoSelected(){
		$(".choose-wrap .item").each(function(){
			var _this = $(this);
			$.each(sku_obj,function(i,item) {
				if(_this.data("name")==item.name && _this.data("value")==item.value){
					_this.addClass("selected");
				}
			});
		});
		changeStatus()
	}
	autoSelected();
	
	function changeStatus(){
	 	var specs_array = new Array();
	 	var regexp = '';
	 	/* 取出所有在规格数据 */
	 	$("#choose .dl").each(function(i){
	        var selected = $(this).find(".selected");
	        if(selected.length > 0) {
	        	specs_array[i] = $.md5(selected.attr("data-id") + ':' + selected.attr("data-value"));
	        } else {
	        	specs_array[i] = "[a-fA-F0-9]{32}";
	        }
	    });
	 	
	 	$("#choose .dl").each(function(k){
	        /* 遍历属性 */
	       	$(this).find(".item").each(function(){
	       		$(this).removeClass('disabled');
	       		var flage = false;
	       		var temp = specs_array.slice();
	       		temp[k] = $.md5($(this).attr('data-id') + ':' + $(this).attr('data-value'));
	       		for(gi in product_json){
					var item = product_json[gi];
					var item_text = JSON.stringify(item.spec_md);
	                var reg = new RegExp(temp.join(";"));	                
	                flage = reg.test(item_text);
	                if(flage) break;
	            }
	            if(!flage) $(this).addClass("disabled");
	       	});
	 	});
	}
}

function widgetCarousel(){
	
	
	$(".widget-carousel-content ul li").live('click',function(){
		var _parent = $(this).parents(".widget-carousel-content");
		var _next = _parent.next(".widget-carousel-box");
		if(_next.find("img").length<=0){
			_parent.find("img").each(function(i){
				_next.append('<img data-ind="'+i+'" src="'+$(this).attr("src")+'" />');
			});
		}
		_next.find("img").eq($(this).index()).show().siblings("img").hide();
		if(!$(this).hasClass("current")){
			$(this).addClass("current").siblings("li").removeClass("current");
			_next.slideDown(300);
		}else{
			$(this).removeClass("current");
			_next.slideUp(300);
		}
		return false;
	});
	
	$(".widget-carousel-box .prev").live('click',function(){
		var ind = 0;
		var _img = $(this).parent().find("img");
		var _len = _img.length;
		_img.each(function(i){
			if($(this).css("display")!='none') ind = i;
		});
		ind--;
		if(ind<0) ind = _len-1;
		_img.eq(ind).show().siblings("img").hide();
		$(this).parent().prev().find("li").eq(ind).addClass("current").siblings("li").removeClass("current");
		return false;
	});
	
	$(".widget-carousel-box .next").live('click',function(){
		var ind = 0;
		var _img = $(this).parent().find("img");
		var _len = _img.length;
		_img.each(function(i){
			if($(this).css("display")!='none') ind = i;
		});
		ind++;
		if(ind>_len-1) ind = 0;
		_img.eq(ind).show().siblings("img").hide();
		$(this).parent().prev().find("li").eq(ind).addClass("current").siblings("li").removeClass("current");
		return false;
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
