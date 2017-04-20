<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">优化设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li class="fixed-nav-tab"><a <?php if(METHOD_NAME == 'rewrite') :?>class="current"<?php endif;?> href="<?php echo url('site/rewrite'); ?>">URL伪静态</a></li>
				<li class="fixed-nav-tab"><a <?php if(METHOD_NAME == 'seo') :?>class="current"<?php endif;?> href="<?php echo url('site/seo'); ?>">SEO设置</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form method="POST" action="<?php echo url('site/seo'); ?>">
			<div class="content content-tabs padding-big have-fixed-nav">
				<div class="form-box clearfix">
					<?php echo form::input('text', 'seos[header_title_add]', $seos['header_title_add'], '标题附加字：', '网页标题通常是搜索引擎关注的重点，本附加字设置出现在标题中商城名称后，如有多个关键字，建议用分隔符分隔'); ?>
					<?php echo form::input('text', 'seos[header_keywords]', $seos['header_keywords'], '商城关键词：', 'Keywords项出现在页面头部的<Meta>标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔'); ?>
					<?php echo form::input('text', 'seos[header_description]', $seos['header_description'], '关键词描述：', 'Description出现在页面头部的Meta标签中，用于记录本页面的摘要与描述，建议不超过80个字'); ?>
					<?php echo form::input('textarea', 'seos[header_other]', $seos['header_other'], '其他页头信息：','如需在<head></head>中添加其他的HTML代码，可以使用本设置，否则请留空'); ?>
				</div>
			</div>
			<div class="padding-large-left">
				<input type="submit" class="button bg-main" value="保存" data-reset="true"/>
			</div>
		</form>
	
	<script>
		$(function(){
			var $val=$("input[type=text]").first().val();
			$("input[type=text]").first().focus().val($val);
		})
	</script>
<?php include template('footer','admin');?>
