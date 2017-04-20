{template header member}
		<!-- 购物车结束 -->
		
		<div class="margin-big-top layout">
			<div class="container border border-gray-white member clearfix">
				<div class="left padding-big">
					<div class="user-head margin-big-left margin-big-top">
						<img src="../images/member/系统默认头像.png" height="148" />
						<span><a href="">修改头像</a></span>
					</div>
					<div class="margin-top text-center text-default"><p><?php echo $this->member['_group']['name'] ?></p></div>
					{template menu_index member}
				</div>
				<div class="right padding-big-left padding-big-right">
					<div class="member-order-top margin-top margin-bottom small-search clearfix">
						<div class="fl padding-small-top">
							<ul class="order-menu">
								<li><a class="current" href="">全部订单</a></li>
								<li><a href="">待付款(0)</a></li>
								<li><a href="">待发货(5)</a></li>
								<li><a href="">待收货(0)</a></li>
								<li><a href="">待评价(0)</a></li>
								<li><a class="text-gray" href="">订单回收站</a></li>
								<div class="mat"></div>
							</ul>
						</div>
						<div class="fr search border">
							<form class="clearfix" name="form-search" action="#" method="post">
								<input class="input border-none bg-none fl" type="text" placeholder="请输入订单号" value="">
								<input class="button text-small text-white radius-none border-none bg-gray-white fr" type="button" value="查询">
							</form>
						</div>
					</div>
					<div class="table-wrap">
						<div class="order-table-th text-center">
							<div class="td"><div class="column-wide">订单详情</div></div>
							<div class="td"><div class="column-narrow">收货人</div></div>
							<div class="td"><div class="column-narrow">总计</div></div>
							<div class="td">
								<div class="column-narrow o-stutas-filter">
									<div><span class="open">全部状态<b></b></span></div>
									<dl class="order-stutas bg-white hidden">
										<dt>全部状态<b></b></dt>
										<dd class="selected"><b></b><a href="javascript:;">全部状态</a></dd>
										<dd><b></b><a href="javascript:;">等待付款</a></dd>
										<dd><b></b><a href="javascript:;">等待收货</a></dd>
										<dd><b></b><a href="javascript:;">已完成</a></dd>
										<dd><b></b><a href="javascript:;">已取消</a></dd>
									</dl>	
								</div>
							</div>
							<div class="td"><div class="column-narrow">操作</div></div>
						</div>
						<div class="margin-top order-table border finish-order">
							<div class="th">
								<span>2015-08-10 17:37:30</span>
								<span class="margin-big-left">订单号：<span class="text-drak-grey">9864211</span></span>
								<a class="fr reclaim" href=""></a>
							</div>
							<div class="line">
								<div class="td order-table-info">
									<div class="column-wide">
										<div class="goods-pic">
											<a href=""><img src="../images/test.jpg" /></a>
										</div>
										<div class="goods-name">
											<p class="fl name"><a href="">忆江南 碧螺春PVC礼盒200g</a></p>
											<p>商品规格，没有就显示空着</p>
										</div>
										<div class="service text-gray">
											<span class="yahei">×1</span>
										</div>
									</div>
								</div>
								<div class="td column-narrow">董浩</div>
								<div class="td column-narrow">
									<b class="yahei">￥89.00</b><br />
									<span class="text-gray songti text-small">在线支付</span>
								</div>
								<div class="td column-narrow">
									<span class="text-gray">已完成</span><br />
									<span><a href="order_detail.html">订单详情</a></span><br />
									<span><a href="return&refund.html">退换货</a></span>
								</div>
								<div class="td column-narrow">
									<div class="m-t-15">
										<a class="obtn obtn1" href="">再次购买</a>
									</div>
								</div>
							</div>
						</div>
						
						<div class="margin-top order-table border">
							<div class="th">
								<span>2015-08-10 17:37:30</span>
								<span class="margin-big-left">订单号：<span class="text-drak-grey">9864211</span></span>
							</div>
							<div class="line">
								<div class="td order-table-info">
									<div class="column-wide">
										<div class="goods-pic">
											<a href=""><img src="../images/test.jpg" /></a>
										</div>
										<div class="goods-name">
											<p class="fl name"><a href="">忆江南 碧螺春PVC礼盒200g</a></p>
											<p></p>
										</div>
										<div class="service text-gray">
											<span class="yahei">×1</span>
										</div>
									</div>
									<div class="column-wide">
										<div class="goods-pic">
											<a href=""><img src="../images/scan_code_bg.gif" /></a>
										</div>
										<div class="goods-name">
											<p class="fl name"><a href="">忆江南 碧螺春PVC礼盒200g</a></p>
											<p></p>
										</div>
										<div class="service text-gray">
											<span class="yahei">×1</span>
										</div>
									</div>
								</div>
								<div class="td column-narrow">董浩</div>
								<div class="td column-narrow">
									<b class="yahei">￥89.00</b><br />
									<span class="text-gray songti text-small">在线支付</span>
									<div class="oico o2"></div>
								</div>
								<div class="td column-narrow">
									<span class="text-mix">未付款</span><br />
									<span><a href="order_detail.html">订单详情</a></span>
								</div>
								<div class="td column-narrow">
									<div class="m-t-15">
										<a class="obtn obtn2" href="">立即付款</a>
									</div>
								</div>
							</div>
						</div>
						
						<div class="margin-top order-table border">
							<div class="th">
								<span>2015-08-10 17:37:30</span>
								<span class="margin-big-left">订单号：<span class="text-drak-grey">9864211</span></span>
							</div>
							<div class="line">
								<div class="td order-table-info">
									<div class="column-wide">
										<div class="goods-pic">
											<a href=""><img src="../images/test.jpg" /></a>
										</div>
										<div class="goods-name">
											<p class="fl name"><a href="">忆江南 碧螺春PVC礼盒200g</a></p>
											<p></p>
										</div>
										<div class="service text-gray">
											<span class="yahei">×1</span>
										</div>
									</div>
								</div>
								<div class="td column-narrow">董浩</div>
								<div class="td column-narrow">
									<b class="yahei">￥89.00</b><br />
									<span class="text-gray songti text-small">在线支付</span>
									<div class="oico o3"></div>
								</div>
								<div class="td column-narrow">
									<span class="text-sub">待收货</span><br />
									<span><a href="order_detail.html">订单详情</a></span>
								</div>
								<div class="td column-narrow">
									<div class="m-t-15">
										<a class="obtn obtn3" href="">确认收货</a>
									</div>
								</div>
							</div>
						</div>
						
						<div class="paging margin-top margin-bottom padding-tb clearfix">
							<ul class="fr">
								<li class="prev disabled"><a href="">上一页</a></li>
								<li class="current"><span>1</span></li>
								<li><a href="">2</a></li>
								<li><a href="">3</a></li>
								<li><span>...</span></li>
								<li><a href="">5</a></li>
								<li class="next"><a href="">下一页</a></li>
								<li class="last">共<b>25</b>页&nbsp;到第<input class="input" type="text" value="1">页&nbsp;<a class="button bg-gray-white" href="#">确定</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!--底部-->
		<div class="layout border-top border-gray-white global-footer margin-big-top">
			<div class="layout border border-main"></div>
			<div class="container hd-service clearfix">
				<dl class="fore">
					<dt>帮助标题</dt>
					<dd>
						<div><a href="">购物流程</a></div>
						<div><a href="">配送时效说明</a></div>
						<div><a href="">购买须知</a></div>
						<div><a href="">发票流程</a></div>
					</dd>
				</dl>
				<dl class="fore">
					<dt>帮助标题</dt>
					<dd>
						<div><a href="">售后政策</a></div>
						<div><a href="">退货规则</a></div>
						<div><a href="">取消订单</a></div>
					</dd>
				</dl>
				<dl class="fore">
					<dt>帮助标题</dt>
					<dd>
						<div><a href="">货到付款</a></div>
						<div><a href="">在线支付</a></div>
						<div><a href="">订单物流查询</a></div>
					</dd>
				</dl>
				<dl class="fore">
					<dt>帮助标题</dt>
					<dd>
						<div><a href="">货到付款</a></div>
						<div><a href="">在线支付</a></div>
						<div><a href="">订单物流查询</a></div>
					</dd>
				</dl>
				<dl class="fore">
					<dt>帮助标题</dt>
					<dd>
						<div><a href="">货到付款</a></div>
						<div><a href="">在线支付</a></div>
						<div><a href="">订单物流查询</a></div>
					</dd>
				</dl>
				<dl class="fore last">
					<dt>手机访问：www.kuwuya.com</dt>
					<dd>
						<span><img src="../images/logo.png" width="158" /></span>
					</dd>
				</dl>
			</div>
			<div class="container copyright border-top border-gray-white padding-tb clearfix">
				<p class="cop-left fl w50 text-lh-small">Powered by Haidao v1.5.0.150524_beta<br />© 2013-2015 Dmibox Inc.</p>
				<div class="cop-right fr text-right w50">
					<p class="text-lh-small"><a href="">手机版</a> | <a href="">电商</a> | <a href="">孙洋毕业论文演示</a> | <a href="">站点统计</a></p>
					<p class="text-lh-small">PRC, 2015-05-24 15:55:53</p>
				</div>
			</div>
		</div>
		<div class="hd-toolbar-footer">
			<div class="hd-toolbar-tab hd-tbar-tab-top margin-bottom">
				<a href="#"><i class="tab-ico"></i></a>
			</div>
			<div class="hd-toolbar-tab hd-tbar-tab-backlist">
				<a href="#"><i class="tab-ico"></i></a>
			</div>
		</div>
		<script>
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
			$(".o-stutas-filter .open").click(function(){
				$(".o-stutas-filter .order-stutas").removeClass("hidden");
			});
			$(".o-stutas-filter .order-stutas dt").click(function(){
				$(this).parent().addClass("hidden");
			});
			$(".o-stutas-filter .order-stutas dd").click(function(){
				$(this).addClass("selected").siblings().removeClass("selected");
				$(this).parent().addClass("hidden");
			});
			
			$(".order-menu li a").bind('mouseover',function(){
				var _left = $(this).offset().left-$(".order-menu").offset().left;
				$(".mat").stop().animate({left:_left+"px",width:$(this).width()},300);
			});
			$(".order-menu").bind('mouseleave',function(){
				orderMenuAutoSlide();
			});
			function orderMenuAutoSlide(){
				var num = 0;
				var width = 0;
				$(".order-menu li").each(function(){
					if($(this).children("a").hasClass("current")){
						num = $(this).children("a").offset().left;
						width = $(this).children("a").width();
					}
				});
				$(".mat").stop().animate({left:num-$(".order-menu").offset().left+"px",width:width},300);
			}
			orderMenuAutoSlide();
			
			//已完成订单的订单删除按钮
			$(".finish-order").hover(function(){
				$(this).find(".reclaim").show();
			},function(){
				$(this).find(".reclaim").hide();
			});
		</script>
<?php include template('footer','admin');?>
