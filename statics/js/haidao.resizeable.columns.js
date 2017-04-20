var _bind = function(fn, me){ 
	return function(){
		return fn.apply(me, arguments);
	};
},
_slice = [].slice;

(function($, window) {
	
	var resizeableColumns = (function(){
		
		resizeableColumns.prototype.defaults = {
			
		};
		function resizeableColumns($table,options){

			this.mousedown = _bind(this.mousedown, this);
			var _this = this;				//this为当前方法
			this.options = $.extend({
				checked: false
			}, this.defaults, options);//Object {}
			this.$table = $table;			//.table对象
			if($table.hasClass('resize-table')){
				$table.find('.tr').eq(0).addClass("resize-th");
			}
			$table.after('<div class="layout" id="table-get-width"></div>');
			this.setLinesWidth();			//设置表格宽度
			this.createHandles();			//调用createHandles()方法
			$(window).resize(function(){
				_this.setLinesWidth();
			});
			setTimeout(function(){			//重新定义一次宽度
				$(window).resize();
			},80);
		}
		
		resizeableColumns.prototype.setLinesWidth = function(){
			
			var $table = this.$table;
			var $padding = $table.find(".tr").eq(0).outerWidth(true) - $table.find(".tr").eq(0).width();//tr的padding值
			var tableWidth = $("#table-get-width").width();
			var tbl = parseInt($table.css('border-left-width'));
			var tbr = parseInt($table.css('border-right-width'));
			var trWidth = tableWidth - $padding - tbl - tbr;
			var totalWidth = 0;
			$table.find('.tr').each(function(){
				$(this).children(".td").each(function(){
					$(this).addClass("r"+$(this).index());
				});
			});
			$table.find(".resize-th").children(".th").each(function(){
				if($(this).attr("data-width")!=undefined){
					var $width = $(this).attr("data-width");
					var strLast = $width.substring($width.length-1,$width.length);
					var strTwo = $width.substring($width.length-2,$width.length);
					var conWidth = 0;
					if(strTwo=='px'){
						conWidth = parseInt($width);
					}else{
						if(strLast=='%'){
							conWidth = ($width.substring(0,$width.length-1)/100)*trWidth;
						}else{
							conWidth = ($width.substring(0,$width.length)/100)*trWidth;
						}
					}
					$(this).width(parseInt(conWidth));
					totalWidth = totalWidth+parseInt(conWidth);
					$table.find(".r"+$(this).index()).width(parseInt(conWidth));
					
				}
				if($(this).index()!=$table.find(".resize-th").children(".th").length-1){
					$(this).append('<span class="spacer"></span>');
				}
			});
			$table.find('.tr').each(function(){
				$(this).css("visibility","visible");				
			});
			$table.width(totalWidth + tbl + tbr + $padding);
		}
		
		resizeableColumns.prototype.destroy = function() {
			this.$handleContainer.remove();
			this.$table.removeData('resizableColumns');
			return $(window).off('.rc');
	    };
			
		resizeableColumns.prototype.createHandles = function() {
		  	var _this = this;//this.$table
		  	this.$table.find('.tr .th').each(function(i, el) {//.th的遍历
		  		
				var $handle;
					
				if (_this.$table.find('.tr .th').eq(i + 1).length === 0 || (_this.$table.find('.tr .th').eq(i).attr('data-resize') != null) || (_this.$table.find('.tr .th').eq(i + 1).attr('data-resize') != null)) {
			  		return;
				}//data-resize=""如果TH里带有这个属性,不管里面是否有值都不能拖拽
				$move = $("<div class='handleMove' />");
				$move.appendTo($(this));
				$move.data('.th', $(el));
				return $move.appendTo($(this));
			});
			return this.$table.on('mousedown', '.handleMove', this.mousedown)
	    };


	    resizeableColumns.prototype.saveColumnWidths = function() {
	    	var _this = this;
	
	    	return this.$table.find('.tr .th').each(function(_, el) {
	       		var id;
					
	        	if ($(el).attr('data-resize') == null) {
	          		id = _this.tableId + '-' + $(el).data('resizable-column-id');
	          		if (_this.options.store != null) {
	            		return store.set(id, $(el).width());
	          		}
	        	}
	      	});
	    };
	
	    resizeableColumns.prototype.mousedown = function(e) {
	      var $currentGrip, $leftColumn, $rightColumn, idx, leftColumnStartWidth, rightColumnStartWidth,
	        _this = this;
				
	      e.preventDefault();
	      this.startPosition = e.pageX;
	      
	      //th区域
	      $currentGrip = $(e.currentTarget);
	      $leftColumn = $currentGrip.data('.th');
	      leftColumnStartWidth = $leftColumn.width();
	      idx = this.$table.find('.tr .th').index($currentGrip.data('.th'));//当前触发的第几个
	      $rightColumn = this.$table.find('.tr .th').eq(idx + 1);//下一个对象
	      rightColumnStartWidth = $rightColumn.width();
	      
	      //td区域
	      var $tdLeftColumn = this.$table.find('.tr').children(".r"+idx);
	      var $tdRightColumn = this.$table.find('.tr').children(".r"+(idx+1));
	      
	      $(document).on('mousemove.rc', function(e) {
	        var difference, newLeftColumnWidth, newRightColumnWidth;
	        difference = e.pageX - _this.startPosition;
	        newRightColumnWidth = rightColumnStartWidth - difference;
	        newLeftColumnWidth = leftColumnStartWidth + difference;
	        if (_this.options.rigidSizing && ((parseInt($rightColumn[0].style.width) < $rightColumn.width()) && (newRightColumnWidth < $rightColumn.width())) || ((parseInt($leftColumn[0].style.width) < $leftColumn.width()) && (newLeftColumnWidth < $leftColumn.width()))) {
	          return;
	        }
	        if(newLeftColumnWidth>40&&newRightColumnWidth>40){
	        	$leftColumn.width(newLeftColumnWidth);
	        	$rightColumn.width(newRightColumnWidth);
	        	$tdLeftColumn.width(newLeftColumnWidth);
	        	$tdRightColumn.width(newRightColumnWidth);
	        }
	        
	      });
	      return $(document).one('mouseup', function() {
	        $(document).off('mousemove.rc');
	        return _this.saveColumnWidths();
	      });
	    };
	
	    return resizeableColumns;
	})();
	return $.fn.extend({
	    resizableColumns: function() {
	      var args, option;
	
	      option = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
	      return this.each(function() {
	        var $table, data;
	
	        $table = $(this);
	        data = $table.data('resizableColumns');
	        if (!data) {
	          $table.data('resizableColumns', (data = new resizeableColumns($table, option)));
	        }
	        if (typeof option === 'string') {
	          return data[option].apply(data, args);
	        }
	      });
	    }
  	});
})(window.jQuery, window);

