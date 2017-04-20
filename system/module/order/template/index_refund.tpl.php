<?php include template('header','admin');?>

		<div class="fixed-nav layout">
			<ul>
				<li class="first">退款单管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a <?php if(!isset($_GET['type'])) {echo ' class="current"';}?> href="<?php echo url('order/admin_server/index_refund'); ?>">全部</a></li>
				<li><a <?php if(isset($_GET['type']) && $_GET['type']==0) {echo ' class="current"';}?> class="" href="<?php echo url('order/admin_server/index_refund',array('type' => 0)); ?>">待处理</a></li>
				<li><a <?php if($_GET['type']==1) {echo ' class="current"';}?> class="" href="<?php echo url('order/admin_server/index_refund',array('type' => 1)); ?>">已处理</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form method="get">
				<input type="hidden" name="m" value="order">
				<input type="hidden" name="c" value="admin_server">
				<input type="hidden" name="a" value="index_return">
				<div class="clearfix">
					<div class="form-group form-layout-rank border-none" style="width: 300px;">
						<span class="label" style="width: auto;">搜索</span>
						<div class="box ">
							<div class="field margin-none">
								<input class="input" type="text" name="keywords" placeholder="<?php if($_GET['keywords']) : ?><?php echo $_GET['keywords']; ?><?php else: ?>输入订单号/会员账号/手机号<?php endif;?>" tabindex="0" />
							</div>
						</div>
					</div>
					<input class="button bg-sub margin-top fl" type="submit" style="height: 26px; line-height: 14px;" value="查询">
				</div>
			</form>
			<div class="table-wrap">
				<div class="table resize-table paging-table high-table border clearfix">
					<div class="tr">
					<?php foreach ($lists['th'] AS $th) {?>
					<span class="th" data-width="<?php echo $th['length']?>">
						<span class="td-con"><?php echo $th['title']?></span>
					</span>
					<?php }?>
						<span class="th w1" data-width="10">
							<span class="td-con">操作</span>
						</span>
					</div>
					<?php foreach ($lists['lists'] AS $list) {?>
				<div class="tr">
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<span class="td">
						<div class="double-click">
							<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
							<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'goods') {?>
						<span class="td">
							<div class="td-con td-pic text-left">
								<span class="pic"><img src="<?php echo $list['sku_thumb']; ?>" /></span>
								<span class="title text-ellipsis txt"><a href="" target="_blank"><?php echo $value; ?></a></span>
								<span class="icon">
									<?php foreach ($list['specs'] as $spec) : ?>
										<em class="text-main"><?php echo $spec['name'] ?>：</em><?php echo $spec['value'] ?>&nbsp;
									<?php endforeach; ?>
								</span>
							</div>
						</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'left_text') {?>
					<span class="td">
						<span class="td-con text-left"><?php echo $value;?></span>
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
							<a href="<?php echo url('order/admin_server/detail_refund',array('refund_id' => $list['id'])) ?>"><?php if($list['status'] == 0): ?>处理<?php else: ?>查看<?php endif; ?></a>&emsp;
						</span>
					</span>
				</div>
				<?php }?>

					<div class="paging padding-tb body-bg clearfix">
						<ul class="fr"><?php echo $pages; ?></ul>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
<?php include template('footer','admin');?>

<script>
	$(".form-group .box").addClass("margin-none");
	$(window).load(function(){
		$(".table").resizableColumns();
		$(".paging-table").fixedPaging();
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
	})
</script>
