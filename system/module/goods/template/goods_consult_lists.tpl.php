<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">咨询列表<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a <?php if($_GET['status'] == 0){?> class="current" <?php }?> href="<?php echo url('index',array('status'=>0))?>">未回复</a></li>
				<li><a <?php if($_GET['status'] == 1){?> class="current" <?php }?> href="<?php echo url('index',array('status'=>1))?>">已回复</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form method="GET" action="">
			<input type="hidden" value="goods" name="m" />
			<input type="hidden" value="goods_consult" name="c" />
			<input type="hidden" value="index" name="a" />
			<input type="hidden" value="<?php echo $_GET['status']?>" name="status" />
			<div class="member-comment-search clearfix">
				<div class="form-box clearfix border-bottom-none" style="width: 590px;">
					<div style="z-index: 4;" id="form-group-id1" class="form-group form-layout-rank group1">
						<span class="label">咨询时间</span>
						<div class="box margin-none">
							<?php echo form::calendar('start',!empty($_GET['start']) ? $_GET['start']:'',array('format' => 'YYYY-MM-DD'))?>
						</div>
					</div>
					<div style="z-index: 3;" id="form-group-id2" class="form-group form-layout-rank group2">
						<span class="label">~</span>
						<div class="box margin-none">
							<?php echo form::calendar('end',!empty($_GET['end'])? $_GET['end']:'',array('format' => 'YYYY-MM-DD'))?>
						</div>
					</div>
					<div style="z-index: 1;" id="form-group-id4" class="form-group form-layout-rank group4">
						<span class="label">搜索</span>
						<div class="box margin-none">
							<input class="input" name="keywords" placeholder="请输入会员名" tabindex="0" type="text" value="<?php echo !empty($_GET['keywords'])?$_GET['keywords'] :''?>">
						</div>
					</div>
				</div>
				<input class="button bg-sub fl" value="查询" type="submit">
			</div>
			</form>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a data-message="是否确定删除所选？" href="<?php echo url('delete')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
					<!--<a href=""><i class="ico_out"></i>导出</a>
					<div class="spacer-gray"></div>-->
				</div>
			</div>
			<div class="table-wrap">
				<div class="table resize-table paging-table check-table high-table table-hover border clearfix">
					<div class="tr">
						<span class="th check-option" data-resize="false">
							<span><input id="check-all" type="checkbox" /></span>
						</span>
						<!--<span class="th" data-width="10">
							<span class="td-con">排序</span>
						</span>-->
						<?php foreach ($lists['th'] AS $th) {?>
						<span class="th" data-width="<?php echo $th['length']?>">
							<span class="td-con"><?php echo $th['title']?></span>
						</span>
						<?php }?>
						<span class="th" data-width="10">
							<span class="td-con">操作</span>
						</span>
					</div>
					<?php foreach ($lists['lists'] AS $list) {?>
					<div class="tr">
						<span class="td check-option"><input type="checkbox" name="id" value="<?php echo $list['id']?>" /></span>
						<?php foreach ($list as $key => $value) {?>
						<?php if($lists['th'][$key]){?>
						<?php if ($lists['th'][$key]['style'] == 'double_click') {?>
						<span class="td">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis text-center" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
							</div>
						</span>
						<?php }elseif ($lists['th'][$key]['style'] == 'goods') {?>
							<span class="td">
								<div class="td-con td-pic text-left">
								<span class="pic"><a target="_blank" href="<?php echo url('goods/index/detail',array('sku_id' => $list['sku_id']))?>"><img src="<?php echo $list['thumb']?>" /></a></span>
								<span class="title text-ellipsis txt"><a target="_blank" href="<?php echo url('goods/index/detail',array('sku_id' => $list['sku_id']))?>"><?php echo $value?></em></a></span>
								<span class="icon">
									<?php foreach($list['spec'] as $k => $v){?>
										<em class="text-main"><?php echo $v['name']?>：</em><?php echo $v['value']?>&nbsp;
									<?php }?>
								</span>
							</div>
							</span>
						<?php }elseif ($lists['th'][$key]['style'] == 'left_text') {?>
						<span class="td">
							<span class="td-con text-left"><?php echo $value;?></span>
						</span>
						<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
						<span class="td">
							<a class="ico_up_rack <?php if($value != 1){?>cancel<?php }?>" href="javascript:;" data-id="<?php echo $list['id']?>" title="点击取消推荐"></a>
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
							<div class="td">
								<span class="td-con"><a href="javascript:setReply('<?php echo $list['id']?>','<?php echo $list['reply_content']?>');">回复</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确定删除？" href="<?php echo url('delete',array('id[]'=>$list['id'],'status'=>$_GET['status']))?>">删除</a></span>
							</div>
						</span>
						<div class="clear"></div>
						<div class="layout comments-list">
							<p class="text-main comment-title"><b>咨询：</b></p>
							<div class="commentinfo text-ellipsis">
								<p><?php echo $list['question']?></p>
							</div>
							<?php if($list['status']){?>
							<p class="text-red reply-title"><b>回复：</b></p>
							<div class="replyinfo text-ellipsis">
								<p><?php echo $list['reply_content']?></p>
							</div>
							<?php }?>
						</div>
					</div>
					<?php }?>

					<?php foreach($result as $key => $value){?>
					<div class="tr">
						<span class="td check-option"><input type="checkbox" name="id" value="<?php echo $value['id']?>" /></span>
						<!--<div class="td"><span class="td-con"><?php echo $value['sort']?></span></div>-->
						<div class="td">
							<div class="td-con td-pic text-left">
								<span class="pic"><a target="_blank" href="<?php echo url('goods/index/detail',array('sku_id' => $value['sku_id']))?>"><img src="<?php echo $value['goods_detail']['thumb']?>" /></a></span>
								<span class="title text-ellipsis txt"><a target="_blank" href="<?php echo url('goods/index/detail',array('sku_id' => $value['sku_id']))?>"><?php echo $value['goods_detail']['sku_name']?></em></a></span>
								<span class="icon">
									<?php foreach($value['goods_detail']['spec'] as $k => $v){?>
										<em class="text-main"><?php echo $v['name']?>：</em><?php echo $v['value']?>&nbsp;
									<?php }?>
								</span>
							</div>
						</div>
						<div class="td">
							<span class="td-con"><?php echo empty($value['username']) ? 游客 : $value['username']?></span>
						</div>
						<div class="td"><span class="td-con"><?php echo date('Y-m-d',$value['dateline'])?></span></div>
						<div class="td">
							<span class="td-con"><a href="javascript:setReply('<?php echo $value['id']?>','<?php echo $value['reply_content']?>');">回复</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确定删除？" href="<?php echo url('delete',array('id[]'=>$value['id'],'status'=>$_GET['status']))?>">删除</a></span>
						</div>
						<div class="clear"></div>
						<div class="layout comments-list">
							<p class="text-main comment-title"><b>咨询：</b></p>
							<div class="commentinfo text-ellipsis">
								<p><?php echo $value['question']?></p>
							</div>
							<?php if($value['status']){?>
							<p class="text-red reply-title"><b>回复：</b></p>
							<div class="replyinfo text-ellipsis">
								<p><?php echo $value['reply_content']?></p>
							</div>
							<?php }?>
						</div>
					</div>
					<?php }?>
					<div class="paging padding-tb body-bg clearfix">
						<?php echo $pages?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(window).load(function(){
				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);
				$(".table").resizableColumns();
				$(".paging-table").fixedPaging();
				$(".table .ico_up_rack").bind('click',function(){
					if(!$(this).hasClass("cancel")){
						$(this).addClass("cancel");
						$(this).attr("title","点击显示咨询");
					}else{
						$(this).removeClass("cancel");
						$(this).attr("title","点击屏蔽咨询");
					}
				});
			})
			function setReply(id,reply_content){
				var d = dialog({
				    title: '回复',
				    width: 320,
				    okValue: "确定",
				    content: '<textarea id="popup-input" class="textarea layout padding border-none" style="height: 150px;" placeholder="请输入你要回复的内容">'+(reply_content ? reply_content :'')+'</textarea>',
				    ok: function () {
						var reply = '<?php echo url('reply')?>';
						var value = $('#popup-input').val();
						$.post(reply,{'id':id,'reply_content':value},function(data){
							console.log(data);
							if(data){
								alert('操作成功');
								window.location.reload();
								return true;
							}else{
								alert('操作失败');
								return false;
							}
						})
				        this.close(value);
				        this.remove();
				    },
				    cancelValue: "取消",
				    cancel: function(){

				    }
				});
				d.addEventListener('close', function () {
				    console.log(this.returnValue);
				});
				d.showModal();
			}
			/*$(".table-work .border a:last").live('click',function(){
				var ids = Array();
				var excel = '<?php echo url('excel')?>';
				$("input[name=id]").each(function(){
					if($(this).attr('checked') == 'checked'){
						ids.push($(this).val());
					}
				})
				$(this).attr('href',excel+'&id[]='+ids);
			})*/
		</script>
<?php include template('footer','admin');?>
