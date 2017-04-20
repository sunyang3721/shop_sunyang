<?php include template('header','admin');?>
	<style>
		.table .button { padding: 1px 15px; }
		.treetable { height: 410px; overflow: hidden; overflow-y: auto; }
	</style>
	<div class="padding clearfix">
		<div class="fl margin-left strong text-lh-large">
			<a class="text-blue refresh-dialog" href="">刷新</a>
			<span class="margin-left margin-right text-gray">|</span>			
			<a class="text-blue" href="<?php echo url('goods_list')?>">已上架商品</a>
			<span class="margin-left margin-right text-gray">|</span>
			<span>商品分类</span>
			<!--<span class="margin-left margin-right text-gray">|</span>
			<a class="text-blue" href="<?php echo url('goods/admin/goods_add')?>">新建商品</a>
			<span class="margin-left margin-right text-gray">|</span>
			<a class="text-blue" href="<?php echo url('goods/category/add')?>">新建分类</a>-->
		</div>
	</div>
	<div class="table th-table border-top clearfix">
		<div class="tr">
			<div class="th w10">&nbsp;</div>
			<div class="th w90 text-left">标题</div>
		</div>
	</div>
	<div class="table treetable clearfix">
		<?php foreach($result AS $category){ ?>
			<div class="tr" data-tree-id="<?php echo $category['id']?>" data-tree-parent-id="0">
				<div class="td w10 padding-left">
					<div class="tree-indenter">
						<a class="tree-ind-status close" data-level="1" data-id="<?php echo $category['id']?>" href="javascript:;"></a>
					</div>
				</div>
				<div class="td w80 text-left"><span class="padding-small-left"><?php echo $category['name']?></span></div>
				<div class="td w10"><a class="button" href="<?php echo url('goods/index/lists',array('id'=>$category['id']))?>" data-id="<?php echo $category['id']?>" data-name="<?php echo $category['name']?>">选取</a></div>
			</div>		
		<?php }?>
	</div>
	<div class="padding-top"></div>
	<script>
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			//TODO
		}
		
		dialog.title("选择商品分类链接");
		dialog.width(720);
		dialog.reset();
		
		$(".treetable").treetable();
		
		$(".refresh-dialog").click(function(){
			window.location.reload();
		});
		
		
		$(".treetable .button").click(function(){
			var data = {};
			data.title = '商品分类 / ' + $(this).data("name");
			data.url = $(this).attr("href");
			dialog.close(data);
			dialog.remove();
		});
		
		$(".tree-ind-status").live('click',function(){
			var $this = $(this);
			var $tr = $(this).parents('.tr');
			if(!$this.data("ajax")){
				var level, classname, html = '';
				if($(this).attr("data-level")==1){
					level = 2;
					className = "tree-one";
				}else if($(this).attr("data-level")==2){
					level = 3;
					className = "tree-two";
				}else if($(this).attr("data-level")==3){
					level = 4;
					className = "tree-three";
				}
				$.hdLoad.start();
				$.ajax({
					type: "GET",
					url: "<?php echo url('wap/admin/ajax_category')?>",
					data: {id:$this.data("id")},
					dataType: "json",
					success: function(data){
						$.hdLoad.end();
						var datas = data.result;//通过获取的的json遍历添加
						console.log(datas)
						$.each(datas, function(i){
							html += '<div class="tr" data-tree-id="'+ $tr.attr("data-tree-id") +'-'+ (i+1) +'" data-tree-parent-id="'+ $tr.attr("data-tree-id") +'">'
								+'	<div class="td w10 padding-left">'
								+'		<div class="tree-indenter">'
								+			(datas[i].row == 0 ? '' :($(this).attr("data-level")!=3?'<a class="tree-ind-status close" data-level="'+level+'" data-id="'+datas[i].id+'" href="javascript:;"></a>':''))
								+'		</div>'
								+'	</div>'
								+'	<div class="td w80 text-left">'
								+'		<div class="tree-edit-input '+ className +'">'
								+'			<span class="tree-input-status'+(datas[i].row == 0 ?' no':' can')+'"></span>'
								+'			<span class="padding-small-left">'+ datas[i].name +'</span>'
								+'		</div>'
								+'	</div>'
								+'	<div class="td w10"><a class="button" href="" data-id="'+ datas[i].id +'" data-name="'+ datas[i].name +'">选取</a></div>'
								+'</div>';
						})
						$tr.after(html);
						$this.data("ajax", "true");
					}
				});
			}
		})
		
	</script>
</body>
</html>