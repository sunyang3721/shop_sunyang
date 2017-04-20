<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">操作日志<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;"></a></li>
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
					<p>- 系统默认关闭了操作日志</p>
					<p>- 开启操作日志可以记录管理人员的关键操作，但会轻微加重系统负担</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a data-message="是否确定删除所选？" href="<?php echo url('del')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table check-table paging-table border clearfix">
				<div class="tr">
					<span class="th check-option" data-resize="false">
						<span><input id="check-all" type="checkbox" /></span>
					</span>
					<span class="th" data-width="30">
						<span class="td-con">操作者</span>
					</span>
					<span class="th" data-width="30">
						<span class="td-con">操作行为</span>
					</span>
					<span class="th" data-width="30">
						<span class="td-con">操作时间</span>
					</span>
					<span class="th" data-width="10">
						<span class="td-con">操作地区</span>
					</span>
				</div>
				<?php foreach($log as $k=>$v):?>
				<div class="tr">
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $v['id']?>" /></div>
					<span class="td">
						<span class="td-con"><?php echo $v['username']?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo $v['remark']?></span>
					</span>
					<span class="td">
						<span class="td-con"><?php echo $v['dateline_text']?></span>
					</span>
					<span class="td">
						<span class="td-con ip" id='showIpInfo_<?php echo $v['id']?>' data-ip="<?php echo $v['action_ip']?>">查看</span>
					</span>
				</div>
				<?php endforeach;?>
				<div class="paging padding-tb body-bg clearfix">
					<ul class="fr">
						<?php echo $pages?>
					</ul>
				</div>
			</div>
		</div>
		<script src='http://whois.pconline.com.cn/ip.js'></script>
		<script>
			$('.table').resizableColumns();
			$(".paging-table").fixedPaging();
			$(function(){
				//查询IP
				$('.ip').on('mousemove',function(){
					if($(this).text() == '查看'){
						$(this).text($(this).attr('查询中'));
						labelIp($(this).attr('id'),$(this).attr('data-ip'));

					}
				})
			})
		</script>
<?php include template('footer','admin');?>
