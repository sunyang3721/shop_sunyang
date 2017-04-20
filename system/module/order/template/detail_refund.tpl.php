<?php include template('header','admin');?>

		<div class="fixed-nav layout">
			<ul>
				<li class="first">订单退款详情</li>
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
							退款信息
							<div class="order-edit-btn fr">
								<?php if($_refund['status'] == 0) : ?>
									<a class="button margin-small bg-main" href="javascript:agree();" title="点此进行处理">处理退款</a>
								<?php else: ?>
									<a class="button margin-small bg-gray">已处理</a>
								<?php endif; ?>
							</div>
						</th>
					</tr>
					<tr class="border">
						<td class="padding-big-left padding-big-right">
							<table cellpadding="0" cellspacing="0" class="layout">
								<tbody>
									<tr class="line-height-40">
										<td class="text-left">主订单号：<?php echo $_refund['order_sn'];?></td>
										<td class="text-left">订单号：<?php echo $_refund['sub_sn'];?></td>
										<?php if ($_refund['type'] == 1) : ?>
											<td class="text-left"><a href="<?php echo url("order/admin_server/detail_return",array('id' => $_refund['return_id'])) ?>" class="text-sub" title="点击查看退货详细信息">查看退货详情</a></td>
										<?php endif; ?>
									</tr>
									<tr class="line-height-40">
										<td class="text-left">退款类型：<?php echo $_refund['type'] == 1 ? 退货并退款 : 仅退款;?></td>
										<td class="text-left">退款金额：<?php echo $_refund['amount'];?></td>

									</tr>
									<tr class="line-height-40 border-bottom">
										<td class="text-left">申请人：<?php echo $_refund['_buyer']['username'];?></td>
										<td class="text-left">联系电话：<?php echo $_refund['_buyer']['mobile'];?></td>
										<td class="text-left">申请时间：<?php echo date('Y-m-d H:i:s',$_refund['dateline']);?></td>
									</tr>
									<tr class="line-height-40">
										<td class="text-left" colspan="3">申请理由：<?php echo $_refund['cause'];?></td>
									</tr>
									<tr class="line-height-40">
										<td class="text-left" colspan="3">退款描述：<?php echo $_refund['desc'];?></td>
									</tr>
									<tr class="line-height-40">
										<td class="text-left" colspan="3">
											<div class="return-goods-wrap">
												<?php if (!empty($_refund['_images'])) : ?>
													<?php foreach ($_refund['_images'] as $img): ?>
														<div class="pic">
															<img src="<?php echo $img; ?>" />
															<a target="_blank" href="<?php echo $img; ?>">查看大图</a>
														</div>
													<?php endforeach ?>
												<?php else: ?>
													<div class="pic">无图</div>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<?php if($track){?>
			<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
				<tbody>
					<tr class="bg-gray-white line-height-40">
						<th class="text-left padding-big-left">订单物流信息</th>
					</tr>
					<tr class="border">
						<td class="padding-big-left padding-big-right">
							<table cellpadding="0" cellspacing="0" class="layout">
								<tbody>
									<tr class="line-height-40 border-bottom">
										<td class="text-left w25">物流公司：<?php echo $deliverys[$track['com']]?></td>
										<td class="text-left w25">物流单号：<?php echo $track['nu']?></td>
										<td class="text-left w50"></td>
									</tr>
									<?php foreach ($track['data'] as $key => $data) {?>
									<tr class="line-height-40">
										<td class="text-left <?php if($key == 0){?>text-main<?php }?>" colspan="3"><?php echo $data['time']?>	 	<?php echo $data['context']?></td>
									</tr>
									<?php }?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<?php }?>
			<!--商品信息-->
			<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
				<tbody>
					<tr class="bg-gray-white line-height-40 border-bottom">
						<th class="text-left padding-big-left">退款商品</th>
					</tr>
					<tr class="border">
						<td>
							<div class="table resize-table high-table clearfix">
								<div class="tr">
									<span class="th" data-width="70">
										<span class="td-con">商品信息</span>
									</span>
									<span class="th" data-width="15">
										<span class="td-con">购买数量</span>
									</span>
									<span class="th" data-width="15">
										<span class="td-con">应付金额</span>
									</span>
								</div>
								<div class="tr">
									<div class="td">
										<div class="td-con td-pic text-left">
											<span class="pic"><img src="<?php echo $_refund['_sku']['sku_thumb'] ?>" /></span>
											<span class="title text-ellipsis txt"><a href="" target="_blank"><?php echo $_refund['_sku']['sku_name'] ?></a></span>
											<span class="icon">
												<?php foreach ($_refund['_sku']['sku_spec'] as $key => $val): ?>
													<em class="text-main"><?php echo $val['name'] ?>：</em><?php echo $val['value'] ?>&nbsp;
												<?php endforeach; ?>
											</span>
											<!-- <i class="return-ico"><img src="statics/images/ico_returning.png" height="60"></i> -->	
										</div>
									</div>
									<div class="td"><span class="td-con">x<?php echo $_refund['_sku']['buy_nums'] ?></span></div>
									<div class="td"><span class="td-con">￥<?php echo $_refund['_sku']['real_price']; ?></span></div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
				<tbody>
					<tr class="bg-gray-white line-height-40 border-bottom">
						<th class="text-left padding-big-left">退款日志</th>
					</tr>
					<tr class="border">
						<td class="padding-big-left padding-big-right">
							<table cellpadding="0" cellspacing="0" class="layout">
								<tbody>
								<?php foreach ($_refund['logs'] as $key => $log) : ?>
									<tr class="line-height-40">
										<td class="text-left">
											<?php echo $log['_operator_type'] ?>&emsp;
											<?php echo $log['operator_name']; ?>&emsp;于&emsp;
											<?php echo date('Y-m-d H:i:s' ,$log['system_time']); ?>&emsp;
											「<?php echo $log['action']; ?>」&emsp;&emsp;备注：
											<?php echo $log['msg']?>
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
				<a href="javascript:history.go(-1)" class="button bg-gray border-none">返回</a>
			</div>
		</div>
<?php include template('footer','admin');?>
<script>
	$('.table').resizableColumns();
	// 处理退款
	var id = "<?php echo $_refund['id']; ?>";
	var url = "<?php echo url('alert_refund')?>";
	function agree(){
		var d = top.dialog({
			url : url,
			title: '处理退款',
			data:id,
			width: 280,
			okValue : '确定',
			cancelValue : '取消',
			onclose: function () {
				window.location.reload();
		    }
		});
		d.showModal();
	}
</script>