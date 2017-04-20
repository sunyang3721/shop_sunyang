<?php include template('header', 'admin'); ?>
<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/mobile.css" />
<script type="text/javascript">
var goods = <?php echo json_encode($goods); ?>;
$(function () {
    $("input[type=text]").eq(1).focus();
    // $("form[name=release_goods]").Validform({
    //     ajaxPost: true,
    //     callback: function (result) {
    //         if (result.status == 1) {
    //             window.location = "<?php echo url('index')?>";
    //         } else {
    //             alert(result.message);
    //         }
    //     }
    // })
})
</script>
<script type="text/javascript" src="./statics/js/goods/jquery.md5.js?v=<?php echo HD_VERSION ?>" ></script>
<script type="text/javascript" src="./statics/js/template.js?v=<?php echo HD_VERSION ?>" ></script>
<script type="text/javascript" src="./statics/js/goods/goods_publish.js?v=<?php echo HD_VERSION ?>" ></script>
<div class="fixed-nav layout">
    <ul>
        <li class="first">商品设置</li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">基本信息</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">商品规格</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">商品图册</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">商品类型</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">详情设置</a></li>
    </ul>
    <div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
    <form action="<?php echo url('goods_add', array('id' => $goods['spu']['id'])) ?>" method="post" name="release_goods">
    <input type="hidden" name="spu[id]" value="<?php echo (int) $goods['spu']['id'] ?>">
    <!-- 基本信息开始 -->
    <div class="content-tabs margin-large-bottom clearfix"><?php include template('goods_base'); ?></div>
    <!-- 基本信息结束 -->
    <!-- 商品规格开始 -->
    <div class="content-tabs margin-large-bottom hidden goods-spec clearfix"><?php include template('goods_spec'); ?></div>
    <!-- 商品规格结束 -->
    <!-- 商品图册开始 -->
    <div class="content-tabs margin-large-bottom hidden clearfix"><?php include template('goods_album'); ?></div>
    <!-- 商品图册结束 -->
    <!-- 商品类型开始 -->
    <div class="content-tabs margin-large-bottom hidden clearfix"><?php include template('goods_type'); ?></div>
    <!-- 商品类型结束 -->
    <!-- 详情设置开始 -->
    <div class="content-tabs margin-large-bottom hidden clearfix"><?php include template('goods_content'); ?></div>
    <!-- 详情设置结束 -->
    <div class="app-actions">
		<input class="save button bg-main" type="submit" value="保存">
		<a href="<?php echo url('index') ?>"><input type="button" class="button margin-left bg-gray" data-reset="false" value="返回" /></a>
	</div>
    </form>
