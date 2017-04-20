<?php include template('header','admin');?>
	<style>
		.check-table .tr { position: relative; padding-left: 0; }
	</style>
	<div class="fixed-nav layout">
		<ul>
			<li class="first">运费模板设置<a class="hidden" id="addHome" title="添加到首页快捷菜单">[+]</a></li>
			<li class="spacer-gray"></li>
		</ul>
		<div class="hr-gray"></div>
	</div>

	<form action="<?php url('order/delivery_tamplate/update'); ?>" method="post" name="delivery_tamplate">
	<div class="content padding-big have-fixed-nav">
		<div class="form-box clearfix">
			<?php echo form::input('text', 'name', $delivery['name'], '运费模板名称：', '设置运费模板名称，请根据实际情况填写。', array(
					'datatype' => '*',
					'nullmsg' => '请输入运费模板名称',
					));
			?>
			<?php echo form::input('radio','type',(isset($delivery['type']) ? $delivery['type'] : 'weight'),'类型：', '请选择当前运费模板的商品计费类型', array('items' => array('weight' => '按重量','volume' => '按体积','number' => '按件数'),'colspan'=>'3')); ?>
			<?php echo form::input('text', 'sort', (isset($delivery['sort'])?$delivery['sort']:100), '排序：', '请填写自然数，运费模板将会根据排序进行由小到大排列显示');
			?>
		</div>
		<div class="padding">
			<div class="table-wrap">
				<div class="table check-table border paging-table clearfix">
					<div class="tr padding-none border-none">
						<div class="th layout text-left">
							<span class="padding-left text-normal text-sub">地区模板</span>
						</div>
					</div>
					<div class="tr border-none nav-name">
						<div class="th bg-none bg-white w5 first_value"><b>首重</b></div>
						<div class="th bg-none bg-white w5 first_fee"><b>首重费用</b></div>
						<div class="th bg-none bg-white w5 follow_value"><b>续重</b></div>
						<div class="th bg-none bg-white w5 follow_fee"><b>续重费用</b></div>
						<div class="th bg-none bg-white w70"><b>配送地区</b></div>
						<div class="th bg-none bg-white w10"><b>操作</b></div>
					</div>
					<?php
						$deliverys =  array();
						foreach ($delivery['_templates'] as $k => $v) :
					?>
						<div class="tr template"  data-id="">
							<div class="td padding-lr w5">
								<input class="input fl" name="first_value" type="text" value="<?php echo $v['first_value']; ?>"/>
							</div>
							<div class="td padding-lr w5">
								<input class="input fl" name="first_fee" type="text" value="<?php echo $v['first_fee']; ?>"/>
							</div>
							<div class="td padding-lr w5">
								<input class="input fl" name="follow_value" type="text" value="<?php echo $v['follow_value']; ?>"/>
							</div>
							<div class="td padding-lr w5">
								<input class="input fl" name="follow_fee" type="text" value="<?php echo $v['follow_fee']; ?>"/>
							</div>
							<div class="td padding-lr w10">
								<a class="fl margin-left dialog_edit" href="javascript:;">编辑地区</a>
								<input type="hidden" name="district_ids" value="<?php echo $v['district_ids']; ?>" data-type="id"/>
							</div>
							<div class="td w60 text-left padding-lr text-ellipsis" title="<?php echo implode(",", $v['district_names']); ?>" data-type="name"><?php echo implode(",", $v['district_names']); ?></div>
							<div class="td w10">
								<a href="javascript:;" class="delete_tr"  data-type="edit" >删除</a>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="spec-add-button">
						<a href="javascript:;"><em class="ico_add margin-right"></em>添加地区模版</a>
					</div>
				</div>
			</div>
		</div>
		<div class="padding">
			<input type="hidden" name="id" value="<?php echo $delivery['id']; ?>" />
			<input type="submit" class="button bg-main" value="确定" name="dosubmit"/>
			<input type="button" class="button margin-left bg-gray" value="返回" />
		</div>
	</div>
	</form>

