<?php include template('header','admin');?>
<div class="fixed-nav layout">
	<ul>
		<li class="first">订单促销<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
		<li class="spacer-gray"></li>
		<li>
			<a class="current" href="javascript:;"></a>
		</li>
	</ul>
	<div class="hr-gray">
	</div>
</div>
<div class="content padding-big have-fixed-nav">
	<div class="tips margin-tb">
		<div class="tips-info border">
			<h6>温馨提示</h6>
			<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
		</div>
		<div class="tips-txt padding-small-top layout">
			<p>- 针对自营商品的一个独立订单的促销活动</p>
			<p>- 限时促销的单品默认不参与订单促销活动</p>
		</div>
	</div>
	<div class="hr-gray">
	</div>
	<div class="table-work border margin-tb">
		<div class="border border-white tw-wrap">
			<a href="<?php echo url('add') ?>"><i class="ico_add"></i>添加</a>
			<div class="spacer-gray">
			</div>
			<a href="<?php echo url('delete') ?>" data-ajax='id' data-message="是否删除所选订单促销活动？"><i class="ico_delete"></i>删除</a>
			<div class="spacer-gray">
			</div>
		</div>
	</div>
	<div class="table resize-table paging-table check-table border clearfix">
		<div class="tr">
			<span class="th check-option" data-resize="false">
				<span>
					<input id="check-all" type="checkbox" />
				</span>
			</span>
			<?php foreach ($lists['th'] AS $th) {?>
			<span class="th" data-width="<?php echo $th['length']?>">
				<span class="td-con"><?php echo $th['title']?></span>
			</span>
			<?php }?>
			<span class="th" data-width="10">
				<span class="td-con">操作</span>
			</span>
		</div>
		<?php foreach ($lists['lists'] AS $list) {?>
				<div class="tr">
					<span class="td check-option"><input type="checkbox" name="id" value="<?php echo $list['id']?>" /></span>
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<span class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input class="input double-click-edit text-ellipsis" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ident') {?>
						<span class="td ident">
							<span class="ident-show">
								<em class="ico_pic_show"></em>
								<div class="ident-pic-wrap">
									<img src="<?php echo $list['logo'] ? $list['logo'] : '../images/default_no_upload.png'?>" />
								</div>
							</span>
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" type="text" value="<?php echo $value?>" />
							</div>
						</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
					<span class="td">
						<a class="ico_up_rack <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'date') {?>
					<span class="td">
						<span class="td-con"><?php echo date('Y-m-d H:i' ,$value) ?></span>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'hidden') {?>
						<input type="hidden" name="id" value="<?php echo $value?>" />
					<?php }else{?>
					<span class="td">
						<span class="td-con"><?php echo $value;?></span>
					</span>
					<?php }?>
					<?php }?>
					<?php }?>
					<span class="td">
						<span class="td-con">
						<a href="<?php echo url('edit',array('id'=>$list['id']))?>">编辑</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('delete', array('id[]' => $list['id'])); ?>">删除</a><?php echo $lists['option']?></span>
					</span>
				</div>
				<?php }?>
		<div class="paging padding-tb body-bg clearfix">
			<?php echo $lists['pages'] ?>
			<div class="clear">
			</div>
		</div>
	</div>
</div>
<script>
$(".table").resizableColumns();
$(".table").fixedPaging();
$(".double-click-edit").live('change',function(){
	var data = {id:$(this).data("id"),name:$(this).val()};
	$.post("<?php echo url('edit') ?>",data,function(ret){
		message(ret.message);
	},'json');
})


function message(t){
	var d = dialog({
	    content: '<div class="padding-large-left padding-large-right padding-big-top padding-big-bottom bg-white text-small">'+t+'</div>'
	});
	d.show();
	setTimeout(function () {
	    d.close().remove();
	}, 1000);
}
</script>
<?php include template('footer','admin');?>
