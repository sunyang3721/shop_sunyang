$(function(){
	
	$(".goods-spec-item label").on('click',function(){
		if($(this).hasClass("selected")){
			$(this).removeClass("selected");
		}else{
			$(this).addClass("selected").siblings("label").removeClass("selected");
		}
	});
	
	//判断规格是否选择
	$("#spec .option .mui-btn").on('click',function(){
		if($(".goods-spec-item .selected").length!=$(".goods-spec-item").length){
			alert("请选择规格！")	
		}
	});
	
	var url = window.location.href;
	var strCart = new RegExp('#cart'),
		strBuy = new RegExp('#buy');
	if(strCart.test(url)||strBuy.test(url)){
		$(".cover-decision").show();
		$(".hd-cover").show();
		$(".hd-cover").addClass("show");
	}
	//加入购物车
	$("#join-cart").on('tap',function(){
		window.location.href = '#cart';
		$(".cover-decision").show();
		$(".hd-cover").show();
		setTimeout(function(){
			$(".hd-cover").addClass("show");
		},50);
	});
	//立即购买
	$("[data-event='buy_now']").on('tap', function(){
		window.location.href = '#buy';
		$(".cover-decision").show();
		$(".hd-cover").show();
		setTimeout(function(){
			$(".hd-cover").addClass("show");
		},50);
	});
	$.each(sku_obj, function(i) {
		var _this = this;
		$("label[data-id="+this.id+"]").each(function(){
			if(_this.value == $(this).data("value")){
				$(this).addClass("selected");
			}
		});
		changeStatus();
	});

	$(".goods-spec-item label").bind('click',function(){
		changeStatus();
	});

	function changeStatus(){
		var arr = window.location.href.split('#');
		var url_pre = arr[1];
	 	var specs_array = new Array();
	 	var regexp = '';
	 	/* 取出所有在规格数据 */
	 	$("dl").each(function(i){
	        var selected = $(this).find(".selected");
	        if(selected.length>0) specs_array[i] = selected.attr("data-id") + ':' + selected.attr("data-value");
	        else specs_array[i] = "\\\d+:\.+";
	    });
	    var id = '';
	 	$("dl").each(function(k){
	 		var selected = $(this).find(".selected");
	 		id += selected.data("id")+":"+selected.data("value")+";";
	    });
	    if(product_json[id] != undefined && product_json[id].sku_id != sku_id){
	    	window.location.href = product_json[id].url + '#' + url_pre;
	    }
	}
	//关闭规格选择
	$("#spec .close").on('tap',function(){
		$(".hd-cover").removeClass("show");
		$(".cover-decision").hide();
		window.location.href = '#';
		setTimeout(function(){
			$(".hd-cover").hide();
		},200);
	});
	
})

