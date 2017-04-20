$(function() {
	var sideTop;
	var height;
	var maxNum;
	var num = 0;
	var sh;

	//显示与隐藏侧边栏
	$(".ico-left").click(function() {
		if ($(this).attr('class') == "ico-left") {
			$(this).addClass("close-left");
			$(".side").animate({
				left: "-200px"
			}, 400);
			$("#main").animate({
				left: "0"
			}, 400);
			$(".welcome ul").animate({
				marginLeft: "18px"
			}, 400);
		} else {
			$(this).removeClass("close-left");
			$(".side").animate({
				left: "0"
			}, 400);
			$("#main").animate({
				left: "200px"
			}, 400);
			$(".welcome ul").animate({
				marginLeft: "88px"
			}, 400);
		}
	});

	//上滑
	$(".show-side .top").click(function() {
		num++;
		if (num > maxNum) {
			num = maxNum;
		}
		$(".side-menu-height").animate({
			marginTop: "-" + num * sh + "px"
		}, 100);
	});
	//下滑
	$(".show-side .bottom").click(function() {
		num--;
		if (num <= 0) {
			num = 0;
		}
		$(".side-menu-height").animate({
			marginTop: "-" + num * sh + "px"
		}, 100);
	});

	$("#side-scroll").find("a").click(function() {
		$("#side-scroll").find("a").each(function() {
			$(this).removeClass("focus");
		});
		$(this).addClass("focus");
		$("#main_frame").attr("src", $(this).attr("href"));
		return false;
	})

	$('.form-select-edit').parents(".form-group").each(function(i) {
		$(this).css({
			zIndex: i + 2
		})
	})

	//侧边菜单滑动
	$(window).resize(function() {
			if ($(".copy").length > 0) {
				sideTop = $(".copy").offset().top - 131; //窗口高度
				height = $(".side-menu-height").height(); //菜单高度
				sh = height / $(".side-menu-height").find("li").length; //滚动高度
				//菜单的高度减去向上滚动的距离，未触动滚动效果时为0
				var str = $(".side-menu-height").css("marginTop");
				var numerial = parseFloat(str.substring(0, str.length - 2));
				var no_s_height = height + numerial; //菜单未滚动的区域
				//滚动后放大窗口时触发
				if (sideTop > no_s_height && numerial != 0 && sideTop < height) {
					$(".side-menu-height").css({
						marginTop: numerial + (sideTop - no_s_height) + "px"
					});
					maxNum = Math.ceil((numerial + 14) / sh);
				} else if (sideTop > height) {
					$(".side-menu-height").css({
						marginTop: "0px"
					});
					num = 0;
				} else {
					maxNum = Math.ceil((height - sideTop + 14) / sh);
				}

				if (num > maxNum) {
					num = maxNum;
				}
				//显示隐藏上下箭头
				if (sideTop < height) {
					$(".show-side").css({
						bottom: "144px"
					});
				} else {
					$(".show-side").css({
						bottom: "130px"
					});
				}
			}
		})
		//侧边滑动
	$(window).trigger('resize');
})

$(function() {
	$("input[name=limit]").bind('keyup', function(e) {
		if (e.keyCode == 13) {
			var url = replaceParamVal('limit', $(this).val());
			console.log(url);
			window.location.href = url
			return false;
		}
	});


})

/**
 * 替换URL目标参数值
 * @param  {string} arg 参数名
 * @param  {string} val 参数值
 * @param  {string} url 目标地址
 * @return {string}
 */
function replaceParamVal(arg, val, url) {
	url = url || this.location.href.toString();
	var pattern = arg + '=([^&]*)';
	var replaceText = arg + '=' + val;
	if (url.match(pattern)) {
		var tmp = '/(' + arg + '=)([^&]*)/gi';
		tmp = url.replace(eval(tmp), replaceText);
		return tmp;
	} else {
		if (url.match('[\?]')) {
			return url + '&' + replaceText;
		} else {
			return url + '?' + replaceText;
		}
	}
	return url + '\n' + arg + '\n' + val;
}


$(function() {
		//加入自定义快捷菜单
		$('.fixed-nav .first a').on('click', function() {
			$.post(menuaddurl, {
				'_m': SYS_MODULE_NAME,
				'_c': SYS_CONTROL_NAME,
				'_a': SYS_METHOD_NAME
			}, function(data) {
				_progress(data.message);
			}, 'json');
			refresh_diymenu();
		})
	})
	//刷新自定义菜单
function refresh_diymenu() {
	$.post(menurefreshurl, '', function(data) {
		$('#diy_menu', window.parent.document).html(data);
	}, "json")
}

function _progress(content) {
	var d = dialog({
		id: 'trip',
		padding: 30,
		align: 'bottom left',
		content: '' + content + '',
		quickClose: true
	}).show();
	setTimeout(function() {
		d.close().remove();
	}, 1000);
}

