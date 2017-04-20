<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">广告设置</li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;">广告列表</a></li>
				<li><a href="<?php echo url('position_index')?>">广告位</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form name="form" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
			<div class="form-box clearfix">
				<?php echo form::input('text', 'title', $title, '广告名称：', '请输入广告名称', array('datatype' => '*')); ?>
				<?php echo form::input('select', 'position_id', $position_id, '广告位：', '请选择广告位', array('items'=>$position_format['items']), array('datatype' => '*')); ?>
				
				<?php echo form::input('calendar', 'starttime', $startime_text, '开始时间', '开始时间'); ?>
				<?php echo form::input('calendar', 'endtime', $endtime_text, '结束时间', '结束时间'); ?>
				<!--<div class="form-group">
					<span class="label">开始时间</span>
					<div class="box">
						<input type="text" id="start" placeholder="YYYY-MM-DD hh:mm:ss" value="<?php echo $startime_text?>" name="starttime" class="input laydate-icon hd-input" datatype = '/\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/'>
					</div>
					<p class="desc">开始时间</p>
				</div>
				<div class="form-group">
					<span class="label">结束时间</span>
					<div class="box">
						<input type="text" id="end" placeholder="YYYY-MM-DD hh:mm:ss" value="<?php echo $endtime_text?>" name="endtime" class="input laydate-icon hd-input" datatype = '/\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/'>
					</div>
					<p class="desc">结束时间</p>
				</div>-->
				
				<?php echo form::input('file', 'content_pic', $content, '广告图片：','请选择广告图片',array('preview'=>$content));?>	
				<?php echo form::input('text', 'content_text', $content, '广告文字：', '请输入广告文字'); ?>	
				<?php echo form::input('text', 'link', $link, '链接地址：', '请输入链接地址(http://www.xxx.com)',array('datatype' => 'url','ignore'=>'ignore')); ?>	

			</div>
			<div class="padding">
				<?php if(isset($id)):?>
					<input type="hidden" name="id" value="<?php echo $id?>" />
				<?php endif;?>
				<input type="hidden" name="type" value="" />
				<input type="submit" class="button bg-main" value="保存" />
				<input type="button" class="button margin-left bg-gray" value="返回" />
			</div>
			</form>
		</div>
		<script src="/statics/js/laydate/laydate.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(window).otherEvent();
			var position_json = <?php echo json_encode($position_format)?>;
			//切换效果

			function loadBegin(position_type){
				$('input[name="type"]').val(position_type);
				if(position_type=="0"){
					$(".form-group:eq(4)").show();
					$(".form-group:eq(5)").hide();
				}else{
					$(".form-group:eq(4)").hide();
					$(".form-group:eq(5)").show();
				}
			}
			$(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				//模拟第一个点击
				if($('input[name="position_id"]').val()=='')$('.listbox-items span:eq(0)').trigger('click');
				$("[name=form]").Validform({
					beforeSubmit:function(curform){
						var startTime = $('input[name="starttime"]').val();
						var endTime = $('input[name="endtime"]').val(); 
						var d1 = new Date(startTime.replace(/\-/g, "\/")); 
						var d2 = new Date(endTime.replace(/\-/g, "\/")); 
						if(startTime !="" && endTime !="" && d1 >= d2){ 
							alert("开始时间不能大于结束时间！"); 
							return false; 
						}
						
					}
				});
				var position_type = position_json.type[parseInt($("input[name='position_id']").val())];
				loadBegin(position_type);
				$('input[name="position_id"]').live('change', function() {
					position_type = position_json.type[parseInt($(this).val())];
					loadBegin(position_type);
				}); 
				//日期时间 
				laydate.skin('danlan');
				var start = {
				    elem: '#start',
				    format: 'YYYY-MM-DD hh:mm:ss',
				    min: laydate.now(), //设定最小日期为当前日期
				    max: '2099-06-16 23:59:59', //最大日期
				    istime: true,
				    istoday: true,
				    choose: function(datas){
				         end.min = datas; //开始日选好后，重置结束日的最小日期
				         end.start = datas //将结束日的初始值设定为开始日
				    }
				};
				var end = {
				    elem: '#end',
				    format: 'YYYY-MM-DD hh:mm:ss',
				    min: laydate.now(),
				    max: '2099-06-16 23:59:59',
				    istime: true,
				    istoday: true,
				    choose: function(datas){
				        start.max = datas; //结束日选好后，重置开始日的最大日期
				    }
				};
				laydate(start);
				laydate(end);
			})
		</script>
<?php include template('footer','admin');?>
