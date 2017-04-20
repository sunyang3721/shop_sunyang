<?php include template('header','admin');?>
<style>
	.table.resize-table{width: 100% !important;}
</style>
<div class="fixed-nav layout">
    <ul>
        <li class="first">附件管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">图片</a></li>
        <li class="fixed-nav-tab statistics"><a href="javascript:;">统计</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">驱动</a></li>
        <li class="fixed-nav-tab"><a href="javascript:;">设置</a></li>
	</ul>
    <div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav margin-top">
    <div class="content-tabs">
        <div class="table paging-table border clearfix">
			<div class="tr border-none">
				<span class="th layout">
					<span class="td-con text-left">图库管理</span>
				</span>
			</div>
			<div class="tr bg-none">
				<span class="td layout">
					<ul class="padding clearfix" id="gallery">
						<?php foreach ($lists as $key => $r): ?>
						<li data-aid="<?php echo $r['aid'] ?>">
							<div class="top">
								<span class="pic-center"><img src="<?php echo $r['url'] ?>" /></span>
							</div>
							<label class="title text-ellipsis"><input class="fl" type="checkbox" name="aids[]" value="<?php echo $r['aid'] ?>" /><?php echo $r['name'] ?></label>
							<div class="bottom">
								<div class="text">
									<span>上传时间：<?php echo date('Y-m-d', $r['datetime']) ?></span>
									<span>原图尺寸：<?php echo $r['width']?>*<?php echo $r['height']?></span>
								</div>
								<div class="handle">
									<label class="left replace_upload" id="replace_upload_<?php echo $r['aid'] ?>">替换上传</label>
									<a class="right" href="<?php echo url('attachment/admin/delete', array('aid[]' => $r['aid'])) ?>" data-confirm>删除图片</a>
								</div>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</span>
			</div>
			<div class="paging padding-tb body-bg clearfix">
				<div class="fl gallery-page-btn">
					<label><input type="checkbox" id="checkAll" name="id" value="1" />全选</label>
					<input type="button" class="button bg-main" data-event="delete" value="删除"/>
				</div>
				<ul class="fr">
					<?php echo $pages; ?>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
    </div>
    <div class="content-tabs hidden">
        <table cellpadding="0" cellspacing="0" class="border bg-white layout margin-top">
			<tbody>
				<tr class="bg-gray-white line-height-40 border-bottom">
					<th class="text-left padding-big-left">
						图片概况
					</th>
				</tr>
				<tr class="border">
					<td class="padding-big-left padding-big-right">
						<table cellpadding="0" cellspacing="0" class="layout">
							<tbody>
								<tr class="line-height-40">
									<td class="text-left">
										图片总张数：<?php echo $datas['img_total'];?>
									</td>
									<td class="text-left">
										未使用张数：<?php echo $datas['unused_img'];?>
									</td>
									<td class="text-left">
										已使用张数：<?php echo $datas['used_img'];?>
									</td>
									<td class="text-left">
										占空间大小：<?php echo $datas['filesize'];?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="layout margin-big-top clearfix">
			<table cellpadding="0" cellspacing="0" class="border bg-white layout">
				<tbody>
					<tr class="bg-gray-white line-height-40 border-bottom">
						<th class="text-left padding-big-left">
							图片模块类型统计
						</th>
					</tr>
					<tr>
						<td class="padding">
							<div id="pay" style="height: 400px;">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
    </div>
     <div class="content-tabs hidden">
       <div class="table-wrap margin-top">
				<div class="table resize-table high-table border clearfix">
					<div class="tr">
						<span class="th w85">
							<span class="td-con">上传方式</span>
						</span>
						<span class="th w15">
							<span class="td-con">操作</span>
						</span>
					</div>
					<?php foreach($attach as $k=>$v):?>
					<div class="tr">
						<span class="td w85">
							<div class="td-con td-pic text-left">
								<span class="pic"><img src="./statics/images/pay/<?php echo $v['code']?>.png" /></span>
								<span class="title txt"><?php echo $v['name']?></span>
								<span class="icon"><?php echo $v['description']?></span>
							</div>
						</span>
						<span class="td w15">
						<?php if($v['code'] != 'local'){?>
							<a class="text-sub" href="<?php echo url('config', array("code" => $v['code'])) ?>">配置</a>
						<?php }else{?>
							-
						<?php }?>
						</span>
					</div>
					<?php endforeach;?>
				</div>
			</div>
    </div>
    <div class="content-tabs hidden">
    <form action="<?php echo url('attachment/admin/setting_update')?>" method="POST" enctype="multipart/form-data">
        <div class="form-box clearfix">
			<?php echo form::input('enabled', 'attach_enabled', isset($setting['attach_enabled']) ? $setting['attach_enabled'] : 1, '是否开启附件上传：', '关闭后仅支持ftp上传', array('colspan' => 2)); ?>
			<?php echo form::input('textarea', 'attach_ext', isset($setting['attach_ext']) ? $setting['attach_ext'] : '', '支持格式后缀：', '支持上传文件格式后缀'); ?>
			<?php echo form::input('text', 'attach_size', isset($setting['attach_size']) ? $setting['attach_size'] : '', '文件大小限制：', '注意考虑php.ini的配置(0为不限制)'); ?>
			<?php echo form::input('enabled', 'attach_replace', isset($setting['attach_replace']) ? $setting['attach_replace'] : 0, '同名文件是否覆盖：', '同名文件是否覆盖', array('colspan' => 2)); ?>
			<?php echo form::input('select', 'attach_type',isset($setting['attach_type']) ? $setting['attach_type'] : '本地', '默认上传方式：', '请选择默认上传的方式"
', array('items' => $setting['attach_name'])); ?>
			<?php echo form::input('radio', 'attach_watermark', isset($setting['attach_watermark']) ? $setting['attach_watermark'] : 0, '水印类型：', '请选择水印类型', array('items' => array('0'=>'无','1'=>'图像'), 'colspan' => 2,)); ?>
	        <div class="position">
	        	<?php echo form::input('radio','attach_position',$setting['attach_position'],'水印位置：', '水印位置。', array('items'=>array(1=>'左上',2=>'上中',3=>'右上',4=>'左中',5=>'居中',6=>'右中',7=>'左下',8=>'下中',9=>'右下'),'colspan'=>'3')); ?>
	        </div>
	        <div class="img">
	            <?php echo form::input('file', 'attach_logo', $setting['attach_logo'], '水印图片：', '水印图片。'); ?>
	            <?php echo form::input('text', 'attach_alpha', $setting['attach_alpha'], '水印透明度：', '水印透明度。') ?>
	        </div>
	        <div class="module">
	        <?php echo form::input('checkbox', 'attach_module[]',$setting['attach_module'], '应用模块：', '请选择水印应用的模块', array('items' => $setting['attach_module_all'], 'colspan' => 2,)); ?>
	        </div>
	        <?php echo form::input('radio', 'attach_thumb', $setting['attach_thumb'], '缩略图：', '请选择缩略图的类型。',array('items'=>array(1=>'等比例缩放',3=>'居中裁剪',2=>'缩放后填充',4=>'左上角裁剪',5=>'右下角裁剪',6=>'固定尺寸缩放'),'colspan'=>'2')); ?>
        </div>
		<div class="padding">
	        <input type="submit" class="button bg-main" value="保存" />
	    </div>
    </form>
    </div>
</div>
<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/js/upload/uploader.css" />
<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/upload/uploader.js"></script>
<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/echarts/dist/echarts.js"></script>
<script>
	$('.statistics').live('click',function () {
        showchart();
        console.log(<?php echo json_encode($datas) ?>)
    });
	watermark();
	$('[name=attach_watermark]').click(function () {
        watermark();
    });
	function watermark(){
		switch($('[name=attach_watermark]:checked').val()){
			case '1':
				$('.img').show();
	         	$('.word').hide();
	         	$('.position').show();
	         	$('.module').show();
				break;
			default:
				$('.img').hide();
	         	$('.word').hide();
	         	$('.position').hide();
	         	$('.module').hide();
				break;
		}
	}
	$(window).load(function(){
		$(".paging-table").fixedPaging();
		$("#gallery li").hover(function(){
			$(this).addClass("current");
			$(this).find(".handle").show();
		},function(){
			$(this).removeClass("current");
			$(this).find(".handle").hide();
		});
	
		$("#checkAll").click(function(){
			if($(this).is(":checked")){
				$("#gallery").find("input").attr("checked",true);
			}else{
				$("#gallery").find("input").attr("checked",false);
			}
		})
	
		$("[data-event=delete]").click(function() {
			var aids = [];
	        $('input[name="aids[]"]:checked').each(function(){
	        	aids.push($(this).val());
	        })
	        if(aids.length < 1) {
	        	alert('请选择要删除的图片');
	        	return false;
	        }
			$.getJSON("<?php echo url('attachment/admin/delete') ?>", {aid:aids}, function(ret) {
				if(ret.status == 0) {
					alert(ret.message);
					return false;
				} else {
					if(confirm('您确定执行本操作？')) window.location.reload();
					
				}
			})
			return false;
		})
		$(".resize-table").resizableColumns();
		$('.resize-table .tr:last-child').addClass("border-none");
		//推荐
		var status = true;
		var post_enabled_url="<?php echo url('ajax_enabled')?>";
		$(".resize-table .ico_up_rack").bind('click',function(){
			if(ajax_enabled($(this).attr('data-id'))==true){					
				if(!$(this).hasClass("cancel")){
					$(this).addClass("cancel");
					$(this).attr("title","点击商品上架");
				}else{
					$(this).removeClass("cancel");
					$(this).attr("title","点击商品下架");
				}
			}
		});
		//改变状态
		function ajax_enabled(paycode){
			$.post(post_enabled_url,{'paycode':paycode},function(data){
				if(data.status == 1){
					status =  true;
				}else{
					status =  false;
				}
			},'json');
			return status;
		}
	})

$(function(){
	var $val=$("input[type=text]").first().val();
	$("input[type=text]").first().focus().val($val);
	$('.replace_upload').each(function(){
		var obj = $(this).parents('li'),
			aid = obj.data("aid");
		var uploader = WebUploader.create({
	        auto:true,
	        fileVal:'upfile',
	        // swf文件路径
	        swf: '<?php echo __ROOT__;?>statics/js/upload/uploader.swf',
	        // 文件接收服务端。
	        server: "<?php echo url('attachment/admin/replace')?>",
	        // 选择文件的按钮。可选
	        formData:{
	            file : 'upfile',
	            aid : aid
	        },
	        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
	        pick: {
	            id: '#replace_upload_' + aid,
	            multiple:false
	        },
	        // 压缩图片大小
	        compress:{
	        	width: 408,
	        	height: 408,
	        	allowMagnify: false
	        },
	        accept:{
	            title: '图片文件',
	            extensions: 'gif,jpg,jpeg,bmp,png',
	            mimeTypes: 'image/*'
	        },
	        chunked: false,
	        chunkSize:1000000,
	        resize: false
	    })

	    uploader.onFileQueued = function(file) {
	    	$(this.options.pick.id).find('.webuploader-pick').html('上传中');
	    }

	    uploader.onUploadProgress = function(file, percentage) {
	    	$(this.options.pick.id).find('.webuploader-pick').html('上传中(' + percentage * 100 + '%)');
	    }

	    uploader.onUploadSuccess = function(file, response) {
	    	$(this.options.pick.id).find('.webuploader-pick').html('重新上传');
	    	if(response.status == 1) {
	    		obj.find('span.pic > img').attr("src", response.result.url);
	    	} else {
	    		alert(response.message);
	    		return false;
	    	}
	    }

	})
})
	//var search = <?php echo json_encode($datas['search']); ?>;
	// showchart();
	function showchart(){
		// 路径配置
		require.config({
			paths: {
				echarts: '<?php echo __ROOT__;?>statics/js/echarts/dist' //配置路径
			}
		});
		// 使用
		require(
		[
			'echarts',
			'echarts/chart/pie' // 饼图
		],DrawEChart);
		function DrawEChart(ec) {
			//支付方式类型统计
			myPay = ec.init(document.getElementById('pay'), 'macarons');
			var _data = <?php echo json_encode($datas) ?>;
			myPay.setOption({
				tooltip: {
					trigger: 'item',
					formatter: "{a} <br/>{b} : {c} ({d}%)"
				},
				legend: {
					orient: 'vertical',
					x: 'left',
					data: _data.img
				},
				toolbox: {
					show: false
				},
				calculable: false,
				series: [{
					name: '访问来源',
					type: 'pie',
					radius: ['50%', '70%'],
					itemStyle: {
						normal: {
							label: {
								show: false
							},
							labelLine: {
								show: false
							}
						},
						emphasis: {
							label: {
								show: true,
								position: 'center',
								textStyle: {
									fontSize: '20',
									fontWeight: 'bold'
								}
							}
						}
					},
					data: _data.all_img
				}]
			});

		}
	}
</script>
<?php include template('footer','admin');?>