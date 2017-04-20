;(function($){
	
	var defaults = {
		url: '#',
		defVal: [100000],
		data: {id:'0'},
		callback: function(){}
	}
	
	var Base = {
		setCountry: function(self){
			var data = Base.data(self.opts,0);
			if(!data) return;//数据查询参数错误！
			$.each(data, function(){
				self.country.find(".country-select-warp").append('<a href="javascript:;" data-id="'+this.id+'" data-level="'+this.level+'" data-text="'+this.location+'">'+this.name+'</a>');
				if(self.opts.defVal[0]==this.id){
					self.country.find('.country-title').html(this.name);
					self.country.find('.country-val').val(this.id);
					self.city.data("text",this.location);
					self.city.data("id",this.id);
				}
			});
		},
		setCity: function(self){
			var opts = self.opts;
			var province = self.city.data("text").split(",");
			var tab = '';
			var citySelect = '';
			for(var i=0;i<province.length;i++){
				if(i==0){
					tab += '<a class="current" href="javascript:;">'+province[i]+'</a>';
				}else{
					tab += '<a href="javascript:;">'+province[i]+'</a>';
				}
			}
			for(var i=0;i<province.length-1;i++){
				citySelect += '<div class="city-select"></div>';
			}
			
			//var data = Base.getFirstCity(Base.data(self.opts,self.city.data("id")));
			var txt = '';
			$.each(Base.data(self.opts,self.city.data("id")), function(){
				txt += '<a title="'+this.name+'" data-id="'+this.id+'" data-parentid="'+this.parent_id+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
			});
			var data = '<dl class="fn-list no-dt"><dd>'+txt+'</dd></dl>';
			
			var _con = '<div class="ct-overlay">'
						+'	<div class="city-select-warp">'
						+'		<div class="city-select-tab">'+tab+'</div>'
						+'		<div class="city-select-content">'
						+'			<div class="city-select" style="display:block;">'+data+'</div>'
						+			citySelect
						+'		</div>'
						+'	</div>'
						+'</div>';
			
			self.city.append(_con)
		},
		getHtml: function(data){
			var _html = '';
			if(!data) return false;
			$.each(data, function() {
				_html += '<a title="'+this.name+'" data-id="'+this.id+'" data-parentid="'+this.parent_id+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
			});
			return _html;
		},
		getFirstCity: function(data){
			//字母排序
			var sortBy = function (filed, rev, primer) {
			    rev = (rev) ? -1 : 1;
			    return function (a, b) {
			        a = a[filed];
			        b = b[filed];
			        if (typeof (primer) != 'undefined') {
			            a = primer(a);
			            b = primer(b);
			        }
			        if (a < b) { return rev * -1; }
			        if (a > b) { return rev * 1; }
			        return 1;
			    }
			};
			
			var $json =	new Array();
			var flog = true;
			
			$.each(data,function(){
				var t = this;
				if(t.pinyin == '' || t.pinyin == null || t.pinyin == undefined){
					$json.push({"pyid":"0","name":t.name,"id":t.id,"level":t.level,"parent":t.parent_id});
					flog = false;
				}else{
					$json.push({"pyid":t.pinyin.charAt(0),"name":t.name,"id":t.id,"level":t.level,"parent":t.parent_id});
				}
			});
		
			if(flog){
				$json.sort(sortBy('pyid', false, String));//进行首字母排序
				var t1 = t2 = t3 = t4 = '';
				var v1 =  new RegExp("^[a-g]+$");
				var v2 =  new RegExp("^[h-k]+$");
				var v3 =  new RegExp("^[l-s]+$");
				var v4 =  new RegExp("^[t-z]+$");
				$.each($json, function() {
					switch (true)
					{
						case v1.test(this.pyid):
							t1 += '<a title="'+this.name+'" data-id="'+this.id+'" data-parent-id="'+this.parent+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
							break;
						case v2.test(this.pyid):
							t2 += '<a title="'+this.name+'" data-id="'+this.id+'" data-parent-id="'+this.parent+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
							break;
						case v3.test(this.pyid):
							t3 += '<a title="'+this.name+'" data-id="'+this.id+'" data-parent-id="'+this.parent+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
							break;
						case v4.test(this.pyid):
							t4 += '<a title="'+this.name+'" data-id="'+this.id+'" data-parent-id="'+this.parent+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
							break;
						default:
					}
				});
				var ag = '<dl class="fn-list"><dt>A-G</dt><dd>'+t1+'</dd></dl>';
				var hk = '<dl class="fn-list"><dt>H-K</dt><dd>'+t2+'</dd></dl>';
				var ls = '<dl class="fn-list"><dt>L-S</dt><dd>'+t3+'</dd></dl>';
				var tz = '<dl class="fn-list"><dt>T-Z</dt><dd>'+t4+'</dd></dl>';
				return ag+hk+ls+tz;
			}else{
				var txt = '';
				$.each($json, function(){
					txt += '<a title="'+this.name+'" data-id="'+this.id+'" data-parent-id="'+this.parent+'" data-level="'+this.level+'" href="javascript:;">'+this.name+'</a>';
				});
				return '<dl class="fn-list no-dt"><dd>'+txt+'</dd></dl>';
			}
		},
		getVal: function(self){
			var vals = [];
			var _title = '';
			vals.push(self.city.data("id"));
			
			$.each(self.city.find(".city-select-content a.current"),function(i){
				if(i==0){
					_title += $(this).html();
				}else{
					_title += '<span>/</span>'+$(this).html();
				}
				vals.push($(this).data('id'));
			});
			
			if(_title!='') self.city.find(".city-title").html(_title).addClass("has-city-title");
			
			return self.opts.callback(vals,self.wrap);
			
		},
		data: function(opts,d){
			var datas = null;
			$.ajax({cache: false,async: false,type: 'post',url: opts.url,data: {id: d},dataType: 'json',success: function(data){
		        	datas = data;
		        }
			});
			return datas;
		}
	}
	
	$.fn.linkageSel = function(options){
		
		var opts = $.extend(defaults, options||{});
		return this.each(function(){
			
			if(opts.url==undefined) return;
			this.opts = opts;
			this.wrap = $('<div class="hd-select-city"></div>');
			this.country = $('<div class="select-country"></div>');
			this.city = $('<div class="select-city"></div>');
			
			this.country.append('<div class="country-title">请选择国家</div><div class="menu-button-dropdown"></div><input class="country-val" type="hidden" /><div class="country-select-warp"></div>');
			this.city.append('<div class="city-title">请选择省市区</div><div class="menu-button-dropdown"></div><input type="hidden" value="'+10000+'" />');//opts.defCountry
			this.wrap.append(this.country,this.city);
			$(this).replaceWith(this.wrap);
			
			Base.setCountry(this);
			
			this.wrap.linkageHandle(this);
			
			if(opts.defVal.length<=1) return false;

			Base.setCity(this);
			this.city.find('.ct-overlay').addClass("ct-overlay-hidden");
			for(var i=1;i<opts.defVal.length;i++){
				var _html = Base.getHtml(Base.data(this.opts,opts.defVal[i]));
				this.city.find('.city-select').eq(i).html(_html);
				this.city.find('.city-select').eq(i).data("parentid",opts.defVal[i]);
				$.each(this.city.find('.city-select').eq(i-1).find("a"),function(){
					if($(this).data("id")==opts.defVal[i]){
						$(this).addClass("current");
					}
				});
			}
			Base.getVal(this);
			
		});
		
	}
	
	$.fn.linkageHandle = function(self){
		
		self.country.on('click','a',function(e){
			$(this).addClass("selected").siblings().removeClass("selected");
			self.wrap.find('.country-select-warp').hide();
			self.wrap.find('.country-title').html($(this).html());
			if($(this).data("id")!=self.wrap.find('.country-val').val()){
				self.wrap.find('.country-val').val($(this).data("id"));
				self.city.find(".ct-overlay").remove();
				self.city.data("id",$(this).data("id"));
				self.city.data("text",$(this).data("text"));
				self.city.find(".city-title").html('请选择省市区').removeClass("has-city-title");
			}
			e.stopPropagation();
		});
		
		self.country.on('click',function(e){
			$(".country-select-warp").hide();
			$(this).children(".country-select-warp").show();
			$(".ct-overlay").addClass("ct-overlay-hidden");
			e.stopPropagation();
		});
		
		$(window).click(function(){
			$(".ct-overlay").addClass("ct-overlay-hidden");
			$(".country-select-warp").hide();
		});

		self.city.on('click',function(e){
			if($(this).children(".ct-overlay").length>0){
				$(this).children(".ct-overlay").removeClass("ct-overlay-hidden");
			}else{
				Base.setCity(self);
			}
			self.country.find(".country-select-warp").hide();
			e.stopPropagation();
		})
		
		//切换
		self.city.on('click','.city-select-tab a',function(){
			$(this).addClass("current").siblings().removeClass("current");
			self.city.find(".city-select").eq($(this).index()).show().siblings().hide();
		});
		
		//城市联动 
		self.city.on('click','.city-select a',function(e){
			
			e.stopPropagation();

			$(this).addClass("current").siblings().removeClass("current");
			
			var $parents = $(this).parents(".city-select");
			var $next = $parents.next(".city-select");
			
			if($next.data("parentid")!=$(this).data("id")){
				$parents.nextAll('.city-select').html('');
				$next.data("parentid",$(this).data("id"));
				var _html = Base.getHtml(Base.data(self.opts,$(this).data("id")));
				$parents.next(".city-select").html(_html);
			}
			if($next.length>0){
				var $index = self.city.find('.city-select').index($parents);
				self.city.find(".city-select-tab").find("a").eq($index+1).trigger("click");
			}else{
				self.city.children(".ct-overlay").addClass("ct-overlay-hidden");
			}
			
			Base.getVal(self);
			
		});
		
	}
	
})(jQuery);
