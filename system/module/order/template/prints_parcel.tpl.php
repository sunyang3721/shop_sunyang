<?php include template('header','admin');?>
<script type="text/javascript" src="<?php echo __ROOT__ ?>statics/js/jquery.print.js"></script>
<style type="text/css">
.button_search{
    background-image: -webkit-linear-gradient(top,#ffffff,#e6e6e6);
    background-image: -moz-linear-gradient(top,#ffffff,#e6e6e6);
    background-image: -o-linear-gradient(top,#ffffff,#e6e6e6);
    border: 1px solid #c7c7c7;
    cursor: pointer;
    width: 80px;
    height: 26px;
    line-height: 24px;
    margin-top: 20px;
    margin-bottom: 20px;
}
</style>

	<div class="fixed-nav layout">
		<ul>
			<li class="first">发货单模版<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
			<li class="spacer-gray"></li>
		</ul>
		<div class="hr-gray">
		</div>
	</div>
	<div class="content padding-big have-fixed-nav">
		<div id="content" class="margin-top">
			<?php echo $info['content']?>
		</div>
	</div>
	<div style="text-align:center;">
    <input type="button" class="button_search" value="开始打印" data-event="start_print" title="点击后会标识该快递单已打印" /> &nbsp;&nbsp;
    <input type="button" class="button_search" onclick="window.history.go(-1);" value="返回上一页" />
	</div>

<script type="text/javascript">
$("[data-event='start_print']").bind("click" ,function(){
    $("#content").jqprint(); 
})
</script>
<script>
	$(".operation").live('click',function(){
		 $(".margin-top").jqprint();
	})
	$(".back").live('click',function(){
		window.history.go(-1);
	})
	$(".delete").live('click',function(){
		var total_num = parseInt($(".total_num").text());
		var number = parseInt($(this).prev().prev().text());
		var total_price = parseFloat($(".total_price").text());
		var total_goods_price = parseFloat($(this).prev().text());
		var new_total_price = total_price - total_goods_price;
		$(".total_num").text(total_num - number);
		$(".total_price").text(new_total_price.toFixed(2));
		$(this).parents("#goodslist").hide();
	})
</script>
<?php include template('footer','admin');?>