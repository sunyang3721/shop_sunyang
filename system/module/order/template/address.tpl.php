<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<title></title>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/jquery-1.7.2.min.js?v=<?php echo HD_VERSION ?>"></script>
		<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/admin.css?v=<?php echo HD_VERSION ?>" />
	</head>
	<body>
		<form name="district">
		<div class="add-address clearfix">
			<ul class="double-line text-left clearfix">
				<li class="list">
					<span class="label">收货地区：</span>
					<div class="content">
						<input type="hidden" name="district_id" value="<?php echo $order['cid']?>">
						<select id="district">
							<option>请选择地区</option>
						</select>
						<span id="check-area" class="text-mix">&nbsp;</span>
					</div>
				</li>
				<li class="list">
					<span class="label">详细地址：</span>
					<div class="content">
						<textarea class="textarea wide" name="address" type="text"><?php echo $order['_main']['address_detail']?></textarea>
						<span id="check-address" class="text-mix"></span>
					</div>
				</li>
				<li class="list">
					<span class="label">收货人：</span>
					<div class="content">
						<input class="input" name="name" type="text" value="<?php echo $order['_main']['address_name']?>" />
						<span id="check-name" class="text-mix"></span>
					</div>
				</li>
				<li class="list">
					<span class="label">手机号：</span>
					<div class="content">
						<input class="input" name="mobile" type="text" value="<?php echo $order['_main']['address_mobile']?>"/>
						<span id="check-mobile" class="text-mix"></span>
					</div>
				</li>
				<li class="list">
					<span class="label">备注：</span>
					<div class="content">
						<textarea class="textarea wide" name="remark" type="text"></textarea>
						<span id="check-remark" class="text-mix"></span>
					</div>
				</li>
			</ul>
		</div>
		<input type="hidden" name="order_sn" value="<?php echo $order['sn'];?>">
		<div class="padding border-top bg-gray-white text-right">
			<input class="button bg-sub" id="hold" type="button" value="保存" />
			<input class="margin-left button bg-gray" id="cancel" type="button" value="取消" />
		</div>
		</form>
		<script type="text/javascript" src="<?php echo __ROOT__;?>statics/js/haidao.linkage.js?v=<?php echo HD_VERSION;?>"></script>
		<script type="text/javascript">
		var def = '<?php echo json_encode($order['cids']);?>';
		$("#district").linkageSel({
			url: '<?php echo url('member/address/ajax_district');?>',
			defVal: eval(def),
			callback: function(vals,tar){
				$("input[name=district_id]").val(vals[vals.length-1]);
			}
		});

		$(function(){
			try {
				var dialog = top.dialog.get(window);
			} catch (e) {
				return;
			}
			dialog.title("变更收货地址");
			dialog.reset();
			
			$("input[name=name]").blur(function(){
				checkName();
			});
			$("input[name=mobile]").blur(function(){
				checkMobile();
			});
			$("[name=address]").blur(function(){
				checkAddress();
			});
			$("[name=remark]").blur(function(){
				checkRemark();
			});
			
			
			$("#hold").click(function(){
				if ($("#hold").prop('disabled') == false) {
					submithandle();
				}
				$("#hold").attr('disabled',true);
			});
			
			$("#cancel").click(function(){
				dialog.remove();
			});


			function checkName(){
				if(!$("input[name=name]").val()){
					$("#check-name").text("请您填写收货人姓名！");
					$("#hold").attr('disabled',true);
					return false;
				}
				$("#check-name").text("");
				$("#hold").attr('disabled',false);
				return true;
			}
			
			function checkMobile(){
				var str = $("input[name=mobile]").val();
				if(!str){
					$("#check-mobile").text("请您填写收货人手机号码！");
					$("#hold").attr('disabled',true);
					return false;
				}
				if(!str.match(/^1[3|4|5|7|8]\d{9}$/)){
					$("#check-mobile").text("手机号码格式不正确！");
					$("#hold").attr('disabled',true);
					return false;
				}
				$("#check-mobile").text("");
				$("#hold").attr('disabled',false);
				return true;
			}
			
			function checkAddress(){
				var str = $("[name=address]").val();
				if(!str){
					$("#check-address").text("请您填写收货人详细地址！");
					$("#hold").attr('disabled',true);
					return false;
				}
				if(str.length<6){
					$("#check-address").text("收货地址至少六个字符！");
					$("#hold").attr('disabled',true);
					return false;
				}
				$("#check-address").text("");
				$("#hold").attr('disabled',false);
				return true;
			}
			function checkRemark(){
				var str = $("[name=remark]").val();
				if(!str){
					$("#check-remark").text("请您填写备注！");
					$("#hold").attr('disabled',true);
					return false;
				}
				$("#check-remark").text("");
				$("#hold").attr('disabled',false);
				return true;
			}
			
			
			function submithandle(){
				
				if(!checkName()){
					$("input[name=name]").focus();
					return false;
				}
				if(!checkMobile()){
					$("input[name=mobile]").focus();
					return false;
				}
				if(!checkAddress()){
					$("input[name=address]").focus();
					return false;
				}
				if(!checkRemark()){
					$("input[name=remark]").focus();
					return false;
				}
				var flog = true;
				$('select').each(function(){
					if($(this).val()==''){
						flog = false;
					}
				});
				if(!flog){
					$("#check-area").text("请您选择完整的地区信息！")
					return false;
				}
				$.post("<?php echo url('address_edit');?>",$("form").serialize(), function(ret) {
					dialog.close();
					if (ret.status != 1) alert(ret.message);
					window.top.main_frame.location.reload();
				},'json');
			}

		});
		</script>
	</body>
</html>