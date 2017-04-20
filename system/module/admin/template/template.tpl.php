<?php include template('header','admin');?>
<div class="fixed-nav layout">
	<ul>
		<li class="first">模板管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
		<li class="spacer-gray"></li>
		<li><a class="current" href="javascript:;"></a></li>
	</ul>
	<div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
	<div class="tips margin-tb">
		<div class="tips-info border">
			<h6>温馨提示</h6>
			<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
		</div>
		<div class="tips-txt padding-small-top layout">
			<p>- 云商的每套模板均为独立，如有多套模板，可任意切换使用</p>
		</div>
	</div>
	<div class="hr-gray"></div>
	<ul class="theme-choose-wrap template-list margin-tb">
		<?php foreach ($tpls as $k => $tpl): ?>
		<li data-tpl="<?php echo $k ?>">
			<div class="pic padding-small border bg-white">
				<img src="<?php echo $tpl['thumb'] ?>" onerror="javascript:this.src='./statics/images/thumb.png';"/>
				<?php if (config('TPL_THEME') == $k): ?>
					<div class="mark"><b></b><span></span></div>
				<?php endif ?>
			</div>
			<div class="text">
				<p>模板名称：<?php echo $tpl['name']?></p>
				<p class="text-ellipsis">开发人员：<span class="text-sub"><?php echo $tpl['author'] ?></span></p>
				<p class="text-ellipsis">当前版本：<span class="text-sub"><?php echo $tpl['version'] ?></span></p>
				<p class="text-ellipsis">更新时间：<span class="text-sub"><?php echo $tpl['time'] ?></span></p>
				<?php if (config('TPL_THEME') == $k): ?>
				<button class="button bg-gray">默认模板</button>
				<?php else: ?>
				<button class="button bg-sub">设为默认</button>
				<?php endif ?>
			</div>
		</li>
		<?php endforeach ?>
	</ul>
</div>
<script type="text/javascript">
$("button.bg-sub").live("click", function() {
	var theme = $(this).parents('[data-tpl]').data('tpl');
	$.post("<?php echo url('setdefault') ?>", {
		theme:theme
	}, function(ret) {
		top.dialog({
			title: '提示信息',
			width: 500,
			content:ret.message,
			ok : function(){
				if(ret.status == 1) {
					window.location.href = ret.referer;
				}
				return false;
			}
		})
		.showModal();
	}, 'json');
})
</script>
<?php include template('footer','admin');?>
