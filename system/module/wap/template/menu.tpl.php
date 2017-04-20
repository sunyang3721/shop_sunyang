<?php include template('header','admin');?>
	<body>
		<a href="#">关闭</a>
		<script>
			try {
				var dialog = top.dialog.get(window);
			} catch (e) {
				
			}
			dialog.title("123");
			dialog.reset();
			$("a").click(function(){
				dialog.close("aaaaa");
				dialog.remove();
			})
		</script>
	</body>
</html>
