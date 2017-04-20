<?php include template('header','admin');?>
	<script type="text/javascript" src="./statics/js/goods/goods_cat.js" ></script>
	<script type="text/javascript" src="./statics/js/goods/goods_list.js"></script>
	<script type="text/javascript" src="./statics/js/template.js" ></script>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">商品列表<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a class="labelbox" data-id="1" href="<?php echo url('index',array('label' => 1))?>">全部商品</a></li>
				<li><a class="labelbox" data-id="2" href="<?php echo url('index',array('label' => 2))?>">下架商品</a></li>
				<li><a class="labelbox" data-id="3" href="<?php echo url('index',array('label' => 3))?>">库存警告</a></li>
				<li><a class="labelbox" data-id="4" href="<?php echo url('index',array('label' => 4))?>">回收站</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="tips margin-tb">
				<div class="tips-info border">
					<h6>温馨提示</h6>
					<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
				</div>
				<div class="tips-txt padding-small-top layout">
					<p>- 双击商品列表页标题可以快速编辑商品标题</p>
					<p>- 新增商品后会生成所有规格商品，每一个规格商品均有独立属性，可自行修改</p>
					<p>- 主商品的信息会继承给规格商品，主商品不在前台作为展示</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="goods-list-search margin-top clearfix" >
			 <form action="<?php echo __APP__?>" method="get">
			 <input type="hidden" name="m" value="goods">
			 <input type="hidden" name="c" value="admin">
			 <input type="hidden" name="a" value="index">
			 <input type="hidden" name="label" value="<?php echo $_GET['label'] ? $_GET['label'] : 1?>">
				<div class="top layout margin-bottom fl">
					<div class="form-group form-layout-rank border-none" style="z-index: 13;">
						<span class="label">按分类查看</span>
						<div class="box ">
							<div class="field">
								<div class="goods-search-class-wrap">
									<div class="form-buttonedit-popup">
										<input class="input" name="" type="text" value="<?php echo $cate['name'] ? $cate['name'] : '请选择分类'?>" readonly="readonly" data-reset="false">
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
					<div class="form-group form-layout-rank border-none" style="z-index: 2;">
				        <span class="label">按品牌查看</span>
				        <div class="box" style="width: 150px;">
			                <div class="form-select-edit select-search-text-box">
			                    <div class="form-buttonedit-popup">
			                        <input class="input" type="text" value="<?php echo $brand['name'] ? $brand['name'] : '商品选择'?>" readonly="readonly" data-reset="false">
			                        <span class="ico_buttonedit"></span>
			                        <input type="hidden" name="brand_id" value="<?php echo $brand['id']?>" data-reset="false">
			                    </div>
			                    <div class="select-search-field border border-main">
		                    		<input class="input border-none" autocomplete="off" type="text" id="brandname" value="" placeholder="请输入品牌名称" data-reset="false" />
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
				     <?php if($_GET['label'] == 3){?>
				     <div class="search-form-box form-layout-rank border-bottom-none">
				    	<?php echo Form::input('select','status_ext',$_GET['status_ext'] ? $_GET['status_ext'] : 0,'按标签查看','',array('items' => array('商品标签','促销','热卖','新品','推荐')))?>
				    </div>
				    <?php }?>
				    <div class="form-group form-layout-rank border-none">
						<span class="label">搜索</span>
						<div class="box ">
							<div class="field">
								<input class="input" type="text" name="keyword" value="<?php echo $_GET['keyword']?>" data-reset="false" placeholder="输入商品名称/货号/条码" tabindex="0">
							</div>
						</div>
					</div>
				    <input class="button bg-sub fl" type="submit" value="查询">
				</div>
			</form>
			</div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('goods_add')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
						<?php if($_GET['label'] == 1 || !isset($_GET['label'])){?>
						<a data-message="是否确定删除所选？" href="<?php echo url('ajax_del')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
						<?php }else{?>
							<a data-message="是否确定删除所选？" href="<?php echo url('ajax_del_sku',array('label'=>4))?>" data-ajax='sku_id'><i class="ico_delete"></i>删除</a>
						<?php }?>
					<div class="spacer-gray"></div>
					<!--<a href=""><i class="ico_in"></i>导入</a>
					<div class="spacer-gray"></div>
					<a href=""><i class="ico_out"></i>导出</a>
					<div class="spacer-gray"></div>-->
				</div>
			</div>
			<?php echo runhook('admin_goods_lists_extra')?>
			<div class="table resize-table check-table paging-table high-table border clearfix">
				<div class="tr">
					<div class="th check-option" data-resize="false">
						<input id="check-all" type="checkbox" />
					</div>
					<?php foreach ($lists['th'] AS $th) {?>
					<span class="th" data-width="<?php echo $th['length']?>">
						<span class="td-con"><?php echo $th['title']?></span>
					</span>
					<?php }?>
					<div class="th" data-width="10">
						<div class="td-con">操作</div>
					</div>
				</div>
				<?php foreach ($lists['lists'] AS $list) {?>
				<div class="goods-list">
					<div class="tr">
					<span class="td check-option"><input type="checkbox" name="id" value="<?php echo $list['id']?>" /></span>
					<?php foreach ($list as $key => $value) {?>
					<?php if($lists['th'][$key]){?>
					<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
					<span class="td">
						<div class="td-con">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
							</div>
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'goods') {?>
						<span class="td">
							<div class="td-con td-pic">
								<div class="pic"><img src="<?php echo $list['thumb'] ? thumb($list['thumb'],150,150) : './statics/images/default_no_upload.png'?>" /></div>
								<div class="title">
									<div class="double-click">
										<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
										<input class="input double-click-edit text-ellipsis" name="<?php if($_GET['label'] == 1 || !isset($_GET['label'])){?>name<?php }else{?>sku_name<?php }?>" data-id="<?php echo $list['id']?>" type="text" value="<?php echo $value?>" />
									</div>
								</div>
								<div class="icon">
								<?php if($_GET['label'] != 1 || isset($_GET['label'])){?>
									<?php echo $list['spec_show']?>
								<?php }?>

								</div>
							</div>
						</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'cate_brand') {?>
					<span class="td">
						<span class="td-con double-row text-left">品牌：<?php echo $list['brand_name']?><br/>分类：<?php echo $list['cate_name'] ?></span>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
					<span class="td">
						<a class="ico_up_rack <?php if($_GET['label'] == 1 || !isset($_GET['label'])){?>spu_status<?}else{?>sku_status<?php }?> <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击<?php if($value == 1){?>取消<?php }?>上架"></a>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'date') {?>
					<span class="td">
						<span class="td-con"><?php echo date('Y-m-d H:i' ,$value) ?></span>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'hidden') {?>
						<input type="hidden" name="id" value="<?php echo $value?>" />
					<?php }else{?>
					<span class="td">
						<span class="td-con"><?php echo $value;?></span>
					</span>
					<?php }?>
					<?php }?>
					<?php }?>
					<span class="td">
						<div class="td-con double-row">
							<?php if(isset($_GET['label']) && $_GET['label'] != 1){?>
								<a target="_blank" href="<?php echo url('goods/index/detail',array('sku_id'=>$list['id']))?>">查看</a><br>
								&nbsp;&nbsp;&nbsp;<a href="<?php echo url('sku_edit',array('sku_id' => $list['id']))?>">编辑</a>
							<?php }else{?>
								<a class="look-attr" target="_blank" data-id="<?php echo $list['id']?>" href="javascript:;">展开</a><br>
								&nbsp;&nbsp;&nbsp;<a href="<?php echo url('goods_add',array('id' => $list['id']))?>">编辑</a>
							<?php }?>
								<?php if($_GET['label'] != 4){?>
								<?php if($_GET['label'] == 1 || !isset($_GET['label'])){?>
								&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('ajax_del',array('id[]'=>$list['id'],'label'=>$_GET['label']))?>">删除</a>
								<?php }else{?>
								&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('ajax_del_sku',array('sku_id[]'=>$list['id'],'label'=>$_GET['label']))?>">删除</a>
								<?php }?>
								<?php }else{?>
								&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('ajax_del_sku',array('sku_id[]'=>$list['id'],'label'=>$_GET['label']))?>">销毁</a>
								&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认恢复？" href="<?php echo url('ajax_recover',array('id[]'=>$list['id']))?>">恢复</a>
								<?php }?>
							</div>
					</span>
					</div>
					<div class="goods-list-box" data-id="<?php echo $list['id']?>">
					</div>
					<script id="sku_info" type="text/html">
					<%for(var item in lists){%>
					<%item = lists[item]%>
					<div class="tr" data-id="<%=item['spu_id']%>">
							<div class="td check-option"><input type="checkbox" name="id" value="<%=item['sku_id']%>" /></div>
							<div class="td">
								<div class="td-con">
									<div class="double-click">
										<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
										<input class="input double-click-edit text-ellipsis text-center" name="sku_sn" data-id="<%=item['sku_id']%>" type="text" value="<%=item['sn']%>" />
									</div>
								</div>
							</div>
							<div class="td">
								<div class="td-con td-pic">
									<div class="pic"><img src="<%=item['thumb'] ? item['thumb'] :'./statics/images/default_no_upload.png'%>" /></div>
									<div class="title">
										<div class="double-click">
											<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
											<input class="input double-click-edit text-ellipsis" name="sku_name" data-id="<%=item['sku_id']%>" type="text" value="<%=item['sku_name']%>" />
										</div>
									</div>
									<div class="icon">
										<span class="fl">商品标签：</span>
										<a class="ico ico_promotion <%if(item['status_ext'] != 1){ %>cancel<% }%>" data-id="<%=item['sku_id']%>" data-value="1" href="javascript:;" title="点击取消促销"></a>
										<a class="ico ico_special <%if(item['status_ext'] != 2){ %>cancel<% }%>" data-id="<%=item['sku_id']%>" data-value="2" href="javascript:;" title="点击取消特卖"></a>
										<a class="ico ico_new <%if(item['status_ext'] != 3){ %>cancel<% }%>" data-id="<%=item['sku_id']%>" data-value="3" href="javascript:;" title="点击取消新品"></a>
										<a class="ico ico_reco <%if(item['status_ext'] != 4){ %>cancel<% }%>" data-id="<%=item['sku_id']%>" data-value="4" href="javascript:;" title="点击取消推荐"></a>
									</div>
								</div>
							</div>
							<div class="td">
								<span class="td-con double-row text-left">&nbsp;<br>非详情页显示：<em data-id="<%=item['sku_id']%>" class="ico_list_show <%if(item['show_in_lists'] == 0){%>cancel<%}%>"></em></span>
							</div>
							<div class="td">
								<div class="td-con">
								<div class="double-click">
									<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
									<input class="input double-click-edit text-ellipsis text-center" name="shop_price" data-spuid="<%=item['spu_id']%>" data-id="<%=item['sku_id']%>" type="text" value="<%=item['shop_price']%>" />
								</div>
							</div>
							</div>
							<div class="td">
								<div class="td-con">
								<div class="double-click">
									<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
									<input class="input double-click-edit text-ellipsis text-center" name="number" data-spuid="<%=item['spu_id']%>" data-id="<%=item['sku_id']%>" type="text" value="<%=item['number']%>" />
								</div>
							</div>
							</div>
							<div class="td"></div>
							<div class="td">
								<span class="td-con">
								<a class="ico_up_rack sku_status <%if(item['status'] == 0){%>cancel<% }%>" href="javascript:;" data-id="<%=item['sku_id']%>" title="点击下架商品"></a>
								</span>
							</div>
							<div class="td">
								<div class="td-con double-row">
									<a target="_blank" href="?m=goods&c=index&a=detail&sku_id=<%=item['sku_id']%>">查看</a><br>
									<a href="?m=goods&c=admin&a=sku_edit&sku_id=<%=item['sku_id']%>">编辑</a>
									&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="?m=goods&c=admin&a=ajax_del_sku&sku_id[]=<%=item['sku_id']%>">删除</a>
								</div>
							</div>
						</div>
					<%}%>
					</script>
				</div>
				<?php }?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $pages;?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>
		var ajax_status= "<?php echo url('ajax_status')?>";
		var ajax_sku_status= "<?php echo url('ajax_status',array('type'=>'sku'))?>";
		var ajax_sn = "<?php echo url('ajax_sn')?>";
		var ajax_name = "<?php echo url('ajax_name')?>";
		var ajax_sku_name = "<?php echo url('ajax_sku_name')?>";
		var ajax_sku_sn = "<?php echo url('ajax_sku_sn')?>";
		var ajax_sort = "<?php echo url('ajax_sort')?>";
		var ajax_show = "<?php echo url('ajax_show')?>";
		var ajax_sku = "<?php echo url('ajax_sku')?>";
		var sure_sku = "<?php echo $lists['sure_sku']?>";
			$(function(){
				var $val=$("input[type=text]").eq(3).val();
				$("input[type=text]").eq(3).focus().val($val);
			})
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".paging-table").fixedPaging();

				$('.look-attr').bind('click',function(){
					var _this = $(this);
					var url = "<?php echo url('goods/admin/ajax_get_sku')?>";
					var id = _this.attr('data-id');
					if($('.goods-list-box[data-id="'+ id +'"]').find('.tr').length == 0){
						$.hdLoad.start()
						$.get(url,{id:id},function(ret){
							$.hdLoad.end();
							var sku_info = template('sku_info', {'lists': ret.lists,'sure_sku':sure_sku});
							$('.goods-list-box[data-id="'+ ret.id +'"]').html(sku_info);
							_this.addClass("open");
							_this.parents(".tr").next(".goods-list-box").slideDown(300,function(){
								_this.html('关闭');
								$(window).resize();
							});
						},'json');
					}else{
						if(_this.hasClass("open")){
							_this.removeClass("open");
							_this.parents(".tr").next(".goods-list-box").slideUp(300,function(){
								_this.html('展开');
								$(window).resize();
							});
						}else{
							_this.addClass("open");
							_this.parents(".tr").next(".goods-list-box").slideDown(300,function(){
								_this.html('关闭');
								$(window).resize();
							});
						}
					}
					return false;
				});
				if(sure_sku != 0){
					$('.look-attr').click();
				}
				$(".ico_list_show").live('click',function(){
					var _this = $(this);
					$.post(ajax_show,{sku_id:$(this).data('id')},function(data){
						if(data.status == 1){
							if(_this.hasClass("cancel")){
								_this.removeClass("cancel");
							}else{
								_this.addClass("cancel");
							}
						}else{
							return false;
						}
					},'json');
				})

				$('.sku_status').live('click',function(){
					var id = $(this).attr('data-id');
					var row = $(this);
					list_action.change_status(ajax_sku_status,id,row,'sku');
				})
				//推荐
				$(".spu_status").live('click',function(){
					var id = $(this).attr('data-id');
					var row = $(this);
					list_action.change_status(ajax_status,id,row,'spu');
				});
				$('input[name=name]').bind('blur',function() {
					var name = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_name(ajax_name,id,name);
				});
				$('input[name=sn]').bind('blur',function() {
					var name = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_name(ajax_sn,id,name);
				});
				$('input[name=sku_name]').live('blur',function() {
					var name = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_name(ajax_sku_name,id,name);
				});
				$('input[name=sku_sn]').live('blur',function() {
					var name = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_name(ajax_sku_sn,id,name);
				});
				$('input[name=sort]').bind('blur',function() {
					var sort = $(this).val();
					var id = $(this).attr('data-id');
					list_action.change_sort(ajax_sort,id,sort);
				});
				$('[name=shop_price]').live('blur',function() {
					var shop_price = $(this).val();
					var sku_id = $(this).attr('data-id'),
						spu_id = $(this).attr('data-spuid');
					list_action.change_price(ajax_sku,spu_id,sku_id,shop_price);
				});
				$('[name=number]').live('blur',function() {
					var number = $(this).val();
					var sku_id = $(this).attr('data-id'),
						spu_id = $(this).attr('data-spuid');
					list_action.change_number(ajax_sku,spu_id,sku_id,number);
				});
				$('.select-search-field').click(function(e){
					e.stopPropagation();
				});
				//buttonedit-popup-hover
				$('.select-search-text-box .form-buttonedit-popup').click(function(){
					if($(this).hasClass('buttonedit-popup-hover')){
						$(this).parent().find('.select-search-field').show();
						$(this).parent().find('.select-search-field').children('.input').focus();
						$(this).parent().find('.listbox-items').show();
					}else{
						$(this).parent().find('.select-search-field').hide();
						$(this).parent().find('.listbox-items').hide();
					}
				});
				 /*
			     *商品状态修改
			     */
			     $(".icon a.ico").live('click',function(){
			     	var _this = $(this);
			     	var id = _this.attr('data-id');
			     	var ajax_statusext = "<?php echo url('goods/admin/ajax_statusext')?>";
			     	var value = _this.attr('data-value');
			     	$.post(ajax_statusext,{sku_id:id,status_ext:value},function(ret){
			     		if(ret.status == 1){
			     			if(! _this.hasClass("cancel")){
							     _this.addClass("cancel");
							}else{
			     				_this.removeClass("cancel").siblings(".ico").addClass("cancel");
			     				}
			     			}
			     		},'json');
			     });
				$('#brandname').live('keyup',function(){
					var url = "<?php echo url('ajax_brand')?>";
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
			})
		</script>
		<script>
			var label = "<?php echo $_GET['label']?$_GET['label']:1?>";
			if(label){
				$.each($('.labelbox'),function(i,item){
					if($(item).attr('data-id') == label){
						$(item).addClass('current');
					}
				})
			}

			 //格式化分类
			jsoncategory = <?php echo json_encode($category) ?> ;
		    nb_category(0, '.root');

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
		</script>
<?php include template('footer','admin');?>
