<?php include template('header','admin');?>
	<?php
		/*通知*/
		$this->load->service('admin/cloud')->api_product_notify();
		$product_notify = cache('product_notify');
	?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">后台首页</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<?php if(!empty($product_notify)):?>
			<div class="warn-info border bg-white margin-top padding-lr">
				<i class="warn-info-ico ico_warn margin-right"></i>
				<div id="FontScroll" style="height: 40px;overflow: hidden;">
					<ul>
						<?php foreach($product_notify as $k =>$v):?>
							<li><?php echo $v?></li>
						<?php endforeach;?>
					</ul>
				</div>
				<a href="javascript:;" class="close text-large fr" style="margin-top:-40px ;">×</a>
			</div>
			<?php endif;?>
			<div class="margin-top">
				<div class="fl w50 padding-small-right">
					<table cellpadding="0" cellspacing="0" class="border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">待办事项</th>
							</tr>
							<tr class="border">
								<td class="padding-big-left padding-big-right padding-bottom">
									<table cellpadding="0" cellspacing="0" class="layout">
										<tbody>
											<tr class="line-height-40">
												<th class="text-left" colspan="3">订单提醒</th>
											</tr>
											<tr class="text-lh-big">
												<td>待付款订单：<a href="<?php echo url('order/admin_order/index',array('type' => 1)) ?>" title="点击去查看"><b class="text-main" data-id="pay">0</b></a></td>
												<td>待确认订单：<a href="<?php echo url('order/admin_order/index',array('type' => 2)) ?>" title="点击去查看"><b class="text-main" data-id="confirm">0</b></a></td>
												<td>待发货订单：<a href="<?php echo url('order/admin_order/index',array('type' => 3)) ?>" title="点击去查看"><b class="text-main" data-id="delivery">0</b></a></td>
											</tr>
											<tr class="text-lh-big">
												<td>待评价商品：<b class="text-main" data-id="load_comment">0</b></td>
												<td>待退货申请：<a href="<?php echo url('order/admin_server/index_return',array('type' => 0)) ?>" title="点击去查看"><b class="text-main" data-id="load_return">0</b></a></td>
												<td>待退款申请：<a href="<?php echo url('order/admin_server/index_refund',array('type' => 0)) ?>" title="点击去查看"><b class="text-main" data-id="load_refund">0</b></a></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr class="border">
								<td class="padding-big-left padding-big-right padding-bottom">
									<table cellpadding="0" cellspacing="0" class="layout">
										<tbody>
											<tr class="line-height-40">
												<th class="text-left" colspan="3">商品管理</th>
											</tr>
											<tr class="text-lh-big">
												<td>出售中的商品：<b class="text-main" data-id="goods_in_sales">0</b></td>
												<td>待上架的商品：<a href="<?php echo url('goods/admin/index',array('label' => 2)) ?>" title="点击去查看"><b class="text-main" data-id="goods_load_online">0</b></a></td>
												<td>库存警告商品：<a href="<?php echo url('goods/admin/index',array('label' => 3)) ?>" title="点击去查看"><b class="text-main" data-id="goods_number_warning">0</b></a></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td class="padding-big-left padding-big-right padding-bottom">
									<table cellpadding="0" cellspacing="0" class="layout">
										<tbody>
											<tr class="line-height-40">
												<th class="text-left" colspan="3">信息管理</th>
											</tr>
											<tr class="text-lh-big">
												<td>待处理咨询：<a href="<?php echo url('goods/goods_consult/index',array('status' => 0)) ?>" title="点击去查看"><b class="text-main" data-id="consult_load_do">0</b></a></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">商城信息</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">已完成订单总数</span>
									<a href="<?php echo url('order/admin_order/index',array('type' => 5)) ?>" title="点击去查看"><span class="fr" data-id="finish">0</span></a>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">注册会员总数</span>
									<a href="<?php echo url('member/member/index') ?>" title="点击去查看"><span class="fr" data-id="member_total">0</span></a>
								</td>
							</tr>
						</tbody>
					</table>
                    <table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">开发团队</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">总策划兼产品经理&emsp;</span>
									<span class="margin-large-left fl">董&emsp;浩</span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">产品设计与研发团队</span>
									<span class="margin-large-left fl">夏雪强&emsp;李春林&emsp;孔智翱&emsp;王小龙&emsp;饶家伟&emsp;秦秀荣</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="fl w50 padding-small-left">
					<table cellpadding="0" cellspacing="0" class="border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">资金管理</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left to本月day-sales padding-big line-height-40">
									<span class="fl">今日销售额</span>
									<span class="margin-left text-big fl">￥<em class="h2 fr" data-id="today-amount">0.00</em></span>
									<span class="text-main fr">人均客单价：￥ <em data-id="today-average">0.00</em>
									</span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">本月销售额</span>
									<span class="margin-left fl">￥ <em data-id="month-amount">0.00</em></span>
									<span class="fr">以自然月统计</span>
								</td>
							</tr>
							<tr>
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">今年销售额</span>
									<span class="margin-left fl">￥ <em data-id="year-amount">0.00</em></span>
								</td>
							</tr>
						</tbody>
					</table>
					<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">系统信息</th>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">系统版本</span>
									<span class="fr">云商<?php if(HD_BRANCH == 'stable'){?>稳定版&nbsp;<?php }else{?>开发版&nbsp;<?php }?>v<?php echo HD_VERSION ?></span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">服务器系统及PHP</span>
									<span class="fr"><?php echo php_uname('s');?>/<?php echo  PHP_VERSION;?></span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">服务器软件</span>
									<span class="fr"><?php echo php_uname('s');?></span>
								</td>
							</tr>
							<tr class="border-bottom">
								<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
									<span class="fl">数据库信息</span>
									<span class="fr">MySQL&nbsp;<?php echo version_compare(phpversion(), '7.0.0') > -1 ? mysqli_get_server_info() : mysql_get_server_info();?>/数据库大小&nbsp;<em data-id="dbsize">0</em>M</span>
								</td>
							</tr>
						</tbody>
					</table>
                    <table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">应用中心</th>
							</tr>
							<tr>
								<td>
									<div class="text-left today-sales layout border-top border-white fl" style="padding:0 20px;height:65px;line-height:64px;background-color:#fbfbfb;">
                                        <span class="fl">您有 <b class="text-main">0</b> 款应用可升级&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- <a class="text-main" href="">详情</a> --></span>
                                    </div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
