<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" >
		<title>后台首页面板</title>
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/haidao.css?v=<?php echo HD_VERSION ?>" />
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/admin.css?v=<?php echo HD_VERSION ?>" />
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/jquery-1.7.2.min.js?v=<?php echo HD_VERSION ?>"></script>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.plug.js?v=<?php echo HD_VERSION ?>" ></script>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.resizeable.columns.js" ></script>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.form.js?v=<?php echo HD_VERSION ?>" ></script>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.validate.js?v=<?php echo HD_VERSION ?>" ></script>
		<link rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/validate.css?v=<?php echo HD_VERSION ?>"/>

		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/admin.js?v=<?php echo HD_VERSION ?>" ></script>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/dialog/dialog-plus-min.js?v=<?php echo HD_VERSION ?>"></script>
		<link type="text/css" rel="stylesheet" href="./statics/js/dialog/ui-dialog.css?v=<?php echo HD_VERSION ?>" />
		<script type="text/javascript">
		var formhash = "<?php echo FORMHASH ?>";
		var SYS_MODULE_NAME = "<?php echo MODULE_NAME ?>";
		var SYS_CONTROL_NAME = "<?php echo CONTROL_NAME ?>";
		var SYS_METHOD_NAME = "<?php echo METHOD_NAME ?>";		
		var menuaddurl = "<?php echo url('admin/public/ajax_menu_add',array('formhash'=>FORMHASH))?>";
		var menurefreshurl = "<?php echo url('admin/public/ajax_menu_refresh',array('formhash'=>FORMHASH))?>";
		var menudelurl = '<?php echo url('admin/public/ajax_diymenu_del',array('formhash'=>FORMHASH))?>';
		var site = {
			root:'<?php echo __ROOT__;?>',
			app:'<?php echo __APP__;?>',
			domain:'<?php echo $_SERVER['REQUEST_URI']?>'
		};

        $(function(){
        		
            $('form').each(function(i, n) {
                $(this).append('<input type="hidden" name="formhash" value="'+ formhash +'"/>');
            })

            $("a").each(function() {
            	var _this = $(this);
                var href = _this.attr('href');
                if(href && href.indexOf('javascript:') == -1 && href.indexOf('formhash') == -1 && $(this).attr('rel') != 'nofollow') {
                    if(href.indexOf('?') != -1) {
                        href += '&formhash='+formhash;
                    } else {
                        href += '?formhash='+formhash;
                    }
                    $(this).attr('href', href);
                }
            })

			$("[data-iframe]").live('click', function() {
				var href = $(this).data('iframe');
				if(href == true) {
					href = $(this).attr('href');
				}
				var width = $(this).data('iframe-width') || 500;
				_iframe(href, width);
				return false;
			})


			$("[data-confirm]").live('click', function() {
				var message = $(this).data('confirm') || '您确定执行本操作？';
				return confirm(message);
			})
        });

		function _iframe(url, width) {
			top.dialog({
				url: url,
				title: 'loading...',
				width: width,
				onclose:function() {
					//console.log(this.returnValue);
				}
			})
			.showModal();
		}

		$("form .bg-gray:not([data-back])").live('click',function(){
			history.go(-1);
		})
		
		window.onload = function(){
			document.onkeydown = function (e) {
				var ev = window.event || e;
				var code = ev.keyCode || ev.which;
				if (code == 116) {
					if(ev.preventDefault) {
						if(document.getElementById("main_frame")){
							document.getElementById("main_frame").contentWindow.location.reload(true)
						}else{
							window.location.reload();
						}
						ev.preventDefault();
					} else {
						if(document.getElementById("main_frame")){
							document.getElementById("main_frame").contentWindow.location.reload(true)
						}else{
							window.location.reload();
						}
						ev.keyCode = 0;
						ev.returnValue = false;
					}
				}
			}
		}
		
		</script>
