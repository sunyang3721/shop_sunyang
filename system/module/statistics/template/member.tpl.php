<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">会员统计<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;"></a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
				<tbody>
					<tr class="bg-gray-white line-height-40 border-bottom">
						<th class="text-left padding-big-left">会员统计</th>
					</tr>
					<tr class="border">
						<td class="padding-big-left padding-big-right">
							<table cellpadding="0" cellspacing="0" class="layout">
								<tbody>
									<tr class="line-height-40">
										<td class="text-left">今日新增会员：<?php echo $member['today']?></td>
										<td class="text-left">本月新增会员：<?php echo $member['tomonth']?></td>
										<td class="text-left">会员总数：<?php echo $member['num']?></td>
										<td class="text-left">会员余额总额：<?php echo sprintf("%.2f",$member['money'])?></td>
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
							会员统计图表
							<div class="count-date fr">
							<a href="javascript:;" class="current" onclick="showchart(6,0,0)">
								最近7天
							</a>
							<a href="javascript:;" onclick="showchart(29,0,0)">
								最近30天
							</a>
							<input type="text" onclick="laydate({istime: true, format: 'YYYY-MM-DD', max: laydate.now()}),laydate.skin('danlan')" tabindex="0" placeholder="YYYY-MM-DD " value="" name="stime" class="input laydate-icon hd-input">
							&emsp; ~&emsp;
							<input type="text" onclick="laydate({istime: true, format: 'YYYY-MM-DD', max: laydate.now()}),laydate.skin('danlan')" tabindex="0" placeholder="YYYY-MM-DD " value="" name="etime" class="input laydate-icon hd-input">
							<input id="submit" class="button bg-main text-normal" type="button" value="确定" />
						</div>
						</th>
					</tr>
					<tr>
						<td class="padding">
							<div id="statistics" style="height: 400px;"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/echarts/dist/echarts.js"></script>
		<script src="<?php echo __ROOT__;?>statics/js/laydate/laydate.js" type="text/javascript"></script>
		<script type="text/javascript">
			var ret ;
			showchart(6,0,0);
			function showchart(days,stime,etime){
				getdata(days,stime,etime);
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
					'echarts/chart/line'// K线图
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
							data: ['新增会员', '会员充值']
						},
						toolbox: {
							show: false
						},
						calculable: false,
						xAxis: [{
							type: 'category',
							boundaryGap: false,
							data: ret.member.xAxis,
						}],
						yAxis: [{
							type: 'value'
						}],
						series: [{
							name: '新增会员',
							type: 'line',
							smooth: true,
							itemStyle: {
								normal: {
									areaStyle: {
										type: 'default'
									}
								}
							},
							data: ret.member.reg[0],
						}, {
							name: '会员充值',
							type: 'line',
							smooth: true,
							itemStyle: {
								normal: {
									areaStyle: {
										type: 'default'
									}
								}
							},
							data: ret.member.money[0]
						}]
					});
				}
			}
		
			//通过Ajax获取数据
			function getdata(days,stime,etime){
				$.ajax({
					type: "get",
					async: false,
					//同步执行
					url: "<?php echo url('ajax_getdata')?>",
					dataType: "json",
					data:{days:days,stime:stime,etime:etime,formhash:formhash},
					success: function(result) {
						ret = result;
					},
					error: function(errorMsg) {
						alert("不好意思，大爷，图表请求数据失败啦!");
					}
				});
			}
			
			$(function(){
				$('.count-date a').on('click',function(){
					$(this).addClass('current').siblings().removeClass('current');
				})
				$('#submit').on('click',function(){
					var stime = $('input[name="stime"]').val();
					var etime = $('input[name="etime"]').val();
					showchart(0,stime,etime);
				})
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
		</script>
<?php include template('footer','admin');?>
