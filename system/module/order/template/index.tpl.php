<?php include template('header','admin');?>

	<div class="fixed-nav layout">
		<ul>
			<li class="first">订单管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
			<li class="spacer-gray"></li>
			<?php $url_arr = $_GET;unset($url_arr['type'],$url_arr['page'],$url_arr['formhash']); ?>
			<li><a <?php if (empty($_GET['type'])){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index').'&'.http_build_query($url_arr); ?>">全部订单</a></li>
			<li><a <?php if ($_GET['type'] == 1){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>1)).'&'.http_build_query($url_arr); ?>">待付款</a></li>
			<li><a <?php if ($_GET['type'] == 2){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>2)).'&'.http_build_query($url_arr); ?>">待确认</a></li>
			<li><a <?php if ($_GET['type'] == 3){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>3)).'&'.http_build_query($url_arr); ?>">待发货</a></li>
			<li><a <?php if ($_GET['type'] == 4){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>4)).'&'.http_build_query($url_arr); ?>">已发货</a></li>
			<li><a <?php if ($_GET['type'] == 5){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>5)).'&'.http_build_query($url_arr); ?>">已完成</a></li>
			<li><a <?php if ($_GET['type'] == 6){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>6)).'&'.http_build_query($url_arr); ?>">已取消</a></li>
			<li><a <?php if ($_GET['type'] == 7){echo 'class="current"';}?> href="<?php echo url('order/admin_order/index',array('type'=>7)).'&'.http_build_query($url_arr); ?>">已回收</a></li>
		</ul>
		<div class="hr-gray"></div>
	</div>
	<div class="content padding-big have-fixed-nav">
		<div class="tips margin-tb">
			<div class="tips-info border">
				<h6>温馨提示</h6>
				<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
			</div>
			<div class="tips-txt padding-small-top layout">
				<p>- 每一个订单均需要经过确认操作才能进行发货货到付款订单须有商家在后台确认订单完成</p>
			</div>
		</div>
		<div class="hr-gray"></div>
		<div class="clearfix">
			<form method="get" >
				<input type="hidden" name="m" value="order" />
				<input type="hidden" name="c" value="admin_order" />
				<input type="hidden" name="a" value="index" />
				<?php if ($_GET['type']): ?>
					<input type="hidden" name="type" value="<?php echo $_GET['type'];?>" />
				<?php endif; ?>
			<div class="form-group form-layout-rank border-none" style="width: 300px;">
				<span class="label" style="width: auto;">搜索</span>
				<div class="box ">
					<div class="field margin-none">
						<input class="input" type="text" name="keyword" value="<?php echo $_GET['keyword'] ?>" placeholder="订单号/收货人姓名/收货人手机号/会员帐号" tabindex="1">
					</div>
				</div>
			</div>
			<input type="submit" class="button bg-sub margin-top fl" style="height: 26px; line-height: 14px;" value="查询">
			</form>
		</div>
		<?php echo runhook('admin_order_lists_extra')?>
		<div class="table-wrap">
			<div class="table resize-table paging-table border clearfix">
				<div class="tr">
					<?php foreach ($lists['th'] AS $th) {?>
					<span class="th" data-width="<?php echo $th['length']?>">
						<span class="td-con"><?php echo $th['title']?></span>
					</span>
					<?php }?>
					<span class="th" data-width="6">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach ($lists['lists'] AS $list) :?>
					<div class="order-list">
						<div class="main-order">
						<div class="tr">
								<?php foreach ($list as $key => $value) {?>
								<?php if($lists['th'][$key]){?>
								<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
								<span class="td">
									<div class="double-click">
										<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
										<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
									</div>
								</span>
								<?php }elseif ($lists['th'][$key]['style'] == 'ident') {?>
									<span class="td ident">
										<span class="ident-show">
											<em class="ico_pic_show"></em>
											<div class="ident-pic-wrap">
												<img src="<?php echo $list['logo'] ? $list['logo'] : '../images/default_no_upload.png'?>" />
											</div>
										</span>
										<div class="double-click">
											<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
											<input class="input double-click-edit text-ellipsis" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" type="text" value="<?php echo $value?>" />
										</div>
									</span>
								<?php }elseif ($lists['th'][$key]['style'] == 'source') {?>
									<span class="td">
										<span class="td-con">
											<?php if($value == 2) : ?>
												<i class="ico_order_mobile"></i>
											<?php elseif ($value == 3) : ?>
												<i class="ico_order_wechat"></i>
											<?php else : ?>
												<i class="ico_order"></i>
											<?php endif; ?>
										</span>
									</span>
								<?php }elseif ($lists['th'][$key]['style'] == 'seller') {?>
								<span class="td">
									<?php if($value == 0) : ?>
										<span class="td-con">自营</span>
									<?php endif; ?>
								</span>
								<?php }elseif ($lists['th'][$key]['style'] == '_status') {?>
								<span class="td">
									<span class="td-con"><?php echo ch_status($value) ?></span>
								</span>
								<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
								<span class="td">
									<a class="ico_up_rack <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
								</span>
								<?php }elseif ($lists['th'][$key]['style'] == 'date') {?>
								<span class="td">
									<span class="td-con"><?php echo date('Y-m-d H:i' ,$value) ?></span>
								</span>
								<?php }elseif ($lists['th'][$key]['style'] == 'hidden') {?>
									<input type="hidden" name="id" value="<?php echo $value?>" />
								<?php }else{?>
								<span class="td">
									<span class="td-con"><?php echo $value;?></span>
								</span>
								<?php }?>
								<?php }?>
								<?php }?>
								<span class="td">
									<span class="td-con">
										<?php if ($list['_showsubs'] == true) : ?>
											<a class="order-handle" href="javascript:;">展开</a>
										<?php else: ?>
											<a href="<?php echo url('order/admin_order/detail',array('sub_sn' => $list['sub_sn'])); ?>">查看</a>
										<?php endif; ?>
									</span>
								</span>
							</div>

						</div>
						<!-- 子订单信息 -->
						<!-- <div class="sub-order">
							<div class="tr">
								<span class="td">
									<span class="td-con">20151113151044525448</span>
								</span>
								<span class="td">
									<span class="td-con">54325432</span>
								</span>
								<span class="td">
									<span class="td-con">2015-11-13 15:10:44</span>
								</span>
								<span class="td">
									<span class="td-con">￥259.00</span>
								</span>
								<span class="td">
									<span class="td-con">货到付款</span>
								</span>
								<span class="td">
									<span class="td-con"><i class="ico_order_mobile"></i></span>
								</span>
								<span class="td">
									<span class="td-con">&nbsp;</span>
								</span>
								<span class="td">
									<span class="td-con">COMPLETION</span>
								</span>
								<span class="td">
									<span class="td-con">
										<a href="">查看</a>
									</span>
								</span>
							</div>
							<div class="tr">
								<span class="td">
									<span class="td-con">20151113151044525448</span>
								</span>
								<span class="td">
									<span class="td-con">54325432</span>
								</span>
								<span class="td">
									<span class="td-con">2015-11-13 15:10:44</span>
								</span>
								<span class="td">
									<span class="td-con">￥259.00</span>
								</span>
								<span class="td">
									<span class="td-con">货到付款</span>
								</span>
								<span class="td">
									<span class="td-con"><i class="ico_order_mobile"></i></span>
								</span>
								<span class="td">
									<span class="td-con">&nbsp;</span>
								</span>
								<span class="td">
									<span class="td-con">COMPLETION</span>
								</span>
								<span class="td">
									<span class="td-con">
										<a href="">查看</a>
									</span>
								</span>
							</div>
						</div> -->
					</div>
				<?php endforeach; ?>
				<!-- 分页 -->
				<div class="paging padding-tb body-bg clearfix">
					<ul class="fr"><?php echo $pages; ?></ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
<?php include template('footer','admin');?>

<script type="text/javascript" src="<?php echo __ROOT__ ?>statics/js/admin/order_action.js"></script>
<script>
	$(".form-group .box").addClass("margin-none");
	$(window).load(function(){
		$(".table").resizableColumns();
		$(".paging-table").fixedPaging();
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
	})
	/* 删除订单 */
	var _orders = <?php echo json_encode($order_arr); ?>;
	var order = {};
	$(".delete_order").bind("click",function() {
		if (confirm('确定删除该订单？该操作不可逆')) {
			var sn = $(this).attr("data-sn");
			if (_orders[sn] == undefined) {
				alert('订单信息有误');
				return false;
			}
			order = _orders[sn];
			order_action.init();
			order_action.order(4,'<?php echo url("order/admin_order/delete"); ?>');
		}
	});

	$(".main-order .order-handle").live('click',function(){
		var $obj = $(this).parents(".main-order").next(".sub-order");
		if($obj.hasClass("show")){
			$obj.removeClass("show");
			$(this).text("展开");
		}else{
			$obj.addClass("show");
			$(this).text("收起");
		}
		$(".table").fixedPaging();
	});
</script>
