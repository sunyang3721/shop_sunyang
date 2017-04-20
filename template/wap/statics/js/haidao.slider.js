var hdSlider = function(options){
	
	if(!options.dom){
		throw new Error("DOM元素不能为空！");
	}
	
	this.opts = options;
    this.setting();
   	this.renderHtml();
   	this.bindHandler();
	
}

hdSlider.prototype.setting = function(){
	
	var opts = this.opts;
	
	this.wrap = this.opts.dom;
	this.isTitle = opts.isTitle || false;			//是否开启标题文字提示
	this.isIndicator = opts.isIndicator || false;	//是否开启指示器提示
	this.isVertical = opts.isVertical || false;		//是否设置滚动轴为true=Y轴，默认false=X轴
	this.axis = this.isVertical ? 'Y' : 'X';		
	this.width = this.wrap.clientWidth;
    this.height = this.wrap.clientHeight;
    this.ratio = this.height / this.width;
	this.scale = opts.isVertical ? this.height : this.width;
	this.duration = opts.duration || 3000;			//自动播放间隔时间
	this.index = 0;
	
	this.els = getElementsByClassName("hd-slider-item") || getElementsByClassName(opts.childName);
	this.sliderIndex = this.els.length;
	this.imgs = [];
	
	//获取图片
	for(var i=0;i<this.sliderIndex;i++){
		this.imgs.push(this.els[i].getElementsByTagName("img")[0]);
	}
	
	if (this.sliderIndex < 2) {
        this.isAutoPlay = false;
        this.isLoop = false;
    } else {
        this.isAutoplay = opts.isAutoplay || false;
        this.isLoop = opts.isLoop || false;
    }
	
	//获取子级容器
	if(!this.outer){
		this.outer = getElementsByClassName("hd-slider-group")[0];
	}else{
		this.outer = opts.outer;
	}
	
	//开启自动播放
	if(this.isAutoplay){
        this.play();
    }
	
    /*
	 * 封装的通过class获取元素的方法，
	 * IE8以下不支持getElementsByClassName，因此使用以下方法来获取
	 */
    function getElementsByClassName(className){  
	    var elems = [];  
	    if(!document.getElementsByClassName){  
	        var dom = document.getElementsByTagName("*");  
	        for(var i =0 ;i<dom.length;i++){  
	            if(dom[i].className){  
	                var classs = dom[i].className.split(" ");  
	                for(var c = 0;c<classs.length;c++){  
	                    if(classs[c]==className){  
	                        elems.push(dom[i]);              
	                    }  
	                }  
	            }  
	        }  
	    }else{  
	        var dom = document.getElementsByClassName(className);  
	        for(var i =0 ;i<dom.length;i++){  
	            elems.push(dom[i]);      
	        }  
	    }  
	    return elems;  
	}
    
    //这里选择轮播滚动的效果，默认为default，后期可拓展效果
    this.animateFuns = (this.opts.animateType in this.animateFuns) 
    ? this.animateFuns[this.opts.animateType] 
    : this.animateFuns['default'];
    
}

hdSlider.prototype.renderHtml = function(){
	//添加标题文字说明
	if(this.isTitle){
		for(var i=0;i<this.sliderIndex;i++){
			var _node = document.createElement("p");
			_node.className = "hd-slider-title";
			_node.innerHTML = this.imgs[i].title;
			this.imgs[i].parentNode.appendChild(_node);
		}
	}
	//添加指示器
	if(this.isIndicator){
		this.indicator = ind = document.createElement("div");
		ind.className = 'hd-slider-indicator';
		for(var i=0;i<this.sliderIndex;i++){
			ind.innerHTML = ind.innerHTML + '<div class="hd-indicator"></div>';
		}
		ind.children[0].className = ind.children[0].className + ' hd-active';
		this.wrap.appendChild(ind);
	}
}

//效果库
hdSlider.prototype.animateFuns = {

	'default': function(dom, axis, scale, i, duration){
		dom.style.webkitTransitionDuration = duration+'ms';
		dom.style.webkitTransform = 'translateZ(0) translate' + axis + '(-' + (scale*i) + 'px)';
		dom.style.webkitTransitionTimingFunction = 'cubic-bezier(0.165, 0.84, 0.44, 1)';
	}
	
}

