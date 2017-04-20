<?php include template('header','admin');?>
	<body class="bg-white">
		<div class="goods-look-attr">
			<div class="table border-none clearfix" style="height: 405px; overflow: hidden; overflow-y: auto;">
				<div class="tr border-none">
					<div class="th fixed">图片</div>
					<div class="th w30">规格</div>
					<div class="th w25">货号</div>
					<div class="th w20">价格</div>
					<div class="th w15">商品状态</div>
					<div class="th w10">库存</div>
				</div>
				<?php foreach ($sku as $key => $value) {?>
				<div class="tr">
					<div class="td fixed">
						<span class="pic"><img src="<?php echo $value['thumb'] ? $value['thumb'] : 'statics/images/default_no_upload.png'?>" /></span>
					</div>
					<div class="td w30  text-ellipsis text-left" title="<?php echo $value['spec_str']?>">
					<?php echo $value['spec_str']?>
					</div>
					<div class="td w25 text-left"><?php echo $value['sn']?></div>
					<div class="td w20"><?php echo $value['shop_price']?></div>
					<div class="icon td w15">
						<a class="ico ico_promotion <?php if($value['status_ext'] != 1){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="1" href="javascript:;" <?php if($value['status_ext'] != 1){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_special <?php if($value['status_ext'] != 2){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="2" href="javascript:;" <?php if($value['status_ext'] != 2){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_new <?php if($value['status_ext'] != 3){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="3" href="javascript:;" <?php if($value['status_ext'] != 3){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_reco <?php if($value['status_ext'] != 4){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="4" href="javascript:;" <?php if($value['status_ext'] != 4){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
					</div>
					<div class="td w10"><?php echo $value['number']?></div>
				</div>
				<div class="tr">
					<div class="td fixed">
						<span class="pic"><img src="<?php echo $value['thumb'] ? $value['thumb'] : 'statics/images/default_no_upload.png'?>" /></span>
					</div>
					<div class="td w30  text-ellipsis text-left" title="<?php echo $value['spec_str']?>">
					<?php echo $value['spec_str']?>
					</div>
					<div class="td w25 text-left"><?php echo $value['sn']?></div>
					<div class="td w20"><?php echo $value['shop_price']?></div>
					<div class="icon td w15">
						<a class="ico ico_promotion <?php if($value['status_ext'] != 1){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="1" href="javascript:;" <?php if($value['status_ext'] != 1){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_special <?php if($value['status_ext'] != 2){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="2" href="javascript:;" <?php if($value['status_ext'] != 2){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_new <?php if($value['status_ext'] != 3){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="3" href="javascript:;" <?php if($value['status_ext'] != 3){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_reco <?php if($value['status_ext'] != 4){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="4" href="javascript:;" <?php if($value['status_ext'] != 4){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
					</div>
					<div class="td w10"><?php echo $value['number']?></div>
				</div>
				<div class="tr">
					<div class="td fixed">
						<span class="pic"><img src="<?php echo $value['thumb'] ? $value['thumb'] : 'statics/images/default_no_upload.png'?>" /></span>
					</div>
					<div class="td w30  text-ellipsis text-left" title="<?php echo $value['spec_str']?>">
					<?php echo $value['spec_str']?>
					</div>
					<div class="td w25 text-left"><?php echo $value['sn']?></div>
					<div class="td w20"><?php echo $value['shop_price']?></div>
					<div class="icon td w15">
						<a class="ico ico_promotion <?php if($value['status_ext'] != 1){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="1" href="javascript:;" <?php if($value['status_ext'] != 1){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_special <?php if($value['status_ext'] != 2){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="2" href="javascript:;" <?php if($value['status_ext'] != 2){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_new <?php if($value['status_ext'] != 3){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="3" href="javascript:;" <?php if($value['status_ext'] != 3){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_reco <?php if($value['status_ext'] != 4){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="4" href="javascript:;" <?php if($value['status_ext'] != 4){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
					</div>
					<div class="td w10"><?php echo $value['number']?></div>
				</div>
				<div class="tr">
					<div class="td fixed">
						<span class="pic"><img src="<?php echo $value['thumb'] ? $value['thumb'] : 'statics/images/default_no_upload.png'?>" /></span>
					</div>
					<div class="td w30  text-ellipsis text-left" title="<?php echo $value['spec_str']?>">
					<?php echo $value['spec_str']?>
					</div>
					<div class="td w25 text-left"><?php echo $value['sn']?></div>
					<div class="td w20"><?php echo $value['shop_price']?></div>
					<div class="icon td w15">
						<a class="ico ico_promotion <?php if($value['status_ext'] != 1){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="1" href="javascript:;" <?php if($value['status_ext'] != 1){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_special <?php if($value['status_ext'] != 2){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="2" href="javascript:;" <?php if($value['status_ext'] != 2){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_new <?php if($value['status_ext'] != 3){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="3" href="javascript:;" <?php if($value['status_ext'] != 3){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_reco <?php if($value['status_ext'] != 4){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="4" href="javascript:;" <?php if($value['status_ext'] != 4){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
					</div>
					<div class="td w10"><?php echo $value['number']?></div>
				</div>
				<div class="tr">
					<div class="td fixed">
						<span class="pic"><img src="<?php echo $value['thumb'] ? $value['thumb'] : 'statics/images/default_no_upload.png'?>" /></span>
					</div>
					<div class="td w30  text-ellipsis text-left" title="<?php echo $value['spec_str']?>">
					<?php echo $value['spec_str']?>
					</div>
					<div class="td w25 text-left"><?php echo $value['sn']?></div>
					<div class="td w20"><?php echo $value['shop_price']?></div>
					<div class="icon td w15">
						<a class="ico ico_promotion <?php if($value['status_ext'] != 1){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="1" href="javascript:;" <?php if($value['status_ext'] != 1){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_special <?php if($value['status_ext'] != 2){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="2" href="javascript:;" <?php if($value['status_ext'] != 2){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_new <?php if($value['status_ext'] != 3){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="3" href="javascript:;" <?php if($value['status_ext'] != 3){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_reco <?php if($value['status_ext'] != 4){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="4" href="javascript:;" <?php if($value['status_ext'] != 4){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
					</div>
					<div class="td w10"><?php echo $value['number']?></div>
				</div>
				<div class="tr">
					<div class="td fixed">
						<span class="pic"><img src="<?php echo $value['thumb'] ? $value['thumb'] : 'statics/images/default_no_upload.png'?>" /></span>
					</div>
					<div class="td w30  text-ellipsis text-left" title="<?php echo $value['spec_str']?>">
					<?php echo $value['spec_str']?>
					</div>
					<div class="td w25 text-left"><?php echo $value['sn']?></div>
					<div class="td w20"><?php echo $value['shop_price']?></div>
					<div class="icon td w15">
						<a class="ico ico_promotion <?php if($value['status_ext'] != 1){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="1" href="javascript:;" <?php if($value['status_ext'] != 1){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_special <?php if($value['status_ext'] != 2){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="2" href="javascript:;" <?php if($value['status_ext'] != 2){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_new <?php if($value['status_ext'] != 3){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="3" href="javascript:;" <?php if($value['status_ext'] != 3){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
						<a class="ico ico_reco <?php if($value['status_ext'] != 4){?>cancel<?php }?>" data-id="<?php echo $value['sku_id']?>" data-value="4" href="javascript:;" <?php if($value['status_ext'] != 4){?>title="点击设为特卖"<?php }else{?>title="点击取消特卖"<?php }?>></a>
					</div>
					<div class="td w10"><?php echo $value['number']?></div>
				</div>
				<?php }?>
			</div>
		</div>
		<div class="padding text-right ui-dialog-footer">
			<input type="button" class="button margin-left bg-gray" id="closebtn" value="关闭" />
		</div>
		<script>
			$(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				 $(".icon a.ico").live('click',function(){
			     	var _this = $(this);
			     	var sku_id = _this.attr('data-id');
			     	var ajax_statusext = "<?php echo url('goods/admin/ajax_statusext')?>";
			     	var value = _this.attr('data-value');
			     	$.post(ajax_statusext,{sku_id:sku_id,status_ext:value},function(ret){
			     		if(ret.status == 1){
			     			if(! _this.hasClass("cancel")){
							     _this.addClass("cancel");
							}else{
			     				_this.removeClass("cancel").siblings(".ico").addClass("cancel");
			     				}
			     			}
			     		},'json');
			     });
				dialog.title('查看商品规格');
				dialog.reset();     // 重置对话框位置
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});

			})
		</script>
<?php include template('footer','admin');?>
