UM.registerUI('商城名称 用户名 用户手机 用户邮箱 商品名称 商品规格 主订单号 订单金额 商品金额 付款金额 支付方式 充值金额 邮件验证链接 验证码 配送方式 运单号 用户可用余额 变动金额', function( name ) {
	var me = this;
    $btn = $.eduibutton({
    	name: ''+name+'',
        text: ''+name+'',
        click : function(){
            me.execCommand('insertHtml', '{'+name+'}');
        },
        title: ''+name+''
    });
    return $btn.addClass('edui-haidaotag');
});
UM.registerUI('换行', function( name ) {
	return "<br>";
});