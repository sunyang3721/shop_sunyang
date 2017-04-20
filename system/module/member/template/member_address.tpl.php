<?php include template('header','admin');?>
		<?php if(!is_null($lists)){?>
		<div class="rece-address-manage clearfix">
			<table class="address-table text-left">
				<tr>
					<th class="w15">收货人</th>
					<th class="w15">手机</th>
					<th class="w10">邮编</th>
					<th class="w60">地址</th>
				</tr>
			<?php foreach ($lists['lists'] AS $r): ?>
				<tr>
					<td class="text-ellipsis w15"><?php echo $r['name'] ?></td>
					<td class="text-ellipsis w15"><?php echo $r['mobile'] ?></td>
					<td class="text-ellipsis w10"><?php echo $r['zipcode']?></td>
					<td class="text-ellipsis w60" title="<?php echo implode(" ", $r['full_district']) ?><?php echo $r['address'] ?>"><?php echo implode(" ", $r['full_district']) ?><?php echo $r['address'] ?></td>
				</tr>
			<?php endforeach ?>
			</table>
			<div class="paging padding-tb body-bg clearfix">
				<?php echo $pages;?>
				<div class="clear"></div>
			</div>
		</div>
		<?php }else{?>
		<div class="padding-large layout bg-white text-center"><div class="padding-big"><span>该用户没有添加收货地址！</span></div></div>
		<?php }?>
		<!--<div class="padding text-right ui-dialog-footer">
			<input type="button" class="button margin-left bg-gray" id="closebtn" value="关闭" />
		</div>-->
		<script>
			$(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				dialog.title('查看收货地址');
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
			})
		</script>
<?php include template('footer','admin');?>
