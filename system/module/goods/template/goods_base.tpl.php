<script type="text/javascript" src="./statics/js/goods/goods_add.js" ></script>
<div class="form-box goods-form">
    <?php echo form::input('text', 'spu[name]', $goods['spu']['name'], '商品名称：', '商品标题名称不能为空，最长不能超过200个字符', array('datatype' => '*', 'nullmsg' => '商品名称不能为空')); ?>

    <div class="form-group">
        <span class="label">商品分类：</span>
        <div class="box ">
            <input class="goods-class-text input hd-input input-readonly" id="choosecat" value="<?php echo $goods['_category']['parent_name'] ?>" tabindex="0"  nullmsg="请选择商品分类" datatype="*" readonly="readonly" type="text" placeholder="请选择商品分类" data-reset="false" />
            <input class="goods-class-btn" type="button" value="选择" onclick="setClass()" data-reset="false" />
            <input type="hidden" name="spu[catid]" value="<?php echo $goods['spu']['catid'] ?>">
            <input type="hidden" name="cat_format" value="<?php echo $goods['_category']['cat_format']?>">
        </div>
        <p class="desc">选择商品所属分类，一个商品只能属于一个分类</p>
    </div>
    <?php echo form::input('text', 'spu[subtitle]', $goods['spu']['subtitle'], '广告语：', '商品广告语是用于介绍商品的描述信息', array('color' => $goods['spu']['style'] ? $goods['spu']['style'] : '', 'key' => 'spu[style]')); ?>
    <div class="form-group" style="z-index: 2;">
        <span class="label">商品品牌：</span>
        <div class="box" style="width: 256px;">
            <div class="form-select-edit select-search-text-box">
                <div class="form-buttonedit-popup">
                    <input class="input" type="text" value="<?php echo $goods['_brand']['name'] ?>" readonly="readonly" data-reset="false">
                    <span class="ico_buttonedit"></span>
                    <input type="hidden" name="spu[brand_id]" value="<?php echo $goods['_brand']['id'] ?>" data-reset="false">
                </div>
                <div class="select-search-field border border-main">
                    <input class="input border-none" autocomplete="off" type="text" id="brandname" value="" placeholder="请输入品牌名称" data-reset="false" />
                    <i class="ico_search"></i>
                </div>
                <div class="listbox-items brand-list">
                    <?php foreach ($brands AS $brand) { ?>
                        <span class="listbox-item" data-val="<?php echo $brand['id'] ?>"><?php echo $brand['name'] ?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <p class="desc">为商品选择所属品牌，便于用户按照品牌进行查找</p>
    </div>
    <?php echo form::input('text', 'spu[warn_number]', isset($goods['spu']['warn_number']) ? $goods['spu']['warn_number'] : 5, '库存警告：', '填写商品库存警告数，当库存小于等于警告数，系统就会提醒此商品为库存警告商品，系统默认为5', array('datatype' => 'n', 'errormsg' => '库存警告只能为数字')); ?>
    <?php echo form::input('enabled', 'spu[status]', isset($goods['spu']['status']) ? $goods['spu']['status'] : '1', '是否上架销售：', '设置当前商品是否上架销售，默认为是，如选择否，将不在前台显示该商品', array('itemrows' => 2)); ?>
    <?php echo form::input('text', 'spu[sort]', isset($goods['spu']['sort']) ? $goods['spu']['sort'] : 100, '商品排序：', '请填写自然数，商品列表将会根据排序进行由小到大排列显示', array('datatype' => 'n', 'errormsg' => '排序只能为数字')); ?>
    <?php echo form::input('text', 'spu[weight]', $goods['spu']['weight'], '重量：', '请填写每件商品重量，以（kg）为单位'); ?>
    <?php echo form::input('text', 'spu[volume]', $goods['spu']['volume'], '体积：', '请填写每件商品体积，以（m³）为单位'); ?>
    <?php echo form::input('select', 'spu[delivery_template_id]', $goods['spu']['delivery_template_id'], '运费模板：', '请选择商品将关联的运费模板（必选）', array('items'=> $delivery_template)); ?>
    <?php echo form::input('text', 'spu[keyword]', $goods['spu']['keyword'], '商品关键词：', 'Keywords项出现在页面头部的<Meta>标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔'); ?>
    <?php echo form::input('textarea', 'spu[description]', $goods['spu']['description'], '商品描述：', 'Description出现在页面头部的Meta标签中，用于记录本页面的高腰与描述，建议不超过80个字'); ?>
</div>
<script type="text/javascript" src="./statics/js/goods/goods_publish.js?v=<?php echo HD_VERSION ?>" ></script>
<script type="text/javascript">
$(window).load(function(){
    $(".cxcolor").find("table").removeClass("hidden");
})
$('.form-group:last-child').addClass('last-group');

$('.select-search-field').click(function (e) {
    e.stopPropagation();
});

$('.select-search-text-box .form-buttonedit-popup').click(function () {
    if (!$(this).hasClass('buttonedit-popup-hover')) {
        $(this).parent().find('.select-search-field').show();
        $(this).parent().find('.select-search-field').children('.input').focus();
        $(this).parent().find('.listbox-items').show();
    } else {
        $(this).parent().find('.select-search-field').hide();
        $(this).parent().find('.listbox-items').hide();
    }
});

$('#brandname').live('keyup', function () {
    var brandname = this.value;
    $.post("<?php echo url('ajax_brand') ?>", {brandname: brandname}, function (data) {
        $('.brand-list').children('.listbox-item').remove();
        if (data.status == 1) {
            var html = '';
            $.each(data.result, function (i, item) {
                html += '<span class="listbox-item" data-val="' + i + '">' + item + '</span>';
            })
            $('.brand-list').append(html);
        } else {
            var html = '<span class="listbox-item">未搜索到结果</span>';
            $('.brand-list').append(html);
        }
    }, 'json')
})
$(".select-search-text-box .listbox-items .listbox-item").live('click', function () {
    $(this).parent().prev('.select-search-field').children('.input').val();
    $(this).parent().prev('.select-search-field').hide();
    $('.select-search-text-box .form-buttonedit-popup .input').val($(this).html());
    $('input[name="spu[brand_id]"]').val($(this).attr('data-val'));
});
</script>
