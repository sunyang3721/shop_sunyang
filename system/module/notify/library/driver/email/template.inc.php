<p class="notice">您正在编辑 <em id="content-label" class="text-main">loading</em> 通知模板</p>

	<?php foreach($hooks as $tk=>$tv):?>

	<div id='edit_<?php echo $tk?>' style="display: none;" class="layout clearfix">
		<div class="form-layout-rank clearfix">
			<?php echo form::input('text', "{$tk}[title]", "{$template[template][$tk]['title']}", '邮件标题：', '标题支持标签请从编辑器中复制');?>
		</div>
		<?php echo form::editor("{$tk}[content]", "{$template[template][$tk]['content']}", '', '', array('mid' => $admin['id'], 'path' => 'notify'),"{$tk}",TRUE); ?>

	</div>
	
	<?php endforeach;?>