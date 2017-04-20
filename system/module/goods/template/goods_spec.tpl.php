<div class="table-work border margin-top border-bottom-none">
	<div class="border border-white tw-wrap border-bottom-none">
		<a href="javascript:;" class="text-main" data-model="spec_popup">编辑规格</a>
		<div class="spacer-gray"></div>
		<a href="javascript:;" class="text-main" data-model="spec_batch">批量修改</a>
		<div class="spacer-gray"></div>
		<a href="javascript:;" class="text-main" data-model="spec_clear">清空规格</a>
		<div class="spacer-gray"></div>
	</div>
</div>
<div class="table resize-table border clearfix">
    <div class="tr border-none spec-name">
        <div class="th" data-width="10">
            <div class="td-con">商品标签</div>
        </div>
        <div class="th" data-width="10">
            <div class="td-con">商品货号</div>
        </div>
        <div class="th" data-width="15">
            <div class="td-con">商品条码</div>
        </div>
        <div class="th" data-width="15">
            <div class="td-con">商品规格</div>
        </div>
        <div class="th" data-width="10">
            <div class="td-con">销售价格</div>
        </div>
        <div class="th" data-width="10">
            <div class="td-con">市场价格</div>
        </div>
        <div class="th" data-width="10">
            <div class="td-con">库存</div>
        </div>
        <div class="th" data-width="5">
            <div class="td-con">重量</div>
        </div>
        <div class="th" data-width="5">
            <div class="td-con">体积</div>
        </div>
        <div class="th" data-width="10">
            <div class="td-con">操作</div>
        </div>
    </div>
    <input type="hidden" name="spu[sn]" value="<?php echo $goods['spu']['sn'] ?>">
    <div id="goods_spec"></div>
    <script id="spec_template" type="text/html">
        <%var i = 0 %>
        <%for(var item in templateData){%>
        <%item = templateData[item]%>
        <div class="tr spec-list" data-id='<%=item['spec_md5']%>'>
             <div class="icon td" data-column='status_ext'>
                <a class="ico ico_promotion <%if(item['status_ext'] != 1){ %>cancel<% }%>" data-value="1" href="javascript:;" title="点击取消促销"></a>
                <a class="ico ico_special <%if(item['status_ext'] != 2){ %>cancel<% }%>" data-value="2" href="javascript:;" title="点击取消特卖"></a>
                <a class="ico ico_new <%if(item['status_ext'] != 3){ %>cancel<% }%>" data-value="3" href="javascript:;" title="点击取消新品"></a>
                <a class="ico ico_reco <%if(item['status_ext'] != 4){ %>cancel<% }%>" data-value="4" href="javascript:;" title="点击取消推荐"></a>
                <input type="hidden" attr="status_ext[<%=item['spec_md5']%>]" value="<%=item['status_ext']%>">
            </div>
            <div class="td" data-column='sn'>
                <div class="td-con"><input class="input sn" attr="sn[<%=item['spec_md5']%>]" type="text" value="<%=item['sn']%>" /></div>
            </div>
            <div class="td" data-column='barcode'>
                <div class="td-con"><input class="input" type="text" attr="barcode[<%=item['spec_md5']%>]" value="<%=item['barcode']%>" /></div>
            </div>
            <div class="td spec_info" data-column='spec'>
                <div class="td-con text-left"><span><%=item['spec_str']%></span></div>
                <%for(var n in item['spec']){%>
                <%var result = item['spec'][n]%>
                <input type="hidden" class="specs" attr="spec[<%=item['spec_md5']%>][]" value='{"id":"<%=result.id%>","name":"<%=result.name%>","value":"<%=result.value%>","style":"<%=result.style%>","color":"<%=result.color%>","img":"<%=result.img%>"}'>
                <%}%>
            </div>
            <div class="td" data-column='shop_price'>
                <div class="td-con"><input class="input" type="text" nullmsg="销售价格必须为数字" datatype="price" attr="shop_price[<%=item['spec_md5']%>]" value="<%=item['shop_price']?item['shop_price']:'0.00'%>" /></div>
            </div>
            <div class="td" data-column='market_price'>
                <div class="td-con"><input class="input" type="text" nullmsg="市场价格必须为数字" datatype="price" attr="market_price[<%=item['spec_md5']%>]" value="<%=item['market_price']?item['market_price']:'0.00'%>" /></div>
            </div>
            <div class="td" data-column='number'>
                <div class="td-con"><input class="input" type="text" nullmsg="库存必须为数字" datatype="n" attr="number[<%=item['spec_md5']%>]" value="<%=item['number']?item['number']:'0'%>" /></div>
            </div>
            <div class="td" data-column='weight'>
                <div class="td-con"><input class="input" type="text" nullmsg="重量必须为数字" datatype="price" attr="weight[<%=item['spec_md5']%>]" value="<%=item['weight']?item['weight']:'0.00'%>" /></div>
            </div>
            <div class="td" data-column='volume'>
                <div class="td-con"><input class="input" type="text" nullmsg="体积必须为数字" datatype="price" attr="volume[<%=item['spec_md5']%>]" value="<%=item['volume']?item['volume']:'0.00'%>" /></div>
            </div>
            <div class="td" data-column='sku_id'>
                <input class="hidden" data-id="<%=item['sku_id']%>" attr="sku_id[<%=item['spec_md5']%>]" type="text" value="<%=item['sku_id']%>" />
               <a href="javascript:;" data-model="spec_delete">删除</a>
            </div>
        </div>
        <%i++;%>
        <%}%>
    </script>
    <script id="spec_desc" type="text/html">
        <%for(var item in spec_desc){%>
        <%var result = spec_desc[item]%>
        <input type="hidden" attr="spec[<%=spec%>][]" value='{"id":"<%=result.id%>","name":"<%=result.name%>","value":"<%=result.value%>","style":"<%=result.style%>","color":"<%=result.color%>","img":"<%=result.img%>"}'>
        <%}%>
        </script>
    </div>

    <script>
        $(function () {
            $(".table").resizableColumns();
            $(window).resize();
            /* 实例化模板 */
        })
        //规格选取
        var goods_init = new Array();
        var defaultProductNo = $('input[name="spu[sn]"]').val() ? $('input[name="spu[sn]"]').val() : "<?php echo 'NU' . time() . rand(10, 99) ?>";
        for(var i=0;i<1;i++){
            goods_init[i] = new Array();
        }
        goods_init[0]['sn'] = defaultProductNo + '-1';
        $('input[name="spu[sn]"]').val(defaultProductNo);

        var sku = goods._sku ? goods._sku : goods_init;
        goods_publish.template('spec_template', sku, function (content) {
            return $('#goods_spec').html(content);
        });
        var selectedItem = <?php echo $goods['extra']['specs'] ? json_encode($goods['extra']['specs']) : '[]'; ?>;

        var productNo = $('input[name="spu[sn]"]').val();
        var n = 1;
        $('.table .tr:last-child').addClass("border-none");
        $(".icon a.ico").live('click', function () {
            if (!$(this).hasClass("cancel")) {
                $(this).addClass("cancel");
            } else {
                $(this).removeClass("cancel").siblings(".ico").addClass("cancel");
                $(this).parent().find('input').val($(this).attr('data-value'));
            }
        });

        function init_template() {
            var goodsRowHtml = template('spec_template', {'templateData': goods_init});
            $('#goods_spec').html(goodsRowHtml);
        }
</script>
