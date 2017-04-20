<?php include template('header','admin');?>
<div class="fixed-nav layout">
    <ul>
        <li class="first">站点设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">站点信息</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">基本设置</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">购物设置</a></li>
		<li class="fixed-nav-tab"><a href="javascript:;">退货设置</a></li>
		<li class="fixed-nav-tab"><a href="javascript:;">快递设置</a></li>
    </ul>
    <div class="hr-gray"></div>
</div>
<form action="" method="POST" enctype="multipart/form-data">
<div class="content padding-big have-fixed-nav">
    <div class="content-tabs">
        <div class="form-box clearfix">
		<?php echo form::input('text', 'site_name', $setting['site_name'], '站点名称：', '站点名称，将显示在浏览器窗口标题等位置'); ?>
		<?php echo form::input('text', 'com_name', $setting['com_name'], '公司名称：', '网站名称，将显示在页面底部的联系方式处'); ?>
		<?php echo form::input('text', 'site_url', $setting['site_url'], '商城URL：', '网站 URL，将作为链接显示在页面底部，请以http://开头'); ?>
		<?php echo form::input('text', 'icp', $setting['icp'], '商城备案信息代码：', '页面底部可以显示 ICP 备案信息，如果网站已备案，在此输入您的备案号，它将显示在页面底部，如果没有请留空'); ?>
		<?php echo form::input('radio', 'site_isclosed', $setting['site_isclosed'], '商城运营状态：', '暂时将站点关闭，其他人无法访问，但不影响管理员访问', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
	    <?php echo form::input('textarea', 'site_closedreason', $setting['site_closedreason'], '请填写站点关闭原因，将在前台显示'); ?>
		 <?php echo form::input('textarea', 'site_statice', $setting['site_statice'], '商城第三方统计代码：','页面底部可以显示第三方统计'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix">
			<?php echo form::input('file', 'site_logo',$setting['site_logo'], '商城LOGO：','商城LOGO图片，此图不与会员中心LOGO共用');?>
        	<?php echo form::input('select', 'timeoffset',$setting['timeoffset'], '系统默认时区：', '当地时间与 GMT 的时差', array('items' => array("-12"=>"(GMT -12:00) 埃尼威托克岛, 夸贾林..","-11"=>"(GMT -11:00) 中途岛, 萨摩亚群岛..","-10"=>"(GMT -10:00) 夏威夷","-9"=>"(GMT -09:00) 阿拉斯加","-8"=>"(GMT -08:00) 太平洋时间(美国和加拿..","-7"=>"(GMT -07:00) 山区时间(美国和加拿大..","-6"=>"(GMT -06:00) 中部时间(美国和加拿大..","-5"=>"(GMT -05:00) 东部时间(美国和加拿大..","-4"=>"(GMT -04:00) 大西洋时间(加拿大), ..","-3.5"=>"(GMT -03:30) 纽芬兰","-3"=>"(GMT -03:00) 巴西利亚, 布宜诺斯艾..","-2"=>"(GMT -02:00) 中大西洋, 阿森松群岛,..","-1"=>"(GMT -01:00) 亚速群岛, 佛得角群岛 ..","0"=>"(GMT) 卡萨布兰卡, 都柏林, 爱丁堡, ..","1"=>"(GMT +01:00) 柏林, 布鲁塞尔, 哥本..","2"=>"(GMT +02:00) 赫尔辛基, 加里宁格勒,..","3"=>"(GMT +03:00) 巴格达, 利雅得, 莫斯..","3.5"=>"(GMT +03:30) 德黑兰","4"=>"(GMT +04:00) 阿布扎比, 巴库, 马斯..","4.5"=>"(GMT +04:30) 坎布尔","5"=>"(GMT +05:00) 叶卡特琳堡, 伊斯兰堡,..","5.5"=>"(GMT +05:30) 孟买, 加尔各答, 马德..","5.75"=>"(GMT +05:45) 加德满都","6"=>"(GMT +06:00) 阿拉木图, 科伦坡, 达..","6.5"=>"(GMT +06:30) 仰光","7"=>"(GMT +07:00) 曼谷, 河内, 雅加达..","8"=>"(GMT +08:00) 北京, 香港, 帕斯, 新..","9"=>"(GMT +09:00) 大阪, 札幌, 首尔, 东..","9.5"=>"(GMT +09:30) 阿德莱德, 达尔文..","10"=>"(GMT +10:00) 堪培拉, 关岛, 墨尔本,..","11"=>"(GMT +11:00) 马加丹, 新喀里多尼亚,..","12"=>"(GMT +12:00) 奥克兰, 惠灵顿, 斐济,.."))); ?>
			<?php echo form::input('text', 'exp_rate', $setting['exp_rate'], '经验获取比例：', '每消费1元所获得的经验值数量，0为关闭'); ?>
			<?php echo form::input('radio', 'regional_classification', $setting['regional_classification'], '选取地区分级：', '设置前台收货地址的地区分级选取，此设置不会影响后台地区数据', array('items' => array('1'=>'三级地区', '0'=>'四级地区'), 'colspan' => 2,)); ?>
			<?php echo form::input('textarea', 'hot_seach', $setting['hot_seach'], '热搜关键字：','请输入搜索热词，用于前台显示，每行一个'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix" id="buy">
        	<?php echo form::input('select', 'pay_type',$setting['pay_type'], '支付类型设置：', '请选择结算时支持的付款类型，默认为支持"在线支付+货到付款"
', array('items' => array(1 => '在线支付 + 货到付款', 2 => '仅在线支付', 3 => '仅货到付款'))); ?>
			<?php echo form::input('checkbox', 'pays[]', implode(',', $setting['pays']), '请选择支持的支付方式：', '设置在线支付时所支持的支付方式，需先在支付平台设置开启支付方式', array('items' => $payment)); ?>
			<?php echo form::input('select', 'cart_jump',$setting['cart_jump'], '购物车设置：', '设置“加入购物车”提示。默认：跳转到购买成功页面', array('items' => array('跳转到购买成功页面', '跳转到购物车页面', '页面不跳转直接加入购物车'))); ?>
			<?php echo form::input('select', 'stock_change',$setting['stock_change'], '库存下降设置：', '设置库存下降时机，默认为当用户下单成功时商品库存下降
', array('items' => array('订单下单时减库存','订单付款时减库存', '订单发货时减库存',))); ?>
			<?php echo form::input('radio', 'invoice_vat_enabled', $setting['invoice_vat_enabled'], '是否显示购买记录：', '前台商品详情页面是否显示商品购买记录', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
			<?php echo form::input('radio', 'balance_pay', $setting['balance_pay'], '是否开启余额支付功能：', '是否开启余额支付功能，开启后请指定支持的余额充值支付方式', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
			<?php
			if(isset($payment['alipay_escow'])) {
				$payment['alipay_escow'] = $payment['alipay_escow'].'(不支持)';
			}
			?>
			<?php echo form::input('checkbox', 'balance_deposit[]', $setting['balance_deposit'], '请选择余额充值的支付方式：', '设置余额充值所支持的支付方式，需先在支付平台设置开启支付方式', array('items' => $payment, 'colspan' => 1, 'disabled' => 'alipay_escow')); ?>
			<?php echo form::input('radio', 'invoice_enabled', $setting['invoice_enabled'], '是否开启发票功能：', '设置是否开启发票功能', array('items' => array('1'=>'开启', '0'=>'关闭'), 'colspan' => 2,)); ?>
        </div>
        <div class="form-box clearfix for-invoice">
			<?php echo form::input('text', 'invoice_tax', $setting['invoice_tax'], '发票税率：', '设置开发票的税率，单位为%'); ?>
			<?php echo form::input('textarea', 'invoice_content', implode("\r\n",$setting['invoice_content']), '发票内容设置：','客户要求开发票时可以选择的内容，每一行代表一个选项，例如：办公用品'); ?>
        </div>
    </div>
	<div class="content-tabs hidden">
        <div class="form-box clearfix">
        <?php echo form::input('select', 'return_time',$setting['return_time'], '设置退货时间：', '设置退货时间', array('items' => array(1=>"发货后可申请退换货",2=>"确认收货后可申请退换货"))); ?>
		<?php echo form::input('text', 'seller_address', $setting['seller_address'], '收件地址：', '商家地址，将显示在买家填写退货物流位置'); ?>
		<?php echo form::input('text', 'seller_name', $setting['seller_name'], '收件人：', '商家名字，将显示在买家填写退货物流位置'); ?>
		<?php echo form::input('text', 'seller_mobile', $setting['seller_mobile'], '联系电话：', '商家电话，将显示在买家填写退货物流位置'); ?>
		<?php echo form::input('text', 'seller_zipcode', $setting['seller_zipcode'], '邮编：', '商家邮编，将显示在买家填写退货物流位置'); ?>
        </div>
    </div>
    <div class="content-tabs hidden">
        <div class="form-box clearfix">
		<?php echo form::input('text', 'sender_name', $setting['sender_name'], '寄件人名称：', '寄件人名称，将显示在快递单寄件人栏中'); ?>
		<?php echo form::input('text', 'sender_mobile', $setting['sender_mobile'], '寄件人电话：', '寄件人电话，将显示在快递单寄件人电话栏中'); ?>
		<?php echo form::input('text', 'sender_address', $setting['sender_address'], '寄件人地址：', '寄件人地址，将显示在快递单寄件人地址栏中'); ?>
        </div>
    </div>
    <div class="padding">
        <input type="submit" class="button bg-main" value="保存" />
    </div>
</div>
</form>
<?php include template('footer','admin');?>

<script>
	$(function(){
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		/* 支付方式 */
		if ($("input[name=pay_type]").val() == '1' || $("input[name=pay_type]").val() == '2') {
			$("input[name=pay_type]").parents('.form-group').next().show();
		} else {
			$("input[name=pay_type]").parents('.form-group').next().hide();
		}
		$("input[name=pay_type]").change(function() {
			if ($(this).val() == '1' || $(this).val() == '2') {
				$("input[name=pay_type]").parents('.form-group').next().show();
			} else {
				$("input[name=pay_type]").parents('.form-group').next().hide();
			}
		});

		$.each($("input[name=site_isclosed]"),function(){
			if($(this).attr('checked') == 'checked'){
				if($(this).val() == 0){
					$(this).parents(".form-group").next().show();
				}else{
					$(this).parents(".form-group").next().hide();
				}
			}
		})
		$.each($("input[name=balance_pay]"),function(){
			if($(this).attr('checked') == 'checked'){
				if($(this).val() == 0){
					$(this).parents(".form-group").next().hide();
				}else{
					$(this).parents(".form-group").next().show();
				}
			}
		})
		$.each($("input[name=invoice_enabled]"),function(){
			if($(this).attr('checked') == 'checked'){
				if($(this).val() == 0){
					$(this).parents(".form-box").next().hide();
				}else{
					$(this).parents(".form-box").next().show();
				}
			}
		})
	})
	$("input[name=site_isclosed]").live('click',function(){
		if($(this).val() == 0){
			$(this).parents(".form-group").next().slideDown(100);
		}else{
			$(this).parents(".form-group").next().slideUp(100);
		}
	})
	$("input[name=balance_pay]").live('click',function(){
		if($(this).val() == 0){
			$(this).parents(".form-group").next().slideUp(100);
		}else{
			$(this).parents(".form-group").next().slideDown(100);
		}
	})
	$("input[name=invoice_enabled]").live('click',function(){
		if($(this).val() == 0){
			$(this).parents(".form-box").next().slideUp(100);
		}else{
			$(this).parents(".form-box").next().slideDown(100);
		}
	})
</script>
