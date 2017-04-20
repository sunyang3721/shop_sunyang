<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">广告管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a href="<?php echo url('index')?>">广告列表</a></li>
				<li><a class="current" href="javascript:;">广告位</a></li>
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
					<p>- 将广告位调用代码放入前台页面，将显示该广告位的广告</p>
					<p>- 温馨提示，创建广告之前需要添加一个广告位，广告位分为图片广告和文字广告</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('position_add')?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('position_del')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table check-table paging-table border clearfix">
				<div class="tr border-none">
					<div class="th check-option" data-resize="false">
						<input id="check-all" type="checkbox" />
					</div>
					<?php foreach ($lists['th'] AS $th) {?>
						<span class="th" data-width="<?php echo $th['length']?>">
							<span class="td-con"><?php echo $th['title']?></span>
						</span>
					<?php }?>
					<div class="th" data-width="15"><span class="td-con">操作</span></div>
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
							<input class="input double-click-edit text-ellipsis" type="text" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" value="<?php echo $value?>" />
						</div>
					</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ident') {?>
						<span class="td ident">
							<span class="ident-show">
								<em class="ico_pic_show"></em>
								<div class="ident-pic-wrap">
									<img src="<?php echo $list['logo'] ? $list['logo'] : '../images/default_no_upload.png'?>" />
								</div>
							</span>
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input class="input double-click-edit text-ellipsis" name="<?php echo $key?>" data-id="<?php echo $list['id']?>" type="text" value="<?php echo $value?>" />
							</div>
						</span>
					<?php }elseif ($lists['th'][$key]['style'] == 'ico_up_rack') {?>
					<span class="td">
					<div id="view<?php echo $list['id'];?>" style="display: none;">
								<textarea style="width:100%;height:300px;" name="view2" class="textarea hd-input">
								{hd:ads tagfile="ads" method="lists" position="<?php echo $list['id'];?>" num="5" order="id desc"}
							<!-- 可删除
								调用参数说明
								position(int) 广告位ID自动生成
								num(int) 调用有效广告数量
								order(string)[id desc(降序)|id asc(升序)|rand()(随机)]
								返回结果
								width 宽度
								height 高度
								type [0(图片)|1(文字)]
								defaultpic 默认图片
								defaulttext 默认文字
								list 结果列表
								list['title'] 广告标题
								list['link'] 链接地址
								list['content'] 广告内容
							-->
						{loop $data['list'] $r}
						<a href="{url('ads/index/adv_view',array('id'=>$r['id'],'url'=>$r['link']))}" title="{$r['title']}">
							{if ($data['type']==0)}
								<img src="{if empty($r['content'])}{$data['defaultpic']}{else}{$r['content']}{/if}" width="{$data['width']}" height="{$data['height']}"/>
							{else}
								{if empty($r['content'])}{$data['defaultpic']}{else}{$r['content']}{/if}
							{/if}
						</a>
						{/loop}
						{/hd}</textarea>
								请复制以调用代码到页面注释说明可删除
							</div>
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
						<span class="td-con">
						<a href="<?php echo url('position_edit',array('id'=>$list['id']))?>">编辑</a>&nbsp;&nbsp;&nbsp;<a href="javascript:popup('view<?php echo $list['id'];?>');">代码调用</a>&nbsp;&nbsp;&nbsp;<a data-confirm="是否确认删除？" href="<?php echo url('position_del', array('id[]' => $list['id'])); ?>">删除</a><?php echo $lists['option']?></span>
					</span>
				</div>
				<?php }?>
				<div class="paging padding-tb body-bg clearfix">
					<ul class="fr">
						<?php echo $lists['pages']?>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>
			var formhash='<?php echo FORMHASH?>';
			var del_url = "<?php echo url('position_del',array('formhash'=>FORMHASH))?>";
			var save_name_url = "<?php echo url('position_save_name')?>"
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".table").fixedPaging();
				$('.table .tr:last-child').addClass("border-none");
				//推荐
				var status = true;
				var post_status_url="<?php echo url('ajax_status')?>";
				$(".table .ico_up_rack").bind('click',function(){
					if(ajax_status($(this).attr('data-id'))==true){
						if(!$(this).hasClass("cancel")){
							$(this).addClass("cancel");
						}else{
							$(this).removeClass("cancel");
						}
					}
				});
				//改变状态
				function ajax_status(id){
					$.post(post_status_url,{'id':id,'formhash':formhash},function(data){
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
				//双击编辑
				$('.double-click-edit').on('blur',function(){
					$.post(save_name_url,{id:$(this).attr('data-id'),name:""+$(this).val()+"",formhash:""+formhash+""},function(data){
					})
				})

			})
			function popup(v){
				top.dialog({
					content:$("[id='"+v+"']").html(),
					title: '调用广告位代码',
					width: 420,
					cancelValue: '关闭',
					cancel: function(){

					}
				})
				.showModal();
			}
		</script>
<?php include template('footer','admin');?>