var hdTouch = {
	init: function(options){
		
		var opts = $.extend({
			outer: undefined,
			pull: undefined,
			animateTime: 1000,
			returnTime: 300,
			toper: 40,
			footer: 40,
			pullHeight: 40,
			tips: ["继续拖动，查看详情", "释放上拉，查看详情", "继续下拉，返回商品简介", "释放下拉，返回商品简介", "正在切换到下一屏"]
		}, options);
		
		if(!opts.outer) throw Error("请指定一个操作对象！"); 	//若没有找到指定对象直接终止程序并报错
		
		this.opts = opts;
		this.elem = opts.outer;
		this.childs = this.elem.getElementsByClassName('hd-scroller');
		this.startY = 0;
	    this.switchView = false; //是否切屏
		this.initScreenH = window.innerHeight;//屏幕初始高度，以解决如UC浏览器有地址栏时屏幕高度不同的问题
		this.slideTo(0, 0);
		this.tips = opts.pull;
		
		//设置容器最小高度
//		var minHeight = window.innerHeight - this.opts.toper - this.opts.footer - this.opts.pullHeight;
		var minHeight = window.innerHeight - this.opts.toper - this.opts.footer;
		this.childs[0].style.minHeight = minHeight + "px";
		this.childs[1].style.minHeight = minHeight + "px";
		var fontSize = $("body").css("font-size").split('p')[0];
		var l_padding=fontSize*0.04/0.24;
		this.maxScrollY = [this.childs[0].clientHeight + opts.footer + opts.pullHeight - l_padding.toFixed(), this.childs[1].clientHeight];
		this.resetHeight(this.maxScrollY[0]);
		
		var that = this;

		//切屏动画中禁止触屏滑动
		document.addEventListener("touchmove", function(e){
			if(that.pull == true){
	            e.preventDefault();
	        }else{
	            return;
	        }
		}, false);
		
		//切屏动画中禁止鼠标滑轮滚动
		document.addEventListener("mousewheel", function(e){
			if(that.pull == true){
				e.preventDefault();
	            return false;
	        }
		}, false);
		
		//获取触屏其实坐标
		function _start(e){
			that.startY = e.touches[0].pageY;
		}
		
		//触屏移动
		function _move(e){
			if(that.pull) return false;//切屏时阻止touchmove事件
			var screenHeight = window.innerHeight;
			var slideY = e.touches[0].pageY - that.startY;
			/*
			 * 当第一屏时，scrollTop到最底部后，继续上拉触发上拉滑动效果
			 * 在第二屏时，scrollTop到达最顶部，继续下拉触发下拉滑动效果
			 */
			if(!that.switchView){
				var distance = that.maxScrollY[0] - screenHeight + opts.toper;
				if(distance == document.body.scrollTop && slideY < 0){
					e.preventDefault();//scrollTop到最底部后，继续上拉则禁止滚动事件
					that.slideY = slideY / 4;
					that.slideTo(that.slideY, 0);
					that.tips.innerText = opts.tips[Math.abs(that.slideY) > opts.pullHeight ? 1 : 0];
				}
			}else{
				if(document.body.scrollTop == 0 && slideY > 0){
					e.preventDefault();//scrollTop到最顶部，继续下拉则禁止滚动事件
					that.slideY = slideY / 4;
					that.slideTo(-that.maxScrollY[0] + opts.pullHeight + opts.footer + that.slideY, 0);
					that.tips.innerText = opts.tips[Math.abs(that.slideY) > opts.pullHeight ? 3 : 2];
				}
			}
		}
		
		function _end(e){
			if(that.pull) return false;//切屏时阻止touchmove事件
			var screenHeight = window.innerHeight;
			if(!that.switchView){
				if(Math.abs(that.slideY) < opts.pullHeight){
					that.slideTo(0, opts.returnTime);
				}else if(Math.abs(that.slideY) >= opts.pullHeight){
					//切换到第二屏
					that.tips.innerText = opts.tips[4];
					that.pull = true;
					if(screenHeight > that.initScreenH) screenHeight = that.initScreenH;
					document.body.scrollTop = 0;
					that.slideTo(-that.maxScrollY[0] + screenHeight - opts.toper + that.slideY, 0);
					that.childs[1].style.display = "block";
					that.resetHeight(that.childs[1].clientHeight + opts.pullHeight + opts.footer);
					that.slideTo(-that.maxScrollY[0] + opts.pullHeight + opts.footer, opts.animateTime);
					that.setBarAnimate(1);
					setTimeout(function(){
						that.pull = false;
						that.switchView = true;
						that.tips.innerText = opts.tips[2];
					}, opts.animateTime);
				}
			}else{
				if(Math.abs(that.slideY) < opts.pullHeight){
					that.slideTo(-that.maxScrollY[0] + opts.pullHeight + opts.footer, opts.returnTime);
				}else if(Math.abs(that.slideY) >= opts.pullHeight){
					//切换到第一屏
					that.pull = true;
					that.tips.innerText = opts.tips[4];
					var scrollY = that.maxScrollY[0] - screenHeight + opts.toper;
					that.slideTo(-scrollY, opts.animateTime);
					that.setBarAnimate(0);
					setTimeout(function(){
						that.pull = false;
						that.switchView = false;
						that.slideTo(0, 0);
						that.resetHeight(that.maxScrollY[0]);
						document.body.scrollTop = scrollY;
						that.tips.innerText = opts.tips[0];
						that.childs[1].style.display = "none";
					}, opts.animateTime)
				}
			}
			that.slideY = 0;
		}
		this.elem.addEventListener("touchstart", _start, false);
		this.elem.addEventListener("touchmove", _move, false);
		this.elem.addEventListener("touchend", _end, false);
	},
	slideTo: function(y, t){
		this.elem.style.transform = "translate3d(0, " + y + "px, 0)";
		this.elem.style.webkitTransform = 'translateZ(0) translateY(' + y + 'px)';
		this.elem.style.transitionTimingFunction = "cubic-bezier(0.25, 0.46, 0.45, 0.94)";
		this.elem.style.transitionDuration = t + "ms";
	},
	setBarAnimate: function(n, t){
		if(n > 1 && n < 0) return false;
		var o = document.getElementById("detail-nav");
		if(n == 1) o.style.display = "block";
		if(n == 0){
			setTimeout(function(){
				o.style.display = "none";
			}, 500)
		}
		o.style.opacity = n;
		o.style.transitionTimingFunction = "cubic-bezier(0.25, 0.46, 0.45, 0.94)";
		o.style.transitionDuration = "500ms";
	},
	resetHeight: function(h){
//		if(h < window.innerHeight) h = window.innerHeight - this.opts.footer;
		if(h < window.innerHeight) h = window.innerHeight - this.opts.footer + this.opts.pullHeight;
		this.elem.style.height = h + "px";
		this.elem.style.transitionDuration = "0ms";
	}
}