<?php include template('footer','admin');?>
<script>
	$('#FontScroll').FontScroll({time: 3000,num: 1});
	/* ajax加载统计 */
	$.ajax({
		url: "<?php echo url('statistics/order/ajax_home') ?>",
		type: 'get',
		dataType: 'json',
		success: function(ret) {
			// 订单数据
			if (ret.orders) {
				$.each(ret.orders,function(k, v) {
					$("[data-id='"+ k +"']").text(v);
				});
			}
			// 商品数据
			if (ret.goods) {
				$.each(ret.goods,function(k, v) {
					$("[data-id='"+ k +"']").text(v);
				});
			}
			// 待处理咨询
			$("[data-id='consult_load_do']").text(ret.consult_load_do);
			// 注册人数
			$("[data-id='member_total']").text(ret.member_total);
			// 资金管理
			$("[data-id='today-amount']").text(ret.sales.today.amount);
			$("[data-id='today-average']").text(ret.sales.today.average);
			$("[data-id='month-amount']").text(ret.sales.month.amount);
			$("[data-id='year-amount']").text(ret.sales.year.amount);
			/* 数据库大小 */
			$("[data-id='dbsize']").text(ret.dbsize[0].db_length);
		},
		error: function(errorMsg) {
			message("请求数据失败，请稍后再试！");
		}
	});
</script>
