<?php include template('header','admin');?>
<script type="text/javascript" src="./statics/js/template.js" ></script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">商品促销管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;"></a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form action="<?php echo url('add')?>" method="post">
			<div class="form-box clearfix">
				<?php echo form::input('text', 'name', '', '促销名称：', '请输入促销名称', array(
					'datatype' => '*',
					'nullmsg' => '请输入促销名称',
					)
				); ?>
				<?php echo form::input('calendar', 'start_time', '', '开始时间：', '促销开始时间', array('format' => 'YYYY-MM-DD hh:mm')); ?>
				<?php echo form::input('calendar', 'end_time', '', '结束时间：', '促销结束时间', array('format' => 'YYYY-MM-DD hh:mm')); ?>
				<?php echo form::input('radio','share_order',$info["share_order"],'是否参加订单促销：','本商品促销活动是否同时参与订单促销活动',array('items'=>array('不参加','参加'),'colspan'=>'2')); ?>
			</div>
			<div class="layout padding clearfix">
				<div class="fl w50 padding-right">
					<div class="table resize-table paging-table border clearfix" data-model="rules">
						<div class="tr border-none">
							<div class="th" data-width="25">
								<span class="td-con text-left">促销类型</span>
							</div>
							<div class="th" data-width="60">
								<span class="td-con">促销规则</span>
							</div>
							<div class="th" data-width="15">
								<span class="td-con">操作</span>
							</div>
						</div>
						<div class="tr" data-id="0">
							<div class="td">
								<div class="td-con text-left" data-model="type">
									<select class="w90" name="rules[0][type]" style="height: 26px; margin-top: 7px;">
										<option value="amount_discount">满额立减</option>
										<option value="number_discount">满件立减</option>
										<option value="amount_give">满额赠礼</option>
										<option value="number_give">满件赠礼</option>
									</select>
								</div>
							</div>
							<div class="td">
								<div class="td-con operate-info text-left" data-model="rule">
									满 <input class="input" type="text" name="rules[0][condition]" value="0" /><span>元</span>，<span>减</span>
<input class="input" type="text" name="rules[0][discount]" /><span>元</span>
								</div>
							</div>
							<div class="td">
								<a href="javascript:;" onclick="delNewAttr(this)">删除</a>
							</div>
						</div>
						<div class="spec-add-button">
							<a href="javascript:;" data-event="add_rule"><em class="ico_add margin-right"></em>添加一个新促销</a>
						</div>
					</div>
				</div>
				<div class="fl w50 padding-left">
					<div class="table resize-table high-table border clearfix" data-model="sku_list">
						<div class="table-add-top">
							<div class="th layout">
								<a class="text-sub text-left" href="javascript:;" data-event="add_skulist"><em class="ico_add margin-right"></em>选择商品</a>
							</div>
						</div>
						<div class="tr border-none">
							<div class="th" data-width="45">
								<span class="td-con">商品名称</span>
							</div>
							<div class="th" data-width="20">
								<span class="td-con">原价</span>
							</div>
							<div class="th" data-width="20">
								<span class="td-con">库存</span>
							</div>
							<div class="th" data-width="15">
								<span class="td-con">操作</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" />
				<button type="button" class="button margin-left bg-gray">返回</button>
			</div>
			</form>
		</div>
<script type="text/javascript">
	function delNewAttr(self){
		if (!confirm("确认要删除么？")) {
                return false;
            }
		$(self).parent().parent('.tr').remove();
	}
	$(function(){
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		$('.resize-table').resizableColumns();
		$("a[data-event='add_rule']").live('click', function(){
			var _indent = $("[data-model='rules']").find("[data-id]:last").data('id');
			var _html = template('rules_template', {"i" : (_indent + 1)});
			$(this).parents("div.spec-add-button").before(_html);
			
		})
		var removeids = {};
		$("[data-event='del_sku']").live('click',function(){
			if(confirm("是否确认删除？")){
				var sku_id = $(this).parents(".tr[data-skuid]").data('skuid');
				removeids[sku_id] = {'removeid':sku_id};
				$(this).parents(".tr[data-skuid]").remove();
			}
			return false;
		});

		$("select[name^=rules]").live("change", function(){
			var _val = $(this).val();
			var _id = $(this).parents(".tr[data-id]").data('id');
			var _html = template('rule_' + _val, {"i" : _id});
			$(this).parents(".tr[data-id]").find("div[data-model='rule']").html(_html);
		})

		/* 选择赠品 */
		$("button[type=buuton][data-event='give_sku']").live("click", function(){
			var _this = $(this);
			var selected = {"id" : _this.siblings("input[type='hidden']").attr('value')};
			top.dialog({
				url: "<?php echo url('goods/sku/select', array('multiple' => 0,'type' => 'give'))?>",
				title: '选择赠品',
				width: 980,
				selected:selected,
				onclose: function () {
					if($.isEmptyObject(this.returnValue) === false){
						_this.siblings("span[data-model='give']").attr("data-id", this.returnValue.id).attr("data-title", this.returnValue.title).html(this.returnValue.title);
						_this.siblings("input[type='hidden']").attr('value', this.returnValue.id);
						_this.removeClass('bg-sub').addClass('bg-gray').html('重新选择');
					} else {
						_this.siblings("span[data-model='give']").attr('data-id', 0).attr('data-title', '').html('');
						_this.removeClass('bg-gray').addClass('bg-sub').html('选择赠品');
						_this.siblings("input[type='hidden']").attr('value', 0);
					}
				}
			})
			.showModal();
			return false;
		})

		/* 选择促销商品 */
		$("a[data-event='add_skulist']").live("click", function(){
			var selected = {};
			$("div[data-model='sku_list']").find("[data-skuid]").each(function(i ,n){
				var sku_id = $(this).data('skuid');
				selected[sku_id] = {
					id : sku_id,
					number : $(this).data('number'),
					pic : $(this).data('thumb'),
					price : $(this).data('price'),
					spec : $(this).data('spec'),
					title : $(this).data('title')
				}
			})
			top.dialog({
				url: "<?php echo url('goods/sku/select', array('multiple' => 1))?>",
				title: '选择促销商品',
				width: 980,
				removeids:removeids,
				selected:selected,
				onclose: function () {
					if(this.returnValue){
						var _html = template('template_skulist', {'skulist' : this.returnValue});
						$("div[data-model='sku_list']").find("[data-skuid]").remove();
						$("div[data-model='sku_list']").find('.border-none').after(_html);
					}
				}
			})
			.showModal();			
			return false;
		})
		
	})			
