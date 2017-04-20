(function($){
	$.fn.editBox = function(B) {
		//对象参数初始化
		var B = $.extend({
			id:"BoxCanvas",         //面板ID
			width:"auto",           //面板宽度
			height:450,             //面板高度
			zindex:1,               //新对象的z-index
			isAddbox:false,        //添加新对象状态
			isResize:true,         //面板是否可改变大小
			reisze:false,
			isFocusbox:new Array,  //对象选中控件
			isBoxEdit:false,       //是否有控件在编辑中(解决编辑状态下 TextArea mousedown命中的BUG)
			selectbox:false,       //选中多个控件外边框
			color:"red",            //对象边框颜色 red,yellow,green,blue,purple,black
			// background:"st.jpg",//背景图片
			delivery:"background",//背景图片
			_x:0,                   //X座标初始化
			_y:0,                   //Y座标初始化
			offsetX:0,              //面板绝对X座标
			offsetY:0,              //面板绝对Y座标
			maxX:0,                 //最大X座标初始化
			maxY:0,                 //最大Y座标初始化
			minHeight:360,            //最大高度限制初始化
			minWidth:700,             //最大宽度限制初始化
			imgurl:"images/editBox/",           //图片地址设置
			coloroption:["red","yellow","green","blue","purple","black"],
			fontsizeoption:["20px","18px","16px","14px","12px","10px"],
			textoption:[
				"收货人{address_name}",
				"收件人手机{address_mobile}",
				"收件人地址{address_detail}",
				"寄件人{sender_name}",
				"寄件人电话{sender_mobile}",
				"寄件人地址{sender_address}",
				"应付订单金额{real_amount}",
				"实付订单金额{paid_amount}",
				"订单备注{remark}",
				"当前日期时间{dateline}"
			],
			deliveryName:'请选择',
			deliveryImage:'',
			deliveryOption:{}
		},B);
		//初始化面板和工具条
		var _parent = $(this),   //父元素
			_tools,               //工具
			_resize,              //改变大小元素
			_canvas,              //面板元素
			_canvasbg,
			_staff_x,_staff_y,
			_addtool,             //添加按钮
			_selecttool,          //选择按钮
			_deltool,             //删除按钮
			_aligntool,           //对齐按钮
			_savetool,            //保存按钮
			_fonttool,　　　　　　//字体设置
			_texttool,            //内容设置
			_bgtool,              //背景选择框
			_colortool,           //颜色选择框
			_pagetool,            //页面设置
			_new;                 //临时新控件
		_parent.css("position","relative");
		//将面板写入页面
		var addCanvas = function(){
			_parent.append("<div id='editTool' class='editTool'></div>");
			_parent.append("<div class='editCanvas_bg'><ul class='staff_x'></ul><ul><li class='staff_y'></li><li><div id='editCanvas' class='editCanvas'></div></li></ul></div>");
			_parent.append("<div id='editResize' class='editBottom'><ul></ul><ul class='br'></ul></div>");
			_canvas = $("#editCanvas");
			_canvasbg = $(".editCanvas_bg");
			_staff_x = $(".staff_x");
			_staff_y = $(".staff_y");
			_tools = $("#editTool");
			_resize = $("#editResize");
			var arr = [];
			arr.push("<ul id='addTool'><li><img src='"+ B.imgurl +"btn_add.png' align='absmiddle'/></li><li>添 加</li></ul>");
			arr.push("<ul id='selectTool' class='current'><li>")
			arr.push("<img src='"+ B.imgurl +"btn_select.png' align='absmiddle'/></li><li>选 择</li></ul>");
			arr.push("<ul id='delTool'><li><img src='"+ B.imgurl +"btn_del.png' align='absmiddle'/></li><li>删 除</li></ul>");
			arr.push("<ul id='alignTool'><li><img src='"+ B.imgurl +"btn_align.png' align='absmiddle'/></li>")
			arr.push("<li>对 齐</li><img src='"+ B.imgurl +"ico_arrow.png' align='right'/><ol><li id='left'>左对齐</li>")
			arr.push("<li id='right'>右对齐</li><li id='x-center'>水平居中</li><li id='top'>上对齐</li>");
			arr.push("<li id='bottom'>下对齐</li><li id='y-center'>垂直居中</li></ol></ul>");
			arr.push("<ul id='panelPerpro'>");
			arr.push("<li><img src='"+ B.imgurl +"ico_x.png' align='absmiddle'/></li>")
			arr.push("<li><input id='panelX' value='0'/></li>")
			arr.push("<li><img src='"+ B.imgurl +"ico_y.png' align='absmiddle'/></li>")
			arr.push("<li><input id='panelY' value='0'/></li>")
			arr.push("<li><img src='"+ B.imgurl +"ico_width.png' align='absmiddle'/></li>")
			arr.push("<li><input id='panelWidth' value='0'/></li>")
			arr.push("<li><img src='"+ B.imgurl +"ico_height.png' align='absmiddle'/></li>")
			arr.push("<li><input id='panelHeight' value='0'/></li>")
			arr.push("</ul><ul id='panelFontsize'><span>字号：</span>");
			arr.push("<img src='"+ B.imgurl +"ico_arrow.png' class='arrow'/><ol></ol></ul>");
			arr.push("<ul id='panelText'><span>自定义</span>");
			arr.push("<img src='"+ B.imgurl +"ico_arrow.png' class='arrow'/><ol></ol></ul>");
			arr.push("<ul id='panelTemplate' style='clear:both;'><li>快递：</li><span>"+ B.deliveryName +"</span>")
			arr.push("<img src='"+ B.imgurl +"ico_arrow.png' class='arrow'/><ol></ol></ul>");
			arr.push("<ul id='panelColor'><li>控件颜色:</li><a></a>")
			arr.push("<img src='"+ B.imgurl +"ico_arrow.png' class='arrow'/><ol></ol></ul>");
			// arr.push("<ul id='panelPage'><li>页面设置</li><img src='"+ B.imgurl +"ico_arrow.png' class='arrow'/><ol></ol><dl></dl></ul>");
			// arr.push("<ul id='printTool' notice='打印测试'><li>")
			// arr.push("<img src='"+ B.imgurl +"ico_print.png' align='absmiddle'/></li>")
			// arr.push("<li>打印测试</li></ul>");
			// arr.push("<ul id='saveTool' notice='保存设计好的模板'><li>")
			// arr.push("<img src='"+ B.imgurl +"btn_save.png' align='absmiddle'/></li>")
			// arr.push("<li>保存模板</li></ul>");
			arr.push("<div style='clear:both;'></div>");
			_tools.html(arr.join('\n'));
			arr.length = 0;//清空数组
			_addtool = $("#addTool");
			_selecttool = $("#selectTool");
			_deltool = $("#delTool");
			_aligntool = $("#alignTool");
			_savetool = $("#saveTool");
			_bgtool = $("#panelTemplate");
			_colortool = $("#panelColor");
			_tools.css({"width":B.width});
			_resize.css({"width":B.width});
			_fonttool = $("#panelFontsize");
			_texttool = $("#panelText");
			_pagetool = $("#panelPage");
			_canvasbg.css({
				"width":B.width,
				"height":B.height - parseInt(_tools.css("height")) - parseInt(_resize.css("height")),
				"background":"url("+ ((B.delivery != 'background') ? B.deliveryImage : B.imgurl) + B.delivery +".jpg) no-repeat 20px 20px"
			});
			B.width = B.width == "auto" ? parseInt(_parent.width()) : B.width;
			_canvas.css({
				"width":B.width - 20,
				"height":B.height - parseInt(_tools.css("height")) - parseInt(_resize.css("height")) - 20
			});
			_staff_y.css({
				"height":B.height - parseInt(_tools.css("height")) - parseInt(_resize.css("height")) - 20
			});
			for(var i = 0;i< B.fontsizeoption.length;i++){
				_fonttool.find("ol").append("<li id='"+ B.fontsizeoption[i] +"'>"+ B.fontsizeoption[i] +"</li>");
			}
			for(var i = 0;i< B.textoption.length;i++){
				_texttool.find("ol").append("<li id='"+ B.textoption[i] +"'>"+ B.textoption[i] +"</li>");
			}
			for(var i = 0;i< B.coloroption.length;i++){
				_colortool.find("ol").append("<li id='"+ B.coloroption[i] +"'><b></b><span>"+ B.coloroption[i] +"</span></li>");
			}
            
			for(var i = 0;i< 50;i++){
				_staff_x.append("<span>"+ i +"</span>");
				_staff_y.append("<span>"+ i +"</span>");
			}
			B.offsetX = parseInt(_canvas.offset().left);
			B.offsetY = parseInt(_canvas.offset().top);
			arr.length = 0;
			arr.push("<li><span>页面宽度：<input value='22.9' />cm</span></li>");
			arr.push("<li><span>页面高度：<input value='12.6' />cm</span></li>");
			arr.push("<li><span>打印偏移x：<input value='0' />cm</span></li>");
			arr.push("<li><span>打印偏移y：<input value='0' />cm</span></li>");
			_pagetool.find("ol").html(arr.join('\n'));
			_tools.find("img").addClass("png");//解决IE6下png图片透明色变灰
		}
		//执行写入
		addCanvas();

		$.fn.content = function() {
			unloadselectbox();
			var _editBox = _canvas.find(".editBox"),
				_tempText = [],
				_editItem,
				_text;
			for(var i = 0; i < _editBox.length; i++){
				_editItem = _editBox.eq(i)
				_tempText.push('{"width":'+ parseInt(_editItem.css('width')) +',"height":'+ parseInt(_editItem.css('height')) +',"left":'+ parseInt(_editItem.css('left')) +',"top":'+ parseInt(_editItem.css('top')) +',"txt":"'+ _editItem.find('ul').text()+'"}');
			};
			_text = '{"width":'+ B.width +',"height":'+ B.height +',"background":"'+ B.background +'","list":['+ _tempText.join(',') +']}';
			return _text;
		}

		//添加新的可编辑文本控件
		$.fn.addBox = function(C){
			//初始化参数
			var C = $.extend({
				id:"autobox",
				text:"文本内容",
				width:200,
				height:45,
				top:100,
				left:100,
				isMove:false,
				isResize:false,
				isEdit:false,
				resizeType:"none",
				minWidth:50,
				minHeight:20
			},C);
			var add = function(){
				//_canvas.find(".editBox").attr("id",null);
				var _this = $("<div class='editBox'></div>");
				_canvas.append(_this);
				//alert(_this.index());
				_this.css({"height":C.height,"width":C.width,"left":C.left,"top":C.top}); //控件位置初始化
				_this.html("<ol class='tl'></ol><ol class='tr'></ol><ol class='bl'></ol><ol class='br'></ol><ol class='t' show='yes'></ol><ol class='l' show='yes'></ol><ol class='b' show='yes'></ol><ol class='r' show='yes'></ol><textarea id='textarea'></textarea><ul></ul>");
				var _text = _this.find("ul"),   //文本内容
					_tl = _this.find(".tl"),    //左上
					_tr = _this.find(".tr"),    //右上
					_bl = _this.find(".bl"),    //左下
					_br = _this.find(".br"),    //右下
					_b = _this.find(".b"),    　//下
					_r = _this.find(".r"),   　 //右
					_input = _this.find("#textarea");
				_text.css("border-color",colorset(B.color,"bd"));
				_this.find("ol").css({
					"border-color":colorset(B.color,"bd"),
					"background":colorset(B.color,"bg")
				});
				_text.html(C.text);_input.val(C.text);
				_input.css({"height":C.height- 8,"width":C.width- 8,"resize":"none"});
				_text.css({"height":C.height - 8,"width":C.width - 8});
				_tr.css({"left":C.width});
				_bl.css({"top":C.height});
				_br.css({"left":C.width,"top":C.height});
				_b.css({"top":C.height,display:"block"});
				_r.css({"left":C.width,display:"block"});
				var _x,_y;
				isBoxFocus();//当前控件取得焦点
				//双击开始编辑文本
				_text.bind({
					"dblclick":function(){
					_input.val(_text.text()).css("display","block").select().focus();
					_text.css("display","none").text("");
					B.isBoxEdit = true;
					C.isEdit = true;
					}
				});
				//编辑完成
				_input.bind({
					"blur":function(){
						_text.css("display","block").text(_input.val());
						_input.css("display","none");
						B.isBoxEdit = false;
						C.isEdit = false;
					},
					"click":function(){
						return false;
					}
				});
				//窗口移动
				_text.mousedown(function(e){
					if(!C.isEdit){
						var _selectbox = _canvas.find("#editBox_SelectBox");//定义选择框
						var _isinbox = false;
						_this.attr("id","yes");
						for(var i=0; i < B.isFocusbox.length ; i++){
							//alert(B.isFocusbox[i].index());
							if(B.isFocusbox[i].attr("id") == _this.attr("id")){
								_isinbox = true;
							}
						}
						_this.removeAttr("id");
						//alert(_isinbox);
						if(!_isinbox){
							_x = e.pageX - parseInt(_this.css("left")) - B.offsetX;
							_y = e.pageY - parseInt(_this.css("top")) - B.offsetY;
							unloadselectbox();//选择框移除
							isBoxFocus();//当前控件取得焦点
						}else{
							if(_selectbox.length > 0){
								_x = e.pageX - parseInt(_selectbox.css("left")) - B.offsetX;
								_y = e.pageY - parseInt(_selectbox.css("top")) - B.offsetY;
							}else{
								_x = e.pageX - parseInt(_this.css("left")) - B.offsetX;//获得左边位置
								_y = e.pageY - parseInt(_this.css("top")) - B.offsetY;//获得上边位置
							}
						}
						B.isAddbox = false;//移除控件添加状态
						_addtool.removeClass("current");//移除添加按钮样式
						_selecttool.addClass("current");//写入选择按钮样式
						_canvas.css("cursor","auto");
						C.isMove=true;
						//t = $this;//初始化当前激活层的对象
						showProperty();//写入属性面板信息
						$(".editBox textarea").blur();
						return false;
					}
				});
				//改变大小
				_this.find("ol").mousedown(function(e){
					unloadselectbox();//选择框移除
					isBoxFocus();//当前控件取得焦点
					C.isResize=true;
					B._x = e.pageX - B.offsetX;//获得左边位置
					B._y = e.pageY - B.offsetY;//获得上边位置
					C.resizeType = $(this).attr("class");
					return false;
				});
				$(document).live("mousemove",function(e){
					if(C.isMove){
						var _selectbox = _canvas.find("#editBox_SelectBox");
						for(var i=0 ; i<B.isFocusbox.length;i++){
							var cx,cy,dx,dy;
							var minX,minY;
							if(_selectbox.length > 0){
								cx = parseInt(_selectbox.css("width"));
								cy = parseInt(_selectbox.css("height"));
								var x = e.pageX - _x - B.offsetX;//移动时根据鼠标位置计算控件左上角的绝对位置
								var y = e.pageY - _y - B.offsetY;
								B.maxX = parseInt(_canvas.css("width")) - cx;
								B.maxY = parseInt(_canvas.css("height")) - cy;
								x = x < 0 ? 0 : x;
								y = y < 0 ? 0 : y;
								x = x > B.maxX ? B.maxX : x;
								y = y > B.maxY ? B.maxY : y;
								_selectbox.css({"top":y,"left":x});//控件新位置
							}else{
								cx = parseInt(B.isFocusbox[0].css("width"));
								cy = parseInt(B.isFocusbox[0].css("height"));
								var x = e.pageX - _x - B.offsetX;//移动时根据鼠标位置计算控件左上角的绝对位置
								var y = e.pageY - _y - B.offsetY;;
								B.maxX = parseInt(_canvas.css("width")) - cx;
								B.maxY = parseInt(_canvas.css("height")) - cy;
								x = x < 0 ? 0 : x;
								y = y < 0 ? 0 : y;
								x = x > B.maxX ? B.maxX : x;
								y = y > B.maxY ? B.maxY : y;
								//document.getElementsByTagName("ul")[0].innerHTML = B.maxX+","+B.maxY;
								B.isFocusbox[0].css({"top":y,"left":x});//控件新位置
								//_text.text(x +","+ y +","+ B.offsetX +","+ _canvas.offset().top);
							}
						}
						showProperty();//写入属性面板信息
						return false;
					}
					if(C.isResize){
						showProperty();//写入属性面板信息
						var x,y,w,h;
						B.maxX = parseInt(_this.css("left"))+parseInt(_this.css("width"))-C.minWidth;    //元素最大x座标
						B.maxY = parseInt(_this.css("top"))+parseInt(_this.css("height"))-C.minHeight;   //元素最大y座标
						B.maxWidth = parseInt(_canvas.css("width"))-parseInt(_this.css("left"));         //元素最大宽度
						B.maxHeight = parseInt(_canvas.css("height"))-parseInt(_this.css("top"));        //元素最大高度
						var _ex = e.pageX - B.offsetX;
						var _ey = e.pageY - B.offsetY;
						_ex = _ex < 0 ? 0 : _ex;
						_ey = _ey < 0 ? 0 : _ey;
						switch(C.resizeType){
							case "t"://上
								x=parseInt(_this.css("left"));
								y=_ey;
								w=parseInt(_this.css("width"));
								h=parseInt(_this.css("height"))+(parseInt(_this.css("top"))-_ey);
								break;
							case "b"://下
								x=parseInt(_this.css("left"));
								y=parseInt(_this.css("top"));
								w=parseInt(_this.css("width"));
								h=_ey-parseInt(_this.css("top"));
								break;
							case "l"://左
								x=_ex;
								y=parseInt(_this.css("top"));
								w=parseInt(_this.css("width"))+(parseInt(_this.css("left"))-_ex);
								h=parseInt(_this.css("height"));
								break;
							case "r"://右
								x=parseInt(_this.css("left"));
								y=parseInt(_this.css("top"));
								w=_ex-parseInt(_this.css("left"));
								h=parseInt(_this.css("height"));
								break;
							case "tr"://右上
								x=parseInt(_this.css("left"));
								y=_ey;
								w=_ex-parseInt(_this.css("left"));
								h=parseInt(_this.css("height"))+(parseInt(_this.css("top"))-_ey);
								break;
							case "tl"://左上
								x=_ex;
								y=_ey;
								w=parseInt(_this.css("width"))+(parseInt(_this.css("left"))-_ex);
								h=parseInt(_this.css("height"))+(parseInt(_this.css("top"))-_ey);
								break;
							case "br"://右下
								x=parseInt(_this.css("left"));
								y=parseInt(_this.css("top"));
								w=_ex-parseInt(_this.css("left"));
								h=_ey-parseInt(_this.css("top"));
								break;
							case "bl"://左下
								x=_ex;
								y=parseInt(_this.css("top"));
								w=parseInt(_this.css("width"))+(parseInt(_this.css("left"))-_ex);
								h=_ey-parseInt(_this.css("top"));
								break;
						}
						w = w < C.minWidth ? C.minWidth : w;
						w = w > B.maxWidth ? B.maxWidth : w;
						h = h < C.minHeight ? C.minHeight : h;
						h = h > B.maxHeight ? B.maxHeight : h;
						x = x > B.maxX ? B.maxX : x;
						x = x < 0 ? 0 : x;
						y = y > B.maxY ? B.maxY : y ;
						y = y < 0 ? 0 : y;
						_input.css({"height":h-8,"width":w-8});
						_text.css({"height":h -8,"width":w-8});
						_this.css({"width":w,"height":h,"top":y,"left":x});
						//_text.text(_canvas.offset().left +","+ _canvas.offset().top +","+ B.offsetX +","+ B.offsetY);
						_tr.css({"left":w});
						_bl.css({"top":h});
						_br.css({"left":w,"top":h});
						_r.css({"left":w,"height":h});
						_b.css({"top":h});
						return false;
					}
				});
				$(document).live("mouseup",function(e){
					C.isMove = false;	
					C.isResize = false;
				});
				function isBoxFocus(){
					B.zindex++;
					$(".editBox").removeClass("current");//所有控件取消选择
					$(".editBox ol").css("display","none");
					_this.find("ol").css("display","block");
					_r.css("height",_this.css("height"));
					_this.css({"z-index":B.zindex});
					_this.addClass("current");
					B.isFocusbox.length = 0;
					B.isFocusbox.push(_this);
					showProperty();//写入属性面板信息
				}
			};
			return add();
		}
		//属性面板显示当前选中可编辑文本控件属性
		var showProperty = function(){
			var _selectbox = _canvas.find("#editBox_SelectBox");
			var _box = _selectbox.length > 0 ? _selectbox : B.isFocusbox[0];
			if( _box == null){
				return false;
			};
			$("#panelX").val(parseInt(_box.css("left")));
			$("#panelY").val(parseInt(_box.css("top")));
			$("#panelWidth").val(parseInt(_box.css("width")));
			$("#panelHeight").val(parseInt(_box.css("height")));
			var _font =_box.find("ul").css("font-size");
			var _content = _box.find("ul").text();
			_content = _content.length == 0 ? "自定义" : _content;
			_exist=$.inArray(_content,B.textoption); 
			_texttool.find("ol li").removeClass("current");
			_texttool.find("span").text(_exist > 0 ? _content : "自定义");
			_texttool.find("ol li:contains('"+ _content +"')").addClass("current");
			_fonttool.find("ol li").removeClass("current");
			_fonttool.find("span").text(_box.find("ul").css("font-size"));
			_fonttool.find("ol li:contains('"+ _font +"')").addClass("current");
		}
		//属性面板写入当前选中可编辑文本控件属性
		var writeProperty = function(){
			if(B.isFocusbox.length == 1){
				_box = B.isFocusbox[0]
				var _x= parseInt($("#panelX").val());
				var _y = parseInt($("#panelY").val());
				var _w = parseInt($("#panelWidth").val());
				var _h = parseInt($("#panelHeight").val());
				B.maxX = parseInt(_canvas.css("width")) - parseInt(B.isFocusbox[0].css("width"));
				B.maxY = parseInt(_canvas.css("height")) - parseInt(B.isFocusbox[0].css("height"));
				B.maxWidth = parseInt(_canvas.css("width")) - parseInt(B.isFocusbox[0].css("left"));
				B.maxHeight = parseInt(_canvas.css("height")) - parseInt(B.isFocusbox[0].css("top"));
				_x = _x > B.maxX ? B.maxX : _x;
				_y = _y > B.maxY ? B.maxY : _y;
				_w = _w > B.maxWidth ? B.maxWidth : _w;
				_h = _h > B.maxHeight ? B.maxHeight : _h;
				$("#panelX").val(_x);
				$("#panelY").val(_y);
				$("#panelWidth").val(_w);
				$("#panelHeight").val(_h);
				B.isFocusbox[0].css({"left":_x,"top":_y,"width":_w,"height":_h});
				B.isFocusbox[0].find("textarea").css({"height":_h-12,"width":_w-12});
				B.isFocusbox[0].find("ul").css({"height":_h - 8,"width":_w - 8});
				B.isFocusbox[0].find(".tr").css({"left":_w});
				B.isFocusbox[0].find(".bl").css({"top":_h});
				B.isFocusbox[0].find(".br").css({"left":_w,"top":_h});
				B.isFocusbox[0].find(".r").css({"left":_w,"height":_h });
				B.isFocusbox[0].find(".b").css({"top":_h,"width":_w});
				if(_texttool.find("span").text() !="自定义"){
					var _msg_ = _texttool.find("span").text().replace(/[\u4e00-\u9fa5]/g,'');	// 把中文注释替换为空
					B.isFocusbox[0].find("ul").text(_msg_);
					B.isFocusbox[0].find("textarea").val(_msg_);
				}
				B.isFocusbox[0].find("ul").css("font-size",_fonttool.find("span").text());
			}
		}
		//将修改后的属性值写入当前控件
		_tools.find("input").keyup(function(){writeProperty()});
		_tools.find("input").ready(function(){
			$("this").keypress(function(){alert(e.keyCode)});
		})
		//更改背景图片
		_bgtool.ready(function() {
			var _ol = _bgtool.find("ol");
			_ol.attr("class",parseInt(_ol.css("height")));
			_ol.find("li").each(function(){
				$(this).bind("click",function(){
                    var bgimg = B.deliveryImage + $(this).attr("id") + '.jpg';
                    $('#bgimg').attr("src", bgimg);
					_canvasbg.css("background","url("+ B.deliveryImage + $(this).attr("id") +".jpg) no-repeat 20px 20px");
                    $('#bgimg').load(function() {
                    	B.width = $(this).width();
                    	B.height = $(this).height();
                    	_parent.css("width", (B.width + 20));
                    	_tools.css("width", (B.width + 20));
                    	_resize.css("width", (B.width + 20));
                    	_staff_y.css("height", (B.height + 10));
                    	_canvasbg.css("width", (B.width + 20));
                    	_canvasbg.css("height", (B.height + 20));
                    });
					_bgtool.find("span").text($(this).text());
					_ol.find("li").removeClass("current");
					$(this).addClass("current");
					B.background = $(this).attr("id");
                    $("input[name=delivery_id]").attr("value", $(this).attr("data-id"));
					return false;
				});
			});
			_bgtool.click(function(){
				B._x = parseInt(_ol.attr("class")); 
				_bgtool.css("z-index",10);
				_ol.css({"height":0,"display":"block"});
				_ol.animate({"height":B._x},300,function(){
				});
			})
			_bgtool.mouseleave(function(){
				_ol.animate({"height":0},300,function(){
					_ol.css({"height":B._x,"display":"none"});
					_bgtool.css("z-index",5);
				});
			})
		});
		//更改控件颜色
		_colortool.ready(function(){
			var _ol = _colortool.find("ol");
			_colortool.find("a").css({
				"border-color":colorset(B.color,"bd"),
				"background":colorset(B.color,"bg")
			});
			_colortool.find("#"+B.color).attr("class","current");
			_ol.attr("class",parseInt(_ol.css("height")));
			_ol.find("li").each(function(){
				$(this).find("b").css({
					"border-color":colorset($(this).attr("id"),"bd"),
					"background":colorset($(this).attr("id"),"bg")
				});
				$(this).bind("click",function(){
					B.color = $(this).attr("id");
					var _ul = $(".editBox").find("ul");
					var _bol = $(".editBox").find("ol");
					_ul.css("border-color",colorset(B.color,"bd"));
					_bol.css({
						"border-color":colorset(B.color,"bd"),
						"background":colorset(B.color,"bg")
					});
					_colortool.find("a").css({
						"border-color":colorset(B.color,"bd"),
						"background":colorset(B.color,"bg")
					});
					_ol.find("li").removeClass();
					$(this).attr("class","current")
					return false;
				});
			});
			_colortool.click(function(){
				B._x =parseInt(_ol.attr("class")); 
				_colortool.css("z-index",10);
				_ol.css({"height":0,"display":"block"});
				_ol.animate({"height":B._x},300,function(){
				});
			})
			_colortool.mouseleave(function(){
				_ol.animate({"height":0},300,function(){
					_ol.css({"height":B._x,"display":"none"});
					_colortool.css("z-index",5);
				});
			})
		});
		//修改字号
		_fonttool.ready(function(){
			var _ol = _fonttool.find("ol");
			_ol.attr("class",parseInt(_ol.css("height")));
			_ol.find("li").each(function(){
				$(this).bind("click",function(){
					_ol.find("li").removeClass();
					_fonttool.find("span").text($(this).text());
					writeProperty();
					$(this).attr("class","current")
					return false;
				});
			});
			_fonttool.click(function(){
				B._x =parseInt(_ol.attr("class")); 
				_fonttool.css("z-index",10);
				_ol.css({"height":0,"display":"block"});
				_ol.animate({"height":B._x},300,function(){
				});
			})
			_fonttool.mouseleave(function(){
				_ol.animate({"height":0},300,function(){
					_ol.css({"height":B._x,"display":"none"});
					_fonttool.css("z-index",5);
				});
			})
		});
		//修改内容
		_texttool.ready(function(){
			var _ol = _texttool.find("ol");
			_ol.attr("class",parseInt(_ol.css("height")));
			_ol.find("li").each(function(){
				$(this).bind("click",function(){
					_ol.find("li").removeClass();
					_texttool.find("span").text($(this).text());
					writeProperty();
					$(this).attr("class","current")
					return false;
				});
			});
			_texttool.click(function(){
				B._x =parseInt(_ol.attr("class")); 
				_texttool.css("z-index",10);
				_ol.css({"height":0,"display":"block"});
				_ol.animate({"height":B._x},300,function(){
				});
			})
			_texttool.mouseleave(function(){
				_ol.animate({"height":0},300,function(){
					_ol.css({"height":B._x,"display":"none"});
					_texttool.css("z-index",5);
				});
			})
		});
		//页面设置面板
		_pagetool.ready(function(){
			var _ol = _pagetool.find("ol");
			var _dl = _pagetool.find("dl");
			_ol.attr("class",parseInt(_ol.css("height")));
			_ol.find("li").each(function(){
				$(this).bind("click",function(){
					return false;
				});
			});
			_pagetool.click(function(){
				_pagetool.css("z-index",10);
				_dl.css("display","block");
				B._x =parseInt(_ol.attr("class")); 
					_ol.css({"height":0,"display":"block"});
					_ol.animate({"height":B._x},300,function(){
				});
			})
			_pagetool.mouseleave(function(){
				_ol.animate({"height":0},300,function(){
					_ol.css({"height":B._x,"display":"none"});
					_dl.css("display","none");
					_pagetool.css("z-index",5);
				});
			})
		});
		//转到添加可编辑文本插件状态
		_addtool.click(function(){
			B.isFocusbox.length = 0;
			$(".editBox").removeClass("current");//所有控件取消选择
			$(".editBox ol").css("display","none");
			$(this).addClass("current");
			_selecttool.removeClass("current");
			$("#editCanvas").css("cursor","crosshair");
			B.isAddbox = true;
		});
		//转到选择可编辑文本插件状态
		_selecttool.click(function(){
			B.isFocusbox.length = 0;
			$(".editBox").removeClass("current");//所有控件取消选择
			$(".editBox ol").css("display","none");
			$(this).addClass("current");
			_addtool.removeClass("current");
			$("#editCanvas").css("cursor","default");
			B.isAddbox = false;
		});
		//转到删除可编辑文本插件状态
		_deltool.click(function() {
			if(B.isFocusbox.length > 0){
				for(var i=0;i<B.isFocusbox.length;i++){
					B.isFocusbox[i].remove();
				}
				B.isFocusbox.length = 0;
			}
			_canvas.find("#editBox_SelectBox").remove();
		})
		//对齐
		_aligntool.ready(function(){
			var _ol = _aligntool.find("ol");
			_ol.attr("class",parseInt(_ol.css("height")));
			_ol.find("li").each(function(){
				$(this).bind("click",function(){
					var _type = $(this).attr("id");
					var _selectbox = _canvas.find("#editBox_SelectBox");
					if(_selectbox.length > 0){
						switch(_type){
							case "left":
								_selectbox.find(".editBox").css("left",0);
								break;
							case "right":
								var _box = _selectbox.find(".editBox")
								_box.each(function(){
									$(this).css("left",
										parseInt(_selectbox.css("width")) - parseInt($(this).css("width"))
									)			   
								});
								break;
							case "x-center":
								var _box = _selectbox.find(".editBox")
								_box.each(function(){
									$(this).css("left",
										(parseInt(_selectbox.css("width")) - parseInt($(this).css("width"))) / 2
									)			   
								});
								break;	
							case "top":
								_selectbox.find(".editBox").css("top",0);
								break;
							case "bottom":
								var _box = _selectbox.find(".editBox")
								_box.each(function(){
									$(this).css("top",
										parseInt(_selectbox.css("height")) - parseInt($(this).css("height"))
									)			   
								});
								break;
							case "y-center":
								var _box = _selectbox.find(".editBox")
								_box.each(function(){
									$(this).css("top",
										(parseInt(_selectbox.css("height")) - parseInt($(this).css("height"))) / 2
									)			   
								});
								break;	
						}
					}else{
						alert("至少得选择两个或两个以上控件！");
					}
					return false;
				});
			});
			_aligntool.click(function(){
				_aligntool.css("z-index",10);
				B._x =parseInt(_ol.attr("class")); 
				//alert(B._x);
				_ol.css({"height":0,"display":"block"});
				_ol.animate({"height":B._x},300,function(){
				});
			})
			_aligntool.mouseleave(function(){
				_ol.animate({"height":0},300,function(){
					_ol.css({"height":B._x,"display":"none"});
					_aligntool.css("z-index",5);
				});
			})
		});
		//控件面板操作
		_canvas.bind("mousedown",function(e){
			if(!B.isBoxEdit){
				$(".editBox textarea").blur();
				B.isFocusbox.length = 0;
				$(".editBox").removeClass("current");//所有控件取消选择
				$(".editBox ol").css("display","none");
				if(B.isAddbox){
					_canvas.append("<div class='editBox' id='temp'></div>");
					B._x = e.pageX - _canvas.offset().left;
					B._y = e.pageY - _canvas.offset().top;
					_new = _canvas.find("div:last");
					_new.css({"left":B._x,"top":B._y})
					return false;
				}else{
					unloadselectbox();
					_canvas.append("<div id='editBox_SelectBox'></div>");
					B._x = e.pageX - _canvas.offset().left;
					B._y = e.pageY - _canvas.offset().top;
					B.selectbox = _canvas.find("#editBox_SelectBox");
					B.selectbox.css({"left":B._x,"top":B._y})
					return false;
				}
			}
		});
		_canvas.bind("mousemove",function(e){
			if(B.isAddbox && _new != null){
				_w = e.pageX - B._x - _canvas.offset().left;
				_h = e.pageY - B._y - _canvas.offset().top;
				_new.css({"width":_w,"height":_h});
				return false;
			}
			if(B.selectbox){
				_w = e.pageX - B._x - _canvas.offset().left;
				_h = e.pageY - B._y - _canvas.offset().top;
				B.selectbox.css({"width":_w,"height":_h});
				return false;
			}
		 });
		//改变Canvas面板大小
		_resize.find("ul:first").bind("mousedown",function(e){
			B.resize = "b";
			return false;
		});
		_resize.find("ul:last").bind("mousedown",function(e){
			B.resize = "br";
			return false;
		});


		$(document).bind({
			"mousemove":function(e){
				return false;
				if(B.resize != false && B.isResize == true){
					var _ex = e.pageX - parseInt(_canvas.offset().left);
					var _ey = e.pageY - parseInt(_canvas.offset().top);
					var _box =  $(".editBox");
					for(var i = 0;i<_box.length;i++){
						B._x = parseInt(_box.eq(i).css("left")) + parseInt(_box.eq(i).css("width"));
						B._y = parseInt(_box.eq(i).css("top")) + parseInt(_box.eq(i).css("height"));
						B.minHeight = B.minHeight < B._y ? B._y : B.minHeight;
						B.minWidth = B.minWidth < B._x ? B._x : B.minWidth;
					}
					_ex = _ex < B.minWidth ? B.minWidth : _ex;
					_ey = _ey < B.minHeight ? B.minHeight : _ey;
					switch(B.resize){
						case "b":
							_canvasbg.css("height",_ey);
							_canvas.css("height",_ey - 20);
							_staff_y.css({"height":_ey - 20});
							break;
						case "br":
							_canvasbg.css({"width":_ex,"height":_ey});
							_canvas.css({"width":_ex -18,"height":_ey - 20});
							_staff_y.css({"height":_ey - 20});
							_tools.css("width",_ex);
							_resize.css("width",_ex);
							break;
					}
					return false;
				}
			},
			"mouseup":function(e){
				if(B.isAddbox && _new != null){
					_new.css("opacity",0.5);
					_canvas.addBox({
						width:parseInt(_new.css("width")),
						height:parseInt(_new.css("height")),
						left:parseInt(_new.css("left")),
						top:parseInt(_new.css("top"))
					});
					_new.remove();
					_new = null;
					_canvas.find("#temp").remove();
				}
				if(B.selectbox != false){
					//alert(B.selectbox.css("left"));
					var _sx = parseInt(B.selectbox.css("left"));
					var _ex = parseInt(B.selectbox.css("width")) + parseInt(B.selectbox.css("left"));
					var _sy = parseInt(B.selectbox.css("top"));
					var _ey = parseInt(B.selectbox.css("height")) + parseInt(B.selectbox.css("top"));
					var _box = _canvas.find(".editBox");
					var x = _ex , y = _ey , w = _sx ,h = _sy;
					for(var i = 0;i< _box.length;i++){
						var _bx = parseInt(_box.eq(i).css("left"));
						var _by = parseInt(_box.eq(i).css("top"));
						var _bw = parseInt(_box.eq(i).css("left")) + parseInt(_box.eq(i).css("width"));
						var _bh = parseInt(_box.eq(i).css("top")) + parseInt(_box.eq(i).css("height"));
						if( _bx >= _sx && _by >= _sy && _bw <= _ex && _bh <= _ey)
						{
							x = x > _bx ? _bx : x;
							y = y > _by ? _by : y;
							w = w < _bw ? _bw : w;
							h = h < _bh ? _bh : h;
							B.isFocusbox.push(_box.eq(i));
							_box.eq(i).find("ol").css("display","block");
							_box.eq(i).find("ul").addClass("current");
						}
					}
					//alert( x +","+ y +","+ w +","+ h);
					B.selectbox.css({"left":x,"top":y,"width":w - x,"height":h - y});
					//alert(B.isFocusbox.length);
					if(B.isFocusbox.length > 0){
						for(var i=0;i<B.isFocusbox.length;i++){
							B.isFocusbox[i].prependTo(B.selectbox);
							B.isFocusbox[i].css({
								"left":parseInt(B.isFocusbox[i].css("left")) - parseInt(B.selectbox.css("left")),
								"top":parseInt(B.isFocusbox[i].css("top")) - parseInt(B.selectbox.css("top"))
							});
						}
					}else{
						$("#editBox_SelectBox").remove();
					};
					showProperty();
					B.selectbox = false;
				}
				B.resize = false;
			},
			//键盘移动事件
			"keydown":function(e){
				var _selectbox = _canvas.find("#editBox_SelectBox"),
					keyCode = e.keyCode;
				if(_selectbox.length > 0){
					var _left = parseInt(_selectbox.css("left"));
					var _top = parseInt(_selectbox.css("top"));
					B.maxX = parseInt(_canvas.css("width")) - parseInt(_selectbox.css("width"));
					B.maxY = parseInt(_canvas.css("height")) - parseInt(_selectbox.css("height"));
					var x1,x2,y1,y2;
					x1 = _left > 0 ? _left-1 : 0;
					x2 = _left < B.maxX ? _left+1 : B.maxX;
					y1 = _top > 0 ? _top-1 : 0;
					y2 = _top < B.maxY ? _top+1 : B.maxY;
					switch(keyCode){
						case 46:
							_selectbox.remove();
							break;
						case 37:
							_selectbox.css("left",x1);
							showProperty();
							break;
						case 39:
							_selectbox.css("left",x2);
							showProperty();
							break;
						case 38:
							_selectbox.css("top",y1);
							showProperty();
							break;
						case 40:
							_selectbox.css("top",y2);
							showProperty();
							break;
					}
				}else if(B.isFocusbox.length > 0){
					for(i = 0; i<B.isFocusbox.length;i++){
						var _left = parseInt(B.isFocusbox[i].css("left"));
						var _top = parseInt(B.isFocusbox[i].css("top"));
						B.maxX = parseInt(_canvas.css("width")) - parseInt(B.isFocusbox[i].css("width"));
						B.maxY = parseInt(_canvas.css("height")) - parseInt(B.isFocusbox[i].css("height"));
						var x1,x2,y1,y2;
						x1 = _left > 0 ? _left-1 : 0;
						x2 = _left < B.maxX ? _left+1 : B.maxX;
						y1 = _top > 0 ? _top-1 : 0;
						y2 = _top < B.maxY ? _top+1 : B.maxY;
						switch(e.keyCode){
							case 46:
								B.isFocusbox[i].remove();
								break;
							case 37:
								B.isFocusbox[i].css("left",x1);
								showProperty();
								break;
							case 39:
								B.isFocusbox[i].css("left",x2);
								showProperty();
								break;
		
							case 38:
								B.isFocusbox[i].css("top",y1);
								showProperty();
								break;
							case 40:
								B.isFocusbox[i].css("top",y2);
								showProperty();
								break;
						}
					}
				}
				B.resize = false;
			}
		});
		//移除选择框
		function unloadselectbox(){
			var _select = _canvas.find("#editBox_SelectBox");
			var _selectbox = _canvas.find("#editBox_SelectBox .editBox");
			//alert(_selectbox.length);
			if(_selectbox.length > 0){
				for(var i = 0; i<_selectbox.length;i++){
					_selectbox.eq(i).prependTo(_canvas);
					_selectbox.eq(i).css({
						"left":parseInt(_selectbox.eq(i).css("left")) + parseInt(_select.css("left")),
						"top":parseInt(_selectbox.eq(i).css("top")) + parseInt(_select.css("top"))
					});
				}
			}
			$("#editBox_SelectBox").remove();
		};
		//颜色选择器
		colorset = function(c,b){
			switch(c){
				case "red":
					return b == "bd" ? "#D00" : "#FA7366";
					break;
				case "yellow":
					return b == "bd" ? "#333" : "#F90";
					break;
				case "green":
					return b == "bd" ? "#016801" : "#008C00";
					break;
				case "blue":
					return b == "bd" ? "#005EC7" : "#82B7F1";
					break;
				case "purple":
					return b == "bd" ? "#AE037B" : "#DA90C8";
					break;
				case "black":
					return b == "bd" ? "#000" : "#555";
					break;
			}
		}
		//IE浏览器中工具条高度修复
		if($.browser.msie){
			var _ex = parseInt(_tools.css("width"));
			_tools.css("width",_ex);
		};

		_savetool.click(function(){
			unloadselectbox();
			var _editBox = _canvas.find(".editBox"),
				_tempText = [],
				_editItem,
				_text;
			for(var i = 0; i < _editBox.length; i++){
				_editItem = _editBox.eq(i)
				_tempText.push('{"width":'+ parseInt(_editItem.css('width')) +',"height":'+ parseInt(_editItem.css('height')) +',"left":'+ parseInt(_editItem.css('left')) +',"top":'+ parseInt(_editItem.css('top')) +',"txt":"'+ _editItem.find('ul').text()+'"}');
			};
			_text = '{"width":'+ B.width +',"height":'+ B.height +',"background":"'+ B.background +'","list":['+ _tempText.join(',') +']}';
			alert(_text);
		});
	};
})(jQuery);
