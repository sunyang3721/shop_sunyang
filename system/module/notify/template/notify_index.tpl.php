<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">通知系统<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
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
					<p>- 邮件通知支持国内大部分邮箱，建议使用企业邮箱。</p>
					<p>- 站内信通知进用于做系统通知，无需进行配置，仅需开启即可。</p>
					<p>- 短信通知需绑定云平台，并确保云平台短信余额充足。</p>
					<p>- 微信通知需到微信公众平台申请模板消息。</p>
				</div>
			</div>
			<div class="hr-gray margin-bottom"></div>
			<div class="table resize-table high-table border clearfix">
				<div class="tr">
					<span class="th" data-width="80">
						<span class="td-con">通知类型</span>
					</span>
					<span class="th" data-width="5">
						<span class="td-con">状态</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach ($notifys AS $notify): ?>
				<div class="tr" data-code="<?php echo $notify['code'] ?>">
					<span class="td">
						<div class="td-con td-pic text-left">
							<span class="pic"><img src="<?php echo __ROOT__ ?>statics/images/notify/<?php echo $notify['code'] ?>.png" onerror="javascript:this.src='<?php echo __ROOT__ ?>statics/images/default_no_upload.png';"/></span>
							<span class="title txt"><?php echo $notify['name'] ?></span>
							<span class="icon"><?php echo $notify['description'] ?></span>
						</div>
					</span>
					<span class="td">
					<?php if (!empty($notify['config'])): ?>
						<a data-id="<?php echo $notify['code'] ?>" class="ico_up_rack<?php if (!$notify['enabled']): ?> cancel<?php endif ?>" href="javascript:;"></a>
					<?php else: ?>
						-
					<?php endif ?>
					</span>
					<span class="td">
						<div class="td-con">
						<?php {?>
                            <?php if(empty($notify['config'])){?>
                            <a href="<?php echo url('config', array('code' => $notify['code'])) ?>">安装</a>
                            <?php }else{?>
                            <a href="<?php echo url('config', array("code" => $notify['code'])) ?>">配置</a>&nbsp;&nbsp;&nbsp;
                            <a href="<?php echo url('template', array("code" => $notify['code'])) ?>">模板</a>&nbsp;&nbsp;&nbsp;
                            <a href="<?php echo url('uninstall', array("code" => $notify['code'])) ?>">卸载</a>
                            <?php }?>
						<?php }?>
						</div>
					</span>
				</div>
				<?php endforeach ?>
			</div>
		</div>
		<script>
			$(window).load(function(){
				$(".table").resizableColumns();
				$('.table .tr:last-child').addClass("border-none");
				//推荐
				//推荐
				var status = true;
				var post_enabled_url="<?php echo url('ajax_enabled',array('formhash'=>FORMHASH))?>";
				$(".table .ico_up_rack").bind('click',function(){
					if(ajax_enabled($(this).attr('data-id'))==true){					
						if(!$(this).hasClass("cancel")){
							$(this).addClass("cancel");
							$(this).attr("title","点击商品上架");
						}else{
							$(this).removeClass("cancel");
							$(this).attr("title","点击商品下架");
						}
					}
				});
				//改变状态
				function ajax_enabled(code){
					$.post(post_enabled_url,{'code':code},function(data){
						if(data.status == 1){
							status =  true;
						}else{
							status =  false;
						}
					},'json');
					return status;
				}
			})
		</script>
	<?php include template('footer','admin');?>
