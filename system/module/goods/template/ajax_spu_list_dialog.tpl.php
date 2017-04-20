<?php include template('header','admin');?>
<script type="text/javascript" src="./statics/js/goods/goods_cat.js" ></script>
<script type="text/javascript" src="./statics/js/template.js" ></script>
		<div class="goods-list-search padding-top padding-bottom padding-big-left border-bottom clearfix" >
		<form name="sku_search" action="<?php echo url('goods/admin/ajax_spu_list')?>" method="get">
			<input type="hidden" name="multiple" value="<?php echo (int) $_GET['multiple'] ?>">
			<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
			<div class="top fl">
				<div class="form-group form-layout-rank border-none" style="z-index: 13;">
						<span class="label">按分类查看</span>
						<div class="box ">
							<div class="field">
								<div class="goods-search-class-wrap">
									<div class="form-buttonedit-popup">
										<input class="input" type="text" value="<?php echo $cate['name'] ? $cate['name'] : '请选择分类'?>" readonly="readonly">
										<span class="ico_buttonedit"></span>
										<input type="hidden" name="catid" value="<?php echo $cate['id']?>">
									</div>
									<div class="goods-search-class-content hidden">
										<div class="goods-add-class-wrap layout bg-white">
											<div class="border-light-gray clearfix">
												<div class="form-box padding-none clearfix">
													<div label="常用分类：" id="select" class="hd-select" name="select1" value="cn,us,en,ge" text="中国,美国,英国,德国" desc="商品所选分类仅能为最后一级分类"></div>
												</div>
											</div>
											<div class="goods-add-class clearfix">
												<div class="root border focus">
												</div>
												<div class="child border focus">
												</div>
												<div class="child border focus">
												</div>
												<div class="child border focus">
												</div>
												<p class="layout fl margin-top goods-class-choose">您当前已选择的分类：<span></span>&emsp;<a class="button bg-main fr margin-right" id="confirm-class" href="javascript:;">确认选择</a></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<div class="form-group form-layout-rank border-none">
					<span class="label">搜索</span>
					<div class="box ">
						<div class="field">
							<input class="input" type="text" name="keyword" value="<?php echo $_GET['keyword']?>" data-reset="false" placeholder="输入商品名称/货号/条码" tabindex="0">
						</div>
					</div>
				</div>
			</div>
			<div class="bottom">
				<div class="form-group form-layout-rank border-none" style="z-index: 2;">
				        <span class="label">按品牌查看</span>
				        <div class="box" style="width: 150px;">
			                <div class="form-select-edit select-search-text-box">
			                    <div class="form-buttonedit-popup">
			                        <input class="input" type="text" value="<?php echo $brand['name'] ? $brand['name'] : '商品选择'?>" readonly="readonly">
			                        <span class="ico_buttonedit"></span>
			                        <input type="hidden" name="brand_id" value="<?php echo $brand['id']?>">
			                    </div>
			                    <div class="select-search-field border border-main">
		                    		<input class="input border-none" autocomplete="off" type="text" id="brandname" value="" placeholder="请输入品牌名称"/>
		                    		<i class="ico_search"></i>
		                    	</div>
			                    <div class="listbox-items brand-list">
			                    <?php foreach ($brands AS $brand) {?>
			                    	<span class="listbox-item" data-val="<?php echo $brand['id']?>"><?php echo $brand['name']?></span>
			                    <?php }?>
			                   </div>
			                </div>
				        </div>
				    </div>
			    <div class="search-form-box border-bottom-none">
			    	<div data-label="商品标签" class="hd-select" data-name="select2" data-value="1,2,3" data-text="促销,热卖,新品"></div>
			    </div>
			</div>
			<input class="button bg-gray fl" type="button" data-back="" value="查询">
		</form>
		</div>
		<div class="table border-none high-table opreate-table clearfix">
			<div class="tr border-none">
				<div class="th w45">商品名</div>
				<div class="th w20">货号</div>
				<div class="th w10">价格</div>
				<div class="th w10">库存</div>
				<div class="th w15">操作</div>
			</div>
			<?php foreach ($goods AS $r) {?>
			<div class="tr" data-id="<?php echo $r['id'] ?>" data-sn="<?php echo $r['sn']?>" data-pic="<?php echo $r['thumb'] ?>" data-title="<?php echo $r['name'] ?>" data-spec="<?php echo $r['catname'] ?>" data-price="<?php echo $r['price'] ?>" data-number="<?php echo $r['sku_total'] ?>">
				<div class="td w45">
					<div class="td-con td-pic">
						<div class="pic"><img src="<?php echo $r['thumb'] ?>" /></div>
						<div class="title text-ellipsis txt text-left"><?php echo $r['name']?></div>
						<div class="icon"><?php echo $r['catname']?></div>
					</div>
				</div>
				<div class="td w20 text-left">
					<span class="td-con"><?php echo $r['sn']?></span>
				</div>
				<div class="td w10"><?php echo $r['price'] ? $r['price'] : $r['shop_price']?></div>
				<div class="td w10"><?php echo $_GET['label'] == 1 || !isset($_GET['label']) ? $r['sku_total'] : ($r['number'] ? $r['number'] : 0)?></div>
				<div class="td w15">
				<?php if(!$sku['prom_type'] || $_GET['type'] == 'give') { ?>
					<label class="check-btn button bg-gray"><span>选择</span></label>
				<?php } else { ?>
					<span class="check-btn selected">该商品已参加其他活动</span>
				<?php }?>
				</div>
			</div>
			<?php }?>
			<script id="skus_template" type="text/html">
			<%for(var item in lists){%>
			<%item = lists[item]%>
			<div class="tr" data-id="<%=item['id']%>" data-sn="<%=item['sn']%>" data-pic="<%=item['thumb']%>" data-title="<%=item['name']%>" data-spec="<%=item['catname']%>" data-price="<%=item['price']%>" data-number="<%=item['sku_total']%>">
				<div class="td w45">
					<div class="td-con td-pic">
						<div class="pic"><img src="<%=item['thumb']%>" /></div>
						<span class="title text-ellipsis txt text-left"><%=item['name']%></span>
						<div class="icon"><%=item['catname']%></div>
					</div>
				</div>
				<div class="td w20 text-left">
					<span class="td-con"><%=item['sn']%></span>
				</div>
				<div class="td w10"><%=item['price']%></div>
				<div class="td w10"><%=item['sku_total']%></div>
				<div class="td w15">
					<%if(!item['prom_type'] || prom_type == 'give'){%>
						<label class="check-btn button bg-gray"><span>选择</span></label>
					<% }else{%>
						<span class="check-btn selected">该商品已参加其他活动</span>
					<%}%>
				</div>
			</div>
			<% }%>
			</script>
			<div class="layout paging padding-tb body-bg clearfix">
				<?php echo $pages;?>
			</div>
		</div>
		<div class="padding text-right ui-dialog-footer">
			<input type="submit" class="button margin-left bg-sub" value="确定" />
			<input type="reset" class="button margin-left bg-gray" value="关闭" />
		</div>
		<style>
		.goods-search-class-content {left:-70px;}
		</style>
		<script>
			$(function(){
				try {
					var dialog = top.dialog.get(window);
				} catch (e) {
					return;
				}
				var $val=$("input[type=text]").eq(1).val();
				$("input[type=text]").eq(1).focus().val($val);
				dialog.reset();     // 重置对话框位置

				var multiple = "<?php echo (int) $_GET['multiple'] ?>"; // 定义本次页面是否允许多项选择
				var selected = dialog.selected; // 接收页面传来的已传值
				var removeids = dialog.removeids //移除商品id

				_selected();

				/* 执行点击事件 */
				$("label.button").live('click', function() {
					var $_this = $(this).parents('.tr'),
						$_id = $_this.data('id');
					if($(this).hasClass('bg-sub') == true) {
						$(this).removeClass("bg-sub").addClass("bg-gray").find('span').text("选择");
						/* 如果仅允许单选 */
						if(multiple == 0) {
							$(this).parents(".tr").siblings().find('label.button').removeClass('bg-sub').addClass('bg-gray').find('span').text("选择");
						}
					} else {
						$(this).removeClass("bg-gray").addClass("bg-sub").find('span').text("已选");
						/* 如果仅允许单选 */
						if(multiple == 0) {
							$(this).parents(".tr").siblings().find('label.button').removeClass('bg-sub').addClass('bg-gray').find('span').text("选择");
						}
					}
					_callback($_id);
				})

				function _selected() {
					var _selected = '<label class="check-btn button bg-sub"><span>已选</span></label>';
					var _checked = '<label class="check-btn button bg-gray"><span>选择</span></label>';
					if(multiple == 0) {
						$(".tr[data-id='"+ selected.id +"']div:last .w15").html(_selected);
					} else {
						//移除商品
						if(removeids){
							$.each(removeids,function(i,n){
								$(".tr[data-id='"+ i +"'] div:last").html(_checked);
							});
						}
						//选中商品
						$.each(selected, function(i ,n){
							$(".tr[data-id='"+ i +"'] div:last").html(_selected);
						});
					}
				}
				function _callback(id) {
					id = id || 0;
					var $_this = $(".tr[data-id='"+ id +"']");
					if($_this.length > 0) {
						var	$_val = {}
						if($_this.find("label.button").hasClass('bg-sub')) {
							$_val = {
								"id" : $_this.data('id'),
								"pic" : $_this.data('pic'),
								"title" : $_this.data('title'),
								"spec" : $_this.data('spec'),
								"price" : $_this.data('price'),
								"number" : $_this.data('number')
							}
						}
						if(multiple == 0) {
							selected = $_val;
						} else {
							selected[id] = $_val;
						}
					}

					var callback = {};
					if(multiple == 1) {
						$.each(selected, function(i ,n) {
							if($.isEmptyObject(n) == false) {
								callback[i] = n;
							}
						})
						selected = callback;
					}
					return selected;
				}
				$('input[type=submit]').on('click', function () {
					dialog.close(_callback());
					dialog.remove();
					return false;
				});
				$('input[type=reset]').on('click', function () {
					dialog.remove();
					return false;
				});


				$('.select-search-field').click(function(e){
					e.stopPropagation();
				});

				//buttonedit-popup-hover
				$('.select-search-text-box .form-buttonedit-popup').click(function(){
					if($(this).hasClass('buttonedit-popup-hover')){
						$(this).nextAll('.select-search-field').show();
						$(this).nextAll('.select-search-field').children('.input').focus();
						$(this).nextAll('.listbox-items').show();
					}else{
						$(this).nextAll('.select-search-field').hide();
						$(this).nextAll('.listbox-items').hide();
					}
				});

				$('#brandname').live('keyup',function(){
					var url = "<?php echo url('goods/admin/ajax_brand')?>";
					var brandname = this.value;
					$.post(url,{brandname:brandname},function(data){
						$('.brand-list').children('.listbox-item').remove();
						if(data.status == 1){
							var html = '';
							$.each(data.result,function(i,item){
								 html += '<span class="listbox-item" data-val="' + i + '">' + item + '</span>';
							})
							$('.brand-list').append(html);
						}else{
							var html = '<span class="listbox-item">未搜索到结果</span>';
							$('.brand-list').append(html);
						}
					},'json')
				})
				$(".select-search-text-box .listbox-items .listbox-item").live('click',function(){
					$(this).parent().prev('.select-search-field').children('.input').val();
					$(this).parent().prev('.select-search-field').hide();
					$('.select-search-text-box .form-buttonedit-popup .input').val($(this).html());
					$('input[name=brand_id]').val($(this).attr('data-val'));
				});
				$('body').click(function(){
					$('.select-search-text-box .select-search-field').hide();
					$('.select-search-text-box .listbox-items').hide();
				});


				$('.goods-search-class-wrap .form-buttonedit-popup').click(function(){
					if($('.goods-search-class-content').hasClass('hidden')){
						$(this).addClass("buttonedit-popup-hover");
						$('.goods-search-class-content').removeClass('hidden');
					}else{
						$(this).removeClass("buttonedit-popup-hover");
						$('#confirm-class').trigger('click');
						$('.goods-search-class-content').addClass('hidden');
					}
				});
				$('.goods-add-class .root a, .goods-add-class .child a').live('click',function(){
					//在下方已选择分类显示
					$('.goods-search-class-wrap .goods-class-choose span').html(classNameText());
					$('input[name=catid]').val(classId());
				});
				$('#confirm-class').click(function(){
					if(classNameText()==""){
						$('.goods-search-class-wrap .form-buttonedit-popup .input').val("请选择分类");
					}else{
						$('.goods-search-class-wrap .form-buttonedit-popup .input').val(classNameText());
					}
					$('.goods-search-class-wrap .form-buttonedit-popup').removeClass("buttonedit-popup-hover");
					$('.goods-search-class-content').addClass('hidden');
				});

				 //格式化分类
				jsoncategory = <?php echo json_encode($category) ?> ;
			    nb_category(0, '.root');

				$('#confirm-class').click(function(){
					if(classNameText()==""){
						$('.goods-search-class-wrap .form-buttonedit-popup .input').val("请选择分类");
					}else{
						$('.goods-search-class-wrap .form-buttonedit-popup .input').val(classNameText());
					}
					$('.goods-search-class-wrap .form-buttonedit-popup').removeClass("buttonedit-popup-hover");
					$('.goods-search-class-content').addClass('hidden');
				});
				//ajax查询商品
				$('input[type="button"]').live('click',function(){
					$("input[name=page]").attr("value", 1);
					ajax_lists();
					return false;

				})
				function ajax_lists(){
					$_form = $('form[name=sku_search]');
					var url = $_form.attr("action");
					var parmas = $_form.serializeArray();
					var prom_type = '<?php echo $_GET['type']?>';
					$.get(url,parmas,function(ret){
						$('.table .tr:gt(0)').remove();
						$('.body-bg').html('');
						if(ret.count > 0){
							var goodsRowHtml = template('skus_template', {'lists': ret.lists, 'selected':selected, 'prom_type':prom_type});
							$('.table .tr').after(goodsRowHtml);
							$('.body-bg').html(ret.pages);
							_selected();
						}else{
							$('.table .tr').append('<div class="tr">很抱歉，没有查询到相关商品！</div>')
						}
					},'json')
				}
				//ajax分页查询
				$('.body-bg a').live('click', function() {
					var page = $.urlParam('page', $(this).attr('href'));
					$("input[name=page]").attr("value", page);
					ajax_lists()
					return false;
				})
				//获取分页的id
				$.urlParam = function(name, url){
				    var url = url || window.location.href;
				    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
				    if(!results) return false;
				    return results[1] || 0;
				}
				//组织分类名称
				function classNameText(){
					var _txt = '';
					$('.goods-add-class div.focus').each(function(){
						if($(this).find("a.focus").html()!=null){
							if($(this).index()==0){
								_txt += $(this).find("a.focus").html();
							}else{
								_txt += '>'+$(this).find("a.focus").html();
							}
						}
					})
					return _txt;
				}
				function classId(){
					var _txt = '';
					$('.goods-add-class div.focus').each(function(){
						if($(this).find("a.focus").html()!=null){
							_txt = $(this).find("a.focus").attr('data-id');
						}
					})
					return _txt;
				}
			})
		</script>
<?php include template('footer','admin');?>
