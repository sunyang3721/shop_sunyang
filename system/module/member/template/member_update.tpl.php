<?php include template('header','admin');?>
<form action="<?php echo url('update')?>" method="post" name="member_update">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="formhash" value="<?php echo FORMHASH ?>">
<div class="edit-user-info">
	<div class="table layout border-none clearfix">
		<div class="tr top-tr">
			<div class="layout bg-white padding-left"><b>会员余额</b></div>
		</div>
		<div class="tr">
			<div class="th w35">
				<label><input type="radio" name="info[money][action]" value="inc" checked="checked" />增加</label>
				<label><input type="radio" name="info[money][action]" value="dec" />减少</label>
			</div>
			<div class="td w65">
				<input class="input" type="text" name="info[money][num]" value="" placeholder="请输入修改余额，如：10"  datatype="/^\d+(\.\d+)?$/" ignore="ignore"/>
			</div>
		</div>

		<div class="tr itop-tr">
			<div class="layout bg-white padding-left"><b>会员经验</b></div>
		</div>
		<div class="tr">
			<div class="th w35">
				<label><input type="radio" name="info[exp][action]" value="inc" checked="checked" />增加</label>
				<label><input type="radio" name="info[exp][action]" value="dec"/>减少</label>
			</div>

			<div class="td w65">
				<input class="input" type="text" name="info[exp][num]" value="" placeholder="请输入修改经验，如：10" datatype="n" ignore="ignore"/>
			</div>
		</div>
		<div class="tr itop-tr">
			<div class="layout bg-white padding-left"><b>修改原因</b></div>
		</div>
		<div class="tr" style="height: 92px;">
			<div class="td layout padding">
				<textarea name="msg" class="textarea layout padding" placeholder="请填写操作日志"></textarea>
			</div>
		</div>
	</div>
</div>

<div class="padding text-right ui-dialog-footer">
	<input type="submit" name="dosubmit" value="确定" class="button bg-main"/>
	<input type="button" name="button" value="取消" class="button margin-left bg-gray" data-back="false"/>
</div>
</form>

<script type="text/javascript">
	$(function(){
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			alert('请勿非法访问');
			return;
		}
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		var form_member_update = $("form[name='member_update']").Validform({
			ajaxPost:true,
			dragonfly:true,
			callback:function(ret) {
				dialog.title(ret.message);
				if(ret.status == 1) {
						dialog.close(ret);
						dialog.remove();
				}
				return false;
			}
		})

		$('input[type=button]').live('click', function () {
			dialog.close();
			dialog.remove();
			return false;
		})
	})
</script>
<?php include template('footer','admin');?>
