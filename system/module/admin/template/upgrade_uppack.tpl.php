<?php include template('header','admin');?>
		<div class="content padding-big ">
			<div class="success-info layout border radius bg-white margin-top">
				<div class="title border-bottom text-white">
					<h6>温馨提示</h6>
				</div>
				<div class="text border-bottom" >
					<span>正在检测最新版本</span><br />
					<img src="./statics/images/ajax_loader.gif" />
					<p></p>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var url = '<?php echo url('admin/cloud/index')?>'
			function showInfo(_text,_status){
				$('.text span').html(_text);
				if(_status){
					$('.text img').hide();
					$('.text p').html('<a href ='+url+'>正在进行跳转,点击此处手动跳转！</a>');
					window.location.href=url;
				}
			}
			function updateProgress(percentage, dlnow, dltotal) {
//				$('.text span').html('正在下载'+ Math.round(percentage * 100) + '% (' + dltotal + '/' + dlnow + ')');
				$('.text span').html('系统正在升级,请勿关闭浏览器');
			}
		</script>
<?php include template('footer','admin');?>