<?php include template('header','admin');?>

	<div class="fixed-nav layout">
		<ul>
			<li class="first">发货单模版<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
			<li class="spacer-gray"></li>
		</ul>
		<div class="hr-gray"></div>
	</div>
	<div class="content padding-big have-fixed-nav">
		<div class="margin-top">
			<form method="post" action="<?php echo url('order/admin_order/tpl_parcel'); ?>">
				<?php echo form::editor('content',$info['content'], '', '', array('mid' => $admin['id'], 'path' => 'common')); ?>
				<div class="margin-top">
			        <input type="submit" class='button bg-main' name="dosubmit" value='保存'/>
				</div>
			</form>
		</div>
	</div>
<?php include template('footer','admin');?>
