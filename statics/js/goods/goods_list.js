var list_action = (function() {
	return{
		change_status : function(url,id,row,label){
			$.post(url,{id:id},function(data){
				if(data.status == 1 && label == 'sku'){
					if(!row.hasClass("cancel")){
						row.addClass("cancel");
						row.attr("title","点击关闭");
					}else{
						row.removeClass("cancel");
						row.attr("title","点击开启");
					}
				}else if(data.status == 1 && label == 'spu'){
					var all_sku = $(row).parents(".tr").next(".goods-list-box").find(".sku_status");
					if(!row.hasClass("cancel")){
						row.addClass("cancel");
						row.attr("title","点击关闭");
						all_sku.addClass("cancel");
						all_sku.attr("title","点击关闭");
					}else{
						row.removeClass("cancel");
						row.attr("title","点击开启");
						if(all_sku.hasClass("cancel")){
							all_sku.removeClass("cancel");
							all_sku.attr("title","点击开启");
						}
					}
				}else{
					return false;
				}
			},'json');
		},
		
		change_name : function(url,id,name){
			$.post(url,{id:id,name:name},function(data){
				if(data.status == 1){
					return true;
				}else{
					return false;
			    }
			},'json');
		},	
		change_sn : function(url,id,sn){
			$.post(url,{id:id,sn:sn},function(data){
				if(data.status == 1){
					return true;
				}else{
					return false;
			    }
			},'json');
		},	
		change_sort : function(url,id,sort){
			$.post(url,{id:id,sort:sort},function(data){
				if(data.status == 1){
				return true;
				}else{
					return false;
				}
			},'json');
		},
		change_price : function(url,spu_id,sku_id,shop_price){
			$.post(url,{spu_id:spu_id,sku_id:sku_id,shop_price:shop_price},function(data){
				if(data.status == 1){
				return true;
				}else{
					return false;
				}
			},'json');
		},
		change_number : function(url,spu_id,sku_id,number){
			$.post(url,{spu_id:spu_id,sku_id:sku_id,number:number},function(data){
				if(data.status == 1){
				return true;
				}else{
					return false;
				}
			},'json');
		}
	};
})()