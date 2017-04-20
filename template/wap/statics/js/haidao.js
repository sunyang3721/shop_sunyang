$(function(){
	
	//A链接跳转,通过href打开指定id层
	mui("body").on('tap','a',function(){
		var str = this.getAttribute("href");
		if( str && str != "#" ){
			if(str.indexOf("#")==0){
				if($(str).hasClass('current')){
					$(str).removeClass('current');
				}else{
					$(str).addClass('current');
				}
			}else{
				if( str != "javascript:;" && str != "javascript:void(0);" ){
					mui.openWindow({url: str});
				}
			}
		}
	});
	
	$(".hd-checkbox input[type=checkbox]").on("click",function(){
		if($(this).is(":checked")){
    		$(this).addClass("checked");
    	}else{
    		$(this).removeClass("checked");
    	}
	});
	
})

;(function(){
	
	$.tips = function(opt){
		var defaults = {
			//type: 'success',
			content: '成功！',
			callback: {}
		};
		var sets = $.extend(defaults,opt||{});
		var $wrap = $('<div class="tips-wrap">');
		$wrap.html(sets.content);
		if($(".tips-wrap").length>1) return false;
		$("body").append($wrap);
		setTimeout(function(){
			$wrap.fadeOut(400,function(){
				$wrap.remove();
				if(typeof(sets.callback)=='object') return false;
				sets.callback(true);
				return false;
			})
		},1500)
		
	}
	$.confirms = function(opt,callback){
		var _this = this;
		var txt = opt || "您是否要执行此操作？";
		var $wrap = $('<div class="confirm-wrap">');
		$wrap.append('<div class="confirm-box"><div class="content margin-top margin-bottom-15"><span>'+txt+'</span></div><div class="mui-row padding-top border-top"><div class="mui-col-xs-6 padding-right"><span class="mui-btn cancel">取消</span></div><div class="mui-col-xs-6 padding-left"><span class="mui-btn mui-btn-primary sure">确定</span></div></div></div>');
		$("body").append($wrap);
		var $cancel = $wrap.find(".cancel");
		var $sure = $wrap.find(".sure");
		$cancel.on('tap',function(){
			$wrap.stop().animate({opacity:'0'},300,function(){
				$wrap.remove();
			});
		});
		$sure.on('tap',function(){
			if($.isFunction(callback)){
				callback(true);
				$wrap.remove();
			}
		});
	}
	
	$.numbers = function(){
		$(".number").each(function(){
			$(this).numberSet();
		});
	}
	
	$.fn.numberSet = function(){
		var $this = this;
		var $dec = $this.find(".num-decrease");
		var $inc = $this.find(".num-increase");
		var $input = $this.find(".num-input");
		var max = parseInt($input.data("max")) || 999;
		var min = 1;
		$dec.on('tap',function(){
			var val = parseInt($input.val());
			if(val>min){
				$input.val(val-1);
			}else{
				$input.val(1);
			}
			numsInput();
		});
		$inc.on('tap',function(){
			var val = parseInt($input.val());
			if(val<max){
				$input.val(val+1);
			}else{
				$input.val(max);
			}
			numsInput();
		});
		$input.on("blur",function(){
			var val = $(this).val();
			if(val>max){
				$(this).val(max);
			}else if(val<1){
				$(this).val(1);
			}
			numsInput();
		});
		function numsInput(){
			var val = parseInt($input.val());
			if(val>=max){
				$dec.removeClass("disabled");
				$inc.addClass("disabled");
			}else if(val<=1){
				$dec.addClass("disabled");
				$inc.removeClass("disabled");
			}else{
				$dec.removeClass("disabled");
				$inc.removeClass("disabled");
			}
		}
		numsInput();
	}
	
	$.checkedAll = function(options){
		var opt = $.extend({
			master: undefined, 	//全选控件
			child: undefined	//可选控件
        },options);
        if(!opt.master) throw new Error("请指定master对象！");
        if(!opt.child) throw new Error("请指定child对象！");
        opt.master.on('click',function(){
        	if (opt.child.children("input:not(:disabled)").length == 0) {
    			opt.master.prop("checked",false).removeClass("checked");
        		return false;
    		}
        	if($(this).is(":checked")){
        		opt.child.children("input:not(:disabled)").prop("checked",true).addClass("checked");
        	}else{
        		opt.child.children("input:not(:disabled)").prop("checked",false).removeClass("checked");
        	}
        });
        opt.child.children("input").on('click',function(){
        	var flog = true;
			$.each(opt.child,function(i){
				if(!$(this).children("input").is(":checked")){
					flog = false
				}
			});
			if(flog){
				opt.master.prop("checked",true).addClass("checked");
			}else{
				opt.master.prop("checked",false).removeClass("checked");
			}
       	});
	}
	
	$.fn.verificationCode = function(options){
		var timer = options || 60;
		var $this = this;
		if(!$this.hasClass("waiting")){
    		$this.addClass("waiting");
    		$this.html(timer+"s 后再试");
    		var interval =  setInterval(function(){
    			timer--;
    			$this.html(timer+"s 后再试");
    			if(timer==0){
    				window.clearInterval(interval);
    				$this.html("发送验证码").removeClass("waiting");
    			}
    		},1000);
    		return true;
    	}else{
    		return false;
    	}
	}
	
})(jQuery);