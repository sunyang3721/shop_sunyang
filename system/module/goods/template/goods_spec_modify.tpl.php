<?php include template('header','admin');?>
<script type="text/javascript" src="./statics/js/dialog/dialog-plus-min.js" ></script>
<link rel="stylesheet" href="./statics/js/dialog/ui-dialog.css" />
<link type="text/css" rel="stylesheet" href="./statics/js/upload/uploader.css" />
<script type="text/javascript" src="./statics/js/upload/uploader.js"></script>
		<div class="table clearfix">
			<div class="tr">
				<div class="th w40">货号</div>
				<div class="th w20">销售价格</div>
				<div class="th w20">市场价格</div>
				<div class="th w20">库存</div>
			</div>
			<div class="tr">
				<div class="td w40">
					<div class="td-con">
						<input class="input" type="text" name="sn" value="" />
					</div>
				</div>
				<div class="td w20">
					<div class="td-con">
						<input class="input" type="text" name="shop_price" value="+0.00" />
					</div>
				</div>
				<div class="td w20">
					<div class="td-con">
						<input class="input" type="text" name="market_price" value="+0.00" />
					</div>
				</div>
				<div class="td w20">
					<div class="td-con">
						<input class="input" type="text" name="number" value="+0" />
					</div>
				</div>
			</div>
		</div>
		<p class="padding-big">小提示：此处修改的值将对所有商品值进行加减修改如:+10 -5库存必须是整数 价格可带两位小数</p>
		<div class="padding text-right ui-dialog-footer">
			<input type="button" class="button bg-main" id="okbtn" value="生成" />
			<input type="button" class="button margin-left bg-gray" id="closebtn" value="取消" />
		</div>
		<script type="text/javascript">
			$(window).load(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				$('[name=sn]').val(dialog.data);
				dialog.reset();
				dialog.title('批量编辑商品信息');
				// 重置对话框位置
				$('#okbtn').on('click', function () {
					var data = [$('input[name="sn"]').val(),$('input[name="shop_price"]').val(),$('input[name="market_price"]').val(),$('input[name="number"]').val()];
					dialog.close(data); // 关闭（隐藏）对话框
					dialog.remove();	// 主动销毁对话框
					return false;
				});
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
			})
		</script>
<?php include template('footer','admin');?>
