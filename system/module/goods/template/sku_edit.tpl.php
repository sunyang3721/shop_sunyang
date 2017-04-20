<?php include template('header','admin');?>
<script type="text/javascript" src="./statics/js/goods/goods_add.js" ></script>
<link type="text/css" rel="stylesheet" href="./statics/js/upload/uploader.css" />
<script type="text/javascript" src="./statics/js/upload/uploader.js"></script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">商品设置</li>
				<li class="spacer-gray"></li>
				<li><a class="current" href="javascript:;">基本信息</a></li>
				<li><a href="javascript:;">商品价格</a></li>
				<li><a href="javascript:;">商品图册</a></li>
				<li><a href="javascript:;">商品详情</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
		<form action="<?php echo url('sku_edit')?>" method="post" name="sku_edit">
			<div class="form-box margin-bottom goods-form sku-tab clearfix">
				<?php echo form::input('text', 'sku_name', $info['sku_name'],'商品名称：','商品标题名称不能为空，最长不能超过200个字符',array('datatype'	=> '*','nullmsg'	=> '商品名称不能为空')); ?>
				<input type="hidden" name="sku_id" value="<?php echo $info['sku_id']?>">
			 	<?php echo form::input('text', 'sn', $info['sn'],'商品货号：','请填写商品货号',array('datatype'	=> '*','nullmsg'	=> '商品名称不能为空')); ?>
				<input type="hidden" name="sku_id" value="<?php echo $info['sku_id']?>">
				<?php echo form::input('text', 'barcode', $info['barcode'],'商品条形码：','请填写商品条形码');?>
				<input type="hidden" name="sku_id" value="<?php echo $info['sku_id']?>">
				<?php echo form::input('text', 'subtitle', $info['subtitle'], '广告语：', '商品广告语是用于介绍商品的描述信息',array('color' => $info['style'] ? $info['style'] : '', 'key' => 'style')); ?>
				<?php echo form::input('text', 'warn_number', isset($info['warn_number']) ? $info['warn_number'] : 5,  '库存警告：', '填写商品库存警告数，当库存小于等于警告数，系统就会提醒此商品为库存警告商品，系统默认为5',array('datatype'	=> 'n','errormsg' => '库存警告只能为数字')); ?>
				<?php echo form::input('enabled','status', isset($info['status']) ? $info['status'] : '1','是否上架销售：','设置当前商品是否上架销售，默认为是，如选择否，将不在前台显示该商品',array('itemrows' => 2)); ?>
				<?php echo form::input('enabled','show_in_lists', isset($info['show_in_lists']) ? $info['show_in_lists'] : '1','是否前台页面展示：','是否前台页面展示',array('itemrows' => 2)); ?>
				<?php echo form::input('text', 'keyword', $info['keyword'] ? $info['keyword'] : '', '商品关键词：', 'Keywords项出现在页面头部的<Meta>标签中，用于记录本页面的关键字，多个关键字请用分隔符分隔'); ?>
				<?php echo form::input('textarea','description', $info['description'] ? $info['description'] : '', '商品描述：','Description出现在页面头部的Meta标签中，用于记录本页面的高腰与描述，建议不超过80个字'); ?>
			</div>
			<div class="form-box margin-bottom sku-tab hidden clearfix">
				<?php echo form::input('text', 'shop_price', $info['shop_price'],'销售价格：','商品标题名称不能为空，最长不能超过200个字符',array('datatype'	=> 'price','nullmsg'	=> '价格格式错误')); ?>
				<?php echo form::input('text', 'market_price', $info['market_price'],'市场价格：','商品标题名称不能为空，最长不能超过200个字符',array('datatype'	=> 'price','nullmsg'	=> '价格格式错误')); ?>
				<?php echo form::input('text', 'number', $info['number'],'商品库存：','商品标题名称不能为空，最长不能超过200个字符',array('datatype'	=> 'n','nullmsg'	=> '库存必须为数字')); ?>
				<?php echo form::input('radio', 'status_ext', $info['status_ext'] ? $info['status_ext'] : 0, '促销状态：', '表单描述', array('items' => array('取消', '促销', '热卖','新品','推荐'), 'colspan' => 3)); ?>
			</div>
			<div class="padding sku-tab hidden">
				<div class="upload-pic-wrap border bg-white">
					<div class="title border-bottom bg-gray-white text-default">
						<b>商品图片</b>
					</div>
					<div class="upload-pic-content clearfix">
					<?php if(!empty($info['img_list'])){?>
					<?php foreach ($info['img_list'] AS $url) {?>
						<div class="box">
							<img src="<?php echo $url?>" />
							<div class="operate">
								<i>×</i>
								<a href="javascript:;">默认主图</a>
								<input type="hidden" name="images[]" value="<?php echo $url?>"/>
							</div>
						</div>
					<?php }?>
					<?php }?>
					<div class="loadpic" >
							<label class="load-button" id="upload"></label>
						</div>
					</div>
				</div>
			</div>
			<div class="padding sku-tab hidden">
				<?php echo form::editor('content', $info['content'], '', '', array('mid' => $admin['id'], 'path' => 'goods')); ?>
			</div>
			<div class="padding">
				<input type="submit" id="release" class="button bg-main" value="提交" />
				<a href="<?php echo url('index')?>"><input type="button" class="button margin-left bg-gray" data-reset="false" value="返回" /></a>
			</div>
		</form>
		</div>
		<script type="text/javascript" src="./statics/js/haidao.validate.js?v=5.3.2" ></script>
		<script>
			$(window).load(function(){
				$(".cxcolor").find("table").removeClass("hidden");
			})
			$(function(){
				var $val=$("input[type=text]").eq(0).val();
				$("input[type=text]").eq(0).focus().val($val);	
				var sku_edit = $("[name=sku_edit]").Validform({
					ajaxPost:false,
				});
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
			var uploader = WebUploader.create({
		        auto:true,
		        fileVal:'upfile',
		        // swf文件路径
		        swf: './statics/js/upload/uploader.swf',
		        // 文件接收服务端。
		        server: "<?php echo url('upload')?>",
		        // 选择文件的按钮。可选
		        formData:{
		            code : '<?php echo $attachment_init; ?>'
		        },
		        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
		        pick: {
		            id: '#upload',
		        },
		        accept:{
		            title: '图片文件',
		            extensions: 'gif,jpg,jpeg,bmp,png',
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
						+		'<input type="hidden" name="images[]" value="'+ result.url +'"/>';
					$('#'+file.id).append(html);
		        }
		    }
		    uploader.onUploadError = function(file, reason) {
		        alert(reason);
		    }
		    uploader.onError = function( code ) {
	            alert( '图片已在列表，请勿重复上传！');
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
	        
	        $(".fixed-nav li a").click(function(){
	        	$(".fixed-nav li a").removeClass("current");
	        	$(this).addClass("current");
	        	console.log($(this).index("a"))
				$(".sku-tab").eq($(this).index("a")).removeClass("hidden").siblings(".sku-tab").addClass("hidden");
	        })
		</script>
<?php include template('footer','admin');?>
