<?php include template('header','admin');?>
		<div class="header layout">
			<div class="logo"><img src="./statics/images/logo.png" /></div>
			<div class="site-menu border-menu">
				<ul>
					<li><a class="current" href="javascript:;" data-id="0">首页</a></li>
					<?php foreach ($nodes as $node): if(!$node['_child']) continue; ?>
					<li><a href="javascript:;" data-id="<?php echo $node['id'] ?>"><?php echo $node['name'] ?></a></li>
					<?php endforeach ?>
				</ul>
			</div>
			<div class="header-hr layout"></div>
		</div>
		<div class="welcome layout">
			<ul class="clearfix">
				<li><p>欢迎您：<?php echo $admin['username']?></p></li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('admin_setup/edit')?>" data-frame="main_frame">账户管理</a></li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo __APP__ ?>" target="_blank">网站前台</a></li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('cache/clear');?>" data-frame="main_frame">更新缓存</a></li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('public/logout'); ?>">安全退出</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="side">
			<div class="head border radius border-small padding-little">
				<img src="<?php echo $admin['avatar'] ? $admin['avatar'] : __ROOT__.'statics/images/head.jpg'?>" />
			</div>
			<div class="hr-black"></div>
			<h3 class="side-top text-small text-white">角色：<?php echo $admin['group_name']?></h3>
			<div class="custom-menu"><em class="ico_left_arrow"></em>自定义快捷菜单</div>
			<div id="side-scroll">
				<div class="side-menu-height">
					<div class="hr-blue"></div>
					<ul data-submenu="0">
						<li class="custom-wrap">
							<a class="focus" href="<?php echo url('home'); ?>">后台首页<em class="ico_set fr custom-link margin-right" style="margin-top:6px;"></em></a>
						</li>
						<div id="diy_menu">
						<?php foreach ($menus as $menu): ?>
						 <li><a href="<?php echo $menu['url'] ?>"><?php echo $menu['title'] ?></a></li>
						<?php endforeach ?>
						</div>
					</ul>
					<?php foreach ($nodes as $key => $node): if(!$node['_child']) continue; ?>
					<ul class="hidden" data-submenu="<?php echo $node['id'] ?>">
						<?php if ($node['_child']): ?>
						<?php foreach ($node['_child'] as $_node): ?>
						<li><a href="<?php echo $_node['url'] ?>"><?php echo $_node['name'] ?></a></li>
						<?php if ($_node['split']): ?>
						<div class="hr-blue"></div>
						<?php endif ?>
						<?php endforeach ?>
						<?php endif ?>
					</ul>
					<?php endforeach ?>
				</div>
			</div>
			<div class="show-side">
				<a class="top" href="javascript:;"></a>
				<a class="bottom" href="javascript:;"></a>
			</div>
			<div class="copy">
				<p>Powered by <a href="javascript:;" target="_blank">Haidao</a><br/>Copyright 2013-2016 Dmibox</p>
			</div>
		</div>
		<div class="wrapper" id="main">
			<a class="ico-left" href="javascript:;"></a>
			<iframe id="main_frame" name="main_frame" frameborder="0" src="<?php echo url('home'); ?>"></iframe>
		</div>
<script type="text/javascript">
$(".custom-wrap .custom-link").hover(function(){
	$(".side .custom-menu").show();
},function(){
	$(".side .custom-menu").hide();
});
$(".custom-link").click(function(){
	dialog({
		id:'menu_index',
		url: '<?php echo url('menu/index');?>',
		title: 'loading',
		width: 681,
		cancelValue: '取消',
		cancel:function(){},
		okValue:'确认',
		ok:function(){
			ajax_diymenu_del(this.returnValue);
		}
	})
	.showModal();
	return false;
});
//删除自定义菜单
function ajax_diymenu_del(ids){
	$.post(menudelurl,{'ids':ids},function(data){
		refresh_diymenu();
	});
}
$(".site-menu ul a").click(function() {
	var node_id = $(this).attr('data-id');
    $(".site-menu ul a").removeClass('current');
    $(this).addClass('current');
	$(".side-menu-height ul[data-submenu='"+ node_id +"']").removeClass('hidden');
	$(".side-menu-height ul[data-submenu!='"+ node_id +"']").addClass('hidden');
	/* 模拟点击左侧第一个菜单 */
	$(".side-menu-height ul[data-submenu='"+ node_id +"']").find('li a').eq(0).click()
})

$("a[data-frame]").click(function() {
	$("#" + $(this).data('frame')).attr("src", $(this).attr('href'));
	return false;
});

</script>
<?php include template('footer','admin');?>
