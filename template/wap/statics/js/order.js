/**
 * 		提交订单处理类
 */

var hd_order = {
	
	start: function(hdkey, hddatas, skuids){
		
		this.skuids = skuids;
		if(!window.localStorage.getItem("hddatas")){
			/*
			 * 没有本地缓存则添加本地缓存
			 */
			window.localStorage.setItem("hdkey", hdkey);
			window.localStorage.setItem("hddatas", JSON.stringify(hddatas));
		}else{
			/*
			 * 已有本地缓存则替换服务器与本地缓存的数据
			 */
			var localKey = window.localStorage.getItem("hdkey");				//获取本地key值
			//若本地key与服务器key不同
			if(localKey != hdkey){
				window.localStorage.setItem("hdkey", hdkey);
				window.localStorage.setItem("hddatas", JSON.stringify(hddatas));
			}
			var localData = $.parseJSON(window.localStorage.getItem("hddatas")); //获取本地缓存
			//会员ID不同时直接替换本地缓存内容
			if(localData[hdkey].mid != hddatas[hdkey].mid){
				localData[hdkey] = hddatas[hdkey];
				window.localStorage.setItem("hdkey", hdkey);
				window.localStorage.setItem("hddatas", JSON.stringify(hddatas));
			}
		}
		localData = $.parseJSON(window.localStorage.getItem("hddatas")); //获取本地缓存
		/*
		 * 会员创建订单，如果有默认地址，自动添加默认地址。如果没有则提示添加收货地址
		 * 会员已选择其他收货地址则以地址ID为选中地址
		 */
		if(localData[hdkey].address && localData[hdkey].address != 'null'){
			var flog = true;
			//若选中地址ID在地址列表中，不设置收货地址为默认收货地址
			$.each(localData[hdkey].address, function(k, v){
				if(localData[hdkey].addrId && localData[hdkey].addrId == v.id){
					localData[hdkey].district = v.district_id;
					flog = false;
				}
			})
			//本地已选收货地址ID不属于更新后的收货地址中，设置默认收货地址为选中
			if(flog){
				$.each(localData[hdkey].address, function(k, v){
					if(parseInt(v.isdefault) == 1){
						localData[hdkey].addrId = v.id;
						localData[hdkey].district = v.district_id;
					}
				})
			}
		}else{
			//若服务器没有收货地址，删除本地缓存已选地址
			delete localData[hdkey].addrId;
			delete localData[hdkey].district;
		}
		//默认物流，只有当有收货地址才执行
		if(localData[hdkey].addrId){
			var _carts = this.isJSON(localData[hdkey].carts);
			//当有配送物流
			if(_carts.deliverys && _carts.deliverys.length > 0 || _carts.deliverys && JSON.stringify(_carts.deliverys) != '{}'){
				var deliverys = [];
				if(typeof localData[hdkey].deliverys[0] == 'string'){	//当获取的物流数据是经过处理后的数组后
					var deli_flog = true;
					$.each(_carts.deliverys,function(k,v){
						$.each(v, function(r,s) {   
							deliverys.push(s.id);
							if(localData[hdkey].deliverys[0] == s.delivery_id){
								deli_flog = false;
							}
						});
					})
					if(deli_flog){
						localData[hdkey].deliverys = [deliverys[0]];
					}
				}else if(typeof localData[hdkey].deliverys[0] == 'object'){	//第一次加载订单时获取的数据是JSON对象
					$.each(_carts.deliverys,function(k,v){
						$.each(v, function(r,s) {    
							deliverys.push(s.id);
						});
					})
					localData[hdkey].deliverys = [deliverys[0]];
				}
			}else{
				localData[hdkey].deliverys = [null];
			}
		}else{
			//没有收货地址时直接设置物流为null
			localData[hdkey].deliverys = [null];
		}
		if(hddatas[hdkey].orderList && hddatas[hdkey].orderList != 'null'){
			//更新本地缓存订单促销内容
			localData[hdkey].orderList = hddatas[hdkey].orderList;
			localData[hdkey].carts.skus = hddatas[hdkey].carts.skus;
			//本地已有订单促销，校验服务器是否还有此促销，若本地没有订单促销选择，添加默认订单促销
			var orderprom = {}
			if(localData[hdkey].orderProm){
				$.each(hddatas[hdkey].orderList, function(k, v){
					if(localData[hdkey].orderProm == v.id){
						orderprom["0"] = v.id;
						localData[hdkey].orderProm = (!orderprom || orderprom == "{}") ? {0:0} : orderprom;
					}
				});
			}else{
				$.each(hddatas[hdkey].orderList, function(k, v){
					if(v._selected == 1){
						orderprom["0"] = v.id;
						localData[hdkey].orderProm = (!orderprom || orderprom == "{}") ? {0:0} : orderprom;
					}
				});
			}
		}else{
			//若没有订单优惠列表，且有订单优惠选中ID，则清除选中ID
			if(localData[hdkey].orderProm){
				delete localData[hdkey].orderProm;
			}
		}
		//默认选中商品促销的优惠
		if(!localData[hdkey].goodsProm){
			var goodsprom = {};
			$.each(localData[hdkey].carts.skus[0].sku_list, function(k, v){
				if(v._promos.length > 0){
					goodsprom[k] = 0;
				}
			});
			localData[hdkey].goodsProm = goodsprom;
		}
		window.localStorage.setItem("hdkey", hdkey);
		window.localStorage.setItem("hddatas", JSON.stringify(localData));
		//更新结算页信息
		this.settlement();
	},
	//确认订单
	settlement: function(){
		this.getData();
		var D = this.datas[this.hdKey],
			T = this,
			carts = T.isJSON(D.carts);
		//修改收货地址信息
		if(D.addrId){
			$.each(D.address, function(k, v){
				if(D.addrId == v.id){
					$("[data-show='address_name']").parent().attr("data-addressid", D.addrId);
					$("[data-show='address_name']").text('收货人：' + v.name);
					$("[data-show='address_mobile']").text(v.mobile);
					$("[data-show='address_detail']").text('收货地址：'+ v._area +" "+ v.address);
				}
			})
		}
		//支付
		if (D.payType) {
			$("[data-show='pay_delivery']").html(D.payType[D.payMethod]);
		}else{
			$("[data-show='pay_delivery']").html('请选择');
		}
		//发票信息 
		if(parseInt(D.invoiceEnabled) == 1){
			$("[data-show='invoice_content']").html(D.invoiceCon);
		}
		//订单促销 
		if(D.orderProm){
			$.each(D.orderProm,function(sd, pd) {
				if (pd > 0) {
					$("[data-sellerid="+ sd +"]").find("[data-show='order_prom']").text(carts.skus[sd]._promos[pd].name);
				}else{
					$("[data-sellerid="+ sd +"]").find("[data-show='order_prom']").text("不使用优惠");
				}
			});
		}
		//商品促销
		if (D.goodsProm) {
			$.each(D.goodsProm ,function(sd, pd) {
				if (pd < 0) {
					$("[data-skuid="+ sd +"]").find("[data-show='goods_prom']").text('不参与促销');
				}else{
					$("[data-skuid="+ sd +"]").find("[data-show='goods_prom']").text(carts.skus[0].sku_list[sd]._promos[pd].title);
				}
			});
		}
		//商家留言 
		if(D.remarks){
			$("[data-id='remarks']").val(D.remarks);
			//这个应该是多商家，但是格式传过去有问题
			/*$.each(D.remarks, function(sellerid, v) {
				$("[data-sellerid="+ sellerid +"]").find("[data-id='remarks']").val(v);
			});*/
		}
	},
	//加载收货地址列表
	setAddress: function(){
		this.getData();
		var D = this.datas[this.hdKey],
			T = this;
		// 获取最新的收货地址并保存到本地存储
		$.ajax({
			url: '?m=order&c=order&a=get_address',
			type: 'POST',
			dataType: 'json',
			data: {},
			async : false,
			success : function(ret) {
				D.address = ret;
				T.datas[T.hdKey].address = ret;
				window.localStorage.setItem('hddatas', JSON.stringify(T.datas));
			}
		});
		if(D.address && D.address != "null"){
			var H = '';
			$.each(D.address ,function(k, v){
				H += '<li class="address-list">'
		        	   + '	<div class="address-text" data-id="address" data-district="'+ v.district_id +'" data-addressid="'+ v.id +'">'
		        	   + '		<a class="mui-block">'
		        	   + '			<span class="name text-ellipsis">'+ v.name +'</span>'
		        	   + '			<span class="address-btn margin-small-right'+ (D.addrId != v.id ?' hide':'') +'" data-id="now">当前选中</span>'
		        	   + '			<span class="mui-pull-right">'+ v.mobile +'</span>'
			    	   + '    		<p>'+ (v.isdefault == 1?'[默认]':'') + v._area +' '+ v.address +'</p>'
		        	   + '		</a>'
		        	   + '	</div>'
		        	   + '	<div class="edit">'
		        	   + '		<a href="?m=member&c=address&a=edit&id='+ v.id +'&referer='+ encodeURIComponent(window.location.search) +'"><img src="template/wap/statics/images/ico_21.png" /></a>'
		        	   + '	</div>'
		        	   + '</li>';
			});
			$("[data-id='address_box']").html(H);
		}else{
			$("[data-id='address_box']").html('<p class="padding text-org mui-text-center">请先添加一个地址！</p>');
		}
		
	},
	//配送 & 支付方式
	setDeliver: function(){
		this.getData();
		var D = this.datas[this.hdKey],
			T = this;
		// 支付方式 
		if(D.payType){
			var P_H = '';
			$.each(D.payType ,function(k, v) {
				var _type = (D.payMethod && D.payMethod == k || !D.payMethod && k == 1) ? 'blue' : 'gray';
				P_H += '<a class="mui-btn hd-btn-'+ _type +' margin-small-right" data-id="'+ k +'">'+ v +'</a>';
			})
			$("[data-id='type_box']").html(P_H);
		}else{
			$("[data-id='type_box']").html('<p class="text-org">系统暂未开启支付方式</p>');
		}
	},
	// 更新选中的收货地址的物流配送方式
	getDeliverys : function(url, id) {
		var outer = this;
		$.getJSON(url, {district_id: id}, function(ret) {
			var deliverys = {};
			$.each(ret, function(sellerid, v) {
				deliverys[sellerid] = v;
			})
			var _key = window.localStorage.getItem('hdkey');
			var _datas = window.localStorage.getItem('hddatas');
				_datas = outer.isJSON(_datas);
			var _carts = outer.isJSON(_datas[_key].carts);
				_carts.deliverys = deliverys;
			_datas[_key].carts = outer.isString(_carts);
			window.localStorage.setItem('hddatas', outer.isString(_datas));
			//window.location.href = hddatas[hdkey].referer;
			mui.openWindow({url: _datas[_key].referer});
		})
	},
	// 填写发票 初始化
	invoice: function() {
		this.getData();
		var D = this.datas[this.hdKey],
			T = this;
		// 添加发票抬头
		if(D.invoiceTitle){
			$("[data-id='invoice_title']").val(D.invoiceTitle);
		}
		//展示发票类型，默认选中不开发票
		var H = '';
		$.each(D.invoices, function(k, v) {
			H += '<div class="hd-radio full margin-bottom"><label>'+ v +'</label><input name="radio" type="radio"'+ (D.invoiceCon == v?' checked="checked"':'') +' data-isinvoice="1"/></div>';
		});
		H += '<div class="hd-radio full margin-bottom"><label>不开发票</label><input name="radio" type="radio" data-isinvoice="0"'+ (D.isinvoice == 0?' checked="checked"':'') +'/></div>';
		$("[data-id='invoice_box']").html(H);
	},
	//订单促销
	orderPromotion: function(sellerid){
		this.getData();
		var D = this.datas[this.hdKey],
			T = this,
			H = '',
			hd_carts = T.isJSON(D.carts);
		$.each(hd_carts.skus[sellerid]._promos, function(k, v) {
			H += '<div class="hd-radio full margin-bottom" data-order="'+ v.id +'"><label>'+ v.name +'</label><input name="radio" type="radio"'+ (D.orderProm && D.orderProm[sellerid] == v.id?' checked="checked"':'') +' /></div>';
		});
		H += '<div class="hd-radio full margin-bottom"><label>不使用优惠</label><input name="radio" type="radio"'+ (D.orderProm[sellerid] == 0?' checked="checked"':'') +' /></div>';
		$("[data-id='order_box']").html(H);
	},
	// 商品促销 
	goodsPromotion: function(sellerid , skuid){
		this.getData();
		var D = this.datas[this.hdKey],
			T = this,
			H = '',
			hd_carts = T.isJSON(D.carts);
		//如果没有选择商品促销，默认选中
		if(!D.goodsProm){
			D.goodsProm = {};
			D.goodsProm[skuid] = 0;
		}
		$.each(hd_carts.skus[sellerid].sku_list[skuid]._promos, function(k, v) {
			H += '<div class="hd-radio full margin-bottom" data-goods="'+ k +'"><label>'+ v.title +'</label><input name="radio" type="radio"'+ (D.goodsProm && D.goodsProm[skuid] == k?' checked="checked"':'') +' /></div>';
		});
		H += '<div class="hd-radio full margin-bottom" data-goods="-1"><label>不参与促销</label><input name="radio" type="radio"'+ (D.goodsProm[skuid] < 0?' checked="checked"':'') +' /></div>';
		$("[data-id='goods_box']").html(H);
	},
	//从本地获取数据
	getData: function(){
		this.hdKey = window.localStorage.getItem('hdkey');
		this.datas = window.localStorage.getItem('hddatas');
		this.datas = this.isJSON(this.datas);
	},
	//参数
	params : function() {
		this.getData();
		var D = this.datas[this.hdKey],
			T = this,
			params = {
				skuids: T.skuids,			//商品列表
				address_id: D.addrId,		//收货地址
				district_id: D.district,	//收货地址
				pay_type: D.payMethod,		//支付方式
				// deliverys: D.deliverys,		//物流组
				order_prom: D.orderProm,	//订单促销组
				sku_prom: D.goodsProm ,		//商品促销组
				remarks: D.remarks,			//留言组
				invoices:{					//发票
					invoice: D.isinvoice,	//发票状态
					title: D.invoiceTitle,	//发票抬头
					content: D.invoiceCon	//发票内容
				},
			}
		return params;
	},
	//转换为JSON对象
	isJSON: function(D){
		if(typeof D == "string"){
			return JSON.parse(D);
		}
		return D;
	},
	//转换为字符串
	isString: function(D){
		if(typeof D == "object"){
			return JSON.stringify(D);
		}
		return D;
	}
	
}
