define("text!create/input.html", [], function(){
	return '<%for(var i=0;i<group.length;i++){%>'
		+'		<div class="control-group">'
		+'			<span class="control-label"><%if(group[i].required){%><em class="required">*</em><%}%><%:=group[i].label%></span>'
		+'			<div class="controls">'
		+'				<input class="input" type="<%:=group[i].type%>" name="<%:=group[i].name%>" value="<%:=group[i].value%>" placeholder="<%:=group[i].placeholder%>"<%if(group[i].required){%> data-validate="<%:=group[i].required%>"<%}%> />'
		+'				<%if(group[i].type === \'color\'){%>\n	<input class="button color-reset" type="button" data-reset="<%:=group[i].initvalue%>" value="重置" />\n	<%}%>'
		+'			</div>'
		+'		</div>'
		+'	<%}%>';
}),
define("text!create/radio.html", [], function(){
	return '<%for(var i=0;i<group.length;i++){%>'
		+'	<div class="control-group">'
		+'		<span class="control-label"><%:=group[i].label%></span>'
		+'		<div class="controls">'
		+'			<%for(var v=0;v<group[i].value.length;v++){%>'
		+'				<label class="radio list"><input type="<%:=group[i].type%>" name="<%:=group[i].name%>" value="<%:=v%>"<%if(v==group[i].checked){%> checked="checked"<%}%> /><%:=group[i].value[v]%></label>'
		+'			<%}%>'
		+'		</div>'
		+'	</div>'
		+'	<%}%>';
}),
define("text!create/upload.html", [], function(){
	return '<ul class="m-choices-box clearfix">'
	+'	<%for(var i=0;i<group.length;i++){%>'
	+'	<li class="choice js-parents control-loads" data-id="<%:=i%>">'
	+'		<div class="choice-image">'
	+'			<div class="pic-center"><img class="control-load-img" src="<%:=group[i].image%>" /></div>'
	+'			<div class="control-group">'
	+'				<div class="controls">'
	+'					<input type="hidden" class="control-load-input" name="upload[<%:=i%>][src]" value="<%:=group[i].image%>"<%if(imgVerif){%> data-validate="required" data-errortitle="图片"<%}%> />'
	+'				</div>'
	+'			</div>'
	+'		</div>'
	+'		<div class="choice-content m-choice-title">'
	+'			<div class="text-box clearfix">'
	+'				<label class="fr js-choice-btn control-load"><%if(group[i].image){%>替换上传<%}else{%>上传图片<%}%></label>'
	+'				<span class="text-lh-big"><%if(group[i].tip){%><%:=group[i].tip%><%}else{%>左侧图片<br />建议尺寸：100*100像素<%}%></span>'
	+'			</div>'
	+'			<div class="m-choice-link clearfix">'
	+'				<div class="fl link-label">&nbsp;&nbsp;|&nbsp;&nbsp;链接：</div>'
	+'				<div class="fl js-modify-link<%if(group[i].link){%> has-link<%}%>">'
	+'					<%if(group[i].link){%><div class="linkto"><span class="text">[<%:=group[i].linktitle%>]</span><em class="remove">×</em></div><%}%>'
	+'					<div class="dropdown">'
	+'						<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;"><%if(group[i].link){%>修改<%}else{%>选择链接页面<%}%><span class="caret"></span></a>'
	+'						<ul class="dropdown-menu"><%:=hd_link_txt%></ul>'
	+'					</div>'
	+'				</div>'
	+'			</div>'
	+'			<div class="control-group">'
	+'				<div class="controls">'
	+'					<input type="hidden" class="link-title" name="upload[<%:=i%>][linktitle]" value="<%:=group[i].linktitle%>" />'
	+'					<input type="hidden" class="link-val" name="upload[<%:=i%>][url]" value="<%:=group[i].link%>"<%if(linkVerif){%> data-validate="required" data-errortitle="链接"<%}%> />'
	+'				</div>'
	+'			</div>'
	+'		</div>'
	+'	</li>'
	+'	<%}%>'
	+'</ul>';
}),
define("text!create/upload-title.html", [], function(){
	return '<ul class="m-choices-box clearfix">'
	+'	<%for(var i=0;i<group.length;i++){%>'
	+'	<li class="choice js-parents m-title control-loads" data-id="<%:=i%>">'
	+'		<div class="choice-image">'
	+'			<div class="image-box">'
	+'				<%if(group[i].image){%>'
	+'					<div class="pic-center"><img class="control-load-img" src="<%:=group[i].image%>" /></div>'
	+'					<label class="btn control-load replace">替换上传</label>'
	+'				<%}else{%>'
	+'					<label class="btn control-load"></label>'
	+'				<%}%>'
	+'			</div>'
	+'			<div class="control-group">'
	+'				<div class="controls">'
	+'					<input type="hidden" class="control-load-input" name="upload[<%:=i%>][src]" value="<%:=group[i].image%>"<%if(imgVerif){%> data-validate="required" data-errortitle="图片"<%}%> />'
	+'				</div>'
	+'			</div>'
	+'		</div>'
	+'		<div class="choice-content">'
	+'			<div class="control-group">'
	+'				<span class="control-label">标题：</span>'
	+'				<div class="controls">'
	+'					<input class="input" name="upload[<%:=i%>][title]" type="text" value="<%:=group[i].title%>" />'
	+'				</div>'
	+'			</div>'
	+'			<div class="control-group">'
	+'				<span class="control-label">链接：</span>'
	+'				<div class="controls">'
	+'					<div class="js-modify-link<%if(group[i].link){%> has-link<%}%>">'
	+'						<%if(group[i].link){%><div class="linkto"><span class="text">[<%:=group[i].linktitle%>]</span><em class="remove">×</em></div><%}%>'
	+'						<div class="dropdown">'
	+'							<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;"><%if(group[i].link){%>修改<%}else{%>选择链接页面<%}%><span class="caret"></span></a>'
	+'							<ul class="dropdown-menu"><%:=hd_link_txt%></ul>'
	+'						</div>'
	+'					</div>'
	+'					<input type="hidden" class="link-title" name="upload[<%:=i%>][linktitle]" value="<%:=group[i].linktitle%>" />'
	+'					<input type="hidden" class="link-val" name="upload[<%:=i%>][url]" value="<%:=group[i].link%>"<%if(linkVerif){%> data-validate="required" data-errortitle="链接"<%}%> />'
	+'				</div>'
	+'			</div>'
	+'		</div>'
	+'	</li>'
	+'	<%}%>'
	+'</ul>';
}),
define("text!create/style.html", [], function(){
	return '<div class="margin-bottom padding-small-bottom border-bottom text-lh-large clearfix">\n	<span><%:=title%></span>\n	<a class="fr js-template-select" href="javascript:;" data-title="<%:=dialog.title%>" data-tip="<%:=dialog.tip%>" data-modal="<%:=dialog.modal%>">选择模板</a>\n	<input name="<%:=name%>" type="<%:=type%>" value="<%:=value%>" />\n	</div>';
}),
define("text!operation/global.html", [], function(){
	return '<div class="control-group">'
		+'		<span class="control-label"><%:=logo.label%></span>'
		+'		<div class="controls">'
		+'			<div class="control-loads clearfix">\n	<span class="pic-center" style="background-color: <%:=logo.headbg%>;">\n	<img class="control-load-img" src="<%:=logo.src%>" />\n	<input class="control-load-input" type="hidden" name="<%:=logo.name%>" value="<%:=logo.src%>" data-validate="required" />\n	</span>\n	<div class="pic-info"><label class="control-load load text-sub">选择图片</label><p>建议尺寸：100 x 100 像素</p></div>\n	</div>'
		+'		</div>'
		+'	</div>'
}),
define("text!operation/ads.html", [], function(){
	return '<ul class="control-pics clearfix">\n	<%if(vals.length>0){%>\n	<%for(var m=0;m<vals.length;m++){%>\n	<li class="control-pic-list control-loads" data-id="<%:=m%>">\n	<div class="top">\n	<span class="pic-center">\n	<img src="<%:=vals[m].src%>" />\n	</span>\n	</div>\n	<input class="control-load-input" type="hidden" name="imgs[<%:=m%>][src]" value="<%:=vals[m].src%>" />\n	<div class="bottom">\n	<p>标题：<input class="input" name="imgs[<%:=m%>][title]" type="text" value="<%:=vals[m].title%>" /></p>\n	<p>链接：<input class="input" name="imgs[<%:=m%>][url]" type="text" value="<%:=vals[m].url%>" /></p>\n	<div class="handle">\n	<label class="btn control-load">替换上传</label>\n	<a class="btn" href="javascript:;">删除图片</a>\n	</div>\n	</div>\n	</li>\n	<%}%>\n	<%}%>\n	<li class="control-pic-list add uploads"><label class="control-load" data-id="<%:=vals.length%>">新增广告图</label></li>\n	</ul>';
}),
define("text!operation/spacing.html", [], function(){
	return '<div class="control-group">\n	<span class="control-label"><%:=label%></span>\n	<div class="controls">\n	<div class="hd-js-slider">\n	<span class="hd-slider-handle" data-valite="false" style="left: <%:=left%>px"></span>\n	<input type="<%:=type%>" name="<%:=name%>" value="<%:=value%>" />\n	</div>\n	<div class="slider-height">\n	<span class="js-height"><%:=value%></span> 像素\n	</div>\n	</div>\n	</div>';
}),
define("text!operation/goods.html", [], function(){
	return '<%for(var i=0;i<form.length;i++){%>'
		+'	<div class="control-group">'
		+'		<span class="control-label">'
		+'			<%if(form[i].required){%><em class="required">*</em><%}%>'
		+'			<%:=form[i].label%>'
		+'		</span>'
		+'		<div class="controls">'
		+'			<%if(form[i].type==\'hidden\'){%>'
		+'				<%:=form[i].category%>'
		+'				<input type="hidden" name="<%:=form[i].name%>" value="<%:=form[i].value%>" data-validate="required">'
		+'			<%}else{%>'
		+'				<%for(var v=0;v<form[i].value.length;v++){%>'
		+'					<%if(form[i].number){%>'
		+'						<label class="radio list"><input type="<%:=form[i].type%>" name="<%:=form[i].name%>" value="<%:=form[i].value[v]%>"<%if(form[i].value[v]==form[i].checked){%> checked="checked"<%}%>><%:=form[i].value[v]%></label>'
		+'					<%}else{%>'
		+'						<label class="radio list"><input type="<%:=form[i].type%>" name="<%:=form[i].name%>" value="<%:=v%>"<%if(v==form[i].checked){%> checked="checked"<%}%>><%:=form[i].value[v]%></label>'
		+'					<%}%>'
		+'				<%}%>'
		+'			<%}%>'
		+'		</div>'
		+'	</div>'
		+'<%}%>';
}),
define("text!operation/cube.html", [], function(){
	return '<div class="control-group">'
		+'	<span class="control-label">布局：</span>'
		+'	<div class="controls">'
		+'		<table class="tablecloth" data-length="">'
		+'			<%for(var i=0;i<cube.layout.length;i++){%>'
		+'				<tr>'
		+'				<%for(var l=0;l<cube.layout[i].length;l++){%>'
		+'					<%if(cube.layout[i][l].flog){%>'
		+'						<td class="empty" data-x="<%:=l%>" data-y="<%:=i%>"><span>+</span></td>'
		+'					<%}else{%>'
		+'						<%if(cube.layout[i][l].width&&cube.layout[i][l].height){%>'
		+'							<td class="no-empty cols-<%:=cube.layout[i][l].width%> rows-<%:=cube.layout[i][l].height%><%if(!cube.layout[i][l].src){%> index-<%:=cube.layout[i][l].index%><%}%>" colspan="<%:=cube.layout[i][l].width%>" rowspan="<%:=cube.layout[i][l].height%>" data-x="<%:=cube.layout[i][l].x%>" data-y="<%:=cube.layout[i][l].y%>" data-index="<%:=cube.layout[i][l].index%>">'
		+'									<span><%if(cube.layout[i][l].src){%><img src="<%:=cube.layout[i][l].src%>"/><%}else{%><%:=cube.layout[i][l].width*160%>×<%:=cube.layout[i][l].height*160%><%}%></span>'
		+'							</td>'
		+'						<%}%>'
		+'					<%}%>'
		+'				<%}%>'
		+'				</tr>'
		+'			<%}%>'
		+'		</table>'
		+'		<input type="hidden" name="layout" value=\'<%:=JSON.stringify(cube.layout)%>\' />'
		+'		<p class="help-desc">点击 + 号添加内容</p>'
		+'	</div>'
		+'</div>';
}),
define("text!operation/cube-list.html", [], function(){
	return '<%if(cube.len>0){%>'
		+'	<ul class="choices">'
		+'		<%for(var i=0;i<cube.layout.length;i++){%>'
		+'			<%for(var l=0;l<cube.layout[i].length;l++){%>'
		+'				<%if(cube.layout[i][l].width&&cube.layout[i][l].height){%>'
		+'			<li class="choice uploads" data-id="<%:=cube.layout[i][l].index%>"<%if(cube.layout[i][l].index!=0){%> style="display:none;"<%}%>>'
		+'				<div class="control-group">'
		+'					<span class="control-label"><em class="required">*</em>图片：</span>'
		+'					<div class="controls">'
		+'						<div class="control-loads clearfix">'
		+'							<div class="pic-center">'
		+'								<%if(cube.layout[i][l].src){%><img src="<%:=cube.layout[i][l].src%>"><%}%>'
		+'							</div>'
		+'							<input class="control-load-input" type="hidden" name="imgs[<%:=cube.layout[i][l].index%>][src]" value="<%:=cube.layout[i][l].src%>" data-validate="required" />'
		+'							<div class="pic-info">'
		+'								<label class="control-load load text-sub">选择图片</label>'
		+'								<p>建议尺寸：<%:=cube.layout[i][l].width*160%> x <%:=cube.layout[i][l].height*160%> 像素</p>'
		+'							</div>'
		+'						</div>'
		+'					</div>'
		+'				</div>'
		+'				<div class="control-group">'
		+'					<span class="control-label">链接到：</span>'
		+'					<div class="controls">'
		+'						<input class="input" type="text" name="imgs[<%:=cube.layout[i][l].index%>][href]" value="<%:=cube.layout[i][l].href%>">'
		+'					</div>'
		+'				</div>'
		+'				<div class="control-group">'
		+'					<span class="control-label">图片占位：</span>'
		+'					<div class="controls">'
		+'						<div class="dropdown">'
		+'							<a class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><%:=cube.layout[i][l].height%>行 <%:=cube.layout[i][l].width%>列<span class="caret"></span></a>'
		+'							<ul class="dropdown-menu">'
		+'							<%for(var h=1;h<=cube.layout[i][l].height;h++){%>'
		+'								<%for(var w=1;w<=cube.layout[i][l].width;w++){%>'
		+'									<li><a class="image-layout" href="javascript:;" data-width="<%:=w%>" data-height="<%:=h%>"><%:=h%>行 <%:=w%>列</a></li>'
		+'								<%}%>'
		+'							<%}%>'
		+'							</ul>'
		+'						</div>'
		+'					</div>'
		+'				</div>'
		+'				<span class="close-modal" data-x="<%:=l%>" data-y="<%:=i%>">×</span>'
		+'			</li>'
		+'				<%}%>'
		+'			<%}%>'
		+'		<%}%>'
		+'	</ul>'
		+'<%}%>';
}),
define("text!operation/menu-link.html", [], function(){
	return '<div class="m-choice-link js-parents clearfix" data-id="<%:=index%>">'
	    +'	<div class="fl shopnav-item-title">'
		+'      <span class="js-edit-title"><%:=title%></span>'
		+'  	<input name="<%:=name%>[<%:=index%>][title]" type="hidden" value="<%:=title%>" />'
		+'  </div>'
	    +'	<div class="fl link-label">&nbsp;&nbsp;|&nbsp;&nbsp;链接：</div>'
	    +'	<div class="fl js-modify-link<%if(link){%> has-link<%}%>">'
	+'			<%if(link){%><div class="linkto"><span class="text">[<%:=linktitle%>]</span><em class="remove">×</em></div><%}%>'
	    +'		<div class="dropdown">'
	    +'			<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;"><%if(link){%>修改<%}else{%>选择链接页面<%}%><span class="caret"></span></a>'
	    +'			<ul class="dropdown-menu">'+ hd_link_txt +'</ul>'
	    +'		</div>'
	    +'	</div>'
	    +'	<input type="hidden" class="link-title" name="<%:=name%>[<%:=index%>][linktitle]" value="<%:=linktitle%>" />'
	    +'	<input type="hidden" class="link-val" name="<%:=name%>[<%:=index%>][url]" value="<%:=link%>" />'
	    +'	</div>'
}),
define(["require", "template", "base", "wap/haidao.cube", "text!operation/global.html", "text!operation/ads.html", "text!operation/spacing.html", "text!operation/goods.html", "text!operation/cube.html", "text!operation/cube-list.html", "text!create/style.html", "text!create/input.html", "text!create/radio.html", "text!create/upload.html", "text!create/upload-title.html", "text!operation/menu-link.html"], function(require, template, base, layoutMap){
	//涉及到require的循环依赖的问题，因此函数中使用的依赖模块均再次使用require调用，以避免同时调用出现undefined。
	return {
		global: function(data, config){
			var B = require("base"),
				D = B.decode(data);

			for(var i = 0; i < config.libs.form.group.length; i++){
				config.libs.form.group[i].value = D[config.libs.form.group[i].name];
			}

			//logo
			config.libs.form.logo.src = D[config.libs.form.logo.name];
			config.libs.form.logo.headbg = D.headbg;

			var html = template(require("text!create/input.html"), config.libs.form);
			html += template(require("text!operation/global.html"), config.libs.form);

			B.createOpt(html);
			B.upLoad();

		},
		ads: function(data, config){
			var B = require("base");
			config.libs.form.group[0].checked = 0;
			config.libs.form.group[1].checked = 0;
			config.libs.form.vals = [];
			if(data){
				data = B.decode(data);
				if(data.imgs) config.libs.form.vals = data.imgs;
				for(var i = 0; i < config.libs.form.group.length; i++){
					config.libs.form.group[i].checked = data[config.libs.form.group[i].name];
				}
			}
			var html = template(require("text!create/radio.html"), config.libs.form);
			html += template(require("text!operation/ads.html"), config.libs.form);
			B.createOpt(html);
			B.upLoad();

			//删除广告图片
			$(document).on('click', ".control-pics .handle a.btn", function(argument) {
				var tid = $(this).parents("li").data("id");
				$.each($(".sidebar-content .control-pics li"), function() {
					var id = $(this).data("id");
					if(id > tid){
						$.each($(this).find("input"), function(){
							var name = $(this).attr("name").replace(id,id-1);
							$(this).attr("name",name)
						});
					}
				});
				$(this).parents("li").remove();
				require("diy").reload();
			})

		},
		search: function(data, config){
			require("base").createOpt('<p>'+config.libs.form[0].text+'</p><p>'+config.libs.form[0].tip+'</p>');
		},
		spacing: function(data, config){
			var B = require("base");
			config.libs.form.value = 20;
			if(data){
				var D = B.decode(data);
				config.libs.form.value = D.height;
			}
			config.libs.form.left = parseInt((config.libs.form.value - 20) * 2.5);
			B.createOpt(template(require("text!operation/spacing.html"), config.libs.form));

			//辅助空白高度调整工具
			$(".hd-slider-handle").live('mousedown',function(){
				var $next = $(this).next("input");
				var $this = $(this);
				var $width = $this.parent().width()-$this.width();
				$(this).data("valite",true)
				var percent = $width / 100;
			    $(document.body).mousemove(function(event) {
			        if ($this.data("valite") == false) return;
			        var changeX = event.clientX-$this.parent().offset().left;//距离
			       	if(changeX >= $width){
						changeX = $width;
					}else if(changeX<=0){
						changeX = 0;
					}
					$this.css({left: changeX+'px'});
					$next.val(parseInt(changeX/2.5+20));
					$(".app-field.editing").find(".custom-white").css({height:parseInt(changeX/2.5+20)+'px'});
					$(".js-height").html(parseInt(changeX/2.5+20));
			   	});
			});
			$(document.body).mouseup(function() {
				if($(".hd-slider-handle").length<1) return;
				$('.hd-slider-handle').data("valite",false);
			});
		},
		goods: function(data, config){

			var c = config.libs.data;
			var B = require("base");
			var $category = '<a class="goods-category-choose" href="javascript:;">请选择商品分类</a>';

			//商品分类数据
			function getCategoryText(cid){
				for(var k in c){
					if(c[k].id == cid){
						if(c[k].parent_id == 0) return c[k].name;
						return c[k].name+ ',' + getCategoryText(c[k].parent_id);
					}
				}
			}

			//有选择商品分类时，通过商品分类数据获取商品分类的名称
			if(data){
				var D = B.decode(data);
				//存在商品分类ID时执行
				if(D.category){
					var arr = getCategoryText(D.category), txt = '';
					//递归获取商品分类
					if(arr){
						arr = arr.split(",");
						for(var i=arr.length-1;i>=0;i--){
							if(i == arr.length-1){
								txt += arr[i];
							}else{
								txt += " / "+arr[i];
							}
						}
					}else{
						txt = "未找到对应的分类，请重新选择或刷新页面";
						D.category = '';
					}
					$category = '<a class="goods-category-choose has-selected" href="javascript:;">'+ txt +'</a>';
				}
				for(var i = 0;i < config.libs.form.length;i++){
					if(i == 0){
						config.libs.form[i].value = D[config.libs.form[i].name];
						config.libs.form[i].category = $category;
					}else{
						config.libs.form[i].checked = D[config.libs.form[i].name];
					}
				}
			}else{
				for(var i = 0;i < config.libs.form.length;i++){
					if(i == 0){
						config.libs.form[i].category = $category;
						config.libs.form[i].value = '';
					}else{
						if(config.libs.form[i].number){
							config.libs.form[i].checked = config.libs.form[i].value[0];
						}else{
							config.libs.form[i].checked = 0;
						}
					}
				}
			}

			B.createOpt(template(require("text!operation/goods.html"), config.libs));

			//商品列表分类选择处理事件

			//得到分类
			function getCategory(id, pid, level){
				var html = '';
				for(var i in c){
					if(c[i].parent_id == pid){
						if(c[i].id == id){
							html += '<a href="javascript:;" id="cat'+ c[i].id +'" data-id="'+ c[i].id +'" class="focus">'+ c[i].name +'</a>';
						}else{
							html += '<a href="javascript:;" id="cat'+ c[i].id +'" data-id="'+ c[i].id +'">'+ c[i].name +'</a>';
						}
					}
				}
				for(var k in c){
					if(c[k].id == pid){
						getCategory(c[k].id, c[k].parent_id, c[k].level)
					}
				}
				if(html) $(".goods-add-class").children(".focus").eq(level).html(html)
			}

			//打开商品分类弹窗
			$('.goods-category-choose').unbind('click').click(function(){
				B.creatModal('<div class="goods-search-class-content"><div class="goods-add-class clearfix"><div class="root border focus"></div><div class="child border focus"></div><div class="child border focus"></div><div class="child border focus"></div><div class="layout fl margin-top goods-class-choose"><a class="button bg-white fr margin-right" data-type="cancel" href="javascript:;">取消选择</a><a class="button bg-main fr margin-right" data-type="confirm" href="javascript:;">确认选择</a></div></div></div>');
				var val = parseInt($(this).next("input").val());
				var html = '';
				if(val == 0){
					$.each(c, function(){
						if(this.parent_id == 0){
							html += '<a href="javascript:;" id="cat'+ this.id +'" data-id="'+ this.id +'">'+ this.name +'</a>';
						}
					})
					$(".root").html(html);
				}else{
					var level = 0, pid = 0;
					for(var k in c){
						if(c[k].id == val){
							level = c[k].level;
							pid = c[k].parent_id;
						}
					}
					getCategory(val, pid, level);
				}
			});

			//商品分类弹窗商品分类上下级选择
			$(document).on('click', '.goods-add-class .focus a', function(){
				var $this = $(this), $id = $this.data("id");
				if($this.hasClass("disable")) return false;	//禁止选中项直接return
				if(!$this.hasClass("focus")){
			    	$this.parent().nextAll('.child').empty();//清空下一级容器
			    	//非选中状态下，显示下级容器内容
			        $this.addClass("focus").siblings().removeClass("focus");
			        var _html = "";
			        for(var k in c){
			        	if($id == c[k].parent_id){
			        		_html += '<a href="javascript:;" id="cat'+ c[k].id +'" data-id="'+ c[k].id +'">'+ c[k].name +'</a>';
			        	}
			        }
			        if($id == -1){
			        	$(".root").html(_html);
			        }else{
			        	$this.parent().next(".child").html(_html);
			        }
			    }
			});

			//关闭弹窗
			$(document).on('click', '.goods-class-choose a', function(){
				if($(this).data("type") == "confirm"){
					var txt = '', cid = '';
					$('.goods-add-class a.focus').each(function(n){
						if(n == 0){
							txt += $(this).html();
						}else{
							txt += '/' + $(this).html();
						}
						cid = $(this).data("id");
					})
					if(txt && cid){
						$(".sidebar-content .goods-category-choose").addClass("has-selected").html(txt);
						$(".sidebar-content .goods-category-choose").next('input[type=hidden]').val(cid);
					}
				}
				$(".modal-wrap").remove();//删除商品分类弹窗
			});

		},
		cube: function(data, config){
			var B = require("base");
			if(data){
				var D = {}, len = 0;
				D.cube = B.decode(data);
				D.cube.layout = eval(D.cube.layout);

				for(var x = 0; x < D.cube.layout.length; x++){
					for(var y = 0; y < D.cube.layout[x].length; y++){
						if(D.cube.layout[x][y].index != undefined){
							len++;
							try{
								D.cube.layout[x][y].src = D.cube.imgs[D.cube.layout[x][y].index].src;
								D.cube.layout[x][y].href = D.cube.imgs[D.cube.layout[x][y].index].href;
							}catch(e){

							}
						}
					}
				}
				D.cube.len = len;
				var html = template(require("text!operation/cube.html"), D);
					html += template(require("text!operation/cube-list.html"), D);
				B.createOpt(html);
				B.upLoad();
			}else{
				var $table = '';
				for(var i=0;i<4;i++){
					var tr = '';
					for(var n=0;n<4;n++){
						tr += '<td class="empty" data-x="'+n+'" data-y="'+i+'"><span>+</span></td>';
					}
					$table += '<tr>'+tr+'</tr>';
				}
				var $cube = layoutMap.coordinate();
					$cube = JSON.stringify($cube);
				B.createOpt('<div class="control-group"><span class="control-label">布局：</span><div class="controls"><table class="tablecloth" data-length="0"><tbody>'+$table+'</tbody></table><input type="hidden" name="layout" data-validate="cube" value=\''+$cube+'\' /><p class="help-desc">点击 + 号添加内容</p></div></div>');
			}

			$(".tablecloth .empty").unbind().bind('click',function(){
				var x = this.getAttribute("data-x");
				var y = this.getAttribute("data-y");
				if(!$(this).hasClass("selected")){
					layoutMap.startX = x;
					layoutMap.startY = y;
					$('.tablecloth td.empty').removeClass("selected");
					$('.tablecloth td.empty').removeClass("start").children("span").html("+")
					$(this).addClass("start").children("span").html("开始");
					layoutMap.optArea($(this).parents("table").next("input").val());
				}else if($(this).hasClass("selected")){
					layoutMap.endX = x;
					layoutMap.endY = y;
					var $edit = $(".app-field.editing");
					var vals = layoutMap.coordinate($(this).parents("table").next("input").val());
					$(".sidebar-content").find("input[name=layout]").val(JSON.stringify(vals));
					require("diy").reload();
				}
			});

			$(".sidebar-content .tablecloth .empty.selected").unbind().bind('mouseover',function(){
				$(this).addClass("hover").children("span").html("结束");
			});
			$(".sidebar-content .tablecloth .empty.selected").unbind().bind('mouseout',function(){
				if($(this).hasClass("start")){
					$(this).removeClass("hover").children("span").html("开始");
				}else{
					$(this).removeClass("hover").children("span").html("+");
				}
			});

			//选项卡切换
			$(".sidebar-content .tablecloth .no-empty").unbind().bind('click','',function(){
				$(".tablecloth .no-empty").removeClass("current");
				$(this).addClass("current");
				var $id = $(this).data("index");
				$.each($(".sidebar-content .choices .choice"), function() {
					var $this = $(this);
					if($(this).data("id")==$id){
						$(this).slideDown(200,function(){
							$this.css('overflow','');
						});
					}else{
						$(this).slideUp(200);
					}
				});
				return false;
			});

			$('.image-layout').unbind().bind('click',function(){
				var height = this.getAttribute("data-width");
				var width = this.getAttribute("data-height");
				var index = $(this).parents('.choice').data("id");
				var maps = $(this).parents('.choices').prev('.control-group').find("input").val();
				layoutMap.modify(index, width, height, maps);
			});

			$(".sidebar-content .choices .choice .close-modal").unbind().bind('click',function(){
				var parents = $(this).parents('.choices');
				var maps = parents.prev('.control-group').find("input").val();
				var x = $(this).data("x");
				var y = $(this).data("y");
				$(this).parent().remove();
				layoutMap.delete(maps, x, y);
			});
		},
		nav: function(data, config){
			var B = require("base");
			var count = 4;
			var radios = {};
				radios.group = [config.libs.form.type];
				radios.group[0].checked = 0;
			if(data){
				data = B.decode(data);
				radios.group[0].checked = data.type;
				if(parseInt(data.type) == 1) count = 5;
				if(parseInt(data.type) == 2) count = 8;
				if(parseInt(data.type) == 3) count = 10;
			}

			var _upload = config.libs.form.upload;//上传配置

			//create upload
			_upload.group = [];

			for(var i=0;i<count;i++){
				_upload.group[i] = {}

				if(data && data.upload[i]){
					_upload.group[i].image = data.upload[i].src;
					_upload.group[i].link = data.upload[i].url;
					_upload.group[i].title = data.upload[i].title;
					_upload.group[i].linktitle = data.upload[i].linktitle;
				}else{
					_upload.group[i].image = '';
					_upload.group[i].link = '';
					_upload.group[i].title = '';
					_upload.group[i].linktitle = '';
				}
			}

			var html = template(require("text!create/radio.html"), radios);
			html += template(require("text!create/upload-title.html"), _upload);

			B.createOpt(html);
			B.upLoad();

		},
		notice: function(data, config){
			var B = require("base"),
				C = config.libs;		//form数据

			var _style = C.form.style,	//
				_group = {},			//表单组
				_upload = C.form.upload,//上传配置
				tips = ["左侧公告图标<br/>建议尺寸：100*50像素", "左侧公告图标<br/>建议尺寸：100*100像素"]
			//create upload
			_upload.group = [
				{
					image: '',
					link: '',
					linktitle: ''
				}
			];

			if(data){
				data = B.decode(data);
				_upload.group[0].image = data.upload[0].src || '';
				_upload.group[0].link = data.upload[0].url || '';
				_upload.group[0].linktitle = data.upload[0].linktitle || '';
			}
			var s = data.style || 0;	//初始模板风格
			_upload.group[0].tip = tips[s];
			//create style
			_style.value = s;
			_style.title = "当前公告模板：" + C.template[s].title;
			_style.dialog = {};
			_style.dialog.title = '选择公告模块';
			_style.dialog.tip = '请选择公告的风格样式';
			_style.dialog.modal = 'notice';

			//create input group
			_group.group = C.form.group[s];
			for(var i = 0; i < _group.group.length; i++){
				_group.group[i].value = data[_group.group[i].name] || _group.group[i].initvalue || '';
			}

			var html = template(require("text!create/style.html"), _style);
				html += template(require("text!create/upload.html"), _upload);
				html += template(require("text!create/input.html"), _group);

			B.createOpt(html);
			B.upLoad();

		},
		menu: function(data, config){
			var B = require("base"),
				F = config.libs.form,
				T = config.libs.template;
			var page = '';
			var _style = F.style;

			data = B.decode(data);
			//页面选择
			for(var i=0;i<F.usepage.value.length;i++){
				var flog = false;
				if(data.page){
					for(var l=0;l<data.page.length;l++){
						if(i == data.page[l]) flog = true;
					}
				}
				page += '<label class="radio list"><input type="checkbox" name="'+ F.usepage.name +'" value="'+ i +'"'+ (flog?' checked="checked"':'') +'>'+ F.usepage.value[i] +'</label>';
			}

			var s = data.style || 0;	//初始模板风格

			var html = '<div class="edit-nav-switch"><div>将导航应用在以下页面：</div><div>'+ page +'</div></div>';

			//create style
			_style.value = s;
			_style.title = "当前导航模板：" + T[s].title;
			_style.dialog = {};
			_style.dialog.title = '选择导航模板';
			_style.dialog.tip = '请选择微店导航的风格样式';
			_style.dialog.modal = 'menu';

			html += template(require("text!create/style.html"), _style);

			function createNav(name, index, title, linkt, url){
				var _json = {};
				_json.name = name;
				_json.index = index;
				_json.title = title;
				_json.linktitle = linkt;
				_json.link = url;
				return template(require("text!operation/menu-link.html"), _json);
			}
			
			if(s == 1){	//QQ空间样式
				//背景颜色
				var _group = {};
					_group.group = F.group;

				//上传模块
				var _upload = F.upload;//上传配置
					_upload.group = [];
				var tips = [
					"中间主图标<br/>建议尺寸：100*100像素",
					"左侧第一个图标<br/>建议尺寸：100*100像素",
					"左侧第二个图标<br/>建议尺寸：100*100像素",
					"右侧第一个图标<br/>建议尺寸：100*100像素",
					"右侧第二个图标<br/>建议尺寸：100*100像素"
				]

				//切换模板时添加默认数据
				if(!data.bgcolor){
					delete data.menu;
					delete data.submenu;
					for(var i = 0; i < 5; i++){
						_upload.group[i] = {};
						_upload.group[i].tip = tips[i];
					}
				}else{
					_group.group[0].value = data[_group.group[0].name];
					for(var i = 0; i < 5; i++){
						_upload.group[i] = {};
						_upload.group[i].image = data.upload[i].src;
						_upload.group[i].link = data.upload[i].url;
						_upload.group[i].linktitle = data.upload[i].linktitle;
						_upload.group[i].tip = tips[i];
					}
				}
				html += template(require("text!create/input.html"), _group);
				html += template(require("text!create/upload.html"), _upload);
			}else{//微信公众号样式
				var len = 0;
				var choice = '';
				if(!data.menu && data.bgcolor){
					delete data.upload;
					delete data.bgcolor;
					data.menu = [
						{title: "全部商品", url: "", linktitle: ""},
						{title: "购物车", url: "", linktitle: ""},
						{title: "标题", url: "", linktitle: ""}
					]
				}
				if(data.menu){
					len = data.menu.length;
					for(var i=0;i<data.menu.length;i++){
						var sub_txt = '';
						if(data.submenu){
							var flog = true;
							for(var l=0;l<data.submenu.length;l++){
								if(data.submenu[l] && data.submenu[l].id == i && data.submenu[l].title){
									//指定对应ID
									flog = false;
									sub_txt += '<input name="submenu['+l+'][id]" type="hidden" value="'+i+'" />';
									var submenu = data.submenu[l];
									if(typeof submenu.title == "string"){
										var txt = createNav("submenu", l, submenu.title, submenu.linktitle, submenu.url);
										sub_txt += '<div class="second-nav-list"><span class="action delete close-modal" title="删除">×</span>'+ txt +'</div>';
										sub_txt += '<p class="add-shopnav add-second-shopnav js-add-second-nav" data-pid="'+i+'" data-id="'+l+'" data-len="1">+ 添加二级导航</p>';
									}else{
										for(var s=0;s<submenu.title.length;s++){
											var txt = createNav("submenu", l, submenu.title[s], submenu.linktitle[s], submenu.url[s]);
											sub_txt += '<div class="second-nav-list"><span class="action delete close-modal" title="删除">×</span>'+ txt +'</div>';
										}
										if(submenu.title.length < 5){
											sub_txt += '<p class="add-shopnav add-second-shopnav js-add-second-nav" data-pid="'+i+'" data-id="'+l+'" data-len="'+submenu.title.length+'">+ 添加二级导航</p>'
										}
									}
								}
							}
							if(!flog){
								mainlink = '<div class="m-choice-link clearfix"><div class="fl shopnav-item-title"><span class="js-edit-title">'+data.menu[i].title+'</span><input name="menu['+i+'][title]" type="hidden" value="'+data.menu[i].title+'" /></div><div class="fl shopnav-item-link">&nbsp;&nbsp;|&nbsp;&nbsp;使用二级导航后主链接已失效。</div></div>';
							}else{
								mainlink = createNav("menu", i, data.menu[i].title, data.menu[i].linktitle, data.menu[i].url);
								sub_txt += '<p class="add-shopnav add-second-shopnav js-add-second-nav" data-pid="'+i+'" data-id="'+data.submenu.length+'" data-len="0">+ 添加二级导航</p>'
							}
						}else{
							sub_txt += '<p class="add-shopnav add-second-shopnav js-add-second-nav" data-pid="'+i+'" data-id="0" data-len="0">+ 添加二级导航</p>'
							mainlink = createNav("menu", i, data.menu[i].title, data.menu[i].linktitle, data.menu[i].url);
						}
						choice += '<div class="choice clearfix" data-id="'+i+'"><span class="action delete close-modal" title="删除">×</span><div class="first-nav"><h3 class="h5 strong">一级导航</h3>'+ mainlink +'</div><div class="second-nav margin-top padding-big-left"><h4 class="strong">二级导航</h4>'+ sub_txt +'</div></div>';
					}
				}
				if(!data.menu || len < 3) choice += '<p class="add-shopnav js-add-nav" data-id="'+ len +'">+ 添加一级导航</p>';
				html += '<div class="choices shopnav-wrap">'+choice+'</div>';
			}

			B.createOpt(html);
			B.upLoad();

			//添加一级导航
			$('.js-add-nav').unbind().bind('click', function(){
				var $id = $(this).data("id");
				var addNav = '<input name="menu['+ $id +'][title]" type="hidden" value="标题" />';
				addNav += '<input name="menu['+ $id +'][linktitle]" type="hidden" value="" />';
				addNav += '<input name="menu['+ $id +'][url]" type="hidden" value="" />';
				$(this).before(addNav);
				require("diy").reload();
			});

			//添加二级导航
			$('.js-add-second-nav').unbind().bind('click', function(){
				var $id = $(this).data("id");
				var pid = $(this).data("pid");
				var len = $(this).data("len");
				var addNav = '<input name="submenu['+ $id +'][title]" type="hidden" value="标题" />';
				addNav += '<input name="submenu['+ $id +'][linktitle]" type="hidden" value="" />';
				addNav += '<input name="submenu['+ $id +'][url]" type="hidden" value="" />';
				if(len <= 0) addNav += '<input name="submenu['+ $id +'][id]" type="hidden" value="'+pid+'" />';
				$(this).before(addNav);
				require("diy").reload();
			});

		}
	}
})
