<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">支付平台设置<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 此处列出了系统支持的支付方式，点击编辑可以设置支付参数及开关状态</p>
					<p>- 系统的支付方式是独立存在，任何环境下需要用到只需开启后单独配置即可，如配送方式下指定某个快递支持某种支付方式</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-wrap margin-top">
				<div class="table resize-table high-table border clearfix">
					<div class="tr">
						<span class="th" data-width="80">
							<span class="td-con">支付方式</span>
						</span>
						<span class="th" data-width="5">
							<span class="td-con">状态</span>
						</span>
						<span class="th" data-width="15">
							<span class="td-con">操作</span>
						</span>
					</div>
					<?php foreach($payments as $k=>$v):?>
					<div class="tr">
						<span class="td">
							<div class="td-con td-pic text-left">
								<span class="pic"><img src="./statics/images/pay/<?php echo $v['pay_code']?>.png" /></span>
								<span class="title txt"><?php echo $v['pay_name']?></span>
								<span class="icon"><?php echo $v['pay_desc']?></span>
							</div>
						</span>
						<span class="td">
						<?php if($v['pay_install']==1):?>
							<?php if($v['enabled']==1):?>
							<a class="ico_up_rack" href="javascript:;" title="点击启用/禁用支付方式" data-id="<?php echo $v['pay_code'];?>"></a>
							<?php else:?>
							<a class="ico_up_rack cancel" href="javascript:;" title="点击启用/禁用支付方式" data-id="<?php echo $v['pay_code'];?>"></a>
							<?php endif;?>
						<?php else:?>
							--
						<?php endif;?>	
						</span>
						<span class="td">
							<?php if($v['pay_install']==0):?>
								<a class="text-sub" href=<?php echo url('config',array('pay_code'=>$v['pay_code']))?>>安装</a>
							<?php else:?>
								<a class="text-sub" href=<?php echo url('config',array('pay_code'=>$v['pay_code']))?>>配置</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo url('uninstall',array('pay_code'=>$v['pay_code']))?>">卸载</a>
							<?php endif;?>	
						</span>
					</div>
					<?php endforeach;?>
				</div>
			</div>
		</div>
		<script>
			$(window).load(function(){
				$(".table").resizableColumns();
				$('.table .tr:last-child').addClass("border-none");
				//推荐
				var status = true;
				var post_enabled_url="<?php echo url('ajax_enabled')?>";
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
				function ajax_enabled(paycode){
					$.post(post_enabled_url,{'paycode':paycode},function(data){
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