<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/template.js"></script>
<script id="district_item" type="text/html">
<div class="tr" data-id="<%=id%>">
	<div class="td padding-lr w5 district">
		<input class="input fl" name="template[<%=id%>][first_value]" type="text" value=""/>
	</div>
	<div class="td padding-lr w5 district">
		<input class="input fl" name="template[<%=id%>][first_fee]" type="text" value=""/>
	</div>
	<div class="td padding-lr w5 district">
		<input class="input fl" name="template[<%=id%>][follow_value]" type="text" value=""/>
	</div>
	<div class="td padding-lr w5 district">
		<input class="input fl" name="template[<%=id%>][follow_fee]" type="text" value=""/>
	</div>
	<div class="td padding-lr w10 district">
		<a class="fl margin-left dialog_edit" href="javascript:;">编辑地区</a>
		<input type="hidden" name="template[<%=id%>][district_ids]" value="" data-type='id'/>
	</div>
	<div class="td w60 text-left padding-lr text-ellipsis" data-type='name'></div>
	<div class="td w10">
		<a href="javascript:;" class="delete_tr">删除</a>
	</div>
</div>
</script>

<script type="text/javascript">
$(function(){
	var $val=$("input[type=text]").first().val();
	$("input[type=text]").first().focus().val($val);
	$('.template').each(function(k,v){
		id = 'edit_'+parseInt(Math.random() * 1000000);
		$(this).attr('data-id',id);
		$(this).find('[name=first_value]').attr('name', 'template['+id+'][first_value]');
		$(this).find('[name=first_fee]').attr('name', 'template['+id+'][first_fee]');
		$(this).find('[name=follow_value]').attr('name', 'template['+id+'][follow_value]');
		$(this).find('[name=follow_fee]').attr('name', 'template['+id+'][follow_fee]');
		$(this).find('[name=district_ids]').attr('name', 'template['+id+'][district_ids]');
  	});
	change();

	var delivery_tamplate = $("form[name=delivery_tamplate]").Validform({
		ajaxPost:false
	})
})

/* 计费类型对应改变地区模板名称 */
function change(){
	switch($('[name=type]:checked').val()){
		case 'weight':
			$('.nav-name').find('.first_value b').html('首重');
			$('.nav-name').find('.first_fee b').html('首重费用');
			$('.nav-name').find('.follow_value b').html('续重');
			$('.nav-name').find('.follow_fee b').html('续重费用');
		break;
		case 'volume':
			$('.nav-name').find('.first_value b').html('首体');
			$('.nav-name').find('.first_fee b').html('首体费用');
			$('.nav-name').find('.follow_value b').html('续体');
			$('.nav-name').find('.follow_fee b').html('续体费用');
		break;
		case 'number':
			$('.nav-name').find('.first_value b').html('首件');
			$('.nav-name').find('.first_fee b').html('首件费用');
			$('.nav-name').find('.follow_value b').html('续件');
			$('.nav-name').find('.follow_fee b').html('续件费用');
		break;
	}
}
/* 点击计费类型 */
$("[name=type]").change(function() {
	change();
})

/* 添加地区模版 */
$(".spec-add-button a").click(function() {
	var content = template('district_item', {id:'news_' + parseInt(Math.random() * 1000000)});
	$('.spec-add-button').before(content);
})

/* 删除地区模版 */
$(".delete_tr").live('click', function(){
	if(confirm('确定删除？'))
	$(this).parents('.tr').fadeOut('slow', function(){
		$(this).remove();
	});
})

//弹出编辑框
$(".dialog_edit").live('click', function() {
	id = $(this).parents('.tr').data('id');
	/* 重新计算需传递的数据 */
	var deliverys = {};
	$("div.tr[data-id]").each(function(i ,n){
		var _id = $(this).data('id'),
			_val = $(this).find("input[data-type='id']").val();

		if(_val.length > 0) {
			_val = 	_val.split(",");
		} else {
			_val = new Array();
		}
		deliverys[_id] = _val;
	})
	top.dialog({
		padding: '0px ',
		width: 720,
		title: '物流编辑地区',
		url: 'index.php?m=order&c=admin_delivery&a=ajax_district_select&id='+ id +'&formhash=<?php echo FORMHASH;?>',
		deliverys:deliverys,
		onclose:function() {
			var dreturn = this.returnValue;
			if($.isEmptyObject(dreturn) == false) {
				deliverys[id] = dreturn.ids;
				$("div[data-id='"+ id +"']").find("[data-type=id]").attr("value", dreturn.ids.join(","));
				$("div[data-id='"+ id +"']").find("[data-type=name]").attr("title", dreturn.txt.join(",")).text(dreturn.txt.join(","));
			}
		}
	}).showModal();
})
</script>