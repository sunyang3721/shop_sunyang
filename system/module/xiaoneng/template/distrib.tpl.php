<?php include template('header','admin');?>
		<style>
			.check-box input { margin-top: -3px; margin-right: 10px; vertical-align: middle; }
			.custom-service-wran { padding: 80px 120px; }
		</style>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">接待组分配<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
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
					<p>- 请在接待组管理页面设置好接待组后，于该页面设置前台页面的对接接待组。</p>
					<p>- 自定义链接请输入包含 http:// 的完整的网站链接。</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<form method="POST" action="<?php echo url('distrib'); ?>" name="distrib">
			<div class="form-box clearfix">
			<?php echo form::input('select', 'config[common]', $info['common'], '通用页对接接待组：', '请选择接待组，该项为必填项',array('items' => $select)); ?>
			<?php echo form::input('select', 'config[goods]', $info['goods'], '商品详情页对接接待组：', '请选择接待组，该项不填默认为通用页对接接待组',array('items' => $select)); ?>
			<?php echo form::input('select', 'config[cart]', $info['cart'], '购物车详情页对接接待组：', '请选择接待组，该项不填默认为通用页对接接待组',array('items' => $select)); ?>
			<?php echo form::input('select', 'config[order_settle]', $info['order_settle'], '订单提交页对接接待组：', '请选择接待组，该项不填默认为通用页对接接待组',array('items' => $select)); ?>
			<?php echo form::input('select', 'config[order_success]', $info['order_success'], '订单提交成功页对接接待组：', '请选择接待组，该项不填默认为通用页对接客服',array('items' => $select)); ?>
			<?php echo form::input('select', 'config[pay_success]', $info['pay_success'], '订单支付成功页对接接待组：', '请选择接待组，该项不填默认为通用页对接接待组',array('items' => $select)); ?>
			</div>
			<div class="padding">
				<div class="table border paging-table clearfix" id="custom-table">
					<div class="tr">
						<div class="th w45">自定义页面</div>
						<div class="th w45">接待组</div>
						<div class="th w10">操作</div>
					</div>
					<?php foreach ($info['diy']['url'] as $key => $url) {?>
					<div class="tr">
						<div class="td w45">
							<div class="td-con"><input class="input" name="config[diy][url][]" value="<?php echo $url?>" type="text" /></div>
						</div>
						<div class="td w45">
							<div class="td-con">
								<select class="layout fl choose" name="config[diy][server][]" style="height: 26px; margin-top: 7px;">
									<?php foreach ($select as $id => $name) {?>
									<option value="<?php echo $id?>" <?php if($id == $info['diy']['server'][$key]){?>selected='selected'<?php }?>><?php echo $name?></option>
									<?php }?>
								</select>
							</div>
						</div>
						<div class="td w10"><a class="remove" href="javascript:;">移除</a></div>
					</div>
					<?php }?>
					<div class="spec-add-button">
						<a href="javascript:;"><em class="ico_add margin-right"></em>添加一个页面</a>
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
		var apply = $("[name=distrib]").Validform({
            ajaxPost:false,
        });
        var select = <?php echo json_encode($select);?>;
        var html = '';
        $.each(select ,function(i, item) {
        	html += '<option value="'+ i +'">'+ item +'</option>';
        });
		$("#custom-table .spec-add-button a").click(function(){
        	$(this).parent().before('<div class="tr"><div class="td w45"><div class="td-con"><input class="input" name="config[diy][url][]" value="" type="text" /></div></div><div class="td w45"><div class="td-con"><select class="layout fl choose" name="config[diy][server][]" style="height: 26px; margin-top: 7px;">'+html+'</select></div></div><div class="td w10"><a class="remove" href="javascript:;">移除</a></div></div>');
        });
        $("#custom-table").on('click', '.remove', function(){
        	$(this).parents('.tr').remove();
        })
	</script>
</html>
