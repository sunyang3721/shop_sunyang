<?php include template('header','admin');?>
<script type="text/javascript" src="./statics/js/dialog/dialog-plus-min.js" ></script>
<script src="./statics/js/cxcolor/jquery.cxcolor.min.js" charset="utf-8" type="text/javascript"></script>
<script type="text/javascript" src="./statics/js/template.js" ></script>
<link rel="stylesheet" href="./statics/js/dialog/ui-dialog.css" />
<link rel="stylesheet" href="./statics/js/cxcolor/jquery.cxcolor.css" type="text/css">
<link type="text/css" rel="stylesheet" href="./statics/js/upload/uploader.css" />
<script type="text/javascript" src="./statics/js/upload/uploader.js"></script>
		<div class="goods-spec-popup layout">
			<div class="setspec-popup bg-white">
				<div class="left fl">
					<p>请选择规格 [可多选]</p>
					<div class="spec-left-body border">
						<?php foreach ($specs AS $spec) {?>
						<a data-id="<?php echo $spec['id']?>" class="specname" href="javascript:;"><?php echo $spec['name']?><em>[0]</em><i>×</i></a>
						<?php }?>
						<a class="new-spec" href="javascript:;"><i class="ico_plus margin-lr"></i>添加新规格</a>
					</div>
				</div>
				<div class="right">
					<p>请选择规格 [可多选]</p>
					<div class="spec-right-body">
						<div class="top border spec_choose">
							<div class="title spec_title">
								<label><input class="margin-small-right" id="checkAll" type="checkbox" />全选</label>
								<p class="hidden">请填写您要添加的新规格名称</p>
								<div class="spec_add hidden">
									<input class="input radius-none fl" type="text" name="new_value" placeholder="新属性名称(可用回车提交)">
								<input class="button bg-sub fl" id="sub-value" type="button" value="确定">
								</div>
							</div>
							<?php foreach ($specs AS $spec) {?>
							<div class="wrap hidden spec-num" data-id="<?php echo $spec['id']?>">
								<?php foreach ($spec['value'] AS $v) {?>
									<a data-id="<?php echo $spec['id']?>" data-name="<?php echo $spec['name']?>" data-value="<?php echo $v?>" data-style="" data-color="" data-img="" href="javascript:;"><?php echo $v?><i>×</i></a>
								<?php }?>
								<span class="new-prop" data-id="<?php echo $spec['id']?>"><i class="ico_plus margin-right"></i>添加属性</span>
							</div>
							<?php }?>
							<script id="spec_value" type="text/html">
								<div class="wrap hidden spec-num" data-id="<%=spec_id%>">
									<span class="new-prop" data-id="<%=spec_id%>"><i class="ico_plus margin-right"></i>添加属性</span>
								</div>
							</script>
							<div class="wrap hidden">
								<input class="input radius-none" type="text" name="new_spec" placeholder="请输入新规格名称">
								<input class="button bg-sub margin-top" id="sub-spec" type="button" value="确定">
							</div>
							<p class="no-choose-tip">请选择左边规格列表</p>
						</div>
						<div class="bottom margin-top border">
							<div class="title">
								<span class="fl">规格展现方式</span>
								<span class="fr padding-right text-sub">请指定规格在前台的展现方式</span>
							</div>
							<?php foreach ($specs AS $k => $spec) {?>
							<div class="table-wrap" data-id="<?php echo $spec['id']?>">
								<div class="fixed-tr">
									<div class="th w33">
										<label><input class="choose-show margin-right" type="radio" name="type[<?php echo $k?>]" value="0" checked="checked" />文字展现</label>
									</div>
									<div class="th w33">
										<label><input class="choose-show margin-right" type="radio" name="type[<?php echo $k?>]" value="1" />颜色展现</label>
									</div>
									<div class="th w34">
										<label><input class="choose-show margin-right" type="radio" name="type[<?php echo $k?>]" value="2" />图片展现</label>
									</div>
									<div class="bg-box"><div class="bg-block"></div></div>
								</div>
								<div class="table">
									<div class="table-h">

									</div>
								</div>
							</div>
							<?php }?>
							<p class="no-choose-tip">请选择上方规格</p>
							<script id="spec_show" type="text/html">
							<div class="table-wrap" data-id="<%=spec_id%>">
								<div class="fixed-tr">
									<div class="th w33">
										<label><input class="choose-show margin-right" type="radio" name="type[<%=spec_id%>]" value="0" checked="checked" />文字展现</label>
									</div>
									<div class="th w33">
										<label><input class="choose-show margin-right" type="radio" name="type[<%=spec_id%>]" value="1" />颜色展现</label>
									</div>
									<div class="th w34">
										<label><input class="choose-show margin-right" type="radio" name="type[<%=spec_id%>]" value="2" />图片展现</label>
									</div>
									<div class="bg-box"><div class="bg-block"></div></div>
								</div>
								<div class="table">
									<div class="table-h">

									</div>
								</div>
							</div>
							</script>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<div class="padding text-right ui-dialog-footer">
				<input type="button" class="button bg-main" id="okbtn" value="生成" />
				<input type="button" class="button margin-left bg-gray" id="closebtn" value="取消" />
			</div>
		</div>
		<script type="text/javascript">
			$(function(){
				//根据选择规格显示相应值容器
				$(".spec-left-body a").live("click", function() {
					$(".spec-right-body .top .no-choose-tip").addClass("hidden");
					$("#checkAll").attr("checked", false);
					$(this).addClass("current").siblings().removeClass("current");
					$(".spec-right-body .wrap").addClass("hidden").removeClass("now_wrap");
					$(".spec-right-body .wrap").eq($(this).index()).removeClass("hidden").addClass("now_wrap");

					if($(this).hasClass("new-spec")){
						$(".spec-right-body .title").children("p").removeClass("hidden");
						$(".spec-right-body .title").children("label").addClass("hidden");
						$(".spec-right-body .title").children(".spec_add").addClass("hidden");
					}else{
						$(".spec-right-body .title").children("label").removeClass("hidden");
						$(".spec-right-body .title").children("p").addClass("hidden");
						$(".spec-right-body .title").children(".spec_add").addClass("hidden");
					}
					spec_select();
				});

				$(".specname i").live('click', function(e){
					e.stopPropagation();
					if(confirm("是否删除此规格？")){
						var $parent = $(this).parent();
						$.post("<?php echo url('goods/spec/ajax_del')?>",{"id":$parent.data("id")},function(ret){
							if(ret.status == 1){
								$parent.remove();
								$.each($(".spec-right-body .spec-num"), function() {
									if($(this).data("id") == $parent.data("id")){
										$(this).remove();
									}
								});
								$.each($(".spec-right-body .table-wrap"), function() {
									if($(this).data("id") == $parent.data("id")){
										$(this).remove();
									}
								});
							}
						},'json');
					}
				})

				function scrollBar(i){
					var $o = $(".table-wrap").eq(i);
					if($o.find(".table-h").children(".tr").length>0){
						$(".table-wrap").eq(i).show().siblings(".table-wrap").hide();
						$(".spec-right-body .bottom .no-choose-tip").hide();
					}else{
						$(".table-wrap").hide();
						$(".spec-right-body .bottom .no-choose-tip").show();
					}

					if($o.find(".table-h").height()>$o.find(".table").height()){
						$o.find(".fixed-tr").addClass("p-right");
					}else{
						$o.find(".fixed-tr").removeClass("p-right");
					}
					$o.find("[type='radio']:checked").trigger("click");

					$(".table-wrap").each(function(i,item) {
						$(item).find('label').on('click',function(){
							var type = $(item).find('input:radio:checked').val();
							$('.spec_choose a[data-id="' + $(item).data('id') + '"]').attr('data-style',type);
						})
					});
				}

				$(".choose-show").click(function(){
					var i = $(this).val();
					var obj = $(this).parents(".table-wrap").find(".bg-block");
					obj.show();
					if(i==2){
						obj.css({left:"66%",width:"34%"})
					}else{
						obj.css({left:i*33+"%",width:"33%"})
					}
				});
				/**
				 *选择规格值处理
				 */
				function spec_select() {
					var current_spec = $(".spec-right-body .now_wrap");
					var num = 0;
					$("a",current_spec).each(function(){
						if($(this).hasClass("current")){
							num++;
						}
					});
					$(".spec-left-body a").eq(current_spec.index()-1).children("em").text(num<0?"[0]":"["+num+"]");
					if($("a",current_spec).length==num && num>0){
						$("#checkAll").attr("checked",true);
					}else{
						$("#checkAll").attr("checked",false);
					}
					scrollBar($(".now_wrap").index()-1);
				}
				var html = '';
				function spec_show(index,spec){
					html = '<div class="tr" data-title="' + index + '" title="'+index+'">'
						+		'<div class="td w33" style="height:25px;cursor:pointer;">' + index + '</div>'
						+		'<div class="td w33">'
						+			'<input class="color-choose input_cxcolor" type="text" readonly="readonly" style="background-color:;" value="">'
						+		'</div>'
						+		'<div class="td w34">'
						+			'<div class="pic-center">'
						+				'<img src="./statics/images/default_no_upload.png"/>'
						+			'</div>'
						+			'<a class="txt upload">添加图片</a>'
						+		'</div>'
						+	'</div>';
					$('div[data-id="' + spec + '"]').find('.table-h').append(html);
					$('div[data-id="' + spec + '"]').find(".input_cxcolor").cxColor();
					create_upload(index);
				}
				function spec_del(index){
					$('[data-title="'+ index +'"]').remove();
				}
				//全选
				$("#checkAll").on("click",function(){
					if($(this).is(":checked")){
						$(".spec-right-body .now_wrap").find("a").addClass('current');
						$(".spec-right-body .now_wrap").find("a").each(function(){
							var spec_id = $(this).data('value');
							var specs = $(this).data('id');
							spec_show(spec_id,specs);
						})
					}else{
						$(".spec-right-body .now_wrap").find("a").removeClass('current');
						$(".spec-right-body .now_wrap").find("a").each(function(){
							var spec_id = $(this).data('value');
							spec_del(spec_id);
						})
					}
					spec_select();
				});

				//点击规格值
				$(".spec_choose a").live('click',function(){
					var spec_id = $(this).attr('data-value');
					var specs = $(this).data('id');
					if($(this).hasClass("current")){
						$(this).removeClass('current');
						spec_del(spec_id);
					}else{
						$(this).addClass('current');
						spec_show(spec_id,specs);
					}
					spec_select();
				})
				//删除规格值
				$(".spec_choose a i").live('click', function(e){
					e.stopPropagation();
					if(confirm("是否删除此规格值？")){
						var $parent = $(this).parent();
						var $title = $parent.data("value");
						$.post("<?php echo url('goods/spec/del_spec_val')?>",{"id": $parent.data("id"),"value": $parent.data("value")},function(ret){
							if(ret.status == 1){
								$parent.remove();
								$.each($(".spec-right-body .table-wrap"), function() {
									if($(this).data("id") == $parent.data("id")){
										$.each($(this).find(".tr"), function() {
											if($(this).data("title") == $title){
												$(this).remove()
											}
										});
									}
								});
							}
						},'json');
					}
				});

				//添加新属性
				$(".spec-right-body .new-prop").live('click',function(){
					if($(this).hasClass("current")){
						$(this).removeClass('current');
						$(".spec-right-body .title").children("label").removeClass("hidden");
						$(".spec-right-body .title").children(".spec_add").addClass("hidden");
						$(".spec-right-body .title").children("p").addClass("hidden");
						$(this).html('<i class="ico_plus margin-right"></i>添加新属性');
					}else{
						$(this).addClass('current');
						$(".spec-right-body .title").children(".spec_add").removeClass("hidden");
						$(".spec-right-body .title").children("p").addClass("hidden");
						$(".spec-right-body .title").children("label").addClass("hidden");
						$(".spec_add").find('.input').attr('data-id',$(this).attr('data-id'));
						$(this).html('<i class="ico_plus margin-right"></i>取消添加');
					}
				});
				// 提交添加新规格
				function add_spec(){
					var new_spec = $.trim($('input[name="new_spec"]').val());
					var spec_url = "<?php echo url('spec/ajax_add_spec')?>";
					if (new_spec.length < 1) {
						//alert('请填写新的规格名称');
						return false;
					}
					$.post(spec_url,{
						name : new_spec
					},function(ret){
						if (ret.status == 1) {
							$('.new-spec').before('<a data-id="'+ ret.result.id +'" class="specname" href="javascript:;">'+ ret.result.name +'<em>[0]</em><i>×</i></a>');
							var spec_list = template('spec_value', {'spec_id': ret.result.id});
							var spec_show = template('spec_show',{'spec_id':ret.result.id});
							if($('.spec-num').length > 0){
								$('.spec-num:last').after(spec_list);
								$('.table-wrap:last').after(spec_show);
							}else{
								$('.spec_title').after(spec_list);
								$('.bottom .title').after(spec_show);
							}
							$('input[name="new_spec"]').val('');
						} else {
							alert(ret.message);
							return false;
						}
					},'json');
				}
				$('#sub-spec').bind('click',function() {
					add_spec();
				});
				$('input[name="new_spec"]').live('keypress',function(event){
					 if(event.keyCode == "13"){
					 	add_spec();
					 }
				})
				// 提交添加新属性
				function add_spec_value(){
					var new_value = $('input[name="new_value"]').val();
					var spec_id = $('input[name="new_value"]').attr('data-id');
					var pop_url = "<?php echo url('spec/ajax_add_pop')?>"
					if (new_value.length < 1) {
						alert('属性值不能为空');
						return false;
					}
					if (spec_id < 1) {
						alert('该规格不存在或规格ID有误');
						return false;
					}
					$.post(pop_url,{
						spec_id : spec_id,
						new_value : new_value
					},function(ret){
						if (ret.status == 1) {
							$('div[data-id="'+ ret.result.id +'"] .new-prop').before('<a data-id="'+ret.result.id+'" data-name="'+ret.result.name+'" data-value="'+ret.result.value+'" data-style="" data-color="" data-img="" href="javascript:;">'+ret.result.value+'<i>×</i></a>');
							$('input[name="new_value"]').val('');
						} else {
							alert(ret.message);
							return false;
						}
					},'json');
				}
				$('input[name="new_value"]').live('keypress',function(event){
					if(event.keyCode == "13"){
						add_spec_value();
					}
				})
				$('#sub-value').bind('click',function() {
					add_spec_value();
				});
			})


		  	$(window).load(function(){
				try {
					var dialog = top.dialog.get(window);
					dialog.reset();
					dialog.title('请选择规格');
				} catch (e) {
					return;
				}
				var selectArr = new Array();
				var selectedItem = dialog.data;
				//显示已选择的项目
				$.each(selectedItem,function(key,val){
					$("a[data-id='"+val.id+"'][data-value='"+val.value+"']").addClass('current');
					num = parseInt($("a[data-id='"+val.id+"'] em").text().replace('[','').replace(']',''));
					$("a[data-id='"+val.id+"'][data-value='"+val.value+"']").attr({"data-style":val.style,"data-img":val.img,"data-color":val.color});
					$("a[data-id='"+val.id+"'] em").text('['+(++num)+']');

					selectArr.push(val.id);
					spec_img = val.img ? val.img :'./statics/images/default_no_upload.png';
					var html = '<div class="tr" data-title="' + val.value + '">'
						+		'<div class="td w33">' + val.value + '</div>'
						+		'<div class="td w33">'
						+			'<input class="color-choose input_cxcolor" type="text" readonly="readonly" style="background-color:' + val.color + ';" value="' + val.color + '">'
						+		'</div>'
						+		'<div class="td w34">'
						+			'<div class="pic-center">'
						+				'<img src="' + spec_img + '"/>'
						+			'</div>'
						+			'<a class="txt upload">添加图片</a>'
						+		'</div>'
						+	'</div>';
					$('div[data-id="' + val.id + '"]').find('.table-h').append(html);
					$('div[data-id="' + val.id + '"]').find('input[type=radio][value="' + val.style + '"]').attr('checked','checked');
				});
				spec_create();

				$(".input_cxcolor").cxColor();//调用颜色选择器插件

				// 重置对话框位置
				$('#okbtn').on('click', function () {
					$('.current').each(function() {
						var _this = $(this);
						if(_this.attr('data-id') && $.inArray(_this.attr('data-id'),selectArr) == -1){
							$('.spec-right-body .current').attr('data-type',1);
						}
						var color = $('[data-title="' + _this.data('value') + '"]').find('.input_cxcolor').val();
						if(color != undefined){
							_this.attr('data-color',color);
						}
					});
					var addSpecObject = $('.spec-right-body .current');
					dialog.close(addSpecObject); // 关闭（隐藏）对话框
					dialog.remove();	// 主动销毁对话框
					return false;
				});
				$('#closebtn').on('click', function () {
					dialog.remove();
					return false;
				});
			})
			function spec_create(){
		  		$.each($('.table-h .tr'),function(index,val){
					create_upload($(val).data('title'));
				})
		  	}
		  	function create_upload(i){
		  		var uploader = WebUploader.create({
			        auto:true,
			        fileVal:'upfile',
			        // swf文件路径
			        swf: '<?php echo __ROOT__;?>statics/js/upload/uploader.swf',
			        // 文件接收服务端。
			        server: "<?php echo url('attachment/index/upload')?>",
			        // 选择文件的按钮。可选
			        formData:{
			            img_id : i,
			            upload_init : '<?php echo $attachment_init; ?>'
			        },
			        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
			        pick: {
			            id: '.tr[data-title="'+ i +'"] .upload',
			        },
			        accept:{
			            title: '图片文件',
			            extensions: 'gif,jpg,jpeg,bmp,png',
			            mimeTypes: 'image/*'
			        },
			        thumb:{
			            width: '50',
			            height: '50'
			        },
			        chunked: false,
			        chunkSize:1000000,
			        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
			        resize: false
			    });
			   	uploader.onUploadSuccess = function( file, response ) {
			   		var pickid = this.options.pick.id;
			    	var obj = eval("(" + response._raw + ")")
			        var result = obj.result;
			        if(result.url.length > 0) {
			        	$(pickid).parent('.td').find('img').attr('src',result.url);
			        	$('a[data-value="' + i + '"]').attr('data-img',result.url);
			        	$(pickid).find('.webuploader-pick').text('更换图片');
			        }
			    }
			    uploader.onUploadProgress = function(file, percentage) {
			    	var pickid = this.options.pick.id;
	        		$(pickid).find('.webuploader-pick').text('上传中');
            	};
			    uploader.onUploadError = function(file, reason) {
			        alert(reason);
			    }
		  	}
		</script>
<?php include template('footer','admin');?>
