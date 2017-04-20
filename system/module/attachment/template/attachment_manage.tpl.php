<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">编辑图片空间</li>
				<li class="spacer-gray"></li>
				<li><a <?php if (!isset($_GET['type'])): ?>class="current"<?php endif ?> href="<?php echo url('attachment/admin/manage', array('folder' => $_GET['folder'])) ?>">全部图片</a></li>
				<li><a <?php if (isset($_GET['type'])): ?>class="current"<?php endif ?> href="<?php echo url('attachment/admin/manage', array('folder' => $_GET['folder'], 'type' => 'use')) ?>">未使用图片</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav margin-top">
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


<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/js/upload/uploader.css" />
<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/upload/uploader.js"></script>
<script type="text/javascript">
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
})

$(function(){
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
</script>
<?php include template('footer','admin');?>
