<?php include template('header','admin');?>
	<body>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">微店装修<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 文章区别于站点帮助，可在文章列表页点击查看</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="<?php echo url('diy_edit') ?>"><i class="ico_add"></i>添加</a>
					<div class="spacer-gray"></div>
					<a data-message="是否确定删除所选？" href="<?php echo url('delete')?>" data-ajax='id'><i class="ico_delete"></i>删除</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="table resize-table check-table treetable border clearfix">
				<div class="tr border-none">
					<div class="th check-option" data-resize="false">
						<input id="check-all" type="checkbox" />
					</div>
					<div class="th" data-width="30"><span class="td-con">标题</span></div>
					<div class="th" data-width="15"><span class="td-con">排序</span></div>
					<div class="th" data-width="15"><span class="td-con">商品数</span></div>
					<div class="th" data-width="20"><span class="td-con">创建时间</span></div>
					<div class="th" data-width="20"><span class="td-con">操作</span></div>
				</div>
				<div class="tr">
					<div class="td check-option"><input type="checkbox" name="id" value="<?php echo $value['id']?>" /></div>
					<div class="td">
						<div class="td-con">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input name="title" class="input double-click-edit text-ellipsis" type="text" value="店铺主页" />
							</div>
						</div>
					</div>
					<div class="td">
						<div class="td-con">
							<div class="double-click">
								<a class="double-click-button margin-none padding-none" title="双击可编辑" href="javascript:;"></a>
								<input name="sort" class="input double-click-edit text-center text-ellipsis" type="text" value="255" />
							</div>
						</div>
					</div>
					<div class="td">
						<span class="td-con">0</span>
					</div>
					<div class="td">
						<span class="td-con">2015-10-20 19:27:15</span>
					</div>
					<div class="td">
						<span class="td-con">
							<a href="<?php echo url('diy_edit') ?>">编辑</a>&nbsp;&nbsp;&nbsp;
							<a href="" data-confirm="是否确认删除？">删除</a>
						</span>
					</div>
				</div>
			</div>
			<div class="paging padding-tb body-bg clearfix">
				<?php echo $pages;?>
				<div class="clear"></div>
			</div>
		</div>
		<script>
			$(window).load(function(){
				$(".table").resizableColumns();
				$(".table").treetable();
				$('.table .tr:last-child').addClass("border-none");
				//启用与关闭
				$(".table .ico_up_rack").bind('click',function(){
					if(!$(this).hasClass("cancel")){
						$(this).addClass("cancel");
						$(this).attr("title","点击显示");
					}else{
						$(this).removeClass("cancel");
						$(this).attr("title","点击关闭");
					}
				});
			})
		</script>
<?php include template('footer','admin');?>
