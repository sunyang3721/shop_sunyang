<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">图片空间<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 相册中的图片删除后，所对应的图片也会直接从服务器中删除，不能恢复，请谨慎操作</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<ul class="theme-choose-wrap margin-tb">
				<?php foreach ($spaces as $key => $space): ?>
				<li>
					<div class="pic padding-small border bg-white">
						<img src="<?php echo __ROOT__ ?>statics/images/attachcment/<?php echo $key ?>.png" />
					</div>
					<div class="text">
						<p>图库名称：<?php echo $space;?></p>
						<p>图片数量：<span class="text-main"><?php echo $attachments[$key]['count'] ?></span> 张</p>
						<p>图片容量：<span class="text-main"><?php echo $attachments[$key]['filesize'] ?></span> <?php echo $attachments[$key]['fileunit'] ?></p>
						<p>更新时间：<span class="text-main"><?php echo ($attachments[$key]['datetime']) ? date($attachments[$key]['datetime'], 'Y-m-d') : '-' ?></span></p>
						<a class="button bg-main" href="<?php echo url('attachment/admin/manage', array('folder' => $key)) ?>">管理图库</a>
					</div>
				</li>
				<?php endforeach ?>
			</ul>
		</div>
		<script>

		</script>
<?php include template('footer','admin');?>
