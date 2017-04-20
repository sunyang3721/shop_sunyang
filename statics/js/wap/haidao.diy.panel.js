define("text!panel/global.html", [], function(){
	return '<div class="logo mui-pull-left"><img src="<%:=logo%>" height="30"></div><h1 class="mui-title"<%if(headbg != \'#0068b7\'){%> style="background-color: <%:=headbg%>"<%}%>><%:=title%></h1>\n	<%if(bgcolor != \'#f9f9f9\'){%>\n	<style>.app-preview .app-field { background-color: <%:=bgcolor%> !important; }</style>\n<%}%>';
}),
define("text!panel/ads.html", [], function(){
	return '<%if(show == 0){%>\n	<div class="mui-slider">\n	<div class="mui-slider-group">\n	<%for(var i=0;i<imgs.length;i++){%>\n	<a class="mui-slider-item" href="<%if(imgs[i].url){%><%:=imgs[i].url%><%}else{%>javascript:;<%}%>">\n	<img src="<%:=imgs[i].src%>">\n<%if(imgs[i].title){%>\n	<p class="mui-slider-title"><%:=imgs[i].title%></p>\n<%}%>\n</a>\n	<%}%>\n	</div>\n	<div class="mui-slider-indicator"><%for(var ind=0;ind<imgs.length;ind++){%>\n<div class="mui-indicator<%if(ind==0){%> mui-active<%}%>"></div>\n	<%}%>\n	</div>\n	</div>\n	<%}else{%>\n	<ul class="custom-image mui-clearfix">\n	<%for(var i=0;i<imgs.length;i++){%>\n	<li<%if(size==1){%> class="custom-image-small"<%}%>><a href="<%if(imgs[i].url){%><%:=imgs[i].url%><%}else{%>javascript:;<%}%>"><img src="<%:=imgs[i].src%>">\n<%if(imgs[i].title){%>\n	<p><%:=imgs[i].title%></p>\n<%}%>\n</a></li>\n	<%}%>\n</ul>\n	<%}%>';
}),
define("text!panel/goods-list.html", [], function(){
	return '<ul class="custom-goods-items<%:=classname%> clearfix">\n	<%for(var i=0;i<length;i++){%><li class="goods-item-list"><div class="list-item"><div class="list-item-pic"><a href="#"><img src="statics/images/wap/goods_<%:=i+1%>.gif"></a></div><div class="list-item-bottom"><div class="list-item-title"><a href="#">此处显示商品名称</a></div><div class="list-item-text"><span class="price-org">￥98.00</span></div></div></div></li><%}%>\n</ul>';
}),
define("text!panel/cube.html", [], function(){
	return '<table class="cube-table">\n	<%for(var i=0;i<layout.length;i++){%>\n		<tr>\n		<%for(var l=0;l<layout[i].length;l++){%>\n			<%if(layout[i][l].width&&layout[i][l].height){%>\n				<td class="no-empty cols-<%:=layout[i][l].width%> rows-<%:=layout[i][l].height%>" colspan="<%:=layout[i][l].width%>" rowspan="<%:=layout[i][l].height%>">\n				<%if(layout[i][l].href){%>\n					<a href="<%:=layout[i][l].href%>"><img src="<%:=layout[i][l].src%>" /></a>\n				<%}else{%>\n					<img src="<%:=layout[i][l].src%>" />\n				<%}%>\n				</td>\n			<%}else if(layout[i][l].flog){%>\n		<td>&nbsp;</td>\n	<%}%>\n	<%}%>\n		</tr>\n	<%}%>\n</table>';
}),
define("text!panel/nav.html", [], function(){
	return '<ul class="quick-entry-nav hd-grid">\n	<%if(type == 1 || type == 3){%>\n	<%for(var i=0;i<upload.length;i++){%>\n	<li class="hd-col-xs-e5 quick-entry-link">\n	<span class="nav-img"><%if(upload[i].src){%><img src="<%:=upload[i].src%>" /><%}%></span>\n	<%if(upload[i].title){%><span class="title"><%:=upload[i].title%></span><%}%>\n	</li>\n	<%}%>\n	<%}else{%>\n	<%for(var i=0;i<upload.length;i++){%>\n	<li class="hd-col-xs-e4 quick-entry-link">\n	<span class="nav-img"><%if(upload[i].src){%><img src="<%:=upload[i].src%>" /><%}%></span>\n	<%if(upload[i].title){%><span class="title"><%:=upload[i].title%></span><%}%>\n	</li>\n	<%}%>\n	<%}%>\n	</ul>';
}),
define(["require", "template", "base", "text!panel/global.html", "text!panel/ads.html", "text!panel/goods-list.html", "text!panel/cube.html", "text!panel/nav.html"], function(require){
	var t = require("template"),
		b = require("base");
	return {
		global: function(config){
			return t(require("text!panel/global.html"), b.decode(config));
		},
		content: function(config){
			return $.base64.decode(config);
		},
		ads: function(config){
			var _html = '<div class="hd-slider"><div class="hd-slider-group"><a class="hd-slider-item" href="javascript:;"><img src="statics/images/wap/ad.jpg"></a></div></div>';
			if(!config) return _html;
			var d = b.decode(config);
			if(!d.imgs) return _html;
			return t(require("text!panel/ads.html"), d);
		},
		search: function(){
			return '<div class="hd-search"><input type="search" placeholder="搜索商品名称"></div>';
		},
		spacing: function(config){
			var height = 20;
			if(config){
				var d = b.decode(config);
				height = parseInt(d.height);
			}
			return '<div class="custom-white" style="height: '+ height +'px;"></div>';
		},
		goods: function(config){
			var c = {};
				c.size = 0;
				c.goods_number = 6;
			if(config) c = b.decode(config); //有值时直接覆盖初始值
			c.length = 3;	//图片显示数量
			switch(parseInt(c.size)){
				case 0:
					c.classname = ' custom-goods-single';
					break;
				case 2:
					c.classname = ' custom-goods-blend';
					break;
				case 3:
					c.classname = ' custom-goods-row';
					break;
				default:
					c.classname = '';
					c.length = 4;
			}
			return t(require("text!panel/goods-list.html"), c);
		},
		cube: function(config){
			if(!config){
				var $table = '';
				for(var i=0;i<4;i++){
					var tr = '';
					for(var n=0;n<4;n++){
						tr += '<td></td>';
					}
					$table += '<tr>'+tr+'</tr>';
				}
				return '<table class="cube-table"><tbody>'+$table+'</tbody></table>';
			}else{
				var d = b.decode(config);
				d.layout = eval(d.layout);
				for(var x = 0; x < d.layout.length; x++){
					for(var y = 0; y < d.layout[x].length; y++){
						try{
							d.layout[x][y].src = d.imgs[d.layout[x][y].index].src;
							d.layout[x][y].href = d.imgs[d.layout[x][y].index].href;
						}catch(e){
							//TODO handle the exception
						}
					}
				}
				return t(require("text!panel/cube.html"), d);
			}
		},
		nav: function(config){
			if(config){
				config = b.decode(config);
				var count = 4;
				if(config.type == 1) count = 5;
				if(config.type == 2) count = 8;
				if(config.type == 3) count = 10;
				if(config.upload.length > count){
					config.upload = config.upload.slice(0, count);
				}else if(config.upload.length < count){
					var len = count - config.upload.length;
					for(var l = 0; l < len; l++){
						config.upload.push({"src":"","title":"","url":""});
					}
				}
			}else{
				config = {};
				config.type = 0;
				config.upload = [
					{"src":"","title":"","url":""},
					{"src":"","title":"","url":""},
					{"src":"","title":"","url":""},
					{"src":"","title":"","url":""}
				]
			}
			return t(require("text!panel/nav.html"), config);
		},
		notice: function(config){
			var src = ['statics/images/wap/img_notice_jd.png', 'statics/images/wap/img_notice_tmall.png'];
			if(config){
				config = b.decode(config);
				var html = '<div class="notice-img"><img src="'+ (config.upload[0].src ? config.upload[0].src : src[config.style]) +'" /></div>';
				if(config.style == 0){
					var t = (config.title ? config.title : '公告标题，如果超出宽度用省略号');
					html = '<a class="custom-notice custom-notice-jd" href="">'+ html +'<div class="notice-text mui-ellipsis" style="color: '+ config.color +';">'+ t +'</div></a>';
				}else if(config.style == 1){
					var t = (config.title ? config.title : '天猫超市买2付1，特价纸尿裤立省100');
					var s_t = (config.subtitle ? config.subtitle : '立省100+0.01元疯抢');
					html = '<a class="custom-notice custom-notice-tmall" href="">'+ html +'<div class="notice-text mui-ellipsis"><div class="notice-title"><div class="main-title" style="color: '+ config.color +';">'+ t +'</div><div class="sub-title" style="color: '+ config.subcolor +';">'+ s_t +'</div></div></div></a>';
				}
				return html;
			}else{
				return '<a class="custom-notice custom-notice-jd" href=""><div class="notice-img"><img src="statics/images/wap/img_notice_jd.png" /></div><div class="notice-text mui-ellipsis">公告标题，如果超出宽度用省略号</div></a>'
			}
		},
		menu: function(config){
			config = b.decode(config);
			var html = '';
			if(config.style == 1){
				if(config.upload){
					for(var i=0;i<config.upload.length;i++){
						if(i != 0){
							html += '<li class="nav-item"><a href="javascript:;" style="background-image: url('+ config.upload[i].src +');"></a></li>'
						}
						if(parseInt(config.upload.length / 2) == i){
							html += '<li class="nav-item nav-main-item"><a href="javascript:;" style="background-image: url('+ config.upload[0].src +'); border-color: '+ config.bgcolor +';"></a></li>';
						}
					}
				}else{
					for(var i=0;i<5;i++){
						if(i != 0){
							html += '<li class="nav-item"><a href="javascript:;"></a></li>'
						}
						if(parseInt(5 / 2) == i){
							html += '<li class="nav-item nav-main-item"><a href="javascript:;""></a></li>';
						}
					}
				}
				html = '<div class="nav-menu-2 nav-menu" style="background-color: '+ config.bgcolor +';"><ul class="nav-pop-sub">'+ html +'</ul></div>';
			}else{
				html = '<div class="nav-main-item"><a href="javascript:;" class="home">主页</a></div>';
				var list = '';
				if(config.menu){
					var len = config.menu.length;
					for(var i=0;i<len;i++){
						var sub = '';
						if(config.submenu){
							for(var l=0;l<config.submenu.length;l++){
								if(config.submenu[l] && config.submenu[l].id == i && config.submenu[l].title){
									var submenu = config.submenu[l];
									if(typeof submenu.title == 'object'){
										for(var s=0;s<submenu.title.length;s++){
											sub += '<li><a href="'+ (submenu.url[s]?submenu.url[s]:'javascript:;') +'" target="_blank">'+ submenu.title[s] +'</a></li>';
										}
									}else{
										sub += '<li><a href="'+ (submenu.url?submenu.url:'javascript:;') +'" target="_blank">'+ submenu.title +'</a></li>';
									}
									sub = '<div class="submenu"><span class="arrow"></span><div class="js-nav-2nd-region"><ul>'+ sub +'</ul></div></div>';
								}
							}
						}
						list += '<div class="nav-item"><a class="mainmenu" href="'+ (config.menu[i].url?config.menu[i].url:'javascript:;') +'"><span class="txt">'+ config.menu[i].title +'</span></a>'+ sub +'</div>';
					}
					html = '<div class="nav-menu-1 nav-menu">'+ html +'<div class="nav-items-wrap"><div class="nav-items item-'+ len +'">'+ list +'</div></div></div>';
				}else if(!config.menu && config.upload){
					html = '<div class="nav-menu-1 nav-menu">'+ html +'<div class="nav-items-wrap"><div class="nav-items item-3"><div class="nav-item"><a class="mainmenu" href="javascript:;"><span class="txt">全部商品</span></a></div><div class="nav-item"><a class="mainmenu" href="javascript:;"><span class="txt">购物车</span></a></div><div class="nav-item"><a class="mainmenu" href="javascript:;"><span class="txt">标题</span></a></div></div></div></div>';
				}else{
					html = '<div class="nav-menu-1 nav-menu no-menu">'+ html +'</div>';
				}
			}
			return html;
		}
	}
})