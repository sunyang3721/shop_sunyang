<?php include template('header','admin');?>
		<style>
			.picture-space { height: 558px; }
			.picture-space .left-wrap { float: left; width: 190px; height: 100%; background-color: #2e5e8c; }
			.picture-space .left-wrap .title { padding-left: 20px; line-height: 46px; font-size: 18px; border-bottom: 1px solid #184776; }
			.picture-space .space-menu { height: 511px; overflow: hidden; overflow-y: auto; }
			.picture-space .space-menu a { display: block; padding-left: 20px; color: #fff; line-height: 38px; }
			.picture-space .space-menu a.current, .picture-space .space-menu a:hover { padding-left: 15px; color: #fff; border-left: 5px solid #fff; background-color: #3b78b2; }
			.picture-space .right-wrap { float: left; width: 790px; height: 100%; }
			.picture-space .right-wrap .space-bar { height: 46px; font-size: 0; line-height: 26px; }
			.picture-space .right-wrap .space-bar a { display: inline-block; margin: 10px 10px 10px 0; padding: 0 10px; font-size: 12px; color: #333; }
			.picture-space .right-wrap .space-bar a:hover, .picture-space .right-wrap .space-bar a.current { border-radius: 3px; color: #fff; background-color: #1380cb; }
			.picture-space .right-wrap .space-bar span { font-size: 12px; }
			.space-bar .form-box { padding: 0; width: auto; }
			.space-bar .form-group { display: inline-block; width: auto; }
			.space-bar .form-group .label { width: 74px; }
			.space-bar .form-group .box { margin-right: 0; width: 100px; }
			.space-bar .form-box .button { padding-top: 2px; padding-bottom: 2px; height: 26px; }
			.space-wrapper { position: relative; width: 100%; height: 456px; overflow: hidden; overflow-y: auto; background-color: #fff; }
			.space-wrapper label input { vertical-align: middle; margin-top: -3px; }
			.space-wrapper .delete-btn { padding: 2px 15px; }
			#gallery { position: relative; padding: 10px 0 56px 12px; z-index: 1; }
			#gallery li { position: relative; height: 247px; }
			#gallery li.selected .handle, #gallery li.current .handle { display: block; }
			#gallery li.selected { background-color: #e7e7e7; }
			#gallery .bottom .handle label { color: #1380cb; }
			#gallery .bottom .handle label, #gallery .bottom .handle a { margin-top: 5px; }
			.selected-icob { display: none; position: absolute; top: 0; right: 0; width: 20px; height: 20px; background: url(<?php echo __ROOT__;?>statics/images/ico_space_selected.png) no-repeat center; z-index: 3; }
			#gallery li.selected .selected-icob { display: block; }
			.space-wrapper .paging { position: absolute; top: 410px; left: 0; right: 0; z-index: 2; }
			.pt-100{ padding-top: 100px }
		</style>
		<div class="picture-space clearfix">
			<div class="left-wrap text-white">
				<div class="title">图片空间</div>
				<ul class="space-menu">
				<?php foreach($module AS $k => $v):?>
					<li><a <?php if($_GET['folder']==$k){echo 'class="current"';}?> href="<?php echo url('picture_space',array('folder'=>$k))?>"><?php echo $v;?></a></li>
				<?php endforeach;?>
				</ul>
			</div>
			<div class="right-wrap">
				<div class="space-bar padding-big-left padding-big-right border-bottom">
					<a href="<?php echo url('picture_space',array('folder'=>$_GET['folder']))?>" <?php if(!$_GET['type']){echo 'class="current"';}?>>全部图片</a>
					<a href="<?php echo url('picture_space',array('folder'=>$_GET['folder'],'type'=>'used'))?>" <?php if($_GET['type']=='used'){echo 'class="current"';}?>>已使用图片</a>
					<a href="<?php echo url('picture_space',array('folder'=>$_GET['folder'],'type'=>'unused'))?>" <?php if($_GET['type']=='unused'){echo 'class="current"';}?>>未使用图片</a>
					<div class="fr form-box form-layout-rank clearfix border-bottom-none">
								<form method="get">
						<div class="form-group">
							<span class="label">按来源筛选</span>
							<div class="box">
								<div class="form-select-edit">
									<div class="form-buttonedit-popup">
										<input class="input" type="text" value="请选择来源" readonly="readonly">
										<span class="ico_buttonedit"></span>
									</div>
									<div class="listbox-items">
										<span class="listbox-item" data-selected="true"  data-val="0">全部</span>
										<span class="listbox-item" data-val="1">115网盘</span>
										<span class="listbox-item" data-val="2">360网盘</span>
										<span class="listbox-item" data-val="3">阿里云</span>
										<span class="listbox-item" data-val="4">华为网盘</span>
									</div>
									<input class="form-select-name" type="hidden" name="group_id" value="0">
								</div>
							</div>
						</div>
						<div class="form-group margin-left">
							<span class="label">按时间筛选</span>
							<div class="box">
								<div class="form-select-edit">
									<input type="hidden" name="m" value="goods" />
									<input type="hidden" name="c" value="admin" />
									<input type="hidden" name="a" value="picture_space" />
									<div class="form-buttonedit-popup">
										<input class="input" type="text" value="请选择时间" readonly="readonly">
										<span class="ico_buttonedit"></span>
									</div>
									<div class="listbox-items">
										<span class="listbox-item" <?php if(!$_GET['time']){echo 'data-selected="true"';} ?> data-val="0">全部时间</span>
										<span class="listbox-item" <?php if($_GET['time']==1){echo 'data-selected="true"';} ?> data-val="1">最近一周</span>
										<span class="listbox-item" <?php if($_GET['time']==2){echo 'data-selected="true"';} ?> data-val="2">最近半月</span>
										<span class="listbox-item" <?php if($_GET['time']==3){echo 'data-selected="true"';} ?> data-val="3">最近一月</span>
										<span class="listbox-item" <?php if($_GET['time']==4){echo 'data-selected="true"';} ?> data-val="4">最近半年</span>
									</div>
									<input class="form-select-name" type="hidden" name="time" value="<?php echo $_GET['time'];?>">
									<?php if ($_GET['type']): ?>
										<input type="hidden" name="type" value="<?php echo $_GET['type'];?>" />
									<?php endif; ?>
									<?php if ($_GET['folder']): ?>
										<input type="hidden" name="folder" value="<?php echo $_GET['folder'];?>" />
									<?php endif; ?>
								</div>
							</div>
						</div>
						<input class="button bg-main fl margin-left margin-top" type="submit" value="筛选">
						</form>
					</div>
				</div>
				<?php if($lists){?>
				<div class="space-wrapper">
					<div class="space-box bg-white clearfix">
						<ul id="gallery" class="clearfix">
							<?php foreach ($lists as $key => $r): ?>
							<li data-aid="<?php echo $r['aid'] ?>">
								<div class="selected-icob"></div>
								<div class="top">
									<span class="pic-center"><img src="<?php echo $r['url'] ?>"></span>
								</div>
								<div class="title text-ellipsis"><?php echo $r['name'] ?></div>
								<div class="bottom">
									<div class="text">
										<span>上传时间：<?php echo date('Y-m-d', $r['datetime']) ?></span>
										<span>原图尺寸：<?php echo $r['width']?>*<?php echo $r['height']?></span>
									</div>
									<div class="handle">
										<div class="text-gray">图片来源：阿里云</div>
										<label class="left replace_upload" id="replace_upload_<?php echo $r['aid'] ?>">替换上传</label>
										<a class="right" href="<?php echo url('attachment/admin/delete', array('aid[]' => $r['aid'])) ?>" data-confirm="">删除图片</a>
									</div>
								</div>
							</li>
							<?php endforeach ?>
						</ul>
					</div>
					<div class="paging padding-tb body-bg layout clearfix">
						<div class="fl padding-big-left">
							<label><input class="margin-small-right" id="checkAll" type="checkbox">全选</label>
							<input type="button" class="margin-left button delete-btn bg-gray" data-event="delete" value="删除" />
						</div>
						<ul class="fr">
							<?php echo $pages; ?>
						</ul>
					</div>
				</div>
				<div class="padding text-right ui-dialog-footer">
					<input type="button" class="button bg-main" id="okbtn" value="确定" />
					<input type="button" class="button margin-left bg-gray" id="closebtn" value="返回" />
				</div>
				<?php }else{?>
				<div class="space-wrapper text-center pt-100">
					<img src="<?php echo __ROOT__;?>statics/images/not_pic.png">
					<p class="margin-big-top">暂无图片</p>
				</div>
				<?php }?>
			</div>
		</div>
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/js/upload/uploader.css" />
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/upload/uploader.js"></script>
		<script>
			$(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) { }

				dialog.width(980);
				dialog.title("选择图片");
				dialog.reset();
				// 图片列表
				$("#gallery li").hover(function(){
					$(this).addClass("current");
				},function(){
					$(this).removeClass("current");
				});
				$("#gallery li").click(function(){
					if($(this).hasClass("selected")){
						$(this).removeClass("selected");
					}else{
						$(this).addClass("selected");
					}
				})
				$("#checkAll").click(function(){
					if($(this).is(":checked")){
						$("#gallery li").addClass("selected");
					}else{
						$("#gallery li").removeClass("selected");
					}
				})

				$(".space-wrapper").scroll(function(e){
					var $top = 410 + this.scrollTop;
					$(".paging").css({top: $top+"px"});
				})

				$("[data-event=delete]").click(function() {
					var aids = [];
			        $('#gallery').find('.selected').each(function(){
			        	aids.push($(this).data('aid'));
			        })
			        console.log(aids)
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
				function getImgUrl() {
			    	var imgData = []
					$(".selected img").each(function () {
						var src =  $(this).attr('src');
						imgData.push(src);
					});
					return imgData;
				}
				/*关闭*/
				$('#closebtn').on('click',function () {
					dialog.remove();
					return false;
				});
				$('#okbtn').on('click', function () {
					dialog.close(getImgUrl());
					dialog.remove();
					return false;
				});
			})
		</script>
<?php include template('footer','admin');?>