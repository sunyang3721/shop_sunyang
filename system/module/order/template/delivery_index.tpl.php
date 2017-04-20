<?php include template('header','admin');?>

		<div class="fixed-nav layout">
			<ul>
				<li class="first">物流配送管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 此处预设了部分常用快递方式，可继续自行添加其他配送方式</p>
					<p>- 如发货时不想让用户自行选择快递公司，可新建一个普通快递，发货时后台指定配送方式即可</p>
					<p>- 请按照《标准快递公司及参数说明》设置快递公司标识，用于系统快递订单追踪</p>
					<p>- 不用的快递公司不建议删除，禁用即可，禁用的快递公司不影响后台发货时指定</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('order/admin_delivery/update'); ?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('deletes',array('name'=>'ids'))?>" data-ajax='ids'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table-wrap">
				<div class="table resize-table high-table check-table border clearfix">
					<div class="tr">
						<span class="th check-option" data-resize="false">
							<span><input id="check-all" type="checkbox" /></span>
						</span>
						<?php foreach ($lists['th'] AS $th) {?>
						<span class="th" data-width="<?php echo $th['length']?>">
							<span class="td-con"><?php echo $th['title']?></span>
						</span>
						<?php }?>
						<span class="th w1_5" data-width="15">
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
							<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'data') {?>
						<span class="td">
								<span class="td-con td-pic text-left">
									<span class="pic">
										<img src="<?php echo $list['logo'];?>" onerror="javascript:this.src='./statics/images/default_no_upload.png';"/>
									</span>
									<span class="title"><?php echo $list['name']; ?></span>
								</span>
							</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
					<span class="td">
						<a class="ico_up_rack <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
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
							<a href="<?php echo url('order/admin_delivery/update',array('id'=>$list['id'])) ?>">编辑</a>&nbsp;&nbsp;&nbsp;
							<a href="<?php echo url('order/admin_delivery/delivery_tpl',array('id'=>$list['id'])) ?>">模板</a>&nbsp;&nbsp;
							<a data-confirm="是否确定删除？" href="<?php echo url('deletes',array('ids'=>$list['id'])) ?>">删除</a>
						</span>
					</span>
				</div>
				<?php }?>
				</div>
				<!-- 分页 -->
				<div class="paging padding-tb body-bg clearfix">
					<ul class="fr"><?php echo $lists['pages'];?></ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
<?php include template('footer','admin');?>
<script>
	$(".form-group .box").addClass("margin-none");
	$(window).load(function(){
		$(".table").resizableColumns();
		$('.table .tr:last-child').addClass("border-none");
		var ajax_url = '<?php echo url("order/admin_delivery/update_field_by_id");?>';
		// 更改状态 (开启|关闭)
		$(".table .ico_up_rack").bind('click',function(){
			var obj = $(this);
			// 要设置的值
			var set_val = ($(obj).hasClass('cancel') == true) ? 1 : 0 ;
			$.post(ajax_url, {
					id : $(obj).attr('data-id'),
					field : 'enabled',
					val : set_val
				}, function(ret) {
					if (ret.status == 1) {
						if(!$(obj).hasClass("cancel")){
							$(obj).addClass("cancel");
							$(obj).attr("title","点击开启物流");
						}else{
							$(obj).removeClass("cancel");
							$(obj).attr("title","点击关闭物流");
						}
					} else {
						alert(ret.message);
					}
			},'json');
		});

		// 双击更改排序
		$(".double-click-edit").blur(function() {
			var obj = $(this);
			if (isNaN(parseInt($(obj).val()))) {
				alert('请设置要排序的值');
				location.reload();
				return false;
			}
			$.post(ajax_url, {
					id : $(obj).attr('data-id'),
					field : 'sort',
					val : $(obj).val()
				}, function(ret) {
					if (ret.status == 1){
						location.reload();
					} else {
						alert(ret.message);
					}
			},'json');
		});
	})
</script>