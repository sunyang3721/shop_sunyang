$(function(){
	
	//banner轮播
	$(".shade-slider").sliderShade({
		width: 1200,
		playtime: 3000
	});
	
	//选项卡切换
	$(".tab").multipleTabs({
		tag: ".tab-tag",
		eventname: "mouseover"
	});
	
	//热销榜
	$(".top-con li").live('mouseover',function(){
		$(this).addClass("hover").siblings().removeClass("hover");
	});
	
	//楼层导航
	$(".floor-nav li").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});
	//楼层导航切换
	$(".floor-nav li").click(function(){
		$(this).addClass("current").siblings().removeClass("current");
		$('html,body').stop().animate({scrollTop:$(".floor").eq($(this).index()).offset().top+20},300);
	});
	
	floorNav();
	
});

function floorNav(){
	
	var $floorNav = $(".floor-nav");
	$floorNav.css({marginTop:-$floorNav.height()/2+"px",left:($(window).width()-1200)/2-$floorNav.width()-5+"px"});
	$floorNav.find("li:first").addClass("current");

	$(window).scroll(function(){
		$floorNav.css({left:($(window).width()-1200)/2-$floorNav.width()-5});
		setTimeout(function(){
			var r = new Array();
			$(".floor").each(function(i){
				r.push($(this).offset().top-$(window).height()/2+$(".floor-nav").height()-20);
			});
			var t = $(document).scrollTop();
			var num;
			
			var h = $(".floor:first").eq(0).offset().top-$(window).height()+20;
			if(t<h){
				$(".floor-nav").hide();
			}else{
				$(".floor-nav").show();
			}
			$.each(r,function(i){
				if(t>=r[i]) num = i;
			});
			if(num!=undefined){
				$(".floor-nav li").eq(num).addClass("current").siblings("li").removeClass("current");
			}
		},400);
	});
	
	$(window).resize(function(){
		var _w = 1210+$floorNav.width()*2;
		if($(window).width()<_w){
			$floorNav.css("left",($(window).width()-_w)/2);
		}else{
			$floorNav.css("left",($(window).width()-1200)/2-$(".floor-nav").width()-5);
		}
		$(window).scroll();
	});
	
	$(window).resize();
	
}