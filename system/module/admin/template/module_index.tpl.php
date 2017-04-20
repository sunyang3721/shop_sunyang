<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">模块管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a <?php if($_GET['status'] == 1 || !isset($_GET['status'])){?>class="current"<?php }?> href="<?php echo url('admin/app/module_index', array('status' => 1))?>">已启用模块</a></li>
				<li><a <?php if($_GET['status'] == 0 && isset($_GET['status'])){?>class="current"<?php }?>href="<?php echo url('admin/app/module_index', array('status' => 0))?>">未启用模块</a></li>
				<li><a <?php if($_GET['status'] == -1){?>class="current"<?php }?>href="<?php echo url('admin/app/module_index', array('status' => -1))?>">未安装模块</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			
			<div class="hr-gray margin-bottom"></div>
			<div class="table resize-table paging-table high-table border clearfix">
				<div class="tr">
					<span class="th" data-width="45">
						<span class="td-con">模块名称</span>
					</span>
					<span class="th" data-width="10">
						<span class="td-con">模块状态</span>
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
				<?php foreach ($lists as $identifier => $module): ?>
				<div class="tr" data-id="<?php echo $module['branch_id']?>">
					<div class="td">
						<div class="td-con td-pic text-left">
							<span class="pic"><img src="<?php echo file_exists('./statics/images/model/'.$identifier.'.png') ? './statics/images/model/'.$identifier.'.png' : (file_exists('./system/module/'.$identifier.'/icon.png') ? './system/module/'.$identifier.'/icon.png' : './statics/images/default_no_upload.png')?>" /></span>
							<span class="title text-ellipsis txt"><?php echo $module['name']?></span>
							<span class="icon">
								<em class="text-main">简介：</em><?php echo $module['description']?>
							</span>
						</div>
					</div>
					<div class="td">
						<span class="td-con module_status">正常</span>
					</div>
					<div class="td">
						<span class="td-con end_time">永久</span>
					</div>
					<div class="td">
						<span class="td-con"><?php echo $module['version']?>&emsp;&emsp;&emsp;&emsp;<em class="text-main new_update"></em></span>
					</div>
					<div class="td">
						<span class="td-con"><?php echo $module['copyright']?></span>
					</div>
					<div class="td">
						<span class="td-con double-row">
							<a href="<?php echo url('admin/app/available',array('identifier' => $identifier,'type' => 'module'))?>"><?php if($_GET['status'] == 1 || !isset($_GET['status'])){?>禁用<?php }?><?php if($_GET['status'] == 0 && isset($_GET['status'])){?>开启<?php }?></a> <?php if($_GET['status'] != -1){?> | <?php }?>
							<?php if($_GET['status'] == -1){?>
							<a href="<?php echo url('admin/app/install',array('identifier' => $identifier,'type' => 'module'))?>">安装</a>
							<?php }else{?>
							<a href="<?php echo url('admin/app/upgrade',array('identifier' => $identifier,'type' => 'module'))?>">更新</a>
							<?php }?>
							<?php if($module['is_system'] != 1){?>
							| <a data-confirm="卸载将清空该模块的所有配置信息，是否确认卸载？" href="<?php echo url('admin/app/uninstall',array('identifier' => $identifier,'type' => 'module'))?>">卸载</a>
							<?php }?>
						</span>
					</div>
				</div>
				<?php endforeach ?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $pages; ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>
			$('.table').resizableColumns();
			$('.paging-table').fixedPaging();
			$(function(){
				$.get('<?php echo url("admin/app/ajax_upgrade")?>',{},function(ret){
					$.each(ret.result,function(i,item){
						if(item.new_version){
							$('.tr[data-id='+item.branch_id+']').find('.new_update').html('发现新版本'+item.new_version);
						}
						if(item.out_time == 1){
							$('.tr[data-id="'+item.branch_id+'"]').find('.module_status').html('已过期');
						}else{
							$('.tr[data-id="'+item.branch_id+'"]').find('.module_status').html('正常');
						}
						if(item._end_time){
							$('.tr[data-id='+item.branch_id+']').find('.end_time').html(item._end_time);
						}
					})
				},'json');
			})
		</script>
<?php include template('footer','admin');?>
