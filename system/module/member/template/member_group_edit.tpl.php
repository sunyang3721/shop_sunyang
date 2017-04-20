<?php include template('header','admin');?>

<form action="<?php echo url('edit') ?>" method="POST" name="fomr-validate">
<input type="hidden" name="id" value="<?php echo $r['id'] ?>">
<div class="fixed-nav layout">
	<ul>
		<li class="first">编辑会员等级</li>
		<li class="spacer-gray"></li>
	</ul>
	<div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
	<div class="form-box clearfix">
		<?php echo form::input('text', 'name', $r['name'], '等级名称：', '设置会员等级名称',array('datatype' => '*')); ?>
		<?php echo form::input('text', 'min_points', $r['min_points'], '最小经验值：', '设置会员等级所需要的最低经验值下限',array('datatype' => 'n')); ?>
		<?php echo form::input('text', 'max_points', $r['max_points'], '最大经验值：', '设置会员等级所需要的最高经验值上限',array('datatype' => 'n')); ?>
		<?php echo form::input('text', 'discount', $r['discount'], '折扣率：', '折扣率单位为%，如输入90，表示该会员等级的用户可以以商品原价的90%购买',array('datatype' => 'n')); ?>
		<?php echo form::input('textarea', 'description', $r['description'], '等级描述：','会员等级描述信息'); ?>
	</div>
	<div class="padding">
		<input type="submit" name="dosubmit" class="button bg-main" value="确定" />
		<input type="reset" name="back" class="button margin-left bg-gray" value="返回" />
	</div>
</div>
</form>
<?php include template('footer','admin');?>

<script type="text/javascript">
$(function() {
	var validate = $("[name=form-validate]").Validfomr({
		ajaxPost:true,
		callback:function(ret) {
			message(ret.message);
			if(ret.status == 1) {
				setTimeout(function(){
					window.top.main_frame.location.href= ret.referer;
				}, 1000);
			} else {
				return false;
			}
		}
	});
	var $val=$("input[type=text]").first().val();
	$("input[type=text]").first().focus().val($val);
})
</script>
