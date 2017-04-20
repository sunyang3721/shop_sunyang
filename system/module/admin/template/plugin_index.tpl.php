<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">插件管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a <?php if($_GET['status'] == 1 || !isset($_GET['status'])){?>class="current"<?php }?> href="<?php echo url('plugin_index',array('status' => 1))?>">已启用插件</a></li>
				<li><a <?php if($_GET['status'] == 0 && isset($_GET['status'])){?>class="current"<?php }?> href="<?php echo url('plugin_index',array('status' => 0))?>">未启用插件</a></li>
				<li><a <?php if($_GET['status'] == -1){?>class="current"<?php }?> href="<?php echo url('plugin_index',array('status' => -1))?>">未安装插件</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="javascript:;" id="ajax_upgrade"><i class="ico_in"></i>获取最新版本</a>
					<div class="spacer-gray"></div>
					<a href="<?php echo url('app_develop')?>" >设计新插件</a>
				</div>
			</div>
			<div class="hr-gray margin-bottom"></div>
			<div class="table resize-table paging-table high-table border clearfix">
				<div class="tr">
					<span class="th" data-width="45">
						<span class="td-con">插件名称</span>
					</span>
					<span class="th" data-width="10">
						<span class="td-con">插件状态</span>
					</span>
					<span class="th" data-width="10">
						<span class="td-con">到期时间</span>
					</span>
					<span class="th" data-width="10">
						<span class="td-con">版本</span>
					</span>
					<span class="th" data-width="10">
						<span class="td-con">服务商</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach ($lists AS $plugin):?>
				<div class="tr" data-id="<?php echo $plugin['branch_id']?>">
					<div class="td">
						<div class="td-con td-pic text-left">
							<span class="pic"><img src="<?php echo file_exists('./system/plugin/'.$plugin['identifier'].'/icon.png') ? './system/plugin/'.$plugin['identifier'].'/icon.png' : './statics/images/default_no_upload.png'?>" /></span>
							<span class="title text-ellipsis txt"><?php echo $plugin['server_version'] ? $plugin['name'].'-'.$plugin['server_version'] : $plugin['name'];?></span>
							<span class="icon">
								<em class="text-main">简介：</em><?php echo $plugin['description']?>
							</span>
						</div>
					</div>
					<div class="td">
						<span class="td-con plugin_status">正常</span>
					</div>
					<div class="td">
						<span class="td-con end_time">永久</span>
					</div>
					<div class="td">
						<span class="td-con"><?php echo $plugin['version']?>&emsp;&emsp;&emsp;&emsp;<em class="text-main new_update"></em></span>
					</div>
					<div class="td">
						<span class="td-con"><?php echo $plugin['copyright']?></span>
					</div>
					<div class="td">
						<span class="td-con text-center double-row">
							<?php if($_GET['status'] == 1 || !isset($_GET['status']) && !empty($plugin['url'])){?><a data-type="control" href="<?php echo url('setting',array('id' => $plugin['id']));?>">管理</a><?php }?>
							<?php if($plugin['branch_id'] == 0){?>
							&nbsp;&nbsp;&nbsp;
							<a data-type="control" href="<?php echo url('app_develop',array('id' => $plugin['id']));?>">设计</a>
							<?php }?>
							<br />
							<?php if($_GET['status'] == 1 || !isset($_GET['status'])){?><a data-type="enable" href="<?php echo url('available',array('identifier' => $plugin['identifier'],'type' => 'plugin'))?>">关闭</a><?php }?>
							<?php if($_GET['status'] == 0 && isset($_GET['status'])){?><a data-type="enable" href="<?php echo url('available',array('identifier' => $plugin['identifier'] ,'type' => 'plugin'))?>">开启</a><?php }?>
							<?php if($_GET['status'] == -1){?><a data-type="install" href="<?php echo url('install',array('identifier' => $plugin['identifier'] ,'type' => 'plugin'))?>">安装</a><?php }?>&nbsp;&nbsp;&nbsp;
							<a data-type="upgrade" href="<?php echo url('upgrade',array('identifier' => $plugin['identifier'] ,'type' => 'plugin'))?>">更新</a>
							<?php if($_GET['status'] == 1 || !isset($_GET['status']) || ($_GET['status'] == 0 && isset($_GET['status']))){?>&nbsp;&nbsp;&nbsp;<a data-type="uninstall" data-confirm="卸载将清空该插件的所有配置信息，是否确认卸载？" href="<?php echo url('uninstall',array('identifier' => $plugin['identifier'] ,'type' => 'plugin'))?>">卸载</a><?php }?>
						</span>
					</div>
				</div>
				<?php endforeach?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $pages?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>

			$('.table').resizableColumns();
			$('.paging-table').fixedPaging();
			$(function(){
				ajax_upgrade(0);
				$('#ajax_upgrade').bind('click',function(){
					ajax_upgrade(1);
				})
			})

		
			function ajax_upgrade(flag){
				$.get('<?php echo url("ajax_upgrade")?>',{flag : flag},function(ret){
					$.each(ret.result,function(i,item){
						if(item.new_version){
							$('.tr[data-id='+item.branch_id+']').find('.new_update').html('发现新版本'+item.new_version);
						}
						if(item.out_time == 1){
							$('.tr[data-id="'+item.branch_id+'"]').find('.plugin_status').html('已过期');
						}else{
							$('.tr[data-id="'+item.branch_id+'"]').find('.plugin_status').html('正常');
						}
						if(item._end_time){
							$('.tr[data-id='+item.branch_id+']').find('.end_time').html(item._end_time);
						}
					})
					if(flag == 1){
						message('插件版本获取成功！');
						setTimeout(function () {
						    window.location.reload();
						}, 1500);
					}
				},'json');

			}
		</script>
<?php include template('footer','admin');?>
