<?php include template('header','admin');?>
		<div class="content padding-big">
			<?php if(!isset($cloud)):?>
			<div class="warn-info border bg-white margin-top padding-lr">
				<i class="warn-info-ico ico_warn margin-right"></i>您还未绑定您的云平台，请先绑定账号后才可使用云平台功能！
			</div>
			<?php endif?>
			<div class="margin-top">
				<div class="fl layout">
					<table cellpadding="0" cellspacing="0" class="border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">系统状态</th>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left padding-big-right user">
									<b class="cloud-label">绑定账号：</b>

										<?php if(isset($cloud) && $cloud):?>
										<?php echo $cloud['username']?>&emsp;&emsp;<a class="text-main" href="javascript:reBind();">重新绑定</a>
										<?php else:?>
										<a class="text-main" href="javascript:reBind();">立即绑定</a>
										<?php endif;?>

								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left">
									<b class="cloud-label">云平台通信：</b>
									<span id = 'cloud_status'>
									正在检测...
									</span>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left plugin">
									<!--<b class="cloud-label">插件系统：</b><i class="ico_error"></i> 失败-->
									<b class="cloud-label">插件系统：</b><?php if(isset($cloud) && $cloud):?>
									<i class="ico_right"></i> 正常
									<?php else:?>
									<i class="ico_error"></i> 失败
									<?php endif;?>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left module">
									<!--<b class="cloud-label">模块系统：</b><i class="ico_right"></i> 正常-->
									<b class="cloud-label">模块系统：</b><?php if(isset($cloud) && $cloud):?>
									<i class="ico_right"></i> 正常
									<?php else:?>
									<i class="ico_error"></i> 失败
									<?php endif;?>
								</td>
							</tr>
						</tbody>
					</table>
					<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">系统状态</th>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left padding-big-right domain">
									<b class="cloud-label">站点URL：</b>
									<?php if(isset($cloud) && $cloud):?>
									<span class="text-normal"><?php echo $cloud['domain']?></span>
									&emsp;&emsp;&emsp;
									<?php else:?>
										未绑定云平台
									<?php endif;?>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left identifier">
									<b class="cloud-label">站点标识：</b>
									<?php if(isset($cloud) && $cloud):?>
									<?php echo cut_str($cloud['identifier'], 3, 0).'****'.cut_str($cloud['identifier'], 3, -3)?>  &emsp;（出于安全考虑，部分隐藏）
									<?php else:?>
										未绑定云平台
									<?php endif;?>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left site_isclosed">
									<b class="cloud-label">关闭站点：</b><?php echo $site_isclosed == 1 ? '否' : '是' ?>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left">
									<b class="cloud-label">服务器时间：</b><?php echo date_default_timezone_get().'&emsp;'.date('Y-m-d',time()); ?>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left">
									<b class="cloud-label">当前版本：</b>v<?php echo HD_VERSION ?>
								</td>
							</tr>
							<tr class="border">
								<td class="line-height-40 padding-big-left authorize">
									<b class="cloud-label">版权状态：</b>
									<?php if(isset($cloud) && $cloud):?>
										<?php if($cloud['authorize'] == 0):?>
										<font color="#F74436">保留版权</font>
										<?php else:?>
										<font color="#00BB9C">去除版权</font>
										<?php endif;?>
									<?php else:?>
										<font color="#F74436">保留版权</font>
									<?php endif;?>
								</td>
							</tr>
						</tbody>
					</table>
					<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
						<tbody>
							<tr class="bg-gray-white line-height-40 border-bottom">
								<th class="text-left padding-big-left">应用中心</th>
							</tr>
							<tr>
								<td>
									<div class="text-left today-sales layout border-top border-white fl" style="padding:0 20px;height:65px;line-height:64px;background-color:#fbfbfb;">
                                        <span class="fl">您有 <b class="text-main">0</b> 款应用可升级&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="text-main" href="">详情</a></span>
                                    </div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<script>
			window.cloud = <?php echo isset($cloud)?1:0?>;
			function reBind(){
				top.dialog({
					url: '<?php echo url('bulid')?>',
					title: '云平台绑定',
					width: 560,
					onclose: function () {
						ret = this.returnValue;
						if(ret){
							$('.user').html('<b class="cloud-label">绑定账号：</b>'+ret.username+'&emsp;&emsp;<a class="text-main" href="javascript:reBind();">重新绑定</a>');	
							if(ret.cloud_status == true){
								$('#cloud_status').html('<i class="ico_right"></i> 成功');		
							}else{
								$('#cloud_status').html('<i class="ico_error"></i> 失败');
							}
							$('.plugin').html('<b class="cloud-label">插件系统：</b><i class="ico_right"></i> 正常');	
							$('.module').html('<b class="cloud-label">模块系统：</b><i class="ico_right"></i> 正常');
							$('.domain').html('<b class="cloud-label">站点URL：</b><span class="text-normal">'+ret.domain+'</span>&emsp;&emsp;&emsp;');
							identifier = ret.identifier.substring(0, 3)+'****'+ret.identifier.substring(ret.identifier.length-3);
							$('.identifier').html('<b class="cloud-label">站点标识：</b>'+identifier+'&emsp;（出于安全考虑，部分隐藏）');
							if(ret.authorize == 0){
								$('.authorize').html('<b class="cloud-label">授权状态：</b><font color="#F74436">未授权</font>');	
							}else{
								$('.authorize').html('<b class="cloud-label">授权状态：</b><font color="#00BB9C">已授权</font>');	
							}
							if(ret.site_isclosed == 1){
								$('.site_isclosed').html('<b class="cloud-label">关闭站点：</b>否');
							}else{
								$('.site_isclosed').html('<b class="cloud-label">关闭站点：</b>是');
							}
						}
					}
				})
				.showModal();
			}
			$(function(){
				var url = "<?php echo url('admin/cloud/getcloudstatus',array('formhas'=>FORMHASH))?>";
				var dom = $('#cloud_status');
				$.getJSON(url,'',function(data){
					if(data.status == 2){
						dom.html('未绑定云平台');
						return;
					}else if(data.status == 1){
						dom.html('<i class="ico_right"></i> 成功');
						return;
					}else if(data.status == 0){
						dom.html('<i class="ico_error"></i> 失败');
						return;
					}
				})
			})
		</script>
<?php include template('footer','admin');?>
