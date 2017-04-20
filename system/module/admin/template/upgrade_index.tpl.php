<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">在线更新<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="success-info layout border radius bg-white margin-top">
				<div class="title border-bottom text-white">
					<h6>温馨提示</h6>
				</div>
				<div class="text border-bottom">
					<span>正在检测最新版本</span><br />
					<img src="./statics/images/ajax_loader.gif" />
					<p></p>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var url = '<?php echo url('admin/upgrade/ajax_checkupgrade',array('formhash'=>FORMHASH))?>';
			$(function(){
				$.getJSON(url,'',function(data){
					$('.text span').html(data.msg);
					$('.text img').hide();
					if(data.code == 200){
						$('.text span').html('发现新版本');
						$('.text p').html('<a href ='+data.url+'>正在进行跳转,点击此处手动跳转！</a>');
						window.location.href=data.url;
					}
				});
			})
		</script>
<?php include template('footer','admin');?>
