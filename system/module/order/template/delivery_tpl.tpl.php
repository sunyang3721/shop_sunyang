<?php include template('header','admin');?>

	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/js/editbox/editbox.css" />
	<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/editbox/jquery.editbox.debug.js" ></script>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">物流模板</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="<?php echo url('delivery_tpl') ?>" method="post">
			<input type="hidden" name="id" value="<?php echo $delivery['id'];?>" />
		<div class="content padding-big have-fixed-nav">
			<div class="padding">
				<div class="editor" id="content"></div>
                <input type="hidden" name="content" value="<?php echo $delivery['tpl'] ?>"/>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</div>
		</form>
<?php include template('footer','admin');?>

<script type="text/javascript">
	var $editor = $("#content");
	var deliverys = <?php echo json_encode($delivery); ?>;
	var contents = <?php echo json_encode($content); ?>;
	$(function(){
	    $editor.editBox({
	        id: 'JaATMu',				//插件ID
	        color:"red",				//控件颜色
	        imgurl:"<?php echo __ROOT__;?>statics/js/editbox/images/",	//图片路径
	        height:<?php echo (int) $content['height']; ?>,
	        width:<?php echo (int) $content['width']; ?>,
	        /* 快递公司列表 */ 
	        delivery:'<?php echo $delivery["identif"] ?>',
	        deliveryName: '<?php echo $delivery["name"] ?>',
	        deliveryImage: '<?php echo __ROOT__;?>statics/images/delivery_express/',
	        deliveryOption:deliverys
	    });
	    if (contents.list) {
		    $.each(contents.list, function(i, n){
		        $editor.addBox({width:n.width,height:n.height,left:n.left,top:n.top,text:n.txt});
		    })
	    }
	})
	$(":submit").click(function() {
	    var content = $editor.content();
	    var check_background = $('#panelTemplate span').text();
	    if (check_background.background == 'undefined') {
	        alert('请选择快递单模版');
	        return false;
	    }
	    $("input[name=content]").attr("value", content);
	});
</script>
