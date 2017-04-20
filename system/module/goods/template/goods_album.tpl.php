<link type="text/css" rel="stylesheet" href="./statics/js/upload/uploader.css" />
<script type="text/javascript" src="./statics/js/upload/uploader.js"></script>
			<div class="atlas-wrap">
				<div class="upload-pic-wrap border bg-white margin-big-top spu_imgs">
					<div class="title border-bottom bg-gray-white text-default">
						<b>默认图片（如商品多个规格图片一致，仅需上传默认图片；多个规格图片不一致，默认图片可不传）</b>
					</div>
					<div class="upload-pic-content clearfix">
					<?php if(!empty($goods['spu']['imgs'])){?>
					<?php foreach ($goods['spu']['imgs'] AS $url) {?>
						<div class="box">
							<img src="<?php echo $url?>" />
							<div class="operate">
								<i>×</i>
								<a href="javascript:;">默认主图</a>
								<input type="hidden" data-name="0" name="album[0][]" value="<?php echo $url?>"/>
							</div>
						</div>
					<?php }?>
					<?php }?>
					<div class="loadpic" >
							<label class="load-button" data-id="0" id="upload_0"></label>
						</div>
					</div>
				</div>
				<div class="spec_box"></div>
				<div class="album_box"></div>
				<script id="spec_select" type="text/html">
				<div class="margin-big-top padding-top padding-left padding-right border border-gray border-dashed spec-right-body bg-white">
					<div class="wrap padding-none clearfix">
					<%for(var item in templateData){%>
        			<%item = templateData[item]%>
						<a <%if(_spec == item['id']){%>class="current"<%}%> data-id="<%=item['id']%>" href="javascript:;"><%=item['name']%></a>
					<%}%>
					<input type="hidden" name="spu[spec_id]" value="<%=_spec%>" />
					</div>
				</div>
				</script>

				<script id="spec_album" type="text/html">
				<%for(var i in goods_specs){%>
        		<%item = goods_specs[i]%>
        		<%if(typeof(item['value']) == 'object'){%>
	        		<%for(var n in item['value']){%>
	        		<%spec_val = item['value'][n]%>
	        		<div class="upload-pic-wrap border bg-white margin-big-top <%if(item['id'] != _spec){%>hidden<%}%> spec_atlas" data-id="<%=item['id']%>" data-md="<%=item['md5'][n]%>">
						<div class="title border-bottom bg-gray-white text-default">
							<b>商品SKU规格：<%=spec_val%></b>
						</div>
						<div class="upload-pic-content clearfix">
							<%for(var j in extra_album[item['md5'][n]]){%>
							<%imgs = extra_album[item['md5'][n]][j]%>
							<div class="box">
								<img src="<%=imgs%>" />
								<div class="operate">
									<i>×</i>
									<a href="javascript:;">默认主图</a>
								</div>
								<input type="hidden" data-name="<%=item['md5'][n]%>" name="album[<%=item['md5'][n]%>][]" value="<%=imgs%>"/>
							</div>
							<%}%>

							<div class="loadpic">
								<label class="load-button" data-id="<%=item['md5'][n]%>" id="upload_<%=item['md5'][n]%>"></label>
							</div>
						</div>
					</div>
					<%}%>
        		<%}else{%>
        			<div class="upload-pic-wrap border bg-white margin-big-top <%if(item['id'] != _spec){%>hidden<%}%> spec_atlas" data-id="<%=item['id']%>" data-md="<%=item['md5']%>">
						<div class="title border-bottom bg-gray-white text-default">
							<b>商品SKU规格：<%=item['value']%></b>
						</div>
						<div class="upload-pic-content clearfix">
							<%for(var j in extra_album[item['md5']]){%>
							<%imgs = extra_album[item['md5']][j]%>
							<div class="box">
								<img src="<%=imgs%>" />
								<div class="operate">
									<i>×</i>
									<a href="javascript:;">默认主图</a>
								</div>
								<input type="hidden" data-name="<%=item['md5']%>" name="album[<%=item['md5']%>][]" value="<%=imgs%>"/>
							</div>
							<%}%>

							<div class="loadpic">
								<label class="load-button" data-id="<%=item['md5']%>" id="upload_<%=item['md5']%>"></label>
							</div>
						</div>
					</div>
        		<%}%>
				<%}%>
				</script>


			</div>
		<script>
			var _spec = <?php echo (int) $goods['spu']['spec_id'] ?>;
			var goods_specs = goods.spu ? goods.spu.specs : null;
			for(var k in goods_specs){
				goods_specs[k].md5 = [];
				if(typeof goods_specs[k].value == "object"){
					for(var i=0;i<goods_specs[k].value.length;i++){
						var md = $.md5(goods_specs[k].name+':'+goods_specs[k].value[i]);
						goods_specs[k].md5.push(md);
					}
				}else{
					var md = $.md5(goods_specs[k].name+':'+goods_specs[k].value);
					goods_specs[k].md5.push(md);
				}
			}
			var spec_select = template('spec_select', {'templateData': goods_specs,'_spec': _spec});

			if(goods_specs && goods_specs.length !== 0){
				$('.spec_box').html(spec_select);
			}

            var extra_album = goods.extra ? goods.extra.album : [];
            var spec_album = template('spec_album', {'goods_specs': goods_specs,'_spec': _spec,'extra_album': extra_album});
			$('.album_box').html(spec_album);

			$(".spec-right-body a").live('click',function(){
				var $id = parseInt($(this).data('id'));
				if($(this).hasClass("current")){
					$(this).removeClass('current');
					$(".spec_atlas").addClass("hidden");
					$(this).parent().find('input').remove();
				}else{
					if(_spec == 0){
						_spec = $id;
					}
					if(_spec != $id){
						if(confirm("更改规格将清空该规格已上传的图片，是否确认更改？")){
							$(this).addClass('current').siblings().removeClass('current');
							var _input = '<input type="hidden" name="spu[spec_id]" value="' + $(this).data('id') + '"/>'
							$(this).parent().find('input').remove();
							$(this).parent().append(_input);
							$(".spec_atlas").each(function(){
								if(parseInt($(this).data("id")) == $id){
									$(this).removeClass("hidden");
								}else{
									$(this).addClass("hidden");
									$(this).find('.box').remove();
								}
							})
							_spec = $id;
						}
					}else{
						$(this).addClass('current').siblings().removeClass('current');
							var _input = '<input type="hidden" name="spu[spec_id]" value="' + $(this).data('id') + '"/>'
							$(this).parent().find('input').remove();
							$(this).parent().append(_input);
							$(".spec_atlas").each(function(){
								if(parseInt($(this).data("id")) == $id){
									$(this).removeClass("hidden");
								}else{
									$(this).addClass("hidden");
								}
							})
					}
				}
			})

			$(".box").live('mouseover',function(){
				$(this).children('.operate').show();
			}).live('mouseout',function(){
				$(this).children('.operate').hide();
			});

			$('.operate a').live('click',function(){
				if($(this).parents(".upload-pic-content").find('.box').length > 1 && !$(this).parents(".box").hasClass("set")){
					$(this).parents(".upload-pic-content").find('.box:first').before($(this).parents(".box"));
				}
				$(this).parents(".box").addClass('set').siblings().removeClass('set');
			});

			$('.operate i').live('click',function(){
				if(confirm("是否删除此图片？"))
				$(this).parents('.box').remove();
			});
			/*上传图片*/
			var imgs = $('.atlas-wrap').find('.load-button');
			var $progress = $('.upload-pic-content').find('.loading').hide();
			init_upload();
			function init_upload(){
				$.each($('.load-button'),function(index,val){
					var i = $(val).attr('data-id');
					var uploader = WebUploader.create({
				        auto:true,
				        fileVal:'upfile',
				        // swf文件路径
				        swf: './statics/js/upload/uploader.swf',
				        // 文件接收服务端。
				        server: "<?php echo url('upload')?>",
				        // 选择文件的按钮。可选
				        formData:{
				            img_id : i,
				            code : '<?php echo $goods["extra"]["attachment_init"]; ?>'
				        },
				        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
				        pick: {
				            id: '#upload_' + i,
				        },
				        accept:{
				            title: '图片文件',
				            extensions: 'jpg,jpeg,bmp,png',
				            mimeTypes: 'image/*'
				        },
				        thumb:{
				            width: '110',
				            height: '110'
				        },
				        chunked: false,
				        chunkSize:1000000,
				        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
				        resize: false
				    });
				   uploader.onUploadSuccess = function( file, response ) {
				   		$('#'+file.id).find('.loading').hide();
				   		var pickid = this.options.pick.id;
				    	var obj = eval("(" + response._raw + ")")
				    	var result = obj.result;
				        if(result.url.length > 0) {
				        	var html =  '<img src="'+ result.url +'" />'
								+		'<div class="operate">'
								+		'<i>×</i>'
								+		'<a href="javascript:;">默认主图</a>'
								+		'</div>'
								+		'<input type="hidden" data-name="' + result.img_id + '" name="album['+ result.img_id +'][]" value="'+ result.url +'"/>';
							$('#'+file.id).append(html);
				        }
				    }
				    uploader.onUploadError = function(file, reason) {
				        alert(reason);
				    }
				    uploader.onError = function( code ) {
				    	if(code == 'Q_TYPE_DENIED'){
				    		alert('图片类型被禁止！');
				    	}else if(code == 'Q_EXCEED_SIZE_LIMIT'){
				    		alert('图片大小超过限制！');
				    	}else{
			            	alert( '图片已在列表，请勿重复上传！');
				    	}
			        };
			        uploader.onUploadProgress = function(file, percentage) {

	                };
			        uploader.onFileQueued = function(file) {
			        	var pickid = this.options.pick.id;
			        	var html = 		'<div class="box" id="' + file.id + '">'
			        			 +		'<div class="loading">'
								 +		'<em>上传中...</em>'
								 +		'<span></span>'
								 +		'</div>';
			        			 +		'</div>';
			            $(pickid).parent().before(html);
			        };
				})
			}
		</script>