;(function($){
	
	$.fn.extend({
		'fixedTr': function(h){
			
			var _this = $(this);
			var _thisParent = _this.parent();
			var width = $(this).width();
			var first = $(this).find(".tr").eq(1);
			var tableHeight = $(this).height();
			var tableOtherHeight = _thisParent.outerHeight(true)-tableHeight;
			var windowHeight = $(window).height()-h-tableOtherHeight;
			if(tableHeight>windowHeight){
				_this.height(windowHeight);
			}
			$("#show-tip").bind('click',function(){
				setTimeout(function(){
					$(window).resize();
				},100)
			});
			
			if(h!=0){
				$(window).resize(function(){
					
					var tips = 0;
					if($(".tips").length>0){
						tips = $(".tips").outerHeight(true);
					}
					
					var resetWidth = _this.find(".fixed-tr").width()-_this.find(".tr").eq(1).width();
					windowHeight = $(window).height()-h-tableOtherHeight-tips;
					if(tableHeight>windowHeight){
						_this.find(".fixed-tr").css({paddingRight:"17px"});
						_this.height(windowHeight);
					}else if(tableHeight<windowHeight){
						_this.find(".fixed-tr").css({paddingRight:"0"});
						_this.height(tableHeight);
					}
					
					if(first.width()<width&&first.length>1){
						_this.find(".fixed-tr").css({paddingRight:"17px"});
					}
				});
				$(window).resize();
			}else{
				if(first.width()<width&&first.length){
					_this.find(".fixed-tr").css({paddingRight:"17px"});
				}
			}
			
			_this.parent().removeClass("visible-hidden");
			
		},
		'fixedPaging': function(){
			
			var _this = this;
			
			if($("#show-tip").length>0){
				$("#show-tip").click(function(){
					setTimeout(function(){
						scrollAnimate();
					},200);
				});
			}
			
			function scrollAnimate(){
				var paging = _this.find('.paging');
				var tableWrapHeight = _this.outerHeight(true);
				var tableHeight = _this.height();
				var pageHeight = paging.outerHeight(true);
				var bh = $('body').height()-tableWrapHeight-_this.offset().top;//表格末尾距底部距离
				var topHeight = $(window).height()-($('body').height()-tableWrapHeight)-pageHeight+bh;
				if($('body').height()<$(window).height()){
					var sh = tableWrapHeight-pageHeight-2;
					paging.css({top:sh+"px"});
				}else{
					if(topHeight+$(window).scrollTop()>tableHeight){
						paging.css({top:tableHeight+"px"});
					}else if(topHeight+$(window).scrollTop()<39){
						paging.css({top:"39px"});
					}else{
						paging.css({top:topHeight+$(window).scrollTop()+"px"});
					}
				}
			}
			scrollAnimate();
			$(window).scroll(scrollAnimate);
			$(window).resize(scrollAnimate);
			
		},
		'treetable': function(){
			this.find(".tree-ind-status").live('click',function(){
				var tid = $(this).parents('.tr').attr('data-tree-id');
				if($(this).hasClass('open')){
					$('.tr').each(function(){
						if($(this).attr("data-tree-parent-id")!=undefined&&$(this).attr("data-tree-parent-id").substring(0,tid.length)==tid){//.substring(0,tid.length)
							$(this).addClass('hidden');
							$(this).find('.tree-ind-status').removeClass('close').addClass('open').attr('data-open','false');
						}
					});
					$(this).removeClass('open').addClass('close');
				}else{
					$('.tr').each(function(){
						if($(this).attr("data-tree-parent-id")!=undefined&&$(this).attr("data-tree-parent-id")==tid){
							$(this).removeClass('hidden');
							$(this).find('.tree-ind-status').removeClass('open').addClass('close');//.attr('data-open','true');
						}
					});
					$(this).removeClass('close').addClass('open');
				}
			});
		}
		
	});
	
})(jQuery);
