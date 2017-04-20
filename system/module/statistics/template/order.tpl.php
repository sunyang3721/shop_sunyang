<?php include template('header','admin');?>
<body>
	<div class="fixed-nav layout">
		<ul>
			<li class="first">
				销售统计
				<a id="addHome" title="添加到首页快捷菜单">
					[+]
				</a>
			</li>
			<li class="spacer-gray">
			</li>
			<li>
				<a class="current" href="javascript:;">
				</a>
			</li>
		</ul>
		<div class="hr-gray">
		</div>
	</div>
	<div class="content padding-big have-fixed-nav">
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">
						销售概况
					</th>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<tr class="line-height-40">
									<td class="text-left">
										今日销售额：￥<?php echo $datas['today']['amount'];?>
									</td>
									<td class="text-left">
										人均客单价：￥<?php echo $datas['today']['average'];?>
									</td>
									<td class="text-left">
										今日订单数：<?php echo $datas['today']['orders'];?>
									</td>
									<td class="text-left">
										今日取消订单：<?php echo $datas['today']['cancels'];?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">
						销售统计图表
						<div class="count-date fr">
							<a href="javascript:;" class="current" onclick="ajax_getdata(7)">
								最近7天
							</a>
							<a href="javascript:;" onclick="ajax_getdata(30)">
								最近30天
							</a>
							<input type="text" onclick="laydate({istart_time: true, format: 'YYYY-MM-DD', max: laydate.now()}),laydate.skin('danlan')" tabindex="0" placeholder="YYYY-MM-DD " value="" name="start_time" class="input laydate-icon hd-input">
							&emsp; ~&emsp;
							<input type="text" onclick="laydate({istart_time: true, format: 'YYYY-MM-DD', max: laydate.now()}),laydate.skin('danlan')" tabindex="0" placeholder="YYYY-MM-DD " value="" name="end_time" class="input laydate-icon hd-input">
							<input id="submit" class="button bg-main text-normal" type="button" value="确定" />
						</div>
					</th>
				</tr>

				<tr>
					<td class="padding">
						<div id="statistics" style="height: 400px;">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="layout margin-big-top clearfix">
			<div class="fl w50 padding-right">
				<table cellpadding="0" cellspacing="0" class="border bg-white layout">
					<tbody>
						<tr class="bg-gray-white line-height-40 border-bottom">
							<th class="text-left padding-big-left">
								订单地区分部
							</th>
						</tr>
						<tr>
							<td class="padding">
								<div id="area" style="height: 400px;">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="fl w50 padding-left">
				<table cellpadding="0" cellspacing="0" class="border bg-white layout">
					<tbody>
						<tr class="bg-gray-white line-height-40 border-bottom">
							<th class="text-left padding-big-left">
								支付方式类型统计
							</th>
						</tr>
						<tr>
							<td class="padding">
								<div id="pay" style="height: 400px;">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/echarts/dist/echarts.js"></script>
	<script src="<?php echo __ROOT__;?>statics/js/laydate/laydate.js" type="text/javascript"></script>
	<script type="text/javascript">
	var search = <?php echo json_encode($datas['search']); ?>;
	showchart(search);
	function showchart(search){
		// 路径配置
		require.config({
			paths: {
				echarts: '<?php echo __ROOT__;?>statics/js/echarts/dist' //配置路径
			}
		});
		// 使用
		require(
		[
			'echarts',
			'echarts/chart/line', // K线图
			'echarts/chart/map', // 地图
			'echarts/chart/pie' // 饼图
		],DrawEChart);
		function DrawEChart(ec) {
			//销售统计
			myChart = ec.init(document.getElementById('statistics'), 'macarons');
			myChart.setOption({
				/*title : {
				text: '销量',//标题
				subtext: '描述',//文字
				x:'center'//坐标
				},*/
				tooltip: {
					trigger: 'axis'
				},
				legend: {
					data: ['交易金额', '订单数', '客单价']
				},
				toolbox: {
					show: false
				},
				calculable: false,
				xAxis: [{
					type: 'category',
					boundaryGap: false,
					data: search.dates,
				}],
				yAxis: [{
					type: 'value'
				}],
				series: [{
					name: '交易金额',
					type: 'line',
					smooth: true,
					itemStyle: {
						normal: {
							areaStyle: {
								type: 'default'
							}
						}
					},
					data: search.series['amounts'],
				}, {
					name: '订单数',
					type: 'line',
					smooth: true,
					itemStyle: {
						normal: {
							areaStyle: {
								type: 'default'
							}
						}
					},
					data: search.series['orders']
				}, {
					name: '客单价',
					type: 'line',
					smooth: true,
					itemStyle: {
						normal: {
							areaStyle: {
								type: 'default'
							}
						}
					},
					data: search.series['averages']
				}]
			});

			//订单地区分部
			myArea = ec.init(document.getElementById('area'), 'macarons');
			myArea.setOption({
				tooltip: {
					trigger: 'item'
				},
				legend: {
					orient: 'vertical',
					x: 'left',
					data: ['订单数量']
				},
				dataRange: {
					min: 0,
					max: 1000,
					x: 'left',
					y: 'bottom',
					text: ['高', '低'], // 文本，默认为数值文本
					calculable: false
				},
				toolbox: {
					show: false
				},
				series: [{
					name: '订单数量',
					type: 'map',
					mapType: 'china',
					roam: false,
		            itemStyle:{
		                normal:{label:{show:true}}
		            },
					data: <?php echo json_encode($datas['districts']) ?>
				}]
			});
			//支付方式类型统计
			myPay = ec.init(document.getElementById('pay'), 'macarons');
			myPay.setOption({
				tooltip: {
					trigger: 'item',
					formatter: "{a} <br/>{b} : {c} ({d}%)"
				},
				legend: {
					orient: 'vertical',
					x: 'left',
					data: <?php echo json_encode($datas['pays']) ?>
				},
				toolbox: {
					show: false
				},
				calculable: false,
				series: [{
					name: '访问来源',
					type: 'pie',
					radius: ['50%', '70%'],
					itemStyle: {
						normal: {
							label: {
								show: false
							},
							labelLine: {
								show: false
							}
						},
						emphasis: {
							label: {
								show: true,
								position: 'center',
								textStyle: {
									fontSize: '20',
									fontWeight: 'bold'
								}
							}
						}
					},
					data: <?php echo json_encode($datas['payments']); ?>
				}]
			});

		}
	}

	function ajax_getdata(days ,start_time, end_time) {
		$.ajax({
			type: "get",
			async: false,
			//同步执行
			url: "<?php echo url('ajax_getdata')?>",
			dataType: "json",
			data:{days:days,start_time:start_time,end_time:end_time,formhash:formhash},
			success: function(result) {
				if (result.status == 1) {
					showchart(result.result.search);
				} else {
					message("请求数据失败，请稍后再试!");
				}
			},
			error: function(errorMsg) {
				message("请求数据失败，请稍后再试!");
			}
		});
	}

	$(function(){
		$('.count-date a').on('click',function(){
			$(this).addClass('current').siblings().removeClass('current');
		})

		/* 日期查询 */
		$('#submit').bind('click',function(){
			var start_time = $('input[name="start_time"]').val();
			var end_time = $('input[name="end_time"]').val();
			if (end_time && start_time == '') {
				message('请选择开始日期！');
				return false;
			}
			if (start_time == '' && end_time == '') {
				message('请选择起止日期！');
				return false;
			}
			ajax_getdata('',start_time ,end_time);
		})
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
	})
</script>
<?php include template('footer','admin');?>
