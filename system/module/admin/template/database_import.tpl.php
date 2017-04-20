<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">数据库管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('index',array('type'=>'export'))?>">数据库备份</a></li>
				<li><a class="current" href="javascript:;">数据库恢复</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="tips margin-tb">
				<div class="tips-info border">
					<h6>温馨提示</h6>
					<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
				</div>
				<div class="tips-txt padding-small-top layout">
					<p>- 恢复数据会直接恢复到备份时间点的所有数据表数据，请谨慎操作</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table resize-table border margin-top clearfix">
				<div class="tr">
					<span class="th" data-width="40">
						<span class="td-con">文件名称</span>
					</span>
					<span class="th" data-width="25">
						<span class="td-con">备份时间</span>
					</span>
					<span class="th" data-width="20">
						<span class="td-con">文件大小</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach($list as $k=>$v):?>
				<div class="tr">
					<span class="td">
						<span class="td-con"><?php echo date('Ymd-His',$v['time']); ?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo $k ?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo format_bytes($v['size']); ?></span>
					</span>
					<span class="td">
						<span class="td-con">
							<a href="javascript:db_import('<?php echo $v['time']?>',null,null)"  data-confirm=''>恢复</a>&nbsp;&nbsp;&nbsp;
							<a data-confirm='' href="<?php echo url('del',array('time'=>$v['time'],'formhash'=>FORMHASH)); ?>">删除</a></span>
					</span>
				</div>
				<?php endforeach;?>
			</div>
		</div>
		<script>
			$('.table').resizableColumns();
			var importurl = '<?php echo url('import')?>';
			var formhash = '<?php echo FORMHASH?>';
			
			function db_import(time,part,start){
				_progress('正在读取文件');
				$.getJSON(importurl,{"time":time,"part":part,"start":start,"formhash":formhash},function(data){
					if (data.status) {
						dialog.get('progress').content(data.message);
						if (data.result.part) {
							db_import(time,data.result.part,data.result.start);
						} else {
							window.onbeforeunload = function() {
								return null;
							}
						}
					} else {
						dialog.get('progress').close();
						_alert(data.message);
					}
				})
				function _progress(content){
					var d = dialog({
						id : 'progress',
						padding:30,
						content: ''+content+''
					}).show();
				}
				function _alert(content){
					var d = dialog({
						id : 'alert',
						width:200,
						padding:30,
						title: '提示',
						content: ''+content+'',
						okValue: '确定',
						ok:function(){},
					}).show();
				}
			}
			
		</script>
<?php include template('footer','admin');?>
