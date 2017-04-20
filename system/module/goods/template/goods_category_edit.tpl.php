<?php include template('header','admin');?>
<div class="fixed-nav layout">
			<ul>
				<li class="first">商品分类设置</li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
		 <form action="" method="POST">
			<div class="form-box">
			<?php echo form::input('text', 'name', $cate['name'], '分类名称：','填写商品分类名称，不能为空',array('validate' => 'required')); ?>
			</div>
			<input type="hidden" name="id" value="<?php echo $cate['id']?>">
			<div class="padding-lr">
				<div class="form-group">
					<span class="label">上级分类：</span>
					<div class="box ">
						<div class="field">
							<input class="goods-class-text input input-readonly" id="choosecat" value="<?php echo $cate['parent_name'] ? $cate['parent_name'] : $parent_name?>" title="" readonly="readonly" type="text" placeholder="请选择商品分类" data-reset="false" />
							<input class="goods-class-btn" type="button" value="选择" id="choose-class" data-reset="false" />
							<input type="hidden" name="parent_id" data-value="<?php echo $cate['cat_format']? $cate['cat_format'] : $cat_format ?>" value="<?php echo !empty($cate['parent_id'])?$cate['parent_id']:$_GET['pid']?>">
						</div>
					</div>
					<p class="desc">选择分类隶属哪个商品分类下，如是顶级分类，请直接选择顶级分类</p>
				</div>
				<div class="form-group">
					<span class="label">关联类型：</span>
					<div class="box ">
						<div class="field">
							<input class="goods-class-text input input-readonly" id="choosetype" value="<?php echo $cate['type_name']?>" title="" readonly="readonly" type="text" placeholder="请选择商品类型" data-reset="false" />
							<input class="goods-class-btn" type="button" value="选择" id="choose-type" data-reset="false" />
							<input type="hidden" name="type_id" value="<?php echo $cate['type_id']?>">
						</div>
					</div>
					<p class="desc">每个分类可以管理一个商品类型，用于前台筛选及商品类型添加</p>
				</div>
			</div>
			<div class="form-box clearfix">
			<?php echo form::input('text','url',$cate['url'] ? $cate['url'] : 'http://','分类外链：','为前台分类列表指定一个固定连接用于跳转，不影响分类的基本属性，特殊情况下使用，否则无需填写')?>
			<?php echo form::input('textarea','grade', $cate['grade'],'价格分级：','填写分类下商品筛选价格分级，如：0-99，每行一个'); ?>
			<?php echo form::input('text', 'sort', isset($cate['sort']) ? $cate['sort']:'100','排序：','请填写自然数。商品分类将会根据排序进行由小到大排列显示'); ?>	
			<?php echo form::input('enabled','status',isset($cate['status']) ? $cate['status'] : '1','是否启用：','请设置商品分类是否启用，不影响后台显示使用', array('colspan' => 2)); ?>	
			<?php echo form::input('enabled','show_in_nav',isset($cate['show_in_nav']) ? $cate['show_in_nav'] : '0','是否显示在导航：','请设置商品分类是否在前台导航上推荐', array('colspan' => 2)); ?>	
			<?php echo form::input('text', 'keywords', $cate['keywords'],'分类关键词：','Keywords项出现在页面头部的<Meta>标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔'); ?>	
			<?php echo form::input('textarea','descript', $cate['descript'],'分类描述：','Description出现在页面头部的Meta标签中，用于记录本页面的高腰与描述，建议不超过80个字'); ?>
			</div>
			<div class="padding">
				<input type="submit" name="dosubmit" class="button bg-main" value="确定" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
		</form>
		</div>
		<script>
			$('#choose-class').bind('click',function(){
				var url = "<?php echo url('category_main')?>";
				var pid = $('input[name=parent_id]').val(),
				 	pname = $('#choosecat').val(),
				 	pvalue = $('input[name=parent_id]').attr('data-value'),
				 	id = "<?php echo $_GET['id']?>";
				var data = [pid,pname,pvalue,id];
				top.dialog({
					url: url,
					title: '加载中...',
					width: 930,
					data: data,
					onclose: function () {
						if(this.returnValue){
							var catname = this.returnValue.html().replace(/&gt;/g,'>'); 
							$('#choosecat').val(catname);
							var catids = this.returnValue.attr('data-id').split(',');
							var catid = catids[catids.length-1];
							$('input[name=parent_id]').attr('data-value',this.returnValue.attr('data-id'));
							$('input[name=parent_id]').val(catid);
							if(catid != 0){
								$('input[name="show_in_nav"]').parents('.form-group').removeClass('hidden');
							}else{
								$('input[name="show_in_nav"]').parents('.form-group').addClass('hidden');
							}
							
						}
					}
				})
				.showModal();
			})
			$('#choose-type').bind('click',function(){
				var url = "<?php echo url('category_relation')?>";
				var tid = $('input[name=type_id]').val();
				var tname = $('#choosetype').val();
				var data = [tid,tname];
//				top.dialog({
//					url: url,
//					title: '加载中...',
//					data: data,
//					width: 681,
//					onclose: function () {
//						if(this.returnValue){
//							$('#choosetype').val(this.returnValue.html());
//							$('input[name=type_id]').val(this.returnValue.attr('data-id'));
//						}
//					}
//				})
//				.showModal();
//				var myEle = $(".relate-norm");
				top.dialog({
					url: url,
					title: '关联规格',
					data: data,
					width: 500,
					onclose: function () {
						if(this.returnValue){
							$('#choosetype').val(this.returnValue.html());
							$('input[name=type_id]').val(this.returnValue.attr('data-id'));
						}
					}
				})
				.showModal();
			});
			$(function(){
				if(!$('[name="parent_id"]').val()){
					$('input[name="show_in_nav"]').parents('.form-group').addClass('hidden');
				}
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
			});
		</script>
<?php include template('footer','admin');?>
