<?php include template('header','admin');?>
	<link type="text/css" rel="stylesheet" href="<?php echo __ROOT__;?>statics/css/mobile.css" />
	<div id="temp-content"></div>
	<div class="padding text-right ui-dialog-footer">
		<input type="button" class="button margin-left bg-sub sure" value="确定" />
		<input type="button" class="button margin-left bg-gray cancel" value="关闭" />
	</div>
	<script>
		try {
			var dialog = top.dialog.get(window);
		} catch (e) {
			//TODO
		}

		var $data = dialog.data;
		dialog.title($data.title);
		dialog.width(728);
		
		var content = '';
		for(var i = 0; i < $data.content.length; i++){
			content += '<div class="list'+ ($data.checked==i?" checked": "") +'" data-id="'+ i +'"><div class="pic-center"><img src="'+ $data.content[i].img +'" /></div><div class="margin-top"><span class="fl">'+ $data.content[i].title +'</span></div></div>';
		}
		//content += '<div class="list'+ ($data.checked==i?" checked": "") +'" data-id="'+ i +'"><div class="pic-center"><img src="'+ $data.content[i].img +'" /></div><div class="margin-top"><span class="fl">'+ $data.content[i].title +'</span><span class="fr">'+ $data.content[i].number +' 次使用</span></div></div>'
		content = '<div class="layout bg-gray-white border-bottom modal-dialog-tip">'+ $data.tip +'</div><div class="modal-dialog-lists bg-white clearfix">'+ content +'</div>';
		
		document.getElementById("temp-content").innerHTML = content;
		
		dialog.reset();
		
		//选中模板样式
		$(document).on('click', '.modal-dialog-lists .list', function(){
			$(this).addClass("checked").siblings().removeClass("checked");
		})
		
		$(".sure").click(function(){
			var $id = $(".modal-dialog-lists .checked").data("id");
			dialog.close($id);
			dialog.remove();
			return false;
		});
		
		$(".cancel").click(function(){
			dialog.remove();
			return false;
		});
		
	</script>
</body>
</html>