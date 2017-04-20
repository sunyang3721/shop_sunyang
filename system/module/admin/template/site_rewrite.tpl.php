<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">优化设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li class="fixed-nav-tab"><a <?php if(METHOD_NAME == 'rewrite') :?>class="current"<?php endif;?> href="<?php echo url('site/rewrite'); ?>">URL伪静态</a></li>
				<li class="fixed-nav-tab"><a <?php if(METHOD_NAME == 'seo') :?>class="current"<?php endif;?> href="<?php echo url('site/seo'); ?>">SEO设置</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content content-tabs padding-big have-fixed-nav">
			<div class="table clearfix bg-none border-none">
				<div class="tr border-none bg-none">
					<div class="th bg-none w15">
						<span class="td-con text-left">页面</span>
					</div>
					<div class="th bg-none w25">
						<span class="td-con text-left">标记</span>
					</div>
					<div class="th bg-none w50">
						<span class="td-con text-left">路径格式</span>
					</div>
					<div class="th bg-none w10">
						<span class="td-con">启用状态</span>
					</div>
				</div>
				<?php foreach($rewrites as $k => $val){?>
					<div class="tr bg-none" data-id="<?php echo $k; ?>">
						<div class="td w15">
							<span class="td-con text-left"><?php echo $val['page']?></span>
						</div>
						<div class="td w25">
							<span class="td-con text-left"><?php echo $val['tab']?></span>
						</div>
						<div class="td w50">
							<span class="td-con text-left">
								<div class="double-click">
									<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
									<input class="input double-click-edit text-ellipsis" type="text" value="<?php echo $val['showurl']?>" data-element="showurl"/>
								</div>
							</span>
						</div>
						<div class="td w10">
							<?php if($val['show'] == 1) : ?>
								<a class="ico_up_rack" title="点击禁用" href="javascript:;"></a>
							<?php else : ?>
								<a class="ico_up_rack cancel" title="点击启用" href="javascript:;"></a>
							<?php endif; ?>
						</div>
					</div>
				<?php }?>
			</div>
		</div>
<?php include template('footer','admin');?>

<script>
	/* 更改路径格式 */
	$("[data-element='showurl']").live('change',function(){
		var data = {field : 'showurl',id:$(this).parents(".tr").data("id"),val:$(this).val()};
		$.post("index.php?m=admin&c=site&a=ajax_update_rewrite",data,function(ret){
			message(ret.message);
		},'json');
	});
	/* 启用状态 */
	$(".table .ico_up_rack").live('click',function(){
		var show = 0;obj = $(this);
		if($(this).hasClass("cancel")){
			show = 1;
		}
		var data = {field : 'show',id:$(this).parents(".tr").data("id"),val:show};
		$.post("?m=admin&c=site&a=ajax_update_rewrite",data,function(ret){
			message(ret.message);
			if (ret.status != 1) return false;
			if (show == 1) {
				$(obj).removeClass('cancel').attr('title' ,'点击禁用');
			} else {
				$(obj).addClass('cancel').attr('title' ,'点击启用');
			}
		},'json');
	});
</script>
