<p class="notice">您正在编辑 <em id="content-label" class="text-main">loading</em> 通知模板 <?php if($sms_num['code'] != 200 && $sms_num['result'] === false){?><span class="fr">您尚未绑定云平台，无法使用短信通知。<a href="<?php echo url('admin/cloud/index')?>" class="text-main">立即绑定</a></span>
	<?php }else{?>
	<span class="fr">您的短信剩余条数：<em class="text-main"><?php echo $sms_num['result'];?></em></span>
	<?php }?></p>

	<?php foreach($hooks as $tk=>$tv):?>
	<div id='edit_<?php echo $tk?>' style="display: none;">
		<div class="form-group">
			<span class="label"></span>
			<div class="">
				<?php foreach($template[$tk] as $sk => $sv):?>
				<label class="select-wrap">
					<?php
					$checked = $sv['id'] == $template['template'][$tk]['template_id'] ? 'checked=checked' : '' ;
					?>
					<input type="radio"  value="<?php echo $sv['id']?>" name="<?php echo $tk?>[template_id]" class="select-btn" <?php echo $checked?>><?php echo $sv['content']?>
				</label><br>
				<?php endforeach;?>
			</div>
		</div>
	</div>
	<?php endforeach;?>