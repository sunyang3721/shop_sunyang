<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">评价列表<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<form action="<?php echo __APP__ ?>" method='get'>
			<input type="hidden" name="m" value="comment">
			<input type="hidden" name="c" value="admin">
			<input type="hidden" name="a" value="index">
			<div class="member-comment-search clearfix">
				<div class="form-box form-layout-rank clearfix border-bottom-none">
					<?php echo form::input('calendar', 'starttime', $_GET['starttime'], '评论时间', '', array('format' => 'YYYY-MM-DD')); ?>
					<?php echo form::input('calendar', 'endtime', $_GET['endtime'], '~', '', array('format' => 'YYYY-MM-DD')); ?>
					<?php $item = array('屏蔽', '显示','所有');krsort($item);?>
					<?php echo form::input('select', 'is_shield', isset($_GET['is_shield']) ? $_GET['is_shield'] : 2, '评论状态', '', array('items' => $item)); ?>
					<?php echo form::input('text', 'keyword', $_GET['keyword'], '搜索', '', array('placeholder'=> '请输入会员名')); ?>
				</div>
				<input class="button bg-sub fl" type="submit" name="dosubmit" value="查询">
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
			<div class="table-wrap resize-table">
				<div class="table resize-table table-hover paging-table check-table high-table border clearfix">
					<div class="tr">
						<span class="th check-option" data-resize="false">
							<span><input id="check-all" type="checkbox" /></span>
						</span>
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
						<div class="td">
							<span class="td-con"><a href="javascript:;" data-id="<?php echo $list['id'] ?>" data-event="reply">回复</a>&nbsp;&nbsp;&nbsp;
							<a data-confirm="是否确定删除？" href="<?php echo url('delete', array('id[]' => $list['id'])) ?>">删除</a></span>
						</div>
						<div class="clear"></div>
						<div class="layout comments-list">
							<p class="text-main comment-title"><b>评价：</b>
							<div class="commentinfo text-ellipsis">
								<p><?php echo $list['content'] ?></p>
							</div>

							<ul class="imgs">
								<?php foreach ($list['imgs'] as $img): ?>
								<li><a class="pic-center margin-right" href="<?php echo $img ?>" target="_blank"><img src="<?php echo $img ?>" alt=""></a></li>
								<?php endforeach ?>
							</ul>

							<p class="text-red reply-title"><b>回复：</b></p>
							<div class="replyinfo text-ellipsis">
								<?php if ($list['reply_content']): ?>
									<p><?php echo $list['reply_content'] ?></p>
								<?php else: ?>
									<p>-</p>
								<?php endif ?>
							</div>
						</div>
					</div>
					<?php }?>
					<div class="paging padding-tb body-bg clearfix">
						<?php echo $lists['pages']; ?>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$('.form-group').each(function(i){
				$(this).addClass('group'+(i+1));
				$(this).find(".box").addClass("margin-none");
			});
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".paging-table").fixedPaging();
			})

			$(function(){

				var $val=$("input[type=text]").first().val();
				$("input[type=text]").first().focus().val($val);

				$("[data-event=reply]").click(function() {
					var $this = $(this);
					var d = dialog({
					    title: '回复',
					    width: 320,
					    okValue: "确定",
					    content: '<textarea id="popup-input" class="textarea layout padding border-none" style="height: 150px;" placeholder="请输入对买家评论的回复"></textarea>',
					    ok: function () {
					        var params = {
					        	id:$this.data('id'),
					        	reply_content:$('#popup-input').val()
					        }
					        if(params.reply_content.length < 1) {
					        	d.title('回复内容不能为空');
					        	return false;
					        }
					        $.post("<?php echo url('reply') ?>", params, function(ret){
					        	if(ret.status == 1) {
					        		d.title(ret.message);
					        		setTimeout(function(){
					        			window.top.main_frame.location.reload();
					        		},1000);
					        	} else {
					        		d.title(ret.message);
					        		return false;
					        	}
					        }, 'json')
					        return false;
					    },
					    cancelValue: "取消",
					    cancel: function(){

					    }
					});
					d.showModal();
					var $val=$("textarea").first().text();
					$("textarea").first().focus().text($val);
				})
				$(".table .ico_up_rack").live('click',function(){
					var _this = $(this);
					var url = "<?php echo url('change_status')?>";
					$.post(url,{id:_this.data('id')},function(ret){
						console.log(ret.status)
						if(ret.status == 1){
							if(!_this.hasClass("cancel")){
								_this.addClass("cancel");
								_this.attr("title","点击显示评论");
							}else{
								_this.removeClass("cancel");
								_this.attr("title","点击屏蔽评论");
							}
						}else{
							alert(ret.message);
						}
					},'json')

				});
			})
		</script>
<?php include template('footer','admin');?>
