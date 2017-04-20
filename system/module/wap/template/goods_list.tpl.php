<?php include template('header','admin');?>
	<style>
		.link-search-form .input { padding: 0 10px; width: 208px; height: 28px; font-size: 12px; line-height: 26px; }
		.link-search-form .button { padding: 3px 15px; }
	</style>
	<div class="padding clearfix">
		<div class="fl margin-left strong text-lh-large">
			<a class="text-blue refresh-dialog" href="">刷新</a>
			<span class="margin-left margin-right text-gray">|</span>
			<span>已上架商品</span>
			<span class="margin-left margin-right text-gray">|</span>
			<a class="text-blue" href="<?php echo url('goods_category')?>">商品分类</a>
			<!--<span class="margin-left margin-right text-gray">|</span>
			<a class="text-blue" target="_blank" href="<?php echo url('goods/admin/goods_add')?>">新建商品</a>
			<span class="margin-left margin-right text-gray">|</span>
			<a class="text-blue" target="_blank" href="<?php echo url('goods/category/add')?>">新建分类</a>-->
		</div>
		<div class="fr margin-right link-search-form">
			<form name="sku_search" method="get">
				<input type="hidden" name="m" value="wap">
				<input type="hidden" name="c" value="admin">
				<input type="hidden" name="a" value="nav">
				<input type="hidden" name="multiple" value="<?php echo (int) $_GET['multiple'] ?>">
				<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
				<input type="text" class="fl input" name="keyword" value="<?php echo $_GET['keyword']?>" placeholder="输入商品名称/货号" />
				<input class="fr margin-small-left button bg-sub" type="submit" value="查询" />
			</form>
		</div>
	</div>
	<div class="choose-goods-link">
		<div class="table high-table border-top clearfix" style="min-height: 334px;">
			<div class="tr">
				<div class="th w25">商品货号</div>
				<div class="th w60">商品名称</div>
				<div class="th w15">操作</div>
			</div>
			<?php foreach ($skus['lists'] AS $sku) {?>
			<div class="tr">
				<div class="td w25">
					<div class="td-con"><?php echo $sku['sn']?></div>
				</div>
				<div class="td w60">
					<div class="td-con td-pic text-left">
						<div class="pic">
							<img src="<?php echo $sku['thumb'] ?>" />
						</div>
						<div style="padding-left: 64px;" class="text-ellipsis" title="<?php echo $sku['sku_name']?>"><?php echo $sku['sku_name']?></div>
					</div>
				</div>
				<div class="td w15">
					<div class="td-con">
						<a href="<?php echo url('goods/index/detail',array('sku_id'=>$sku['sku_id']))?>" class="button" data-id="<?php echo $sku['sku_id'] ?>" data-name="<?php echo $sku['sku_name'] ?>">选取</a>
					</div>
				</div>
			</div>
			<?php }?>
		</div>
	</div>
	<div class="paging padding-tb body-bg layout clearfix">
		<?php echo $pages;?>
	</div>
	<script>
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			//TODO
		}
		
		dialog.title("选择已上架商品链接");
		dialog.width(720);
		dialog.height($(".choose-goods-link").height() + 96);
		dialog.reset();
		
		$(".refresh-dialog").click(function(){
			window.location.reload();
		});
		
		$(".choose-goods-link .button").click(function(){
			var data = {};
			data.title = '商品 / ' + $(this).data("name");
			data.url = $(this).attr("href");
			dialog.close(data);
			dialog.remove();
			return false;
		});
		
	</script>
</body>
</html>