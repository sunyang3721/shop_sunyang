<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">文章管理设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<form action="" method="POST" enctype="multipart/form-data">
		<div class="content padding-big have-fixed-nav">
			<div class="hidden">
				<input type="hidden" name="id" value="<?php echo $info['id']?>" />
				<input type="hidden" name="category_id" value="<?php echo $info['category_id']?>" />
			</div>
			<div class="form-box clearfix">
			   <?php echo form::input('text', 'title', $info['title'], '文章标题：', '请填写文章标题'); ?>
			</div>
			<div class="padding-lr">
				<div class="form-group">
					<span class="label">上级分类：</span>
					<div class="box ">
						<div class="field">
							<input name="category" class="goods-class-text input input-readonly" id="choosecat" value="<?php echo $info['category'];?>" title="" readonly="readonly" type="text" placeholder="请选择文章分类" data-reset="false" />
							<input name="category" data-ids="<?php echo implode(',',$info['category_ids']);?>" class="goods-class-btn" type="button" value="选择" data-reset="false" />
						</div>
					</div>
					<p class="desc">选择文章所属分类</p>
				</div>
			</div>
			<div class="form-box clearfix">
				<?php echo form::input('file', 'thumb', $info['thumb'], '文章图片：','上传文章封面图片，显示在文章列表页');?>
				<?php echo form::input('radio', 'display', isset($info['display']) ? $info['display']: 1, '是否显示：', '是否显示文章', array('items' => array('1'=>'是','0'=> '否'), 'colspan' => 2,)); ?>
				<?php echo form::input('radio', 'recommend', isset($info['recommend']) ? $info['recommend']: 1, '是否推荐：', '是否推荐文章', array('items' => array('1'=>'是','0'=> '否'), 'colspan' => 2,)); ?>
				<?php echo form::input('text', 'url', $info['url'], '文章外链：', '可为文章设置一个外链地址，设置后点击文章标题跳转到指定链接'); ?>
				<?php echo form::input('text', 'keywords', $info['keywords'], '文章关键字：', '文章关键词出现在页面头部的<Meta>标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔'); ?>
				<?php echo form::input('text', 'sort', $info['sort'] ? $info['sort'] : 100, '排序：', '请填写自然数，文章列表将会根据排序进行由小到大排列显示'); ?>
			</div>
			<div class="padding">
				<span class="margin-bottom show clearfix">文章内容：</span>
				<?php echo form::editor('content', $info['content'], '', '', array('module'=>'article','mid' => $admin['id'], 'path' => 'article')); ?>
			</div>
			<div class="padding">
				<input type="submit" class="button bg-main" value="保存" name="dosubmit"/>
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</div>
		</form>
		<script>
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			})
			$("input[name=category]").live('click',function(){
				var data = $(this).attr('data-ids');
				top.dialog({
					url: '<?php echo url('misc/article/article_category_choose',array('type'=>'article_category'))?>',
					title: '加载中...',
					width: 930,
					data:data,
					onclose: function () {
						if(this.returnValue){
							$("input[name=category]").attr('data-ids',this.returnValue.split("category_ids=")[1].split(',').reverse());
							$("#choosecat").val(html_encode(this.returnValue,0));
							$("input[name=category_id]").val(html_encode(this.returnValue,1));
						}
					}
				})
				.showModal();
			})
			//选择分类操作
			function html_encode(str,i){
			    str = str.split("category_ids=")[i];
				if(i == 1){
					var arr_str = str.split(",");
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
