<?php include template('header','admin');?>

<script type="text/javascript" src="<?php echo __ROOT__ ?>statics/js/admin/order_action.js"></script>
<script type="text/javascript">
	var order = <?php echo json_encode($order); ?>;
	$(document).ready(function(){
		order_action.init();
	});
</script>

<body>
	<div class="fixed-nav layout">
		<ul>
			<li class="first">订单详情</li>
			<li class="spacer-gray"></li>
		</ul>
		<div class="hr-gray"></div>
	</div>
	<div class="content padding-big have-fixed-nav">
		<!--订单概况-->
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">
						订单概况
						<div class="order-edit-btn fr">
							<!-- 确认付款(仅在线支付) -->
			                <?php if ($order['pay_type'] == 1): ?>
			                	<button <?php if ($order['_status']['now'] == 'create'): ?> onclick="order_action.pay('<?php echo url("order/admin_order/pay",array("order_sn"=>$order['order_sn'])); ?>');" class="bg-main"<?php else: ?>class="bg-gray"<?php endif; ?>>确认付款</button>
			                <?php endif; ?>

							<!-- 确认订单 -->
							<button <?php if ($order['pay_type'] == 1 && $order['_status']['now'] == 'pay' || $order['pay_type'] == 2 && $order['_status']['now'] == 'create') : ?>class="bg-main" onclick="order_action.confirm('<?php echo url("order/admin_order/confirm",array("sub_sn" => $order['sub_sn'])); ?>');"<?php else:?>class="bg-gray"<?php endif; ?>>确认订单</button>

							<!-- 确认发货 -->
							<button <?php if($order['status'] == 1 && $order['confirm_status'] != 0 && $order['delivery_status'] != 2): ?>class="bg-main" onclick="order_action.delivery('<?php echo url("order/admin_order/delivery",array("sub_sn" => $order['sub_sn'])); ?>');"<?php else: ?>class="bg-gray"<?php endif;?>>确认发货</button>

							<!-- 确认完成 -->
							<button <?php if($order['delivery_status'] == 2 && $order['finish_status'] != 2): ?>class="bg-main" onclick="order_action.finish('<?php echo url("order/admin_order/finish",array("sub_sn" => $order['sub_sn'])); ?>');"<?php else: ?>class="bg-gray"<?php endif;?>>确认完成</button>

							<!-- 取消订单 -->
							<button <?php if($order['delivery_status'] == 0 && $order['status'] == 1): ?>class="bg-main" onclick="order_action.order(2,'<?php echo url("order/admin_order/cancel",array("sub_sn" => $order['sub_sn'])); ?>');"<?php else: ?>class="bg-gray"<?php endif; ?>>取消订单</button>

							<!-- 作废 -->
							<button <?php if($order['status'] == 2):?>class="bg-main" onclick="order_action.order(3,'<?php echo url("order/admin_order/recycle",array("sub_sn" => $order['sub_sn'])); ?>');"<?php else: ?>class="bg-gray"<?php endif; ?>>作废</button>

							<!-- 删除订单 -->
							<!-- <button <?php if($order['status'] == 3):?>class="bg-main" onclick="{if (confirm('确定删除吗？该操作不可逆')){order_action.order(4,'<?php echo url("order/admin_order/delete"); ?>');}}"<?php else: ?>class="bg-gray"<?php endif; ?>>删除订单</button> -->
						</div>
					</th>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<tr class="line-height-40">
									<th class="text-left">
										订单号：<?php echo $order['order_sn'];?>&emsp;
										<?php if($order['source']==1) { ?>
											<i class="ico_order_mobile"></i>
										<?php }elseif($order['source']==2) { ?>
											<i class="ico_order_wechat"></i>
										<?php }else { ?>
											<i class="ico_order"></i>
										<?php } ?>
									</th>
									<th class="text-left">支付方式：<?php echo ($order['pay_type']==2) ? '货到付款' : '在线支付'?></th>
									<th class="text-left">订单状态：<?php echo ch_status($order['_status']['now']);?></th>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<!--订单详情-->
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">订单详情</th>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<tr class="line-height-40">
									<td class="text-left">会员账号：<?php echo $order['_member']['username']; ?></td>
									<td class="text-left">支付类型：<?php echo ($order['_main']['pay_method']) ? $order['_main']['pay_method'] : '--';?></td>
									<td class="text-left">下单时间：<?php echo date('Y-m-d H:i:s',$order['system_time']); ?></td>
								</tr>
								<tr class="line-height-40">
									<td class="text-left">支付时间：<?php echo ($order['pay_time']) ? date('Y-m-d H:i:s',$order['pay_time']) : '--' ?></td>
									<td class="text-left">发货时间：<?php echo ($order['delivery_time']) ? date('Y-m-d H:i:s',$order['delivery_time']) : '--' ?></td>
									<td class="text-left">完成时间：<?php echo ($order['finish_time']) ? date('Y-m-d H:i:s',$order['finish_time']) : '--' ?></td>
								</tr>
								<?php runhook('admin_order_send_time',$order['send_time']);?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<tr class="line-height-40">
									<th class="text-left" colspan="3">
										应付订单总额：￥<?php echo ($order['real_price']);?>
										<!-- （商品退货总额：￥<?php echo $order['return_amount'];?>） -->
										<?php if($order['status'] == 1 && $order['pay_status'] == 0 && $order['pay_type'] != 2): ?>
											<a class="text-main"  onclick="order_action.update_real_price('<?php echo url("order/admin_order/update_real_price",array("sub_sn"=>$order['sub_sn'])); ?>');" href="javascript:;">修改订单应付总额</a>
										<?php endif; ?>
									</th>
								</tr>
								<tr class="line-height-40">
									<td class="text-left">商品总额：￥<?php echo $order['sku_price'];?></td>
									<td class="text-left">配送费用：￥<?php echo $order['delivery_price'];?></td>
									<td class="text-left">发票税额：<?php echo ($order['_main']['invoice_tax']) ? '￥'.$order['_main']['invoice_tax'] : '-';?></td>
								</tr>
								<!-- <tr class="line-height-40">
									<td class="text-left">商品折扣：￥<?php echo $order['discount'];?></td>
									<td class="text-left">优惠券减免：￥<?php echo $order['coupons'];?></td>
									<td class="text-left"></td>
								</tr> -->
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right line-height-40">
						<span class="text-main">订单留言：</span><?php echo (!empty($order['remark'])) ? $order['remark'] : '--'; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<!--收货人信息-->
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">收货人信息</th>
					<th class="text-right padding-big-right">
						<?php if ($status !== 1): ?>
			              <a id="add-address" class="bg-gray-edit" href="<?php echo url('address_edit',array("order_sn"=>$order['order_sn']));?>" data-iframe="true" data-iframe-width="680">编辑</a>
			            <?php endif; ?>
			        </th>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<tr class="line-height-40">
									<td class="text-left w25">收货人姓名：<?php echo $order['_main']['address_name'];?></td>
									<td class="text-left w25">电话号码：<?php echo $order['_main']['address_mobile'];?></td>
									<td class="text-left w50">详细地址：<?php echo $order['_main']['address_detail']; ?></td>
								</tr>
								<tr class="line-height-40">
									<td class="text-left w25">发票抬头：<?php echo ($order['_main']['invoice_title']) ? $order['_main']['invoice_title'] : '-';?></td>
									<td class="text-left w25">发票内容：<?php echo ($order['_main']['invoice_content']) ? $order['_main']['invoice_content'] : '-';?></td>
									<td class="text-left w50">&nbsp;</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- 商品信息 -->
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">商品信息</th>
				</tr>
				<tr class="border">
					<td>
						<div class="table resize-table high-table clearfix">
							<div class="tr">
								<span class="th" data-width="40">
									<span class="td-con">商品信息</span>
								</span>
								<span class="th" data-width="10">
									<span class="td-con">单价</span>
								</span>
								<span class="th" data-width="10">
									<span class="td-con">实付金额</span>
								</span>
								<span class="th" data-width="10">
									<span class="td-con">购买数量</span>
								</span>
								<span class="th" data-width="15" data-min="100">
									<span class="td-con">运费模板名称</span>
								</span>
								<span class="th" data-width="15" data-min="100">
									<span class="td-con">物流状态</span>
								</span>
							</div>
							<?php foreach ($order['_skus'] as $delivery_id => $skus) : ?>
								<div class="order-detail-merge layout">
									<?php foreach ($skus as $key => $sku): ?>
										<div class="tr">
											<div class="td">
												<div class="td-con td-pic text-left">
													<span class="pic"><img src="<?php echo $sku['sku_thumb'] ?>"></span>
													<span class="title text-ellipsis txt margin-none padding-small-top"><a href="<?php echo url('goods/index/detail',array('sku_id' => $sku['sku_id'])) ?>" target="_blank"><?php echo $sku['sku_name'] ?></a></span>
													<span class="icon">
														<?php foreach ($sku['sku_spec'] as $spec): ?>
															<em class="text-main"><?php echo $spec['name'] ?>：</em><?php echo $spec['value'] ?>&nbsp;
														<?php endforeach ?>
														<!-- <em class="text-main">处理时间：2015-05-03 10:12:12</em> -->
														<!-- <a href="javascript:;">查看详情</a> -->
														<br/>
														<?php if ($sku['is_give'] == 1) : ?>
															<span class="bg-blue text-white padding-small-left padding-small-right fl margin-small-top text-lh-little">赠品</span>
														<?php endif; ?>
														<?php if ($sku['promotion']) : ?>
															<p class="text-gray text-ellipsis"><span class="bg-red text-white padding-small-left padding-small-right fl margin-small-top text-lh-little margin-small-right"><?php echo ch_prom($sku['promotion']['type']); ?></span><?php echo $sku['promotion']['title']; ?></p>
														<?php endif; ?>
													</span>
													<!-- <i class="return-ico"><img src="../images/ico_returning.png" height="60"></i> -->
												</div>
											</div>
											<div class="td"><span class="td-con">￥<?php echo $sku['sku_price'];?></span></div>
											<div class="td"><span class="td-con">￥<?php echo $sku['real_price'];?></span></div>
											<div class="td"><span class="td-con"><?php echo $sku['buy_nums'] ?></span></div>
											<div class="td"><span class="td-con"><?php echo $sku['delivery_template_name'] ? $sku['delivery_template_name'] : '-' ?></span></div>
										
											<div class="td detail-logistics">
												<?php if ($delivery_id > 0): ?>
													<a class="button bg-sub text-ellipsis look-log" href="javascript:;" data-did="<?php echo $delivery_id; ?>">查看物流</a>
												<?php else: ?>
													<a class="button bg-gray text-ellipsis" href="javascript:;">暂未发货</a>
												<?php endif ?>
											</div>
										</div>
									<?php endforeach ?>
								</div>
							<?php endforeach; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- 订单日志 -->
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>	
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">订单日志</th>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<?php foreach ($order_logs as $k => $log) : ?>
									<tr class="line-height-40">
										<td class="text-left">
											<?php if($log['operator_type']==1){echo '系统';} elseif($log['operator_type']==2){echo '买家';} ?>&emsp;
											<?php echo $log['operator_name'] ?>&emsp;于&emsp;
											<?php echo date('Y-m-d H:i:s' ,$log['system_time']); ?>&emsp;
											「<?php echo $log['action']; ?>」&emsp;
											<?php if ($log['msg']) : ?>操作备注：<?php echo $log['msg']; endif;?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="padding-tb">
			<input class="button margin-left bg-gray border-none" type="button" value="返回" />
		</div>
	</div>