(function($){
    $.fn.FontScroll = function(options){
        var d = {time: 3000,s: 'fontColor',num: 1}
        var o = $.extend(d,options);


        this.children('ul').addClass('line');
        var _con = $('.line').eq(0);
        var _conH = _con.height(); //滚动总高度
        var _conChildH = _con.children().eq(0).height();//一次滚动高度
        var _temp = _conChildH;  //临时变量
        var _time = d.time;  //滚动间隔
        var _s = d.s;  //滚动间隔


        _con.clone().insertAfter(_con);//初始化克隆

        //样式控制
        var num = d.num;
        var _p = this.find('li');
        var allNum = _p.length;
        if(allNum == num + 1) return false;

        _p.eq(num).addClass(_s);


        var timeID = setInterval(Up,_time);
		this.hover(function(){clearInterval(timeID)},function(){timeID = setInterval(Up,_time);});

        function Up(){
            _con.animate({marginTop: '-'+_conChildH});
            //样式控制
            _p.removeClass(_s);
            num += 1;
            _p.eq(num).addClass(_s);

            if(_conH == _conChildH){
                _con.animate({marginTop: '-'+_conChildH},"normal",over);
            } else {
                _conChildH += _temp;
            }
        }
        function over(){
            _con.attr("style",'margin-top:0');
            _conChildH = _temp;
            num = 1;
            _p.removeClass(_s);
            _p.eq(num).addClass(_s);
        }
    }
    
    $.hdLoad = {
    	start: function(){
    		if($("#hd-load-tips").length > 0){
    			this.loading();
    			return;
    		}
			if(typeof this.elems == "undefined") this.elems = [];
    		var name = 'hd-load-' + Date.now();
    		this.elems.push(name);
    		$('body').append('<div class="hd-load-tips '+ name +'"><div class="load-tips-text">开始加载！</div><div class="load-tips-bg"></div></div>');
    		this.loading();
    	},
    	loading: function(){
    		$('.'+this.elems[0]).children(".load-tips-text").html('加载中，请稍后...');
    	},
    	end: function(){
    		var that = this;
    		$('.'+this.elems[0]).children(".load-tips-text").html('加载完成!');
			setTimeout(function(){
				$('.'+that.elems[0]).animate({opacity: 0},300,function(){
					$('.'+that.elems[0]).remove();
					that.elems.splice(0,1);
				});
			},300);
		},
		error: function(){
			var that = this;
			$('.'+this.elems[0]).children(".load-tips-text").html('加载失败!').css({color: red});
			setTimeout(function(){
				$('.'+that.elems[0]).animate({opacity: 0},300,function(){
					$('.'+that.elems[0]).remove();
					that.elems.splice(0,1);
				});
			},300)
		}
    }
    
})(jQuery);

function message(t){
	var d = dialog({
	    content: '<div class="padding-large-left padding-large-right padding-top padding-bottom bg-white text-small">'+t+'</div>'
	});
	d.show();
	setTimeout(function () {
	    d.close().remove();
	}, 1000);
}
// try{if (window.console && window.console.log) {console.clear();console.log("\u6b22\u8fce\u4f7f\u7528\u8fea\u7c73\u76d2\u5b50\u65d7\u4e0b\u6d77\u76d7\u7535\u5546\u7cfb\u7edf\uff1a");console.log("\x25\x63\x31\uff0c\u4e00\u6b3e\u514d\u8d39\u5f00\u6e90\u7684\u7535\u5b50\u5546\u52a1\u5e73\u53f0\u7ba1\u7406\u7cfb\u7edf\n\x32\uff0c\u57fa\u4e8e\u76ee\u524d\u6700\u6d41\u884c\u7684\x57\x45\x42\x32\x2e\x30\u7684\u67b6\u6784\uff08\x70\x68\x70\x2b\x6d\x79\x73\x71\x6c\uff09\n\x33\uff0c\u5f53\u524d\u6700\u6d41\u884c\u7684\u4f01\u4e1a\u7ea7\u7535\u5b50\u5546\u52a1\u7ba1\u7406\u7cfb\u7edf\n\x34\uff0c\u62e5\u6709\u8d85\u5f3a\u7684\u6269\u5c55\u6027\u80fd\u548c\u6d3b\u8dc3\u7684\u7b2c\u4e09\u65b9\u5f00\u53d1\u8005\n\x35\uff0c\u826f\u597d\u7684\u5f00\u53d1\u6846\u67b6\u3001\u6587\u6863\uff0c\u8f7b\u677e\u6269\u5c55\u3001\u5b9a\u5236\u79c1\u6709\u529f\u80fd\n","\x63\x6f\x6c\x6f\x72\x3a\x67\x72\x65\x65\x6e\x3b\x6c\x69\x6e\x65\x2d\x68\x65\x69\x67\x68\x74\x3a\x32\x35\x70\x78\x3b");console.log("\x25\x63\u5b98\u65b9\u7f51\u7ad9\uff1a\x68\x74\x74\x70\x3A\x2F\x2F\x77\x77\x77\x2E\x68\x61\x69\x64\x61\x6F\x2E\x6C\x61","\x63\x6f\x6c\x6f\x72\x3a\x72\x65\x64");console.log("\x25\x63\u5b98\u65b9\u8bba\u575b\uff1a\x68\x74\x74\x70\x3A\x2F\x2F\x62\x62\x73\x2E\x68\x61\x69\x64\x61\x6F\x2E\x6C\x61","\x63\x6f\x6c\x6f\x72\x3a\x62\x6c\x75\x65")}}catch(e){}