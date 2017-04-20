<?php include template('header','admin');?>
		<style>
			.custom-download img { display: inline-block; margin: -3px 10px 0 0; vertical-align: middle; }
			.check-box input { margin-top: -3px; margin-right: 10px; vertical-align: middle; }
			.custom-service-wran { padding: 80px 120px; }
		</style>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">客服首页<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="hr-gray"></div>
			<?php if($info){?>
			<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout text-left line-height-40">
				<tbody>
					<tr class="bg-gray-white border-bottom">
						<th class="text-left padding-big-left">开发团队</th>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">企业ID：</span>
							<span><?php echo $info['siteid']?></span>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">账户名称：</span>
							<span><?php echo $info['admin_userid']?></span>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">账户密码：</span>
							<span><?php echo $info['admin_pass']?></span>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">开通时间：</span>
							<span><?php echo date('Y-m-d H:i:s',$info['start_time'])?></span>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">到期时间：</span>
							<span><?php echo date('Y-m-d H:i:s',$info['end_time'])?></span>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">开通时长：</span>
							<span><?php echo floor(($info['end_time']-$info['start_time'])/2592000)?>个月</span>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">坐&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;席：</span>
							<span>总共<?php echo $info['reception_num']?>个</span>
							<!-- <span class="margin-big-left"><i class="ico_right"></i>已启用8个</span> -->
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">下&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;载：</span>
							<a class="margin-left custom-download" href="<?php echo $info['client_download_url']?>"><img src="<?php echo __ROOT__?>system/module/xiaoneng/statics/images/ico_download.png" />程序</a>
							<a class="margin-left custom-download" href="<?php echo $info['doc_download_url']?>"><img src="<?php echo __ROOT__?>system/module/xiaoneng/statics/images/ico_download.png" />文档</a>
						</td>
					</tr>
					<tr class="border-bottom">
						<td class="padding-big-left padding-big-right">
							<span class="strong">启用状态：</span>
							<label class="margin-left check-box"><input type="radio" name="status" <?php if($status == 1){?>checked="checked"<?php }?> value="1">开启</label>
							<label class="margin-left check-box"><input type="radio" value="0" name="status" <?php if($status == 0){?>checked="checked"<?php }?>>关闭</label>
						</td>
					</tr>
				</tbody>
			</table>
			<!-- <input class="margin-big-top button bg-main" type="submit" value="续费" /> -->
			<?php }else{?>
			<div class="margin-top border custom-service-wran clearfix">
				<div class="fl margin-big-right padding-small-right"><img src="<?php echo __ROOT__?>system/module/xiaoneng/statics/images/custom_service_wran.png" /></div>
				<div class="fl w">
					<h2 class="h1">暂未开发！</h2>
					<!-- <p class="margin-top text-default text-drak-grey">云商为您提供客服功能，您可以点击以下按钮进行操作~</p> -->
					<div class="margin-large-top padding-big-top">
						<input class="button bg-main" type="submit" value="现在开通" />
						<a class="margin-big-left button bg-gray" href="#">返回首页</a>
					</div>
				</div>
			</div>
			<?php }?>
		</div>
	</body>
	<script type="text/javascript">
	$(function(){
		$('[type=submit]').bind('click', function() {
			window.location.href = '<?php echo url('apply')?>';
		});
		$('[name=status]').change(function() {
			$.post('<?php echo url('update')?>',{status:$(this).val()},function(ret){
				message(ret.message);
			},'json');
		});
	})
	</script>
</html>
