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
					<div class="app-config"></div>
					<div class="app-fields app-field-region"></div>
				</div>
				<div class="app-add-region">
					<h3>添加内容(添加模块会自动跟随页面底部，可鼠标拖拽位置)</h3>
					<ul class="padding-left padding-bottom clearfix"></ul>
				</div>
			</div>
		</div>
		<div class="app-sidebar">
			<div class="sidebar-inner padding border bg-white margin-big-left">
				<b class="arrow"></b>
				<div class="sidebar-content"></div>
			</div>
		</div>
		<div class="app-actions">
			<input class="btn-add" type="submit" value="保存" />
			<!--<input class="btn-save" type="button" value="保存为草稿" />-->
			<input class="btn-cancel" type="button" value="取消" />
		</div>
	</div>
</div>
<div class="hidden" id="diyHtml"><?php echo $tmpl?></div>
<script type="text/javascript">
var upload_url = "<?php echo url('goods/admin/upload')?>"; //图片上传地址
var upload_code = "<?php echo $attachment_init ?>";	//
var dialog_temp = "<?php echo url('wap/admin/dialog_temp')?>";//模板选择地址
var jsoncategory = <?php echo json_encode($category) ?>; //商品类别数据
var hd_links = <?php echo json_encode($links) ?>;
var hd_link_txt = '';	//链接选择字符串
for(var i=0;i<hd_links.length;i++){
	hd_link_txt += '<li><a class="js-modal-'+ hd_links[i].type +'" data-name="'+ hd_links[i].name +'" href="'+ hd_links[i].link +'">'+ hd_links[i].title +'</a></li>';
}
hd_link_txt += '<li><a class="js-modal-custom" data-name="custom" href="javascript:;">自定义链接</a></li>';

var hd_config = {
	"global": {
		"libs": {
			"form": {
				"group": [
					{"label": "页面标题：", "name": "title", "type": "text", "required": "required;maxlength:50"},
					{"label": "页面描述：", "name": "desc", "type": "text", "placeholder": "用户通过微信分享给朋友时，会自动显示页面描述"},
					{"label": "头部背景：", "name": "headbg", "type": "color", "initvalue": "#0068b7"},
					{"label": "背景颜色：", "name": "bgcolor", "type": "color", "initvalue": "#f9f9f9"}
				],
				"logo": {"label": "页面LOGO：", "name": "logo"}
			}
		}
	},
	"ads": {
		"name": "图片广告",
		"libs": {
			"form": {
				"group": [
					{"name": "show", "label": "显示方式：", "type": "radio", "value": ["折叠轮播","分图显示"]},
					{"name": "size", "label": "显示大小：", "type": "radio", "value": ["大图","小图"]}
				]
			}
		}
	},
	"search": {
		"name": "商品搜索",
		"libs": {
			"form": [
				{"text": "可随意插入任何页面和位置，方便粉丝快速搜索商品。", "tip": "注意：记得给商品添加合适的商品标签吧。"}
			]
		}
	},
	"spacing": {
		"name": "辅助空白",
		"libs": {
			"form": {
				"name": "height",
				"type": "hidden",
				"label": "空白高度：",
				"value": 20
			}
		}
	},
	"goods": {
		"name": "商品列表",
		"libs": {
			"form": [
				{"name": "category", "label": "商品分类：", "type": "hidden", "required": "required"},
				{"name": "goods_number", "label": "显示个数：", "type": "radio", "value": [6,12,18], "number": true},
				{"name": "size", "label": "列表样式：", "type": "radio", "value": ["大图","小图","一大两小","列表"]}
			],
			"data": jsoncategory
		}
	},
	"cube": {
		"name": "魔方",
		"libs": {
			"form": [
				{"name": "href", "type": "text", "label": "链接到："},
				{"name": "src", "type": "hidden", "required": "required"}
			]
		}
	},
	"nav": {
		"name": "图片导航",
		"libs": {
			"form": {
				"upload": {"imgVerif": true, "linkVerif": true},
				"type": {"label": "显示设置：", "name": "type", "type": "radio", "value": ["四个一行", "五个一行", "八个两行", "十个两行"]}
			}
		}
	},
	"notice": {
		"name": "公告",
		"libs": {
			"template": [
				{"name": "jd", "title": "仿京东样式", "number": "40001", "img": "<?php echo __ROOT__;?>statics/images/wap/temp_notice_jd.gif"},
				{"name": "tmall", "title": "仿天猫样式", "number": "4012", "img": "<?php echo __ROOT__;?>statics/images/wap/temp_notice_tmall.gif"}
			],
			"form": {
				"style": {"name": "style", "type": "hidden"},
				"upload": {"imgVerif": true, "linkVerif": true},
				"group": [
					[
						{"label": "公告标题：", "name": "title", "type": "text", "placeholder": "请输入公告标题", "required": "required"},
						{"label": "标题颜色：", "name": "color", "type": "color", "initvalue": "#0c0303"}
					],
					[
						{"label": "公告标题：", "name": "title", "type": "text", "placeholder": "请输入公告标题", "required": "required"},
						{"label": "公告副标题：", "name": "subtitle", "type": "text", "placeholder": "公告副标题，可选择不输入"},
						{"label": "标题颜色：", "name": "color", "type": "color", "initvalue": "#0c0303"},
						{"label": "副标题颜色：", "name": "subcolor", "type": "color", "initvalue": "#f9370a"}
					]
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
	    	"base": "wap/haidao.diy.base"
	    },
	    urlArgs: "v=" + (new Date()).getTime()
	}
</script>
<!-- 插件预留位置起始 -->
<?php runhook('diy_edit_extra');?>
<!-- 插件预留位置截止 -->
<script type="text/javascript">
	envConfig.paths.diy = "wap/haidao.diy";
	var r_config_name = [];
	for(var k in envConfig.paths){
		r_config_name.push(k);
	}
	require.config(envConfig);	//设置require配置
    require(r_config_name, function(){
    	var diy = require("diy");
    	diy.init(hd_config);
    });
</script>
</body>
</html>
