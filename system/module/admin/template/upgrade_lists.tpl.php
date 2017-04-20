<?php include template('header','admin');?>
		
		<div class="content padding-big ">
			<div class="tips margin-tb">
				<div class="tips-info border">
					<h6>温馨提示</h6>
					<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
				</div>
				<div class="tips-txt padding-small-top layout">
					<p>- 添加商品时可选择商品分类，用户可根据分类查询商品列表</p>
					<p>- 点击分类名前“+”符号，显示当前分类的下级分类</p>
					<p>- 对分类作任何更改后，都需要到 设置 -> 清理缓存 清理商品分类，新的设置才会生效</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<table cellpadding="0" cellspacing="0" class="margin-top border bg-white layout">
				<tbody>
					<tr class="bg-gray-white line-height-40 border-bottom">
						<th class="text-left padding-big-left">检测到新版本</th>
					</tr>
					<?php foreach($r as $k=>$v):?>
					<tr class="border-bottom">
						<td class="text-left today-sales padding-big padding-small-top padding-small-bottom line-height-40">
							<span class="fl"><?php echo $v['version']?></span>
							<div class="log-text padding-big bg-white" style="display: none;" id = "<?php echo 'log_'.$v['version']?>">
								<?php foreach($v['log'] as $k1=>$v1):?>
									<p>- <?php echo $v1?><p>
								<?php endforeach;?>	
							</div>
							<span class="fr"><a href="javascript:popups('<?php echo 'log_'.$v['version']?>');">更新日志</a>&nbsp;&nbsp;&nbsp;<a class="text-main" href="<?php echo url('upgrade',array('version'=>$v['version'])) ?>">更新至此版本</a></span>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<script>
			function popups(v){
				top.dialog({
					content: '<div class="log-text padding-big bg-white">'+$("[id='"+v+"']").html()+'</div>',
					title: '更新日志',
					width: 680,
					data: v,//这里可传递值到弹窗页面
					cancelValue: '关闭',
					cancel: function(){

					}
				})
				.showModal();
			}
		</script>
<?php include template('footer','admin');?>
