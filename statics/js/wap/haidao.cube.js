define(["require", "diy"], function(require, diy){
	return {
		coordinate: function(C){ //获取坐标重新绘制
			C = eval(C);
			var M, I = $(".tablecloth").find(".no-empty").length || 0;
			var rows = 4, cols = 4;	//行， 列
			if(!C){
				M = [];
				for(var i=0;i<cols;i++){
					var arr = [];
					for(var n=0;n<rows;n++){
						var _json = {};
						_json.x = n;
						_json.y = i;
						try{
							if((n >= this.startX && n <= this.endX) && (i >= this.startY && i <= this.endY)){
								if(n == this.startX && i == this.startY){
									_json.width = parseInt(this.endX - this.startX) + 1;
									_json.height = parseInt(this.endY - this.startY) + 1;
									_json.index = I;
									I++;
								}
							}else{
								_json.flog = true;
							}
						}catch(e){
							//TODO handle the exception
						}
						arr.push(_json);
					}
					M.push(arr);
				}
			}else{
				for(var i=0;i<cols;i++){
					for(var n=0;n<rows;n++){
						if((n >= this.startX && n <= this.endX) && (i >= this.startY && i <= this.endY)){
							delete C[i][n].flog;
						}
						if(n==this.startX && i==this.startY){
							C[i][n].width = parseInt(this.endX - this.startX) + 1;
							C[i][n].height = parseInt(this.endY - this.startY) + 1;
							C[i][n].index = I;
							I++;
						}
					}
				}
				M = C;
			}
			$(".tablecloth").data("length", I);
			return M;
		},
		optArea: function(D){	//标出可选区域
			var rows = 4, cols = 4;	//行， 列
			D = eval(D);
			var result = [];
			if(!D){	//没有数据时初始化坐标
				D = [];
				for(var y=0;y<rows;y++){
					var $arr = [];
					for(var x=0;x<cols;x++){
						var $json = {};
						$json.flog = true;
						$json.x = x;
						$json.y = y;
						$arr.push($json);
					}
					D.push($arr);
				}
			}
			//获取可选区域坐标
			for(var i=this.startY;i<rows;i++){
				var arr = [];
				var flog = true;
				for(var n=this.startX;n<cols;n++){
					var _json = {};
					if(D[i][n].flog){
						_json.x = n;
						_json.y = i;
						arr.push(_json);
					}else{
						if(n==this.startX && !D[i][n].flog){
							flog = false;
						}
						break;
					}
				}
				if(arr.length>0) result.push(arr);
				if(!flog) break;
			}
			
			//当只有一格时直接选中
			if(result.length==1&&result[0].length==1){
				this.endX = result[0][0].x;
				this.endY = result[0][0].y;
				var $edit = $(".app-field.editing");
				var vals = this.coordinate(D);
				$(".sidebar-content").find("input[name=layout]").val(JSON.stringify(vals));
				require("diy").reload();
				return;
			}
			
			//去除下一列的长度长于上一列的问题
			for(var s=1;s<result.length;s++){
				if(result[s].length > result[s-1].length){
					result[s].splice(result[s-1].length,result[s].length-1);
				}
			}
			
			//遍历添加可选区域
			$.each($(".tablecloth").find("td"),function(){
				for(var i=0;i<result.length;i++){
					for(var n=0;n<result[i].length;n++){
						if($(this).data("x") == result[i][n].x && $(this).data("y") == result[i][n].y){
							$(this).addClass("selected");
						}
					}
				}
			});
		},
		delete: function(maps, x, y){
			maps = eval(maps);
			var D, I;
			$.each(maps, function() {
				$.each(this, function(){
					if(this.x == x && this.y == y){
						D = this;
					}
				})
			});
			I = D.index;
			for(var i = D.y;i < D.y + parseInt(D.height);i++){
				for(var n = D.x;n < D.x + parseInt(D.width);n++){
					if(i == D.y && n == D.x){
						delete maps[D.y][D.x];
						maps[i][n] = {};
						maps[i][n].x = D.x;
						maps[i][n].y = D.y;
					}
					maps[i][n].flog = true;
				}
			}
			$.each(maps, function() {
				$.each(this, function(){
					if(this.index > I){
						this.index = this.index-1;
					}
				})
			});
			
			$.each($(".sidebar-content .choices li"), function() {
				var id = $(this).data("id");
				if(id > I){
					$(this).data("id",id-1);
					$.each($(this).find("input"), function(){
						var name = $(this).attr("name").replace(id,id-1);
						$(this).attr("name",name)
					});
				}
			});
			
			$(".sidebar-content").find("input[name=layout]").val(JSON.stringify(maps));
			require("diy").reload();
		},
		modify: function(i,h,w,m){
			var M = eval(m), X, Y, D,
				W = parseInt(w),
				H = parseInt(h);
	
			$.each(M, function() {
				$.each(this, function(){
					if(this.index == i){
						D = this;
					}
				})
			});
			for(var y=D.y;y<D.y+D.height;y++) {
				for(var x=D.x;x<D.x+D.width;x++) {
					if(x>=D.x+W || y>=D.y+H){
						M[y][x].flog = true;
					}
				}
			}
			D.width = W;
			D.height = H;
			$(".sidebar-content").find("input[name=layout]").val(JSON.stringify(M));
			require("diy").reload();
		}
	}
})