</div>
<script type="text/javascript">
    $(function(){
        $(".resize-table").resizableColumns();
        $(".fixed-nav-tab").on('click', function() {
            $(window).resize();
        });
        var release_goods = $("[name=release_goods]").Validform({
            ajaxPost:false,
            beforeSubmit:function(curform){
                var specs = goods_publish._spec();
                var form = '';
                $.each(specs,function(i, spec) {
                    form += '<input type="hidden" name="specs['+ i +']" value=\'' + spec + '\'>'
                });

                $(curform).append(form);
            },
        });

        $(".save").on('click',function(){
        	var $input=$(".content-tabs").find(".input");
        	$input.each(function(){
        		if($(this).hasClass("input-error")){
	        		$(".content-tabs").addClass("hidden")
	        		$(this).parents(".content-tabs").removeClass("hidden");
	        		$(".fixed-nav .fixed-nav-tab").find("a").removeClass("current");
	        		$(".fixed-nav .fixed-nav-tab").eq(0).find("a").addClass("current");
	        	}
        	});
        });
    })
    //分类选择
    function setClass() {
        var pid = $('input[name="spu[catid]"]').val();
        var pname = $('#choosecat').val();
        var pvalue = $('input[name=cat_format]').val();
        var data = [pid, pname, pvalue];
        top.dialog({
            url: "<?php echo url('category/category_popup') ?>",
            title: '加载中...',
            data: data,
            width: 930,
            onclose: function () {
                if (this.returnValue) {
                    var catname = this.returnValue.html().replace(/&gt;/g, '>');
                    $('#choosecat').val(catname);
                    var catids = this.returnValue.attr('data-id').split(',');
                    var catid = catids[catids.length - 1];
                    $('input[name=cat_format]').val(this.returnValue.attr('data-id'));
                    $('input[name="spu[catid]"]').val(catid);
                    $.post('<?php echo url("ajax_get_attr")?>',{id:catid},function(data){
                        var attr_select = template('attr_select', {'extra_attr': data.result.attr});
                        $('.goods-attr-content').html(attr_select);
                        var type_select = '',type_default = 0;
                        for(var k in data.result.types){
                            type_select += '<span class="listbox-item" data-val="'+k+'">'+data.result.types[k]+'</span>';
                        }
                        $("#spu-typeid").find(".listbox-items").html(type_select);
                        $('#spu-typeid').find('.input').val(data.result.types[0]);
                    },'json')
                }
            }
        })
        .showModal();
    }
    // 规则选取
    $("[data-model=spec_popup]").live('click', function () {
        var productNu = selectedItem ? productNo + '-1' : $('input[name="sn[]"]').eq(0).val();
        top.dialog({
            url: "<?php echo url('goods_spec_pop',array('id' => $_GET['id']));?>",
            title: 'loading...',
            width: 681,
            data: selectedItem,
            onclose: function () {
                if (this.returnValue) {
                    var addSpecObject = this.returnValue;
                    //开始遍历规格
                    var specValueData = {}
                    var specData = {};
                    var selectedNewItem = [];
                    var selectType = 0;
                    var spec_names = [],
                        spec_values = [],
                        spec_ids = [];
                    addSpecObject.each(function (i,item) {
                        if ($(this).hasClass('new-prop') == true) { // 如果是全选则排除添加属性这个<li>
                            return true;
                        }
                        var data_id = $(this).attr('data-id');
                        var data_name = $(this).attr('data-name');
                        var data_value = $(this).attr('data-value');
                        var data_style = $(this).attr('data-style');
                        var data_color = $(this).attr('data-color');
                        var data_img = $(this).attr('data-img');
                        selectType = $(this).attr('data-type') ? $(this).attr('data-type') : 0;
                        //组装图册页面数据
                        spec_ids[i] = data_id;
                        spec_names[i] = data_name;
                        spec_values[i] = data_value;
                        //生成规格
                        if (typeof (specValueData[data_id]) == 'undefined') {
                            specValueData[data_id] = [];
                        }
                        specValueData[data_id].push({
                            'value': data_value,
                            'img': data_img,
                            'color': data_color
                        });
                        specData[data_id] = {
                            'id': data_id,
                            'name': data_name,
                            'style': data_style,
                        };
                        selectedNewItem.push({
                            'id': data_id,
                            'value': data_value,
                            'color': data_color,
                            'img': data_img,
                            'style': data_style
                        });
                    });
                    selectedItem = selectedNewItem;
                     //生成货品的笛卡尔积
                    var specMaxData = descartes(specValueData, specData);
                     //从表单中获取默认商品数据
                    var productJson = {};
                    productJson['sn'] = productNu;
                    //获取当前页面货号最大值
                    var sn_num = [],
                            sn_length = $('.sn').length;
                    $('.sn').each(function (i, item) {
                        var num = $(item).val().split('-');
                        sn_num.push(num[1]);
                    })
                    var max_sn_num = Math.max.apply(null, sn_num);
                    //生成最终的货品数据
                    var productList = [];
                    for (var i = 0; i < specMaxData.length; i++) {
                        var productItem = {};
                        productItem['spec_array'] = specMaxData[i];
                        for (var index in productJson) {
                            //自动组建货品
                            if (index == 'sn') {
                                //值为空时设置默认货号
                                if (productJson[index] == '') {
                                    productJson[index] = productNo;
                                }
                                if (productJson[index].match(/(?:\-\d*)$/) == null) {
                                    //正常货号生成
                                    productItem['sn'] = productJson[index] + '-' + (i + 1);
                                } else {
                                    //货号已经存在则替换
                                    productItem['sn'] = productJson[index].replace(/(?:\-\d*)$/, '-' + (i + 1));
                                }
                            } else {
                                productItem[index] = productJson[index];
                            }
                        }
                        productList.push(productItem);
                    }
                    var selectArr = [];
                    $.each(productList, function (i, item) {
                        var spec = spec_string = specJson = '';
                        for (var j = 0; j < (item.spec_array.length); j++) {
                            spec_string += item.spec_array[j].name + ':' + item.spec_array[j].value + ' ';
                            spec_md5 = $.md5(spec_string);
                        }
                        productList[i]['spec'] = item.spec_array;
                        productList[i]['spec_str'] = spec_string;
                        productList[i]['spec_md5'] = spec_md5;
                        selectArr.push(spec_md5);
                    });
                    if (selectType == 1) {
                        if (productList.length != 0) {
                           init_template();
                        }
                        var goodsRowHtml = template('spec_template', {'templateData': productList});
                        $('#goods_spec').html(goodsRowHtml);
                    } else {
                        var specArr = [];
                        $('.spec-list').each(function () {
                            specArr.push($(this).attr('data-id'));
                        })
                        var select_diff = diff_array(selectArr, specArr);
                        var spec_diff = diff_array(specArr, selectArr);
                        if (spec_diff.length == 0 && select_diff.length == 0) {
                            $.each(productList, function (i, item) {
                                var spec_desc = template('spec_desc', {'spec_desc': item.spec, 'spec': item.spec_md5, 'spec_str': item.spec_str});
                                $('.tr[data-id="' + item.spec_md5 + '"]').find('.spec_info').find('input').remove();
                                $('.tr[data-id="' + item.spec_md5 + '"]').find('.spec_info').append(spec_desc);
                            })
                        }
                        //新增
                        if (select_diff.length > 0) {
                            var speclist = [];
                            var t = max_sn_num + 1;
                            $.each(productList, function (i, item) {
                                var spec = specJson = '';
                                for (var j = 0; j < (item.spec_array.length); j++) {
                                    spec += item.spec_array[j].name + ':' + item.spec_array[j].value + ' ';
                                    spec_md5 = $.md5(spec);
                                }
                                if ($.inArray(spec_md5, select_diff) > -1) {
                                    productList[i].sn = productNo + '-' + t;
                                    speclist.push(productList[i]);
                                    t++;
                                }
                            });
                            var goodsRowHtml = template('spec_template', {'templateData': speclist});
                            $('#goods_spec').append(goodsRowHtml);
                        }
                        //删减
                        if (spec_diff.length > 0) {
                            $.each(spec_diff, function (i, item) {
                                var del_id = $("div[data-id='" + item + "']").find('input[name="_sku_ids[]"]').val();
                                $('.table').append('<input type="hidden" name="del_sku_ids[]" value="' + del_id + '">');
                                $("div[data-id='" + item + "']").remove();
                            })
                        }
                    }
                    $(window).resize();
                    create_album(spec_ids,spec_names,spec_values);
                }

            }
        })
        .showModal();
    });
    //生成图册页面规格数据
    function create_album(spec_ids,spec_names,spec_values){
        var select_album = {};
        for(var i=0;i<spec_ids.length;i++){
            if(select_album[spec_ids[i]]){
                var arr = md5 = [];
                var f_val = select_album[spec_ids[i]].value;
                var md_val = select_album[spec_ids[i]].md5;
                if(typeof f_val == "object"){
                    arr = f_val;
                    arr.push(spec_values[i]);
                    md5 = md_val;
                    md5.push($.md5(spec_names[i]+':'+spec_values[i]))
                }else{
                    arr = [];
                    arr.push(f_val);
                    arr.push(spec_values[i]);
                    md5 = [];
                    md5.push(md_val);
                    md5.push($.md5(spec_names[i]+':'+spec_values[i]));
                }
                select_album[spec_ids[i]].value = arr;
                select_album[spec_ids[i]].md5 = md5;
            }else{
                select_album[spec_ids[i]] = {};
                select_album[spec_ids[i]].id = spec_ids[i];
                select_album[spec_ids[i]].name = spec_names[i];
                select_album[spec_ids[i]].value = spec_values[i];
                select_album[spec_ids[i]].md5 = $.md5(spec_names[i]+':'+spec_values[i]);
            }
        }
        var spec_select = template('spec_select', {'templateData': select_album,'_spec': _spec});
        $('.spec_box').html(spec_select);
        if(extra_album == undefined) extra_album = [];
        var spec_album = template('spec_album', {'goods_specs': select_album,'_spec': _spec,'extra_album': extra_album});
        $('.album_box').html(spec_album);
        init_upload();
    }

    $("[data-model='spec_delete']").live('click', function () {
        if (confirm("您确定执行此操作？")){
            $(this).parent().parent('.tr').remove();
            var spec_info = [];
            $.each($('[attr^="spec"]'), function(i, n){
                spec_info.push($(this).val());
            })
            var spec_ids = [],spec_names=[],spec_values=[];
            selectedItem = {};
            $.each(spec_info,function(i, item) {
                var spec = JSON.parse(item);
                selectedItem[i] = spec;
                if($.inArray(spec.value,spec_values) == -1){
                    spec_ids.push(spec.id);
                    spec_names.push(spec.name);
                    spec_values.push(spec.value);
                }
            });
            create_album(spec_ids,spec_names,spec_values)
        }
    })

    $("[data-model='spec_clear']").live('click', function () {
        if (confirm("是否清空规格？")) {
            $('.table').children('.spec-list').remove();
            goods_publish.clear('spec_template', '#goods_spec');
            selectedItem = [];
            $('.spec_box').html('');
            $('.album_box').html('');
            init_upload();
            $(window).resize();
        }
    })

    $("[data-model='spec_batch']").live('click',function(){
                top.dialog({
                    url: "<?php echo url('goods_spec_modify')?>",
                    title: '加载中...',
                    width: 400,
                    data : $('[name="spu[sn]"]').val(),
                    onclose: function(){
                        if(this.returnValue){
                            _sn         =  this.returnValue[0];
                            _shop_price_change      = this.returnValue[1];
                            _market_price_change    = this.returnValue[2];
                            _goods_number_change    = this.returnValue[3];
                            var num_reg = /^[-\+]?\d*$/;
                            var price_reg = /^[-\+]?\d+(\.\d{2})?$/;
                            if(!(num_reg.test(_goods_number_change)) || !(price_reg.test(_shop_price_change)) || !(price_reg.test(_market_price_change))){
                                alert('请输入正确的数字!');
                                return false;
                            }
                            //货号
                            var i = 1;
                            $('[name="spu[sn]"]').val(_sn);
                            $('[attr^="sn"]').each(function(index,data){
                                sn = _sn + '-' + i;
                                i++;
                                $(this).val(sn);
                            })
                            //销售价
                            $('[attr^="shop_price"]').each(function(index,data){
                                num = Number($(this).val()) + Number(_shop_price_change);
                                num = num < 0 ? 0 : num;
                                $(this).val(num.toFixed(2));
                            })
                            //市场价
                            $('[attr^="market_price"]').each(function(index,data){
                                num = Number($(this).val()) + Number(_market_price_change);
                                num = num < 0 ? 0 : num;
                                $(this).val(num.toFixed(2));
                            })
                            //库存
                            $('[attr^="number"]').each(function(index,data){
                                num = parseInt($(this).val()) + parseInt(_goods_number_change);
                                num = num < 0 ? 0 : num;
                                $(this).val(num);
                            })
                        }
                    }
                }).showModal();
            });
</script>
<?php include template('footer', 'admin'); ?>