</script>
<script id="rules_template" type="text/html">
<div class="tr" data-id="<%=i%>" style="visibility: visible;">
	<div class="td w25">
		<div class="td-con text-left" data-model="type">
			<select class="w90" name="rules[<%=i%>][type]" style="height: 26px; margin-top: 7px;">
				<option value="amount_discount">满额立减</option>
				<option value="number_discount">满件立减</option>
				<option value="amount_give">满额赠礼</option>
				<option value="number_give">满件赠礼</option>
			</select>
		</div>
	</div>
	<div class="td w60">
		<div class="td-con operate-info text-left" data-model="rule">
			<span>满</span> <input class="input" type="text" name="rules[<%=i%>][condition]" value="0" /><span>元</span>，<span>减</span><input class="input" type="text" name="rules[<%=i%>][discount]" /><span>元</span>
		</div>
	</div>
	<div class="td w15"><a href="javascript:;" data-event="del_rule" onclick="delNewAttr(this)">删除</a></div>
</div>
</script>

<script id="rule_amount_discount" type="text/html">
满 <input class="input" type="text" name="rules[<%=i%>][condition]" value="0" /><span>元</span>，<span>减</span><input class="input" type="text" name="rules[<%=i%>][discount]" /><span>元</span>
</script>
<script id="rule_number_discount" type="text/html">
满 <input class="input" type="text" name="rules[<%=i%>][condition]" value="0" /><span>件</span>，<span>减</span> <input class="input" type="text" name="rules[<%=i%>][discount]" /><span>元</span>
</script>
<script id="rule_amount_give" type="text/html">
满 <input class="input" type="text" name="rules[<%=i%>][condition]" value="0" /><span>元</span>，<span>赠</span><button type="buuton" class="button bg-sub" data-event="give_sku" data-back="false">选择赠品</button><span data-model="give"></span><input class="input" type="hidden" name="rules[<%=i%>][discount]" value="0"/>
</script>
<script id="rule_number_give" type="text/html">
满 <input class="input" type="text" name="rules[<%=i%>][condition]" value="0" /><span>件</span>，<span>赠</span><button type="buuton" class="button bg-sub" data-event="give_sku" data-back="false">选择赠品</button><span data-model="give"></span><input class="input" type="hidden" name="rules[<%=i%>][discount]" value="0"/>
</script>



<script id="template_skulist" type="text/html">
<%for(var id in skulist){%>
<%sku = skulist[id]%>
<div class="tr" style="visibility: visible;" data-skuid="<%=sku['id']%>" data-number="<%=sku['number']%>" data-thumb="<%=sku['pic']%>" data-title="<%=sku['title']%>" data-spec="<%=sku['spec']%>" data-price="<%=sku['price']%>">
	<div class="td w45">
		<div class="td-con td-pic text-left">
			<div class="pic"><img src="<%=sku['pic']%>" /></div>
			<div class="title">
				<p class="text-ellipsis padding-small-left"><%=sku['title']%></p>
			</div>
			<div class="icon">
				<p class="text-ellipsis"><span class="text-sub">商品类型:</span><%=sku['spec']%></p>
			</div>
		</div>
	</div>
	<div class="td w20">
		<div class="td-con">￥<%=sku['price']%></div>
	</div>
	<div class="td w20">
		<div class="td-con"><%=sku['number']%></div>
	</div>
	<div class="td w15">
		<div class="td-con"><a href="javascript:;" data-event="del_sku">移除</a></div>
	</div>
</div>
<input type="hidden" name="sku_ids[]" value="<%=sku['id']%>"/>
<% }%>
</script>
<?php include template('footer','admin');?>
