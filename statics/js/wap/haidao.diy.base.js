define(["require", "diy"], function(require, diy){
	return {
		decode: function(code){
			return eval("("+$.base64.decode(code)+")");
		},
		encode: function(code){
			code = JSON.stringify(code);//IE8以下存在不兼容问题
			return $.base64.encode(code);
		},
		createPanel: function(h, m, d){
			var _html = '';
			if(m == "global" || m == "content"){
				_html = '<div class="app-field" data-model="'+ m +'" data-diy="'+ d +'"><div class="demo">'+ h +'</div><div class="shade"></div></div>';
			}else if(m == "menu"){
				_html = '<div class="app-field app-ban-field" data-model="'+ m +'" data-diy="'+ d +'">'+ h +'</div>';
			}else{
				_html = '<div class="app-field" data-model="'+ m +'" data-diy="'+ d +'"><div class="demo">'+ h +'</div><div class="actions"><div class="actions-wrap"><span class="action edit">编辑</span><span class="action delete">删除</span></div></div></div>';
			}
			return _html;
		},
		createOpt: function(h){
			$(".sidebar-content").html('<form class="control-form">'+ h +'</form>');
		},
		parsePath: function(path){	//序列化数据为json二维数组
			var steps = [];
			var key = path.substr(0, path.indexOf('['));
			if(!key.length){
				steps.push(path);
			}else{
				path = path.substr(path.indexOf('['),path.length);
				var keyNum = parseInt(path.substr(1, path.indexOf(']')-1));
				path = path.substr(path.indexOf(']'),path.length);
				var keyVal = path.replace(/[\W]/g,'')
				steps.push(key);
				steps.push(keyNum);
				steps.push(keyVal);
			}
			return steps;
		},
		newCssFile: function(n, v){	//加载模块自定义css
			var h = document.getElementsByTagName('HEAD').item(0);
			function creatfile(t){
				var l = document.createElement("link");
					l.type = "text/css";
					l.rel = "stylesheet";
					l.href = t;
					l.id = "css_" + n;
					h.appendChild(l);
			}
			if(typeof v == 'object'){
				for(var i=0; i<v.length; i++){
					creatfile(v[i]);
				}
			}else{
				creatfile(v);
			}
		},
		confirms: function(opts){
			var options = $.extend({
				text: "您是否要执行此操作？",
				input: false,
				outer: undefined,
				callback: function(){}
	        },
	        opts);
			
			if(!options.outer){
				alert("请先指定对象！");
				return false;
			}
			
			//若传递过来的非对象先转化为对象
			if(typeof options.outer != "object") options.outer = $(options.outer);
			
			if($(".hd-popover").length > 0) $(".hd-popover").remove();
			
			var $pop = $('<div class="hd-popover bottom">');
			var $sure = $('<a href="javascript:;" class="button bg-main sure">确定</a>');
			var $cancel = $('<a href="javascript:;" class="button bg-gray-white cancel">取消</a>');
			var $handle = $('<div class="hd-popover-handle">');
			var $inner = $('<div class="hd-popover-inner clearfix">');
			
			$handle.append($sure);
			$handle.append($cancel);
			$inner.append('<div class="hd-popover-text">'+ options.text +'</div>');
			$inner.append($handle);
			$inner.append('<div class="arrow">');
			$pop.append($inner);
			
			$('body').append($pop);
			
			if(options.input){
				$pop.find("input").focus();
				$pop.find("input").select();
			}
			
			var x = options.outer.offset().left - $pop.width() + options.outer.outerWidth(true) + 5 + 'px';
			var y = options.outer.offset().top + parseInt(options.outer.css("paddingTop")) + options.outer.height() + 5 + 'px';
			
			$pop.css({left: x, top: y});
			
			$('body').on('click', function(){
				$pop.remove();
			});
			
			$pop.on('click', function(e){
				e.stopPropagation();
			});
			
			$sure.on('click', function(e){
				e.stopPropagation();
				if($.isFunction(options.callback)){
					if(options.input){
						options.callback($pop.find("input").val());
					}else{
						options.callback(true);
					}
					$pop.remove();
					return false;
				}
			})
			
			$cancel.on('click', function(){
				$pop.remove();
			})
			
		},
		upLoad: function(){
			var uploader = WebUploader.create({
		        auto:true,
		        fileVal:'upfile',
		        // swf文件路径
		        swf: './statics/js/upload/uploader.swf',
		        // 文件接收服务端。
		        server: upload_url,
		        // 选择文件的按钮。可选
		        formData:{
		            //img_id : $id,
		            file : 'upfile',
			        code : upload_code
		        },
		        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
		        pick: {
		            id: '.control-load'
		        },
		        accept:{
		            title: '图片文件',
		            extensions: 'gif,jpg,jpeg,bmp,png',
		            mimeTypes: 'image/*'
		        },
		        chunked: false,
		        chunkSize:1000000,
		        resize: false
		    })
	
		    uploader.onUploadSuccess = function(file, response) {
		    	var $load = $(".control-load.current");
		    	$load.find('.webuploader-pick').html('替换上传');
		    	if(response.status == 1) {
		    		if($load.data('id') != undefined){
		    			$load.parents(".control-pics").append('<li class="hidden"><input type="hidden" name="imgs['+$load.data("id")+'][src]" value="'+response.result.url+'"><input name="imgs['+$load.data("id")+'][title]" type="hidden" /><input name="imgs['+$load.data("id")+'][url]" type="hidden" /></li>');
		    		}else{
		    			$load.parents(".control-loads").find(".control-load-img").attr("src", response.result.url);
		    			$load.parents(".control-loads").find(".control-load-input").val(response.result.url);
		    		}
		    		diy.reload();
		    	} else {
		    		alert(response.message);
		    		return false;
		    	}
		    }
		    
		    $(document).on('click','.control-load',function(){
		    	$(".control-load").removeClass("current");
		    	$(this).addClass("current");
		    });
		},
		creatModal: function(h){
			$("body").append('<div class="modal-wrap"><div class="modal">'+ h +'</div><div class="modal-backdrop"></div></div>');
		}
	}
})