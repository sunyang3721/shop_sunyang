<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">文章分类设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST">
		<div class="content padding-big have-fixed-nav">
			<div class="hidden">
				<input type="hidden" name="parent_id" value="<?php echo isset($info['parent_id']) ? $info['parent_id'] : $_GET['parent_id']?>">
			</div>
			<div class="form-box clearfix">
				<?php echo form::input('text', 'name', $info['name'] ? $info['name'] : '', '分类名称：', '请填写文字分类的名称'); ?>
			</div>
			<div class="padding-lr">
					<div class="form-group">
					<span class="label">上级分类：</span>
					<div class="box ">
						<div class="field">
							<input name="parent" class="goods-class-text input input-readonly" id="choosecat" value="<?php echo $parent_name ? $parent_name : $info['parent_name'] ;?>" title="" readonly="readonly" type="text" placeholder="请选择文章分类" data-reset="false" />
							<input name="parent" data-ids="<?php echo implode(',',$info['category_id'])?>"class="goods-class-btn" type="button" value="选择" data-reset="false" />
						</div>
					</div>
					<p class="desc">如果选择上级分类，那么新增的分类则为被选择上级分类的子分类</p>
				</div>
			</div>
			<div class="form-box clearfix">
				<?php echo form::input('text', 'sort', $info['sort'] ? $info['sort'] :100 , '排序：', '请填写自然数，文字分类列表将会根据排序进行由小到大排列显示'); ?>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" name="dosubmit" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</div>
		</form>
		<script>
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
			$("input[name=parent]").attr("readonly","readonly");
			$("input[name=parent]").live('click',function(){
				var data = $(this).attr('data-ids');
				top.dialog({
					url: '<?php echo url('misc/article/article_category_choose',array('type'=>'category'))?>',
					title: '加载中...',
					width: 930,
					data:data,
					onclose: function () {
						if(this.returnValue){
							$("input[name=parent]").attr('data-ids',this.returnValue.split("category_ids=")[1].split(',').reverse());
							$("#choosecat").val(html_encode(this.returnValue,0));
							$("input[name=parent_id]").val(html_encode(this.returnValue,1));
						}
					}
				})
				.showModal();
			})
			//选择分类操作
			function html_encode(str,i){ 
			    str = str.split("category_ids=")[i];
				if(i == 1){
					var id = "<?php echo $_GET['id']?>";
					var arr_str = str.split(",");
					if(arr_str[arr_str.length-1] == id){
						return;
					}
					return arr_str[arr_str.length-1];
				}
				str = str.replace(/&gt;/g, ">"); 
				str = str.replace(/&lt;/g, "<");   
				str = str.replace(/&gt;/g, ">");   
				str = str.replace(/&nbsp;/g, " ");   
				str = str.replace(/&#39;/g, "\'");   
				str = str.replace(/&quot;/g, "\""); 
				return str;
			}
		</script>
	<?php include template('footer','admin');?>
