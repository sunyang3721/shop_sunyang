<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">数据库管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;">数据库备份</a></li>
				<li><a href="<?php echo url('index',array('type'=>'import'))?>">数据库恢复</a></li>
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
					<p>- 养成良好的数据备份可以保障数据损失</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="javascript:void(0)" class="action" data-url="<?php echo url('export')?>" data-backup=""><i class="ico_add"></i>备份</a>
					<div class="spacer-gray"></div>
					<a href="javascript:void(0)" class="action" data-url="<?php echo url('optimize')?>"><i class="ico_delete"></i>优化</a>
					<div class="spacer-gray"></div>
					<a href="javascript:void(0)" class="action" data-url="<?php echo url('repair')?>"><i class="ico_delete"></i>修复</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table check-table border clearfix">
				<div class="tr">
					<span class="th check-option" data-resize="false">
						<span><input id="check-all" type="checkbox" /></span>
					</span>
					<span class="th" data-width="35">
						<span class="td-con">表名</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">数据量</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">数据大小</span>
					</span>
					<span class="th" data-width="20">
						<span class="td-con">创建时间</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach($list as $k=>$table):?>
				<div class="tr">
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $table['name']; ?>" /></div>
					<span class="td">
						<span class="td-con"><?php echo $table['name']; ?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo $table['rows']; ?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo format_bytes($table['data_length']); ?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo $table['create_time']; ?></span>
					</span>
					<span class="td">
						<span class="td-con">
							<a href="<?php echo url('optimize',array('tables'=>$table['name'])); ?>">优化表</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo url('repair',array('tables'=>$table['name'])); ?>"">修复表</a></span>
					</span>
				</div>
				<?php endforeach;?>
			</div>
		</div>
		<script>
			var backurl = '<?php echo url('export'); ?>';
			var formhash = '<?php echo FORMHASH?>';
			$('.table').resizableColumns();
			$(function(){
				//优化表 修复表 备份表
				$('.action').on('click',function(){
					var _tables = new Array;
					if($("[name = id]:checked").length == 0) {
						_alert('请选择要操作的数据！');
						return false;
					}					
					$("[name = id]:checkbox[checked]").each(function(i){ 
						_tables[i] = $(this).val(); 
					}); 
					ajax_ids(_tables,$(this).attr('data-url'));		
				})
				//修复 优化
				function ajax_ids(tab,url){
					$.post(url,
					{"tables[]":tab,"formhash":formhash},
					function(data){
						if(data.status ==1 && data.message=="初始化成功"){
							backup(data.tab);
							return;
						}
						_alert(''+data.message+'');
						$("#check-all,[name = id]").attr("checked", false);
						$(".table").children('.tr').removeClass('selected');
					},'json')
				}
				//备份数据库
				function backup(tab, status) {
					tab['formhash']=formhash;
					$.get(backurl, tab, function(data) {
						if (data.status) {
							_progress('备分数据库,请不要关闭窗口');
							if (!$.isPlainObject(data.tab)) {
								dialog.get('progress').content(data.info);
								return;
							}else{
								dialog.get('progress').content('正在处理 '+data.tab.table+' ...');
							}
							backup(data.tab, tab.id != data.tab.id);
						} else {
							$("#check-all,[name = id]").attr("checked", false);
							$(".table").children('.tr').removeClass('selected');
							dialog.get('progress').close();
							_alert('备份完成');
						}
					}, "json");
				}	
			})
			//提示框
			function _trip(content){
				var d = dialog({
					padding:30,
					content: ''+content+''
				}).show();
				setTimeout(function () {
					d.close().remove();
				}, 2000);
			}
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
		</script>
<?php include template('footer','admin');?>
