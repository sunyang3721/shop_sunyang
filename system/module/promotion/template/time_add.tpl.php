<?php include template('header','admin');?>
<script type="text/javascript" src="./statics/js/template.js" ></script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">限时促销管理<?php if(!$_GET['id']){?><a id="addHome" title="添加到首页快捷菜单">[+]</a><?php }?></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;"></a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form action="<?php echo url('edit')?>" method="post">
			<div class="form-box clearfix">
				<input type="hidden" name="id" value="<?php echo $info['id'] ? $info['id'] : ''?>" />
				<?php echo form::input('text', 'name', $info['name'], '促销名称：', '请输入促销名称'); ?>
				<?php echo form::input('calendar', 'start_time', $info['start_time'] ? $info['start_time'] : '', '开始时间：', '促销开始时间'); ?>
				<?php echo form::input('calendar', 'end_time', $info['end_time'] ? $info['end_time'] : '', '结束时间：', '促销结束时间'); ?>
		    </div>
			<div class="padding">
				<div class="table-work border margin-tb">
					<div class="border border-white tw-wrap">
						<a class="choose-goods" href="javascript:;"><i class="ico_add"></i>选择商品</a>
						<div class="spacer-gray"></div>
					</div>
				</div>
				<div class="table resize-table border high-table clearfix">
					<div class="tr border-none">
						<div class="th" data-width="30">
							<span class="td-con">商品名称</span>
						</div>
						<div class="th" data-width="20">
							<span class="td-con">原价</span>
						</div>
						<div class="th" data-width="15">
							<span class="td-con">库存</span>
						</div>
						<div class="th" data-width="20">
							<span class="td-con">优惠价格</span>
						</div>
						<div class="th" data-width="15">
							<span class="td-con">操作</span>
						</div>
					</div>
					<script id="prom_template" type="text/html">
					<%for(var item in templateData){%>
            		<%item = templateData[item]%>
					<div class="tr sku_lists" style="visibility: visible" data-skuid="<%=item['id']%>">
						<div class="td w30">
							<div class="td-con td-pic text-left">
								<div class="pic"><img src="<%=item['pic']%>" /></div>
								<div class="title">
									<p class="text-ellipsis padding-small-left"><%=item['title']%></p>
								</div>
								<div class="icon">
									<p class="text-ellipsis"><span class="text-sub"><%=item['spec']%></span></p>
								</div>
							</div>
						</div>
						<div class="td w20 price">
							<div class="td-con" data-id="<%=item['id']%>" data-price="<%=item['price']%>" data-number="<%=item['number']%>">￥<%=item['price']%></div>
						</div>
						<div class="td w15 ">
							<div class="td-con"><%=item['number']%></div>
						</div>
						<div class="td w20">
							<div class="td-con">
								<div class="padding-top padding-small-bottom"></div>
								<input class="input double-click-edit text-ellipsis text-center" name="prom_price[<%=item['id']%>]" type="text" value="<%=item['prom_price']%>" data-reset="false" />
							</div>
						</div>
						<div class="td w15">
							<div class="td-con"><a class="remove-tr" href="">移除</a></div>
						</div>
					</div>
					<% }%>
					</script>
				</div>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" />
				<a class="button margin-left bg-gray" href="">返回</a>
			</div>
			</form>
		</div>
		<script>
			$(window).load(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				$(".form-select-edit").live('click',function(){
					$(".prom-list").eq($(this).find('.form-select-name').val()).removeClass("hidden").siblings(".prom-list").addClass("hidden");
				});
				<?php if (isset($lists)): ?>
				$('.table .tr:gt(0)').remove();
				var info = <?php echo json_encode($lists) ?> ;
				var goodsRowHtml = template('prom_template', {'templateData': info});
				$('.table .tr').after(goodsRowHtml);
				<?php endif ?>
				$('.resize-table').resizableColumns();
				
				//时间选择验证
				$(".laydate-icon").attr("onclick","laydate({istime: true, min: laydate.now(), format: 'YYYY-MM-DD hh:mm:ss' });");
				$("input[type=submit]").click(function(){
					var start = new Date($(".laydate-icon").eq(0).val());
					var end = new Date($(".laydate-icon").eq(1).val());
					var time = new Date();
					if(start>end){
						alert("结束时间必须大于开始时间！");
						return false;
					}
					if(start<=time){
						alert("开始时间必须大于当前时间！");
						return false;
					}
					if(end<=time){
						alert("结束时间必须大于当前时间！");
						return false;
					}
				});
				//时间选择验证结束
				
				//移除
				var removeids = {};
				$(".remove-tr").live('click',function(){
					if(confirm("是否确认删除？")){
						var sku_id = $(this).parents(".tr").data('skuid');
						removeids[sku_id] = {'removeid':sku_id};
						$(this).parents(".tr").remove();
					}
					return false;
				});
				
				$(".choose-goods").live('click',function(){
					var data = {};
					$('.sku_lists').each(function(i,item){
						var params = {
							id : $(this).find(".price div").attr('data-id'),
							title : $(this).find(".title p").html(),
							pic : $(this).find(".pic img").attr('src'),
							spec : $(this).find(".icon span").html(),
							price : $(this).find(".price div").attr('data-price'),
							number : $(this).find(".price div").attr('data-number'),
						}
						data[$(this).find(".price div").attr('data-id')] = params;
					})
					top.dialog({
						url: '<?php echo url('goods/sku/select', array('multiple' => 1))?>',
						title: '加载中...',
						removeids:removeids,
						selected:data,
						width: 980,
						onclose: function () {
							if(this.returnValue){
								$('.table .tr:gt(0)').remove();
								var goodsRowHtml = template('prom_template', {'templateData': this.returnValue});
								$('.table .tr').after(goodsRowHtml);
							}
						}
					})
					.showModal();
				})
			})
			
		</script>
<?php include template('footer','admin');?>
