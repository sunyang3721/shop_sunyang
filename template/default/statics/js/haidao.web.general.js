$(window).load(function(){
	
	//设为首页
	$(".fun-homepage").click(function(){ 
        if(document.all){
        	document.body.style.behavior = 'url(#default#homepage)'; 
        	document.body.setHomePage(document.URL); 
        }else{
			$.dialogTip({content: '非 IE 浏览器请手动将本站设为首页'});
        }
	});
	//加入收藏
	$(".fun-favorite").click(function(){
		var sURL=document.URL; 
		var sTitle=document.title; 
		try {window.external.addFavorite(sURL, sTitle);} 
		catch(e){ 
			try{window.sidebar.addPanel(sTitle, sURL, "");} 
			catch(e){
				$.dialogTip({content: '请按 Ctrl+D 键添加到收藏夹'});
			}
		}
	});
	
	//返回顶部
	$(".fun-backtop").click(function(){$('body,html').animate({scrollTop:0},1000);return false;});
	//刷新页面
	$(".fun-refresh").click(function(){
		window.location.reload();
	});
	//打印
	$(".fun-print").click(function(){
		window.print();
	});
	//关闭窗口
	$(".fun-close").click(function(){
		window.close();
	});
	
	//内部内容的显示与隐藏
	$(".fun-show").hover(function(){
		$(this).addClass("show");
	},function(){
		$(this).removeClass("show");
	});
	
	/*$('.pub_input').bind('focus blur mouseover mouseout', function(event) {
        switch(event.type) {
            case 'focus':
                $(this).attr('checkFlag','TRUE');
                $(this).attr('style','border:1px solid #2a95de;background-color:#e9fbfe;color:#000000;');
                break;
            case 'blur':
                $(this).attr('checkFlag','FALSE');
                $(this).attr('style','color:#000000;');
                break;
            case 'mouseout':
                if ($(this).attr('checkFlag') != 'TRUE'){
                    $(this).attr('style','border:1px cccccc;background-color:none;color:#000000;');
                }
                break;
            default:
                if ($(this).attr('checkFlag') != 'TRUE'){
                    $(this).attr('style','border:1px solid #a6a6a6;');
                }
        }
    });*/
});

