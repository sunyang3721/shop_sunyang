/*
 * 表单插件
 * 根据自定义标签自动渲染固定的html格式
 */
(function($){

	$.fn.reRenderForm = function(){

		var options = $.extend({
			layout: (this.data('layout')!=undefined&&this.data('layout')!=''?this.data('layout'):'default'),
			zIndex: this.children().length
        },
        options);

        for(var i=0;i<this.length;i++){
        	var forms = $(this).eq(i);
        	if(forms.data('reset') == undefined) {
        		forms.find('input').each(function(){
        			if($(this).data("reset") == undefined && $(this).attr("type") != "submit" && $(this).attr("type") !="hidden" && $(this).attr("type") != "reset"){
        				$(this).resetFormHtml(options);
        			}
        		})
        	}
        }

		$("[type=submit]").click(function() {
			var _form = $(this).parents("form");
			//表单验证，在已有错误验证提示的情况下或有元素不能为空的情况下有用
    		var flog = true;
    		_form.find(".hd-input").each(function(){
    			$(this).blur();
    		});
    		if(_form.find(".validation-tips").length>=1){
    			_form.find(".validation-tips").eq(0).prev().focus();
				flog = false;
    		}
    		if(!flog) return false;//阻止提交

			if(_form.data("ajax")==true){

        		//ajax提交
				var _type = _form.attr("method");
				var _url = _form.attr("action");
				var _param = _form.serialize();
				for(var i=0;i<_form.find(".file").length;i++){
					_param += '&'+_form.find(".file").eq(i).attr("name")+'='+_form.find(".file").eq(i).val();
				}

				if(_form.data("handkey")!=undefined){
					var _success = _form.data("handkey")+"_success";
					var _error = _form.data("handkey")+"_error";
				}
				if(!isExitsFunction(_success)) {
					_success = 'ajax_success';
				}
				if(!isExitsFunction(_error)) {
					_error = 'ajax_error';
				}
				_form.formSubmit(_type,_url,_param, _success, _error);//使用eval函数将字符串转为回调函数名
        		return false;

			}
		})

		if($(".laydate-icon").length>0){
			laydate.skin('danlan');
		}
		//$(".form-select-edit").selectSimulation();
		$(window).otherEvent();//其他事件

	}

	$.fn.resetFormHtml = function(opt){

		var htmlWrap = '<div class="form-group'+(opt.layout=="rank"?" form-layout-rank":"")+(opt.layout=="line"?" form-layout-line":"")+($(this).hasClass('hidden')?' hidden"':'')+'"'+(this.typetxt()=="file"||this.typetxt()=="select"?' style="z-index: '+opt.zIndex--+'"':'')+'>';
			htmlWrap += (this.data("label")!=undefined?'<span class="label">'+this.data("label")+'</span>':'');

		var attrs = this.inputAttributes();

		var inputHtml = '';
		if(this.typetxt()=="text"){
			inputHtml = this.resetTextBox(attrs);
		}else if(this.typetxt()=="password"){
			inputHtml = this.resetPassWord(attrs);
		}else if(this.typetxt()=="area"){
			inputHtml = this.resetTextArea(attrs);
		}else if(this.typetxt()=="radio"||this.typetxt()=="checkbox"){
			inputHtml = this.resetRadioOrCheck();
		}else if(this.typetxt()=="file"){
			inputHtml = this.resetFile(attrs);
		}else if(this.typetxt()=="select"){
			inputHtml = this.resetSelect();
		}

		/*
		 * 在IE8及以下使用label控件来定位显示代替placeholder
		 * 在jQuery版本1.10版本及以上不支持 $.browser.msie来获取浏览器版本，使用$.support.leadingWhitespace代替
		 * 	$.support.leadingWhitespace 为判断浏览器是否支持html5的新特性，true为支持，false为不支持
		 * 由于IE9不支持placeholder，但支持部分html5的特性，因此不得不使用jQuery版本1.10以下版本支持的$.browser.mise来判断浏览器版本
		 */
		var msieflog = false;
		var userAgent = window.navigator.userAgent.toLowerCase();
  		$.browser.msie9 = $.browser.msie && /msie 9\.0/i.test(userAgent);
		$.browser.msie8 = $.browser.msie && /msie 8\.0/i.test(userAgent);

		if($.browser.msie9&&this.attr("placeholder")!=undefined||$.browser.msie8&&this.attr("placeholder")!=undefined){
			if(this.val()){
				inputHtml += '<div class="field-tip hidden">'+this.attr("placeholder")+'</div>';
			}else{
				inputHtml += '<div class="field-tip">'+this.attr("placeholder")+'</div>';
			}
			msieflog = true;
		}

		htmlWrap += '<div class="box'+(msieflog?' hd-placeholder':'')+'">'+inputHtml+'</div>';
		htmlWrap += this.data("desc")!=undefined?'<p class="desc">'+this.data("desc")+'</p>':'';

		this.replaceWith(htmlWrap);

		if(this.data("validate")!=undefined){
			this.formValidate();
		}
	}
	//文本框、日期选择框
	$.fn.resetTextBox = function(attrs){

		var proptext = '';
		$.each(attrs,function(n,v) {
			if(v!=undefined){
				proptext += ' '+n+'="'+v+'"';
			}
		});

		var $html = '';
		if(this.data("format")!=undefined){
			$html = '<input class="input'+(this.classname()!=undefined?' '+this.classname():'')+' laydate-icon hd-input" type="text"'+proptext+'onclick="laydate({istime: true, format: \''+this.data("format")+'\' })" />';
		}else{
			$html = '<input class="input'+(this.classname()!=undefined?' '+this.classname():'')+' hd-input" type="text"'+proptext+' />';
		}
		return $html;
	}
	//密码框
	$.fn.resetPassWord = function(attrs){

		var proptext = '';
		$.each(attrs,function(n,v) {
			if(v!=undefined){
				proptext += ' '+n+'="'+v+'"';
			}
		});
		var $html = '<input class="input'+(this.classname()!=undefined?' '+this.classname():'')+' hd-input" type="password"'+proptext+' />';
		return $html;

	}
	//文本域
	$.fn.resetTextArea = function(attrs){

		var proptext = '';
		$.each(attrs,function(n,v) {
			if(v!=undefined&&n!="value"){
				proptext += ' '+n+'="'+v+'"';
			}
		});

		var $html = '<textarea class="textarea'+(this.classname()!=undefined?' '+this.classname():'')+' hd-input"'+proptext+'>'+attrs.value+'</textarea>';
		return $html;

	}
	//文件上传文本框模拟
	$.fn.resetFile = function(attrs){

		var _this = this;
		var $html = '';
		var proptext = ''
		$.each(attrs,function(n,v) {
			if(n!='name'&&n!='id'&&v!=undefined){
				proptext += ' '+n+'="'+v+'"';
			}
		});

		var preview = '';
		if(_this.data("preview")!=undefined){
			if(_this.data("preview")==''){
				preview = "../images/default_no_upload.png";
			}else{
				preview = _this.data("preview");
			}
		}

		$html = '<div class="input hd-input file-box clearfix">';
		$html += '<input class="file-txt"'+proptext+' type="text" />';
		$html += '<input class="file-btn" type="button" value="浏览" />';
		$html += '<input class="file'+(_this.data("preview")!=undefined?" file-view":"")+'" '+(attrs.id!=undefined?'id="'+attrs.id+'"':'')+' type="file" name="'+(attrs.name!=undefined?attrs.name:'')+'" />';
		if(preview!=''&&preview!=true){
			$html += '<div class="file-preview"><i class="ico_pic_show no"></i><span class="file-pic"><img src="'+preview+'" /></span></div>';
		}

		$html += '</div>';

		return $html;

	};
	//下拉框模拟
	$.fn.resetSelect = function(){

		var _this = this;
		var _type = _this.typetxt();
		var _val =  _this.val();
		var _default = jQuery.parseJSON(_this.val());
		var _selected = new Array();
		var _var_arr = new Array();
		var $html = $lists = _selected_txt = _selected_val = '';

		$.each(_default, function(i, item) {
			var flog = (i == _this.data('selected') || item == _this.data('selected')) ? true : false;
			if(i==_this.data('selected')||item==_this.data('selected')){
				_selected_txt = item;
				_selected_val = i;
			}
			if(_this.data('selected')==undefined||_this.data('selected')==''){
				_selected.push(item);
				_var_arr.push(i);
				flog = (_selected.length<=1)?true:false;
			}
			$lists += '<span class="listbox-item"'+ (flog?' data-selected="true"':'') +'" data-val="'+ i +'">'+ item +'</span>';
		});

		$html = '<div class="form-select-edit"'+(_this.id()!=undefined?_this.id():'')+'>';
		$html += '<div class="form-buttonedit-popup"><input class="input" type="text" value="'+(_selected_txt!=''?_selected_txt:_selected[0])+'" readonly="readonly" /><span class="ico_buttonedit"></span></div>';
		$html += '<div class="listbox-items">';
		$html += $lists;
		//设置初始选中值
		$html += 	'</div>'
				+	'<input class="form-select-name" type="hidden" name="'+(_this.name()!=undefined?_this.name():'')+'" value="'+(_selected_val!=''?_selected_val:_var_arr[0])+'" />'
				+'</div>';

		return $html;

	};
	//模拟下拉框触发事件
	$.fn.selectSimulation = function(){
			var _this = $(this);
			var _input = $(".form-buttonedit-popup");
			var _content = $(this).children(".listbox-items");
			var _name = $(this).children(".form-select-name");
	 		var _now = 0;
	 		var select=_content.find('.listbox-item[data-selected="true"]');
			for(var i=0;i<select.length;i++){
				if(select.eq(i)){
		 			var parent=select.eq(i).closest('.form-select-edit');
		 			parent.find(".form-buttonedit-popup").children("input").val(select.eq(i).html())
		 		}
			}
	 		
			_input.on('click',function(e){
				var num = $(this).index(".form-buttonedit-popup");
				if(!$(this).parent().hasClass('disabled')){
					if($(this).hasClass("buttonedit-popup-hover")){

						$(this).removeClass("buttonedit-popup-hover");
						$(this).next(".listbox-items").hide();
						$(this).children("input").blur();
					}else{
						$(this).addClass("buttonedit-popup-hover");
						_input.not(_input.eq(num)).removeClass("buttonedit-popup-hover");
						_input.not(_input.eq(num)).next(".listbox-items").hide();
						$(this).next(".listbox-items").show();
						$(this).next(".listbox-items").find(".listbox-item").each(function(){
							if($(this).attr("data-selected")!=undefined){
								$(this).addClass("listbox-item-selected");
							}else{
								$(this).removeClass("listbox-item-selected");
							}
						});
					}
				}
				e.stopPropagation();
			});

			_this.on('click', ".listbox-item", function(){
				_input.removeClass("buttonedit-popup-hover");

				$(this).parent().prev(".form-buttonedit-popup").children("input").val($(this).html());
				$(this).parent().next(".form-select-name").val($(this).attr("data-val")).change();
				$(this).attr("data-selected","true");
				$(this).siblings().removeAttr("data-selected");
				_content.hide();
			});
			_this.on('mouseover', ".listbox-item", function(){
				$(this).addClass("listbox-item-selected").siblings().removeClass("listbox-item-selected");
			});

			$('body').bind("click",function(){
	            _input.removeClass("buttonedit-popup-hover");
	            _content.hide();
	       	});
	};
	//单选复选框
	$.fn.resetRadioOrCheck = function(){

		var _this = this;
		var _type = _this.typetxt();
		var _val =  _this.val();
		var _default = jQuery.parseJSON(_val);
		var flog = 0;

		var $html = '';
		$.each(_default, function(i, item) {

			flog++;
			i = String(i);
			var _checked = ($.inArray(i, _this.data('checked')) != -1 || $.inArray(item, _this.data('checked')) != -1) || (_this.typetxt()=='radio'&&_this.data("checked")==undefined&&flog==1) ? ' checked' : '';
			//console.log(i+",",_this.data('checked'))
			var _disabled = ($.inArray(i, _this.data('disabled')) != -1 || $.inArray(item, _this.data('disabled')) != -1) ? ' disabled' : '';
			var _itemrows = _this.data("itemrows") || 1;

			$html += '<label class="select-wrap"><input class="select-btn" type="'+_type+'" name="'+(_this.name()!=undefined? _this.name():'')+'" value="'+ i +'"'+ _checked + _disabled +'/>'+ item +'</label>';

			if(flog % _itemrows == 0){
				$html += '</br>';
			}

		});
		return $html;

	};

	$.fn.inputAttributes = function(){

		//class,id,name,value,style,placeholder,disabled,maxlength
		var attrs = {
			id: this.id(),									//id
			name: this.name(),								//name
			value: this.val(),								//值
			style: this.attr("style"),						//样式
			placeholder: this.attr("placeholder"),			//提示
			maxlength: this.attr("maxlength"),				//输入字符最大长度
			disabled: this.attr("disabled"),				//禁用此元素
			contenteditable: this.attr("contenteditable"),	//内容是否可编辑
			readonly: this.attr("readonly"),				//输入字段为只读
			tabindex: this.attr("tabindex")					//规定元素的 tab 键次序。
		};
		return attrs;

	}
	//其他事件
	$.fn.otherEvent = function(){

		//文件上传
		$(".file-box .file").bind('change',function(){
			var $this = $(this);
			var $parent=$(this).parent();
			if($(this).hasClass("file-view")){
				var fileType = ["bmp", "gif", "png", "jpeg", "jpg"];
				if($(this).val()){
					if(!RegExp("\.(" + fileType.join("|") + ")$", "i").test(this.value.toLowerCase())){
						alert('请选择"bmp,gif,png,jpeg,jpg"图片格式！');
						$this.prevAll(".file-txt").val('');
						if($parent.find('.file-preview')){
							$parent.find('.file-preview').remove();
						}
						return false;
					}else{
						$this.prevAll(".file-txt").val($(this).val());
						if($parent.find('.file-preview')){
							$parent.find('.file-preview').remove();
						}
					}
				}
			}else{
				$this.prevAll(".file-txt").val($(this).val());
			}
		});
		$(".file-box .file-preview i").hover(function(){
			$(this).parents('.form-group').css({zIndex:'99'});
			$(this).next().show();
		},function(){
			$(this).parents('.form-group').css({zIndex:'1'});
			$(this).next().hide();
		});

		//IE9下的placeholder显示与隐藏
		$(".field-tip").on('click',function(){
			$(this).prev().focus();
			$(this).addClass("hidden");
		});
		$('.hd-placeholder .hd-input').bind('focus',function(){
			if($(this).next(".field-tip").length>0){
				$(this).next().addClass("hidden");
			}
		});
		$('.hd-placeholder .hd-input').bind('blur',function(){
			var _this = $(this);
			setTimeout(function(){
				if(_this.next(".field-tip")&&!_this.val()){
					_this.next().removeClass("hidden");
				}
			},100);
		});

	};
	//表单验证
	$.fn.formValidate = function(){

		var _this = $(this);
		var _valid = this.data("validate").split(";");
		var _param = new Array();
		for(var i=0;i<_valid.length;i++){
			_param[i] = _valid[i].split(":");
		}
		$('[name="'+$(this).name()+'"]').bind('blur',function(){
			var val = $(this).val();
			var warn;
			for(var n=0;n<_param.length;n++){

				var vname =_param[n][0];
				if(vname=="required"){
					if(isRequired(val)!=undefined) warn = isRequired(val);
				}
				if(vname=="email"){
					if(isEmail(val)!=undefined&&val!='') warn = isEmail(val);
				}
				if(vname=="mobile"){
					if(isMobile(val)!=undefined&&val!='') warn = isMobile(val);
				}
				if(vname=="digits"){
					if(isDigits(val)!=undefined&&val!='') warn = isDigits(val);
				}
				if(vname=="idcar"){
					if(istrCard(val)!=undefined&&val!='') warn = istrCard(val);
				}
				if(vname=="integer"){
					if(isIinteger(val)!=undefined&&val!='') warn = isIinteger(val);
				}
				if(vname=="firsten"){
					if(isFirstEnglish(val)!=undefined&&val!='') warn = isFirstEnglish(val);
				}
				if(vname=="rightstr"){
					if(isRightfulString(val)!=undefined&&val!='') warn = isRightfulString(val);
				}
				if(vname=="range"){
					if(isnumRange(val,_param[n][1])!=undefined&&val!='') warn = isnumRange(val,_param[n][1]);
				}
				if(vname=="rangelength"){
					if(israngeLength(val,_param[n][1])!=undefined&&val!='') warn = israngeLength(val,_param[n][1]);
				}
				if(vname=="url"){
					if(isUrl(val)!=undefined&&val!='') warn = isUrl(val);
				}
				if(vname=="maxlength"){
					if(isMaxLength(val,_param[n][1])!=undefined&&val!='') warn = isMaxLength(val,_param[n][1]);
				}
				if(vname=="minlength"&&val!=''){
					if(isMinLength(val,_param[n][1])!=undefined&&val!='') warn = isMinLength(val,_param[n][1]);
				}
				if(vname=="english"){
					if(isEnglish(val)!=undefined&&val!='') warn = isEnglish(val);
				}
				if(vname=="chinese"){
					if(isChineseChar(val)!=undefined&&val!='') warn = isChineseChar(val);
				}
				if(vname=="specialchar"){
					if(isSpecialChar(val)!=undefined&&val!='') warn = isSpecialChar(val);
				}
				if(vname=="double"){
					if(isDoubleNum(val)!=undefined&&val!='') warn = isDoubleNum(val);
				}
				if(vname=="date"&&_param[n][1]=="YYYY-MM-DD"){
					if(isDate1(val)!=undefined&&val!='') warn = isDate1(val);
				}
				if(vname=="date"&&_param[n][1]=="YYYY/MM/DD"){
					if(isDate2(val)!=undefined&&val!='') warn = isDate2(val);
				}
			}
			if(warn!=undefined){
				if($(this).next().hasClass("validation-tips")){
					$(this).next().html('<i></i>'+warn);
				}else{
					$(this).after('<div class="validation-tips"><i></i>'+warn+'</div>');
				}
			}else{
				if($(this).next().hasClass("validation-tips")){
					$(this).next().remove();
				}
			}
		})

		//不能为空
		function isRequired(str){
			if(!str) return "不能为空！";
		}
		//匹配Email地址
		function isEmail(str){
			if(!str.match(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/)) return "请输入正确的邮箱地址！";
		}

		//只能输入数字[0-9]
		function isDigits(str){
		    if(!str.match(/^\d+$/)) return "请输入数字！";
		}

		//匹配mobile
		function isMobile(str){
		    if(!str.match(/^((\(\d{2,3}\))|(\d{3}\-))?((13\d{9})|(15\d{9})|(18\d{9}))$/)) return "请输入正确的手机号码格式！";
		}

		//以字母开头
		function isFirstEnglish(str){
		    if(!str.match(/^[a-zA-Z]/)) return "必须以字母为开头！";
		}

		//判断是否为合法字符(a-zA-Z0-9-_)字母，数字，下划线
		function isRightfulString(str){
		    if(str==null||str=="") return false;
		    var result=str.match(/^[A-Za-z0-9_-]+$/);
		    if(result==null)return false;
		    return true;
		}

		//匹配身份证号码
		function istrCard(str){
		  	var aCity = { 11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江 ", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北 ", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏 ", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外 " }

	        var iSum = 0;
	        var info = "";
	        str = str.replace(/x$/i, "a");
	        if (aCity[parseInt(str.substr(0, 2))] == null) return "非法地区";
	        sBirthday = str.substr(6, 4) + "-" + Number(str.substr(10, 2)) + "-" + Number(str.substr(12, 2));
	        var d = new Date(sBirthday.replace(/-/g, "/"))
	        if (sBirthday != (d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate())) return "非法生日";
	        for (var i = 17; i >= 0; i--) iSum += (Math.pow(2, i) % 11) * parseInt(str.charAt(17 - i), 11)
	        if (iSum % 11 != 1) return "非法证号";
	        //str.substr(16, 1) % 2 ? "男" : "女"//获取身份证性别
		}

		//字符串长度
		function israngeLength(str,para){
			var paraName = para.split(",");
			if(str.length<parseInt(paraName[0])||str.length>parseInt(paraName[1])){
				return "字符长度必须是"+paraName[0]+"到"+paraName[1]+"之间！";
			}
		}

		//数值范围0-10
		function isnumRange(str,para){
			var paraName = para.split(",");
			if(str<paraName[0]||str>paraName[1]){
				return "","数值范围必须是"+paraName[0]+"到"+paraName[1]+"之间！";
			}
		}

		//URL
		function isUrl(str){
		    if(!str.match(/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/)){
		    	return "请输入正确的url地址,必须以http://开头！"
		    }
		}

		//整数
		function isInteger(str){
			if(!str.match(/^[-\+]?\d+$/)) return "请输入整数！";
		}

		//浮点数
		function isDoubleNum(str){
			if(!str.match(/^[-\+]?\d+(\.\d+)?$/)) return "请输入浮点数！";
		}

		//字符串最大长度
		function isMaxLength(str,para){
			if(str.length>para) return "不能超过"+para+"字符！";
		}

		//字符串最小长度
		function isMinLength(str,para){
			if(str.length<para) return "不能少于"+para+"字符！";
		}

		//必须输入英文
		function isEnglish(str){
			if(!str.match(/^[A-Za-z]+$/)) return "请输入英文，不区分大小写！";
		}

		//必须输入中文
		function isChineseChar(str){
			if(!str.match(/^[\u0391-\uFFE5]+$/)) return "请输入中文！";
		}

		//判断是否包含中英文特殊字符，除英文"-_"字符外
		function isSpecialChar(str){
			var reg = RegExp(/[(\ )(\`)(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\+)(\=)(\|)(\{)(\})(\')(\:)(\;)(\')(',)(\[)(\])(\.)(\<)(\>)(\/)(\?)(\~)(\！)(\@)(\#)(\￥)(\%)(\…)(\&)(\*)(\（)(\）)(\—)(\+)(\|)(\{)(\})(\【)(\】)(\‘)(\；)(\：)(\”)(\“)(\’)(\。)(\，)(\、)(\？)]+/);
    		if(reg.test(str)) return "不能输入特殊字符！";
		}

		//日期验证 2015-9-6
		function isDate1(str,para){
			var dateformat = /^[0-9]{4}-[0-1]?[0-9]{1}-[0-3]?[0-9]{1}$/;
			if(!dateformat.test(str)) return "请输入正确的日期格式！"
		}

		//日期验证 2015/9/6
		function isDate2(str,para){
			var dateformat = /^[0-9]{4}[/][0-1]?[0-9]{1}[/][0-3]?[0-9]{1}$/;
			if(!dateformat.test(str)) return "请输入正确的日期格式！"
		}

	};

	$.fn.id = function(){
		return this.attr("id");
	}
	$.fn.typetxt = function(){
		return this.attr("type");
	}
	$.fn.classname = function(){
		return this.attr("class");
	}
	$.fn.name = function(){
		return this.attr("name");
	}

	$.fn.formSubmit = function(type,url,data,callback,errorback){

		callback = callback || 'ajax_success';
		errorback = errorback || 'ajax_error';

		//loading状态
		$("body").ajaxStart(function(){
			$(this).append('<div class="form-loading">提交中...</div>');
		});
		//ajax提交成功删除提示
		$(this).ajaxSuccess(function(){
			$(".form-loading").remove();
		});
		//ajax提交失败提示
		$(this).ajaxError(function(){
			$(".form-loading").remove();
			$("body").append('<div class="form-error">提交失败！</div>');
			setTimeout(function(){
				$(".form-error").fadeOut(300).remove();
			},3000);
		});

	    $.ajax({
			cache: true,
			type: type,
			url: url,
			data: data,//serialize() 方法通过序列化表单值，创建 URL 编码文本字符串
			//async: false,
		    success: eval(callback),
		    error: eval(errorback)
		});

	}

})(jQuery);

function ajax_success(data, textStatus, jqXHR) {
	return;
}

function ajax_error(XMLHttpRequest, textStatus, errorThrown) {
	return;
}

function isExitsFunction(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}

$(document).ready(function(){
	$(".form-select-edit").selectSimulation();
	
	$(document).otherEvent();
	//$("form").reRenderForm();
})