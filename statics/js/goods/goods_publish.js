var goods_publish = (function() {

	return {
		init : function(goods, options) {
			if (typeof(goods) != 'object') {
				alert('无法读取商品信息！');
				location.href = '/';
				return;
			}

		},
		template:function(tpl, data, callback) {
			var content = template(tpl, {'templateData': data});
			if(typeof callback === 'function') {
				callback(content);
			}
		},


		_spec:function() {
            var specs = {};
			$('input[attr]').each(function(i, item) {
                if(!specs[$(item).parents('.td').attr('data-column')]){
                    specs[$(item).parents('.td').attr('data-column')] = [];
                }
                specs[$(item).parents('.td').attr('data-column')].push($(item).attr('attr')+'='+$(item).val());
            });
            var post = {};
            $.each(specs,function(i,spec){
                post[i] = spec.join('___hd___');
            });
            return post;
		},

		/* 规格 - 弹窗选取 */
		spec_popup : function() {

		},

		//dialog:function(url, params)
        spec_batch:function(url, data, options, callback) {
            top.dialog({
                url: url,
                title: '加载中...',
                width: 400,
                data: data,
                onclose: function () {
                    if (this.returnValue) {
                        _sn = this.returnValue[0];
                        _shop_price_change = this.returnValue[1];
                        _market_price_change = this.returnValue[2];
                        _goods_number_change = this.returnValue[3];
                        var num_reg = /^[-\+]?\d*$/;
                        var price_reg = /^[-\+]?\d+(\.\d{2})?$/;
                        if (!(num_reg.test(_goods_number_change)) || !(price_reg.test(_shop_price_change)) || !(price_reg.test(_market_price_change))) {
                            alert('请输入正确的数字!');
                            return false;
                        }
                        //货号
                        var i = 1;
                        $('[name=spu[sn]]').val(_sn);
                        $('[attr^="sn"]').each(function (index, data) {
                            sn = _sn + '-' + i;
                            i++;
                            $(this).val(sn);
                        })
                        //销售价
                        $('[attr^="shop_price"]').each(function (index, data) {
                            num = Number($(this).val()) + Number(_shop_price_change);
                            num = num < 0 ? 0 : num;
                            $(this).val(num.toFixed(2));
                        })
                        //市场价
                        $('[attr^="market_price"]').each(function (index, data) {
                            num = Number($(this).val()) + Number(_market_price_change);
                            num = num < 0 ? 0 : num;
                            $(this).val(num.toFixed(2));
                        })
                        //库存
                        $('[attr^="number"]').each(function (index, data) {
                            num = parseInt($(this).val()) + parseInt(_goods_number_change);
                            num = num < 0 ? 0 : num;
                            $(this).val(num);
                        })
                    }
                }
            })
		},



		clear:function(tpl, obj) {
			var content = template(tpl, {'templateData': goods_init});
			$(obj).html(content);
		}
	};
})();


// 数组对比
function diff_array(arr1, arr2) {
    var arr3 = [];
    for (var i = 0; i < arr1.length; i++) {
        var flag = true;
        for (var j = 0; j < arr2.length; j++) {
            if (arr2[j] == arr1[i]) {
                flag = false;
            }
        }
        if (flag) {
            arr3.push(arr1[i]);
        }
    }
    return arr3;
}

//笛卡儿积组合
function descartes(list, specData) {
    var point = {};
    var result = [];
    var pIndex = null;
    var tempCount = 0;
    var temp = [];
    for (var index in list) {
        if (typeof list[index] == 'object') {
            point[index] = {
                'parent': pIndex,
                'count': 0
            }
            pIndex = index;
        }
    }
    if (pIndex == null) {
        return list;
    }
    while (true) {
        for (var index in list) {
            tempCount = point[index]['count'];
            temp.push({
                "id": specData[index].id,
                "name": specData[index].name,
                "style": specData[index].style,
                "color": list[index][tempCount].color,
                "img": list[index][tempCount].img,
                "value": list[index][tempCount].value
            });
        }
        result.push(temp);
        temp = [];
        while (true) {
            if (point[index]['count'] + 1 >= list[index].length) {
                point[index]['count'] = 0;
                pIndex = point[index]['parent'];
                if (pIndex == null) {
                    return result;
                }
                index = pIndex;
            } else {
                point[index]['count']++;
                break;
            }
        }
    }
}