;(function($){
	
	$.fn.sliderRoll = function(options){
		
		options = jQuery.extend({
			time: 300,
			mouseover: false
        },
        options);
        
		for(var i=0;i<this.length;i++){
			this.eq(i).attr("data-num","0");
			this.eq(i).sliderAction(options);
		}
		
	}
	
	$.fn.sliderAction = function(opt){
		
		var _this = this;
		var _prev = this.find(".prev");
		var _next = this.find(".next");
		var roll = this.find("ul");
		var _width = roll.children("li").outerWidth(true);
		var _normal = roll.children("li").width();
		var num = 0;
		var rec_btn=this.parents().find("#rec_btn");
	    rec_btn.on('click',function(){
	        num=0;
	        disabledBtn();
	    })
		
		roll.children("li").eq(0).addClass("current");
		roll.width(_width*roll.children("li").length);
		
		var _error = Math.abs(roll.parent().width()-roll.width());
		if(_error<_width){
			_prev.addClass("disabled");
			_next.addClass("disabled");
		}else{
			_prev.addClass("disabled");
		}
		
		_prev.bind('click',function(){
			if(!$(this).hasClass("disabled")){
				if(num>0&&parseInt(roll.css("left"))!=0){
					num--;
				}
				roll.stop(true,false).animate({left: "-"+_width*num+"px"},opt.time,function(){
					disabledBtn();
				});
			}else{
				return false;
			}
		});
		
		_next.bind('click',function(){
			if(!$(this).hasClass("disabled")){
				var dif = Math.abs(roll.parent().width()-roll.width());
				var _left = parseInt(roll.css("left"));
				if(num<roll.children("li").length&&dif>Math.abs(_left-_normal)){
					num++;
				}
				roll.stop(true,false).animate({left: "-"+_width*num+"px"},opt.time,function(){
					disabledBtn();
				});
			}else{
				return false;
			}
		});
		
		if(opt.mouseover==true){
			roll.find("li").bind('mouseover',function(){
				$(this).addClass("current").siblings().removeClass("current");
			})
		}
		
		function disabledBtn(){
			_prev.removeClass("disabled");
			_next.removeClass("disabled");
			if(Math.abs(parseInt(roll.css("left")))==0){
				_prev.addClass("disabled");
			}else if(_error-Math.abs(parseInt(roll.css("left")))<_width){
				_next.addClass("disabled");
			}
		}
		
	}
	
	//单个选项卡调用
	$.fn.tabs = function(options){
		
		options = jQuery.extend({
			tag: ".tab-tag",
			eventname: "click",
			show: 1
        },
        options);
        
		var _this = this;
		var _con = this.next(options.tag);
		this.find("li").eq(options.show-1).addClass("current").siblings().removeClass("current");
		_con.find('.tag').eq(options.show-1).addClass("selected").siblings().removeClass("selected");
		
		this.find("li").bind(options.eventname,function(){
			$(this).addClass("current").siblings().removeClass("current");
			_con.find('.tag').eq($(this).index()).addClass("selected").siblings().removeClass("selected");
		});
		
	}
	
	//多几个选项卡调用
	$.fn.multipleTabs = function(options){
		
		options = jQuery.extend({
			tag: ".tab-tag",
			eventname: "click",
			show: 1
        },
        options);
		
		for(var i=0;i<this.length;i++){
			this.eq(i).tabs({
				tag: options.tag,
				eventname: options.eventname,
				show: options.show
			})
		}
		
	}
	
	//弹窗
	$.fn.usePopup = function(){
		$(this).fadeIn(100);
		var _this = this;
		var _width = $(this).find(".popup-cont").width()+10;
		var _height = $(this).find(".popup-cont").height()+10;
		$(this).children(".popup-wrap").css({marginLeft:"-"+(_width/2)+"px",marginTop:"-"+(_height/2)+"px"});
		
		$(this).find(".close").click(function(){
			_this.fadeOut(100);
		});
		$(".popup-btn").children(".button").click(function(){
			if(!$(this).hasClass("ban")){
				$(this).parents(".popup").fadeOut(100);
			}
			return false;
		});
	}
	
	//slider
	$.fn.sliderShade = function(options){
		
		options = jQuery.extend({
			width: 1200,
			thumb: true,
			current: 0,
			arrow: true,
			speed: 400,
			playtime: 3000
        },
        options);
		
		var _this = this;
		var box_w = $(window).width();
		var _width = options.width-(box_w-options.width)/2;
		var _child = this.children(".box");
		var _len = this.find(".item").length;
		var num = 1;
		
		if(options.arrow){
			var arrow = '';
			arrow += '<div class="shade-left" style="width: '+(box_w-options.width)/2+'px; left: '+-(box_w-options.width)/2+'px;"><a class="prev" href="javascript:;"></a></div>';
			arrow += '<div class="shade-right" style="width: '+(box_w-options.width)/2+'px; right: '+-(box_w-options.width)/2+'px;"><a class="next" href="javascript:;"></a></div>';
			_child.before(arrow);
		}
		
		if(options.thumb){
			var thumb = '';
			this.find(".item").each(function(i){
				if(i==options.current){
					thumb += '<li class="current"></li>';
				}else{
					thumb += '<li></li>';
				}
			});
			this.append('<div class="thumb"><ul>'+thumb+'</ul></div>')
		}
		
		function init(){
			_child.css({
				width:options.width*(_this.find(".item").length+2)+"px"
			});
			var _first = _this.find(".item:first").clone();
			var _last = _this.find(".item:last").clone();
			_first.appendTo(_child);
			_last.prependTo(_child);
		}
		init();

		
		this.find('.thumb ul li').mouseover(function(e){
			num = $(this).index()+1;
			$(this).addClass('current').siblings('li').removeClass('current');
			_child.stop().animate({left:-($(this).index()+1)*options.width},options.speed);
			e.stopPropagation();
		});

		this.find(".prev").live('click',function(){
			if(_child.is(":animated")) return false;
			num--;
			if(num<1){
				num=_len;
				_child.css({left:-(_len+1)*options.width+"px"});
				_child.animate({left:-num*options.width},options.speed);
			}else{
				_child.animate({left:-num*options.width},options.speed);
			}
			_this.find('.thumb ul li').eq(num-1).addClass('current').siblings('li').removeClass('current');
			return false;
		});
		
		this.find(".next").live('click',function(){
			if(_child.is(":animated")) return false;
			num++;
			if(num>_len){
				num=1;
				_child.css({left:"0"});
				_child.animate({left:-num*options.width},options.speed);
			}else{
				_child.animate({left:-num*options.width},options.speed);
			}
			_this.find('.thumb ul li').eq(num-1).addClass('current').siblings('li').removeClass('current');
			return false;
		});
		
		var interval = setInterval(autoPlay,options.playtime);
		
		this.parent().hover(function(){
			clearInterval(interval);
		},function(){
			interval = setInterval(autoPlay,options.playtime);
		});
		
		function autoPlay(){
			if(num>_len-1) num = 0;
			_this.find('.thumb ul li').eq(num).trigger("mouseover");
		}
		
	}


	$.tips = function(options){
		var defaults = {
			icon: 'normal',
			content: '成功！',
			callback: function(){}
		};
		var sets = $.extend(defaults,options||{});
		var id = parseInt(Math.random()*100);
		var tip = '<div class="tips" id="tip_'+id+'">';

		tip += '<div class="'+ sets.icon +'"><span><b></b>'+sets.content+'</span></div>';
		
		$('body').append(tip);
		$("#tip_"+id).css({marginLeft:-$("#tip_"+id).width()/2}).fadeIn(300)
		setTimeout(function(){
			$("#tip_"+id).animate({opacity:'0'},300,function(){
				$("#tip_"+id).remove();
				sets.callback(true);
				return false;
			});
		},1500);
	}

	//倒计时插件，使用时间戳
	$.fn.timer = function(){
		var $this = $(this);
		window.setInterval(function(){
			$this.each(function(){
				var timer = $(this).data("time");//总时间
				if(timer<=0) return false;
				timer--;
				var d = parseInt(timer/3600/24);
			    var h = parseInt(timer/3600%24);
			    var m = parseInt(timer%3600/60);
			    var s = timer%60;
			    $(this).children(".d").text((d>9?d:"0"+d));
			    $(this).children(".h").text((h>9?h:"0"+h));
			    $(this).children(".m").text((m>9?m:"0"+m));
			    $(this).children(".s").text((s>9?s:"0"+s));
				$(this).data("time",timer)
			});
		},1000);
	}
	
	$.dialogTip = function(options){
		var options = jQuery.extend({
			content: '提示信息内容',
			title: '提示信息',
			cancelText: '关闭'
        },
        options);
		top.dialog({
			content : '<div class="padding-large">'+options.content+'</div>',
			title: options.title,
			fixed: true,
			cancelValue: options.cancelText,
			cancel: function(){}
		}).showModal();
	}
	
	$.dialogConfirm = function(options){
		var options = jQuery.extend({
			content: '提示信息内容',
			title: '提示信息',
			callback: function(){
				
			}
        },
        options);
        top.dialog({
			content : '<div class="padding-large">'+options.content+'</div>',
			title: options.title,
			fixed: true,
			okValue: '确定',
			ok: function(){
				options.callback(1);
			},
			cancelValue: '取消',
			cancel: function(){}
		}).showModal();
	}
	
})(jQuery)
























