define(["require", "panel", "opts", "base"], function(require, panel, opts, base){
	return {
		init: function(config){
			
			this.config = config;
			
			var _P = require("panel"),
				_B = require("base");

			//根据配置生成对应的模块名
			var region = '';
			for(var key in config){
				if(config[key].name){
					region += '<li><a class="new-field" data-field="'+key+'">'+ config[key].name +'</a></li>';
				}
				//加载配置文件中的js、css文件
				if(config[key].jsfile) _B.newJsFile(key, config[key].jsfile);
				if(config[key].cssfile) _B.newCssFile(key, config[key].cssfile);
			}
			if(region) $(".app-add-region ul").html(region);
			
			//获取前台首页页面内容
			var $app = document.getElementById("diyHtml").innerHTML.match(/(diy[^\}]+)/ig);                                             
			if(!$app){
				alert("没有相关内容！");
				return false;
			}
			
			//根据配置生成对应模块HTML
			var module = '';
			for(var i = 0; i < $app.length; i++){
				var _type = $app[i].split(" ");
				try{
					var _html = _P[_type[1]](_type[2]);	//获取模板代码
	       				_html = _B.createPanel(_html, _type[1], _type[2]);//创建并生成field模板html
		   			if(_type[1] == "global"){
		   				$(".app-config").append(_html);
		   			}else{
		   				module += _html;
		   			}
		   			if(i == 0) opts[_type[1]](_type[2], config[_type[1]]);	//加载第一个模块的操作面板
	       		}catch(e){
	       			alert("没有找到对应的 "+ _type[1] +" 模块！");
	       		}
			}
			$(".app-fields").append(module);
			if(module) $(".app-field").eq(0).addClass("editing");
	       	this.events();
		},
		events: function(){
			
			var _this = this,
				_P = require("panel"),
				_B = require("base");
			var _config = this.config;
			
			//滚动条事件
			if($(".app-add-region").length > 0){
				$(".app-content").css({minHeight: $(".app-add-region").offset().top - $(".app-entry").offset().top +"px"});
				$(".app-preview").css({paddingBottom: $(".app-add-region").height() - 2});
			}
		
			//增加板块
			$(".app-add-region").on('click','a',function(){
				var $type = $(this).data("field");
				try{
		   			var _html = _P[$type]();	//获取模板代码
	       				_html = _B.createPanel(_html, $type, '');//创建并生成field模板html
		   			if($(".app-fields .editing").length > 0){
		   				$(".app-fields .editing").after(_html);
		   				$(".app-fields .editing").next(".app-field").trigger("click");
		   			}else{
		   				$(".app-fields").append(_html);
		   				$(".app-fields .app-field:last-child").trigger("click");
		   			}
				}catch(e){
					//alert("没有找到对应的 "+$type+" 模块！");
				}
			});
			
			//控制面板input事件监听，有值改变时触发reload重置对应的app-field里data-diy的值
			$(".sidebar-content").on('change','.control-form input',function(){
				_this.reload();
			});
			
			//验证
			/*$(".sidebar-content").focusout(function(){
				_this.validate();
			});*/
			
			//板块拖拽
			$('.app-fields').sortable({
				axis:'y',
				opacity: 0.8,
				cancel: '.app-ban-field',	//禁止拖拽
				scroll: true,
				scrollSensitivity: 100,
				addClasses: false,
				stop: function(event,ui){
					$(".app-sidebar").css({marginTop:$(".app-field.editing").offset().top-$(".app-sidebar").parent().offset().top});
				}
			});
			
			//编辑
			$(".app-content").on("click",".actions .edit",function(){
				document.body.scrollTop = $(this).parents(".app-field").offset().top - 37;
			});
			
			//编辑
			$(".app-content").on('click','.app-field',function(){
				var $model = $(this).data("model");
				//已是编辑状态下不执行下面的事件
				if($(this).hasClass("editing") || $model == 'content' || $model == 'menu'){
					return false;
				}
				//点击新板块，创建新容器时先保存上一个板块的内容
				if(!$(this).hasClass("editing")&&$(".app-field.editing").length>0){
					_this.reload();
				}
				var $this = $(this);
				$(".app-content .app-field").removeClass("editing");
				$this.addClass("editing");
				$(".app-sidebar").css({marginTop:$this.offset().top-$(".app-sidebar").parent().offset().top});
				document.body.scrollTop = $this.offset().top - 37;
				if($model){
					opts[$model]($this.data('diy'), _config[$model]);
				}
			});
			
			//删除
			$(".app-content").on("click",".actions .delete",function(e){
				e.stopPropagation();
				var $this = $(this);
				_B.confirms({
					outer: $this,
					text: '是否确定删除？',
					callback: function(ret){
						if(ret){
							var p = $this.parents(".app-field");
							var pp = $this.parents(".app-field").prev();
							p.remove();
							pp.trigger("click");
						}
					}
				});
			});
			
			//上架
			$(".app-actions .btn-add").click(function(){
				_this.formSubmit();
			});
			
			//取消
			$(".app-actions .btn-cancel").click(function(){
				//TODO
			});
			
			//下拉菜单
			$(document).unbind('mouseover').on('mouseover', '.dropdown', function(){
				var top = $('body').height() - $(this).offset().top - 70;
				$(this).addClass("hover");
				if(top < $(this).children('.dropdown-menu').outerHeight()) $(this).addClass("bottom");
			})
			$(document).unbind('mouseout').on('mouseout', '.dropdown', function(){
				$(this).removeClass("hover", "bottom");
			});
			$(document).unbind('click').on('click', '.dropdown .dropdown-menu a', function(){
				$(this).parents(".dropdown").blur();
				$(this).parents(".dropdown").removeClass("hover");
			});
			
			//颜色重置
			$(document).unbind('click').on('click','.color-reset',function(){
				if($(this).prev("input").val() != $(this).data("reset")){
					$(this).prev("input").val($(this).data("reset"));
					_this.reload();
				}
			});
			
			//模板选择
			//打开弹窗
			$(".sidebar-content").on('click', '.js-template-select', function(){
				var $this = $(this);
				var temp = {};
					temp.content = _config[$this.data("modal")].libs.template;
					temp.title = $this.data("title");
					temp.tip = $this.data("tip");
					temp.checked = $this.next("input").val();
				top.dialog({
					url: dialog_temp,
					title: "loading...",
					data: temp,
					width: 728,
					onclose: function(){
						if(this.returnValue || this.returnValue == 0){
							$this.next("input").val(this.returnValue);
							_this.reload();
						}
					}
				}).showModal();
			})
			
			//链接选择
			$(document).on('click', '.js-modify-link .js-modal-popup', function(e){
				e.stopPropagation();
				var $parents = $(this).parents(".js-parents");
				var $link = $(this).attr("href");
				top.dialog({
					url: $link,
					title: "loading...",
					width: 500,
					onclose: function(){
						if(this.returnValue){
							$parents.find(".link-title").val(this.returnValue.title);
							$parents.find(".link-val").val(this.returnValue.url);
							_this.reload();
						}
					}
				}).showModal();
				return false;
			});
			$(document).on('click', '.js-modify-link .js-modal-link', function(e){
				e.stopPropagation();
				var $parents = $(this).parents(".js-parents");
				$parents.find(".link-title").val(this.innerHTML);
				$parents.find(".link-val").val(this.getAttribute("href"));
				_this.reload();
				return false;
			});
			$(document).on('click', '.js-modify-link .js-modal-custom', function(e){
				e.stopPropagation();
				var $this = $(this);
				var $parents = $(this).parents(".js-parents");
				_B.confirms({
					outer: $this.parents(".js-modify-link"),
					text: '<input type="text" class="input" placeholder="链接地址：http://example.com" autofocus="true" />',
					input: true,
					callback: function(ret){
						if(ret){
							ret = ret.replace("http://", "");
							ret = "http://" + ret;
							$parents.find(".link-title").val("外链 / "+ ret);
							$parents.find(".link-val").val(ret);
							_this.reload();
						}
					}
				});
			});
			//删除链接
			$(document).on('click', '.js-modify-link .remove', function(e){
				var $parents = $(this).parents(".js-parents");
				$parents.find(".link-title").val("");
				$parents.find(".link-val").val("");
				_this.reload();
			});
			
			//编辑标题
			$(document).on('click', '.js-edit-title', function(e){
				e.stopPropagation();
				var $this = $(this);
				_B.confirms({
					outer: $this,
					text: '<input type="text" class="input" value="'+ $this.text() +'" maxlength="20" />',
					input: true,
					callback: function(ret){
						if(ret){
							$this.text(ret);
							$this.next("input").val(ret);
							_this.reload();
						}
					}
				});
			});
			
			//操作面板删除小模块功能
			$(document).on('click', '.choices .choice .close-modal', function(){
				var $parent = $(this).parent();
				$.each($parent.siblings($parent), function() {
					var $id = $(this).data("id");
					if($id > $parent.data("id")){
						$(this).data("id",	$id - 1);
						$.each($(this).find("input"), function(){
							var name = $(this).attr("name").replace($id, $id - 1);
							$(this).attr("name",name)
						});
					}
				});
				$parent.remove();
				_this.reload();
			})
			
		},
		formSubmit: function(){
			var arr = [], flog = true, that = this;
			that.reload();
			$.each($(".app-content").find(".app-field"), function() {
				if($(this).data('error')){
					if($(this).find(".actions").length > 0){
						$(this).find(".actions .edit").trigger("click");
					}else{
						$(this).trigger("click");
						document.body.scrollTop = $(this).offset().top - 37;
					}
					that.validate();
					flog = false;
				}    
				arr.push('{diy '+$(this).data("model")+' '+$(this).data("diy")+'}');
			});
			
			if(flog){
				$.post('#',{content:arr},function(ret){
					if(ret.status == 1){
						var d = dialog({
						    content: '<div class="bg-white text-small" style="padding: 10px 20px; border: 2px solid #1380cb;">保存成功！</div>'
						});
						d.show();
						setTimeout(function () {
						    d.close().remove();
						    window.location.href = ret.referer;
						}, 1000);
					}
				},'json');
			}
		},
		reload: function(){
			var _P = require("panel"), _B = require("base");
			var vals = $('.sidebar-content').find("form").serializeArray();
			var results = {};
			var _config = this.config;
			for(var k in vals){
				var steps = _B.parsePath(vals[k].name);//将name为[name][0][name]格式的分解
				if(steps.length<=1){
					if(!results[steps[0]]){
						results[steps[0]] = vals[k].value;
					}else{
						if(typeof results[steps[0]] == "object"){
							results[steps[0]].push(vals[k].value);
						}else{
							results[steps[0]] = [results[steps[0]]];
							results[steps[0]].push(vals[k].value);
						}
					}
				}else{
					var s1 = steps[0],
						s2 = steps[1],
						s3 = steps[2];
					if(typeof results[s1] == 'undefined') results[s1] = [];
					if(typeof results[s1][s2] == 'undefined') results[s1][s2] = {};
					//results[s1][s2][s3] = vals[k].value;
					//相同name的值存为数组
					if(!results[s1][s2][s3]){
						results[s1][s2][s3] = vals[k].value;
					}else{
						if(typeof results[s1][s2][s3] == "object"){
							results[s1][s2][s3].push(vals[k].value);
						}else{
							results[s1][s2][s3] = [results[s1][s2][s3]];
							results[s1][s2][s3].push(vals[k].value);
						}
					}
				}
			}
			results = _B.encode(results);
	       	var $edit = $(".app-field.editing"),
	       		$index = $edit.index(".app-field"),
	       		$model = $edit.data("model"),
	       		$html = _P[$model](results),
	       		$field = _B.createPanel($html, $model, results);
			$(".app-content").find(".app-field").eq($index).replaceWith($field);
			$(".app-content").find(".app-field").eq($index).addClass("editing");
			opts[$model](results, _config[$model]);
			this.validate();
		},
		validate: function(){
			var validation = {
				required: function(str){	//不能为空
					if(!str) return "不能为空！";
				},
				digits: function(str){	//只能输入数字[0-9]
					if(!str.match(/^\d+$/)) return "请输入数字！";
				},
				txtrange: function(str,para){	//字符串长度
					var paraName = para.split(",");
					if(str.length<parseInt(paraName[0])||str.length>parseInt(paraName[1])){
						return "字符长度必须是"+paraName[0]+"到"+paraName[1]+"之间！";
					}
				},
				numrange: function(str,para){	//数值范围0-10
					var paraName = para.split(",");
					if(str<paraName[0]||str>paraName[1]){
						return "","数值范围必须是"+paraName[0]+"到"+paraName[1]+"之间！";
					}
				},
				url: function(str){	//URL
					if(!str.match(/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/)){
				    	return "请输入正确的url地址,必须以http://开头！"
				    }
				},
				integer: function(str){	//整数
					if(!str.match(/^[-\+]?\d+$/)) return "请输入整数！";
				},
				doublenum: function(str){	//浮点数
					if(!str.match(/^[-\+]?\d+(\.\d+)?$/)) return "请输入浮点数！";
				},
				maxlength: function(str,para){	//字符串最大长度
					if(str.length>para) return "不能超过"+para+"个字符！";
				},
				minlength: function(str,para){	//字符串最小长度
					if(str.length<para) return "不能少于"+para+"个字符！";
				},
				english: function(str){	//必须输入英文
					if(!str.match(/^[A-Za-z]+$/)) return "请输入英文，不区分大小写！";
				},
				cube: function(obj){
					//魔方必需选完的验证加 data-validate="cube"
					if(obj.prev("table").find("td.empty").length>0) return "必须添加满4列。";
				}
			}
			var $error = '';
			$.each($(".sidebar-content").find("[data-validate]"), function() {
				var warn, $parent = $(this).parents(".control-group");
				var $valid = $(this).data("validate").split(";");
				for(var i=0;i<$valid.length;i++){
					var $para = $valid[i].split(":");
					var $tip;
					if($para.length>1){
						$tip = validation[$para[0]]($(this).val(),$para[1]);
					}else{
						if($para[0]=="cube"){
							$tip = validation[$para[0]]($(this));
						}else{
							$tip = validation[$para[0]]($(this).val());
						}
					}
					if($tip) warn = $tip;
				}
				
				var label = '';
				//如果有自定义错误提示标头时采用自定义标题
				if($(this).data("errortitle")){
					label = $(this).data("errortitle");
				}else{
					label = $parent.find(".control-label").text().replace('*','').replace('：','')
				}
				
				if(warn!=undefined){
					$parent.addClass("error");
					//有错误提示则直接替换内容，没有则新建
					if($parent.find(".help-block").length>0){
						$parent.find(".help-block").html(label+warn);
					}else{
						$parent.find(".controls").append('<p class="help-block error-message">'+label+warn+'</p>');
					}
					$error = label+warn;
					//return false;
				}else{
					var errorTxt = $parent.find(".help-block").text();
					errorTxt = errorTxt.substring(0, label.length);
					if(errorTxt == label){
						$parent.find(".help-block").remove();
						$parent.removeClass("error");
					}
				}
			});
			if($error){
				$(".app-field.editing").data("error",$error);
			}
		}
	}
})
