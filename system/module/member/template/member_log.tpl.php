<?php include template('header','admin');?>
<div class="fixed-nav layout">
	<ul>
		<li class="first">余额管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
		<li class="spacer-gray"></li>
	</ul>
	<div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
	<form method="GET" action="">
			<input type="hidden" value="member" name="m" />
			<input type="hidden" value="member_log" name="c" />
			<input type="hidden" value="index" name="a" />
			<div class="member-comment-search clearfix">
				<div class="form-box clearfix border-bottom-none" style="width: 590px;">
					<div style="z-index: 4;" id="form-group-id1" class="form-group form-layout-rank group1">
						<span class="label">操作时间</span>
						<div class="box margin-none">
							<?php echo form::calendar('start',!empty($_GET['start']) ? $_GET['start']:'',array('format' => 'YYYY-MM-DD'))?>
						</div>
					</div>
					<div style="z-index: 3;" id="form-group-id2" class="form-group form-layout-rank group2">
						<span class="label">~</span>
						<div class="box margin-none">
							<?php echo form::calendar('end',!empty($_GET['end'])? $_GET['end']:'',array('format' => 'YYYY-MM-DD'))?>
						</div>
					</div>
					<div style="z-index: 1;" id="form-group-id4" class="form-group form-layout-rank group4">
						<span class="label">搜索</span>
						<div class="box margin-none">
							<input class="input" name="keywords" placeholder="请输入会员名搜索余额信息" tabindex="0" type="text" value="<?php echo !empty($_GET['keywords'])?$_GET['keywords'] :''?>">
						</div>
					</div>
				</div>
				<input class="button bg-sub fl" value="查询" type="submit">
			</div>
			</form>
	<div class="table-wrap member-info-table">
		<div class="table resize-table check-table border paging-table clearfix">
			<div class="member  tr">
				<?php foreach ($lists['th'] AS $th) {?>
				<span class="th" data-width="<?php echo $th['length']?>">
					<span class="td-con"><?php echo $th['title']?></span>
				</span>
				<?php }?>
				<span class="th" data-width="10">
					<span class="td-con">状态</span>
				</span>
			</div>
			<?php foreach ($lists['lists'] AS $list) {?>
					<div class="member tr">
						<?php foreach ($list as $key => $value) {?>
						<?php if($lists['th'][$key]){?>
						<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
						<span class="td">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
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
							<span class="td-con">成功</span>
						</span>
					</div>
					<?php }?>
			<div class="paging padding-tb body-bg clearfix">
				<?php echo $pages; ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
	<script>
		$(".table").resizableColumns();
		$(".paging-table").fixedPaging();
		/*$('.batch-delete').batchDelete({
			url: "<?php echo url('del')?>",
			formhash: "<?php echo FORMHASH?>"
		});*/
	</script>
<?php include template('footer','admin');?>
