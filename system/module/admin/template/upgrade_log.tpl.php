<?php include template('header','admin');?>
		<div class="log-text padding-big bg-white">
			<p>- [新增] wap版所有弹窗提示效果</p>
			<p>- [修复] 后台运营推广删除某一促销活动时未清空参与该活动的商品</p>
			<p>- [修复] 后台商品排序无法保存的问题</p>
			<p>- [修复] 找回密码页面丢失的css</p>
			<p>- [修复] 帮助中心和品牌页面的title信息</p>
			<p>- [修复] pc端无法用手机和邮箱登录的问题</p>
			<p>- [修复] 文章页面面包屑导航的链接错误</p>
			<p>- [修复] 手机端收藏商品无效的链接</p>
			<p>- [修复] 发货单模版中变量未替换的问题</p>
			<p>- [优化] 发货单模版中增加收货地址</p>
			<p>- [优化] 前台成功和错误提示页面中的链接点击无效的问题</p>
			<p>- [其它] 修复其它已知问题</p>
		</div>
		<script>
			$(function(){
				var dialog = top.dialog.get(window);
				var data = dialog.data; // 获取对话框传递过来的数据
			})
		</script>
<?php include template('footer','admin');?>
