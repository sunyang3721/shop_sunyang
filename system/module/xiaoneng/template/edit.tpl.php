<?php include template('header','admin');?>
		<style>
			.check-box input { margin-top: -3px; margin-right: 10px; vertical-align: middle; }
			.custom-service-wran { padding: 80px 120px; }
		</style>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">接待组管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 请在小能客服客户端设置好接待组后，在该页面填写接待组名称及对应的id。</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<form method="POST" action="<?php echo url('edit'); ?>" name="edit">
			<div class="padding">
				<div class="table border paging-table clearfix" id="custom-table">
					<div class="tr">
						<div class="th w45">接待组名称</div>
						<div class="th w45">接待组ID</div>
						<div class="th w10">操作</div>
					</div>
					<?php if($infos){?>
					<?php foreach ($infos AS $info) {?>
					<div class="tr">
						<div class="td w45">
							<div class="td-con"><input class="input" name="name[]" value="<?php echo $info['name']?>" type="text" /></div>
						</div>
						<div class="td w45">
							<div class="td-con"><input class="input" name="identifier[]" value="<?php echo $info['identifier']?>" type="text" /></div>
						</div>
						<div class="td w10"><a class="remove" href="javascript:;">移除</a></div>
					</div>
					<?php }?>
					<?php }else{?>
					<div class="tr">
						<div class="td w45">
							<div class="td-con"><input class="input" name="name[]" value="" type="text" /></div>
						</div>
						<div class="td w45">
							<div class="td-con"><input class="input" name="identifier[]" value="" type="text" /></div>
						</div>
						<div class="td w10"><a class="remove" href="javascript:;">移除</a></div>
					</div>
					<?php }?>
					<div class="spec-add-button">
						<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个接待组</a>
					</div>
				</div>
			</div>
			<div class="padding">
       	 		<input type="submit" class="button bg-main" value="保存" />
    		</div>
    		</form>
		</div>
	</body>
	<script type="text/javascript">

        $("#custom-table .spec-add-button a").click(function(){
        	$(this).parent().before('<div class="tr"><div class="td w45"><div class="td-con"><input class="input" name="name[]" value="" type="text" /></div></div><div class="td w45"><div class="td-con"><input class="input" name="identifier[]" value="" type="text" /></div></div><div class="td w10"><a class="remove" href="javascript:;">移除</a></div></div>');
        });
        $("#custom-table").on('click', '.remove', function(){
        	$(this).parents('.tr').remove();
        })
	</script>
</html>