<?php include template('footer','admin');?>
<script>
$('.table').resizableColumns();

$(".look-log").live('click',function(){
	if($(this).hasClass('bg-gray')) return false;
	$(this).removeClass('bg-sub').addClass('bg-gray').html("加载中...");
	var $this = $(this);
	var txt = '';
	$.getJSON('<?php echo url("order/cart/get_delivery_log") ?>', {o_d_id: $(this).attr('data-did')}, function(ret) {
		if (ret.status == 0) {
			alert(ret.message);
			return false;
		}
		if (ret.result.logs.length > 0) {
			$.each(ret.result.logs,function(k, v) {
				txt += '<p>'+ v.add_time +'&nbsp;&nbsp;&nbsp;&nbsp;'+ v.msg +'</p>';
			});
			top.dialog({
				content: '<div class="logistics-info padding-big bg-white text-small"><p class="border-bottom border-dotted padding-small-bottom margin-small-bottom"><span class="margin-big-right">物流公司：'+ret.result.delivery_name+'</span>&nbsp;&nbsp;物流单号：'+ret.result.delivery_sn+'</p>'+ txt +'</div>',
				title: '查看物流信息',
				width: 680,
				okValue: '确定',
				ok: function(){
					$this.removeClass('bg-gray').addClass('bg-sub').html("查看物流");
				},
				onclose: function(){
					$this.removeClass('bg-gray').addClass('bg-sub').html("查看物流");
				}
			})
			.showModal();
		}
	});
})
</script>
