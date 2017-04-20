window.gearArea = (function(){
	
	var a = function(){
		this.rate = 1;
	}
	
	a.prototype = {
		init: function(opts){
			
			var that = this;
			this.opts = $.extend({
				url: null,
				outor: undefined,
				checkValue: undefined,
				address: '',
				callback: function(){}
			}, opts);
			this.country = document.getElementById("country");
			this.stopGear = false;
			this.initCountry = undefined;
			
			//初始化已有的数据
			if(this.opts.address){
				var address = this.opts.address;
					address = address.split(" ");
				this.initCountry = address[0];
				this.country.setAttribute("data-value", address[0]);
				var adr_txt = '';
				for(var i = 1; i < address.length; i++){
					adr_txt += address[i] + " ";
				}
				this.opts.outer.value = adr_txt;
			}
			
			//获取国家数据
			var d = that.getData(0),
				t = '';
			for(var i = 0;i < d.length; i++){
				if(this.initCountry == d[i].name){
					that.countryId = d[i].id;
					t += '<option value="'+ d[i].id +'" data-location="'+ d[i].location +'" selected="selected">'+ d[i].name +'</option>';
				}else{
					t += '<option value="'+ d[i].id +'" data-location="'+ d[i].location +'">'+ d[i].name +'</option>';
				}
			}
			that.country.innerHTML = t;
			
			function createModal(){
				var f_d = that.getData(that.country.value);
				var lists = '', txt;
				for(var i = 0; i < that.itemNum; i++){
					if(i == 0){
						txt = that.returnItem(f_d);
					}else{
						f_d = that.getData(f_d[0].id);
						txt = that.returnItem(f_d);
					}
					lists += '<div class="roll-item"><div class="gear gear-item-'+ i +'" data-length="'+ txt.length +'" data-child="0">'+ txt.items +'</div><dig class="area-grid"></dig></div>';
				}
				
				var div = document.createElement("div");
					div.className = "gear-area";
					div.innerHTML = '<div class="area-wrap"><div class="area-control border-bottom mui-clearfix"><div class="area-btn mui-pull-left text-gray js-area-cancel">取消</div><div class="area-btn mui-pull-right text-blue js-area-sure">确定</div></div><div class="area-roll-mask"><div class="area-roll">'+ lists +'</div></div></div></div>';
					
				document.body.appendChild(div);
				
				that.bindEvent();
				
			}
			
			this.country.addEventListener('change', function(){
				if(this.value != that.countryId){
					if(that.opts.outer.value){
						that.oldValue = that.opts.outer.value;//当国家ID不同时清除地区，并保存留作取消时用
						that.oldId = document.querySelector(".district-text-id").value;
					}
					that.opts.outer.value = '';
					document.querySelector(".district-text-id").value = '';
				}else{
					that.opts.outer.value = that.oldValue;
					document.querySelector(".district-text-id").value = that.oldId;
				}
			});
			
			this.opts.outer.addEventListener('tap', function(){
				if(that.countryId == that.country.value && document.querySelector(".gear-area")){
					document.querySelector(".gear-area").className = 'gear-area';
				}else{
					if(document.querySelector(".gear-area")) document.body.removeChild(document.querySelector(".gear-area"));
					that.countryId = that.country.value
					var c_s_id = that.country.options.selectedIndex;
					var local = that.country.options[c_s_id].getAttribute("data-location");
					local = local.split(",");
					that.itemNum = local.length;
					createModal();
				}
			})
			
		},
		getData: function(d){
			var self = this;
				self.stopGear = true;
			var f = {
				cache: false,
				async: false, 
		        type: 'post',
		        url: self.opts.url,
		        data: {id:d},
		        dataType: 'json',
		        success: function(data){
		        	datas = data;
		        	self.stopGear = false;
		        }
			}
			
			$.ajax(f);
			
			return datas;
		},
		returnItem: function(arr){
			var obj = {
				items: '',
				length: 0
			};
			if(!arr) return obj;
			for(var i = 0; i < arr.length; i++){
				obj.items += '<div class="item" data-id="'+ arr[i].id +'" data-level="'+ arr[i].level +'">'+ arr[i].name +'</div>';
				obj.length = i + 1;
			}
			return obj;
		},
		bindEvent: function(){
			
			var _self = this;
				_self.moveDist = 0;
			
			function gearTouchStart(e) {
                e.preventDefault();
                if(_self.stopGear) return false;//ajax数据加载时禁止滚动
                this.startY = e.targetTouches[0].screenY; //起始位置，基于screen屏幕
                if(!this.startTop) this.startTop = 0;
                
            }
            
            function gearTouchMove(e) {
                e.preventDefault();
                if(_self.stopGear) return false;//ajax数据加载时禁止滚动
               	_self.moveDist = (e.targetTouches[0].screenY - this.startY) * 20 / window.innerHeight;
               	_self.slideTo(this, _self.moveDist + this.startTop, 0);
            }
            
            function gearTouchEnd(e) {
                e.preventDefault();
                if(_self.stopGear) return false;//ajax数据加载时禁止滚动
                var that = this;
                var childs = this.getElementsByTagName("div"),
                	maxlen = -((childs.length - 1) * _self.rate);
                this.startTop = parseFloat(_self.moveDist) + parseFloat(this.startTop);
                
                if(this.startTop > 0){
                	this.startTop = 0;
                }else if(this.startTop < maxlen){
                	this.startTop = maxlen;
                }else{
                	var r = Math.abs(this.startTop);
	            	var rn = parseInt(r / _self.rate);
	            	var ry = Math.abs(r % _self.rate);//求余
	                if(ry < _self.rate / 2){
	                	this.startTop = -rn * _self.rate;
	                }else{
	                	this.startTop = -(rn + 1) * _self.rate;
	                }
                }
				_self.slideTo(this, this.startTop, 50);
				
				setTimeout(function(){
					_self.moveDist = 0;
	                var current = parseInt(Math.abs(that.startTop) / _self.rate);
	                that.setAttribute("data-child", current);
	                that.setAttribute("data-top", that.startTop);
	                
	                //更换数据
	                var level = childs[current].getAttribute("data-level");
	                var num_id = current; //每个
	                var _data = undefined;//_self.getData(childs[current].getAttribute("data-id"));//数据存储对象
	                for(var i = level; i < _self.itemNum; i++){
	                	
	                	var _prev = document.querySelector(".gear-item-"+ (i - 1));//当前滑动元素前面的一个兄弟节点
	                	var _selector = document.querySelector(".gear-item-"+ i); //当前滑动元素其后面的兄弟节点
	                	
	                	var g_id = _prev.getElementsByTagName("div")[num_id].getAttribute("data-id");
	                	_data = _self.getData(g_id);
	                	if(!_data){
	                		for(var j = i; j < _self.itemNum; j++){
	                			var j_elem = document.querySelector(".gear-item-"+ j);
	                			j_elem.innerHTML = '';
	                			_self.slideTo(j_elem, 0, 0);
	                			j_elem.startTop = 0;
	                			j_elem.setAttribute("data-top", 0);
	                		}
	                		break;
	                	}else{
	                		var _item = _self.returnItem(_data);
		                	
		                	_selector.innerHTML = _item.items;
		                	if(_selector.getAttribute("data-child") > _item.length){
		                		_selector.setAttribute("data-child", _item.length);
		                		var s_top = -(_item.length - 1) * _self.rate;
		                		_self.slideTo(_selector, s_top, 80);
		                		_selector.startTop = s_top;
		                		_selector.setAttribute("data-top", s_top);
		                	}
		                	num_id = _selector.getAttribute("data-child");
	                	}
	                }
				}, 80);
				
            }
            
            for(var i = 0; i < _self.itemNum; i++){
				var selector = document.querySelector(".gear-item-"+ i);
				selector.addEventListener('touchstart', gearTouchStart, false);
            	selector.addEventListener('touchmove', gearTouchMove, false);
            	selector.addEventListener('touchend', gearTouchEnd, false);
			}
            
            var gear = document.querySelector(".gear-area");
            
            //取消地区选择
            document.querySelector(".js-area-cancel").onclick = function(){
            	gear.className = 'gear-area mui-hidden';//隐藏地区选择
            }
            
            //确定地区选择并获取选中地区
            document.querySelector(".js-area-sure").onclick = function(){
            	var _select = {};
            	for(var i = 0; i < _self.itemNum; i++){
            		var _g = document.querySelector(".gear-item-"+ i);
            		var _c_id = _g.getAttribute("data-child") || 0;
            		var _c = _g.getElementsByTagName("div")[_c_id];
            		_select[_c.getAttribute("data-id")] = _c.innerHTML;
            	}
            	_self.opts.callback(_select);
            	gear.className = 'gear-area mui-hidden';//隐藏地区选择
            }
            
		},
		slideTo: function(elem, y, t){
			elem.style.transform = "translate3d(0, " + y + "rem, 0)";
			elem.style.webkitTransform = 'translateZ(0) translateY(' + y + 'rem)';
			elem.style.transitionDuration = t + "ms";
		}
	}
	
	return a;
	
})();