hdSlider.prototype.slide = function(){
	var self = this;
	self.index++;
	self.animateFuns(this.outer,this.axis,this.scale,self.index,500);
	setTimeout(function(){
    	if(self.index>self.sliderIndex){
        	self.index = 1;
        }
        self.animateFuns(self.outer,self.axis,self.scale,self.index,0);
        self.size = -self.index*self.scale;
        if(self.isIndicator){
	    	self.indicatorFun();
	    }
    },500);
}

hdSlider.prototype.indicatorFun = function(){
	for(var i=0;i<this.sliderIndex;i++){
		this.indicator.childNodes[i].className = "hd-indicator";
	}
	var num = this.isLoop?1:0;
	this.indicator.childNodes[this.index-num].className = "hd-indicator hd-active";
}

hdSlider.prototype.bindHandler = function(){
	
	var self = this;
	var opts = this.opts
	var outer = this.outer;
	this.size = -this.scale;
	
	//开启循环时
	if(this.isLoop){
		this.index = 1;
		this.animateFuns(this.outer,this.axis,this.scale,1,0);
		//克隆第一块和第二块
		var FIRST_NODE = this.els[0].cloneNode(true);
		var LAST_NODE = this.els[this.sliderIndex-1].cloneNode(true);
		outer.appendChild(FIRST_NODE);
		outer.insertBefore(LAST_NODE,this.els[0]);
	}else{
		this.index = 0;
		this.size = 0;
	}
	
	if(this.isVertical){
		outer.style.whiteSpace = "normal";
	}
	
	
	
	var startHandler = function (evt) {
		self.pause();
    	self.startTime = new Date().getTime();
    	self.startX = evt.targetTouches[0].pageX;
    	self.startY = evt.targetTouches[0].pageY;
	};

    var moveHandler = function (evt) {
        evt.preventDefault();
		var axis = self.axis;
        var offset = evt.targetTouches[0]['page' + axis] - self['start' + axis];
		outer.style.webkitTransform = 'translateZ(0) translate' + axis + '(' + (self.size + offset) + 'px)';
        outer.style.webkitTransitionDuration = '0ms';
        self.offset = offset;
    };

    var endHandler = function (evt) {
        var boundary = self.scale / 2;
        var metric = self.offset;
        var endTime = new Date().getTime();

        //快速滑动的时间必须在300ms内
        //滑块至少应该滑动14px以上
        boundary = endTime - self.startTime > 300 ? boundary : 14;
		if (metric >= boundary) {
            self.index = self.index-1;
        } else if (metric < -boundary) {
            self.index = self.index+1;
        }
        
        if(!self.isLoop){
        	if(self.index>self.sliderIndex-1){
        		self.index = self.sliderIndex-1;
        	}else if(self.index<0){
        		self.index = 0;
        	}
        }
        
        self.animateFuns(outer,self.axis,self.scale,self.index,500);
        
        if(metric!=0 && self.isLoop){
        	if(self.index==0){
	        	self.index = self.sliderIndex;
	        }else if(self.index>self.sliderIndex){
	        	self.index = 1;
	        }
		    setTimeout(function(){
	        	self.animateFuns(outer,self.axis,self.scale,self.index,0);
		        self.size = -self.index*self.scale;
	        },500)
        }
        
        if(self.isIndicator){
	    	self.indicatorFun();
	    }
        
        self.isAutoplay && self.play();
        self.size = -(self.scale*self.index);
        self.offset = 0;
        
    };

	outer.addEventListener('touchstart', startHandler);
    outer.addEventListener('touchmove', moveHandler);
    outer.addEventListener('touchend', endHandler);
}

hdSlider.prototype.play = function() {
    var self = this;
    var duration = this.duration;
    clearInterval(this.autoPlayTimer);
    this.autoPlayTimer = setInterval(function () {
        self.slide();
    }, duration);
};

hdSlider.prototype.pause = function() {
    clearInterval(this.autoPlayTimer);
};