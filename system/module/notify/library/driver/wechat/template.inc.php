<p class="notice">您正在编辑 <em id="content-label" class="text-main">loading</em> 通知模板</p>
	<style>
		.form-group{ float:none; }
		.wechat-temp{ padding-left:20px; }
		.textarea{ padding:20px; width:100%; height:320px; border:none; border-top:1px solid #ccc;}
	</style>
	<?php foreach($hooks as $tk=>$tv):?>
	<div id='edit_<?php echo $tk?>' style="display: none;" class="form-layout-rank">
		<?php echo form::input('text', "{$tk}template_id", "{$template['template'][$tk]['template_id']}", '微信通知模板ID：', '请从微信公众平台获取');?>
		<textarea class="textarea hd-input" name="<?php echo $tk;?>template" readonly="readonly"><?php echo "{$template['template'][$tk]['template']}";?></textarea>	
	</div>
	<?php endforeach;?>
	
	
	<script>
	    var template=<?php echo json_encode($templat);?>;
		template=template.template_list;
		$(function(){
			$("input[name$=template_id]").change(function(){
				var val=$(this).val();
				var name=$(this).attr("name");
				var parent = $(this).parents(".form-group");
				$.each(template,function(index,data){
					if(val==data.template_id){
						parent.next(".textarea").html(data.content);
					}
				})
				
			})
		})
	
	</script>