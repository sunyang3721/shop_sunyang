<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" >
	<title>后台首页面板</title>
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/haidao.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/admin.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/mobile.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/wap/mui.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/wap/haidao.mobile.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/js/upload/uploader.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/js/dialog/ui-dialog.css">
	<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/upload/uploader.js"></script>
	<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/wap/require.js"></script>
</head>
<body>
	<div class="fixed-nav layout">
	    <ul>
	        <li class="first">微店编辑<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
	        <li class="spacer-gray"></li>
	    </ul>
	    <div class="hr-gray"></div>
	</div>
	<div class="have-fixed-nav"></div>
	<div class="app padding-big">
		<div class="app-inner clearfix">
			<div class="app-preview">
				<div class="app-header layout"></div>
				<div class="app-entry bg-white layout">
					<div class="app-content">
						<div class="app-config">
							<div class="app-field-menu">
								<div class="logo mui-pull-left"><img src="template/wap/statics/images/logo.png" height="30"></div>
								<h1 class="mui-title">云商</h1>
							</div>
						</div>
						<div class="app-fields app-field-region" style="margin-top: 300px;"></div>
					</div>
				</div>
			</div>
			<div class="app-sidebar" style="margin-top: 211px;">
				<div class="sidebar-inner padding border bg-white margin-big-left">
					<b class="arrow"></b>
					<div class="sidebar-content"></div>
				</div>
			</div>
			<div class="app-actions">
				<input class="btn-add" type="submit" value="保存" />
				<input class="btn-cancel" type="button" value="取消" />
			</div>
		</div>
	</div>
	<div class="hidden" id="diyHtml"><?php echo $list;?></div>
<script type="text/javascript">
var upload_url = "<?php echo url('goods/admin/upload')?>"; //图片上传地址
var upload_code = "<?php echo $attachment_init ?>";	//
var dialog_temp = "<?php echo url('wap/admin/dialog_temp')?>";//模板选择地址
var hd_links = eval('<?php echo json_encode($links) ?>');
var hd_link_txt = '';	//链接选择字符串
for(var i=0;i<hd_links.length;i++){
	hd_link_txt += '<li><a class="js-modal-'+ hd_links[i].type +'" data-name="'+ hd_links[i].name +'" href="'+ hd_links[i].link +'">'+ hd_links[i].title +'</a></li>';
}
hd_link_txt += '<li><a class="js-modal-custom" data-name="custom" href="javascript:;">自定义链接</a></li>';

var config = {
	"menu": {
		"libs": {
			"template": [
				{"name": "weixin", "title": "微信公众号样式", "number": "0", "img": "statics/images/wap/temp_menu_weixin.gif"},
				{"name": "qzone", "title": "QQ空间样式", "number": "0", "img": ""}
			],
			"form": {
				"usepage": {"name": "page", "type": "checkbox", "value": ["店铺主页", "会员主页", "全部商品分类", "商品列表页", "商品搜索"]},
				"style": {"name": "style", "type": "hidden"},
				"upload": {"imgVerif": false, "linkVerif": false},
				"group":[
					{"label": "背景颜色：", "name": "bgcolor", "type": "color", "initvalue": "#232323"}
				]
			}
		}
	}
}
</script>
<script>
	//require 环境配置
	var envConfig = {
		baseUrl: '<?php echo __ROOT__;?>statics/js/',//加载路径 
	    paths: {
	    	"ui": "wap/jquery-ui.min",
	    	"base64": "wap/jquery.base64",
	    	"dialog": "dialog/dialog-plus-min",
	    	"template": "wap/template",
	    	"text": "wap/text",
	    	"panel": "wap/haidao.diy.panel",
	    	"opts": "wap/haidao.diy.opts",
	    	"base": "wap/haidao.diy.base",
	    	"diy": "wap/haidao.diy"
	    },
	    urlArgs: "v=" + (new Date()).getTime()
	}
</script>
<!-- 插件预留位置起始 -->
<!-- 插件预留位置截止 -->
<script type="text/javascript">
	var r_config_name = [];
	for(var k in envConfig.paths){
		r_config_name.push(k);
	}
	require.config(envConfig);	//设置require配置
    require(r_config_name, function(){
    	var diy = require("diy");
    	diy.init(config);
    });
    
    $('.app-content').on('click', '.nav-menu-1 .nav-item', function(){
    	if($(this).hasClass("current")){
    		$(this).removeClass("current");
    		return false;
    	}
    	var tw = $(this).outerWidth(true);
    	var ch = $(this).children(".submenu");
    	var cw = ch.outerWidth(true);
    	ch.css({left: (tw - cw) / 2 + 'px'});
		$(this).addClass("current").siblings(".nav-item").removeClass("current");
	});
</script>
</body>
</html>