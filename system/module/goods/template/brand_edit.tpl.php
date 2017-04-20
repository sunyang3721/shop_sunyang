<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">商品品牌设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		
		<div class="content padding-big have-fixed-nav">
			<form action="" method="POST" enctype="multipart/form-data">
			<div class="form-box clearfix" id="form">
				<?php echo form::input('text', 'name', $info['name'], '品牌名称：', '请填写商品品牌的名称；例如：耐克；可口可乐等。',array('validate' => 'required')); ?>
				<?php echo form::input('file','logo', $info['logo'], '品牌标识：','请上传品牌LOGO，用于前台展示。',array('preview'=>$info['logo'])); ?>
				<?php echo form::input('text', 'url', $info['url'], '品牌网址：','请填写品牌网址，以http://开头。'); ?>
				<?php echo form::input('text', 'sort', $info['sort'] ? $info['sort'] : 100, '品牌排序：', '请填写自然数。品牌列表将会根据排序进行由小到大排列显示。'); ?>
				<?php echo form::input('enabled','isrecommend',isset($info['isrecommend']) ? $info['isrecommend'] : '1',  '是否推荐：', '请设置品牌是否为推荐品牌。', array('colspan' => 2)); ?>
				<?php echo form::input('textarea','descript', $info['descript'], '品牌描述：', '请填写品牌的描述信息。'); ?>
			</div>
			<div class="padding">
				<input type="hidden" name="id" value="<?php echo $info['id']?>">
				<input type="submit" name="dosubmit" class="button bg-main" value="确定" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
			</form>
		</div>
		<script type="text/javascript">
			$(window).otherEvent();
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
		</script>
<?php include template('footer','admin');?>
