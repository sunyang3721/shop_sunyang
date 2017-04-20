<?php
class module_wap_hook1
{
	public function tmpl_compile(&$template){
		$hook = '{template header common}';
		$hooka ='{template header goods}';
		$hooksa = strpos($template,$hooka);
		$hooks = strpos($template,$hook);

		if($hooksa !==false){
			if($hooks ==false){
				$model= MODULE_NAME.'_'.CONTROL_NAME.'_'.METHOD_NAME;
				$page = array(
					'goods_index_index' => '0' ,
					'member_index_index' => '1',
					'goods_index_category_lists' => '2',
					'goods_index_lists' => '3',
					'goods_search_search' =>'4',
					);
				$list = model('wap_template')->find();
				$tpl = explode(' ',$list['content']);
				$tmpl =	json_decode(base64_decode($tpl[2]),TRUE);
				if(is_string($tmpl['page'])){
          			$data[] = $tmpl['page'];
          			$tmpl['page'] = $data;
         		 }

				$nav = array_intersect($page,$tmpl['page']);
				foreach ($nav as $key => $value) {
					if($key === $model ){
						$content_tmpl = '';
						if($tmpl["style"] == 0){
							$mainNav = '<div class="nav-main-item"><a href="'.__APP__.'" class="home">主页</a></div>';
							if(empty($tmpl["menu"])){
								$content_tmpl = '<div class="nav-menu-1 nav-menu no-menu">'.$mainNav.'</div>';
							}else{
								$menu = $tmpl["menu"];
								$len = count($menu);
								$menuHtml = '';
								if(!empty($tmpl["submenu"])){
									$submenu = $tmpl["submenu"];
									foreach ($menu as $k => $v) {
										$submenuHtml = '';
										foreach($submenu as $sub){
											if($sub && $sub["id"] == $k){
												$submenuHtml .= '<div class="submenu"><span class="arrow"></span><div class="js-nav-2nd-region"><ul>';
												if(is_array($sub["title"])){
													foreach ($sub["title"] as $tk => $tit) {
														$submenuHtml .= '<li><a href="'.$sub["url"][$tk].'">'.$tit.'</a></li>';
													}
												}else{
													$submenuHtml .= '<li><a href="'.$sub["url"].'">'.$sub["title"].'</a></li>';
												}
												$submenuHtml .= '</ul></div></div>';
											}
										}
										if($submenuHtml != ''){
											$menuHtml .= '<div class="nav-item"><a class="mainmenu" href="javascript:;"><span class="txt">'.$v['title'].'</span></a>'. $submenuHtml .'</div>';
										}else{
											$menuHtml .= '<div class="nav-item"><a class="mainmenu" href="'.$v['url'].'"><span class="txt">'.$v['title'].'</span></a></div>';
										}
									}
								}else{
									foreach ($menu as $k => $v) {
										$menuHtml .= '<div class="nav-item"><a class="mainmenu" href="'.$v['url'].'"><span class="txt">'.$v['title'].'</span></a></div>';
									}
								}
								$content_tmpl = '<div class="nav-menu-1 nav-menu">'.$mainNav.'<div class="nav-items-wrap"><div class="nav-items item-'.$len.'">'.$menuHtml.'</div></div></div>';
								$content_tmpl .= '<script type="text/javascript">mui(".nav-menu").on("tap", ".nav-item", function(){if($(this).hasClass("current")){$(this).removeClass("current");}else{var tw = $(this).outerWidth(true);var ch = $(this).children(".submenu");var cw = ch.outerWidth(true);ch.css({left: (tw - cw) / 2 + "px"});$(".nav-menu .nav-item").removeClass("current");$(this).addClass("current");}});</script>';
							}
						}else if($tmpl["style"] == 1){
							$len = count($tmpl["upload"]);
							$content_tmpl = '<div class="nav-menu-2 nav-menu" style="background-color: '.$tmpl["bgcolor"].';"><ul class="nav-pop-sub">';
							foreach ($tmpl["upload"] as $key => $v) {
								if($key != 0){
									$content_tmpl .= '<li class="nav-item"><a href="'.$v['url'].'" style="background-image: url('.$v['src'].');"></a></li>';
									if($key == 2){
										$content_tmpl .= '<li class="nav-item nav-main-item"><a href="'.$tmpl["upload"][0]['url'].'" style="background-image: url('.$tmpl["upload"][0]['src'].'); border-color: '.$tmpl['bgcolor'].';"></a></li>';
									}
								}
							}
							$content_tmpl .= '</ul></div>';
						}
						$content_tmpl .= '<div class="nav-menu-mask"></div><script>if($(".mui-content.mui-scroll-wrapper").length > 0){$(".mui-content.mui-scroll-wrapper").css({bottom: "1.05rem"})}</script>';
						$template .= $content_tmpl;
					}
				}
			}
		}else{
			if($hooks !==false){
				$model= MODULE_NAME.'_'.CONTROL_NAME.'_'.METHOD_NAME;
				$page = array(
					'goods_index_index' => '0' ,
					'member_index_index' => '1',
					'goods_index_category_lists' => '2',
					'goods_index_lists' => '3',
					'goods_search_search' =>'4',
					);
				$list = model('wap_template')->find();
				$tpl = explode(' ',$list['content']);
				$tmpl =	json_decode(base64_decode($tpl[2]),TRUE);
				if(is_string($tmpl['page'])){
          			$data[] = $tmpl['page'];
          			$tmpl['page'] = $data;
          		}

				$nav = array_intersect($page,$tmpl['page']);
				foreach ($nav as $key => $value) {
					if($key === $model ){
						$content_tmpl = '';
						if($tmpl["style"] == 0){
							$mainNav = '<div class="nav-main-item"><a href="'.__APP__.'" class="home">主页</a></div>';
							if(empty($tmpl["menu"])){
								$content_tmpl = '<div class="nav-menu-1 nav-menu no-menu">'.$mainNav.'</div>';
							}else{
								$menu = $tmpl["menu"];
								$len = count($menu);
								$menuHtml = '';
								if(!empty($tmpl["submenu"])){
									$submenu = $tmpl["submenu"];
									foreach ($menu as $k => $v) {
										$submenuHtml = '';
										foreach($submenu as $sub){
											if($sub && $sub["id"] == $k){
												$submenuHtml .= '<div class="submenu"><span class="arrow"></span><div class="js-nav-2nd-region"><ul>';
												if(is_array($sub["title"])){
													foreach ($sub["title"] as $tk => $tit) {
														$submenuHtml .= '<li><a href="'.$sub["url"][$tk].'">'.$tit.'</a></li>';
													}
												}else{
													$submenuHtml .= '<li><a href="'.$sub["url"].'">'.$sub["title"].'</a></li>';
												}
												$submenuHtml .= '</ul></div></div>';
											}
										}
										if($submenuHtml != ''){
											$menuHtml .= '<div class="nav-item"><a class="mainmenu" href="javascript:;"><span class="txt">'.$v['title'].'</span></a>'. $submenuHtml .'</div>';
										}else{
											$menuHtml .= '<div class="nav-item"><a class="mainmenu" href="'.$v['url'].'"><span class="txt">'.$v['title'].'</span></a></div>';
										}
									}
								}else{
									foreach ($menu as $k => $v) {
										$menuHtml .= '<div class="nav-item"><a class="mainmenu" href="'.$v['url'].'"><span class="txt">'.$v['title'].'</span></a></div>';
									}
								}
								$content_tmpl = '<div class="nav-menu-1 nav-menu">'.$mainNav.'<div class="nav-items-wrap"><div class="nav-items item-'.$len.'">'.$menuHtml.'</div></div></div>';
								$content_tmpl .= '<script type="text/javascript">mui(".nav-menu").on("tap", ".nav-item", function(){if($(this).hasClass("current")){$(this).removeClass("current");}else{var tw = $(this).outerWidth(true);var ch = $(this).children(".submenu");var cw = ch.outerWidth(true);ch.css({left: (tw - cw) / 2 + "px"});$(".nav-menu .nav-item").removeClass("current");$(this).addClass("current");}});</script>';
							}
						}else if($tmpl["style"] == 1){
							$len = count($tmpl["upload"]);
							$content_tmpl = '<div class="nav-menu-2 nav-menu" style="background-color: '.$tmpl["bgcolor"].';"><ul class="nav-pop-sub">';
							foreach ($tmpl["upload"] as $key => $v) {
								if($key != 0){
									$content_tmpl .= '<li class="nav-item"><a href="'.$v['url'].'" style="background-image: url('.$v['src'].');"></a></li>';
									if($key == 2){
										$content_tmpl .= '<li class="nav-item nav-main-item"><a href="'.$tmpl["upload"][0]['url'].'" style="background-image: url('.$tmpl["upload"][0]['src'].'); border-color: '.$tmpl['bgcolor'].';"></a></li>';
									}
								}
							}
							$content_tmpl .= '</ul></div>';
						}
						$content_tmpl .= '<div class="nav-menu-mask"></div><script>if($(".mui-content.mui-scroll-wrapper").length > 0){$(".mui-content.mui-scroll-wrapper").css({bottom: "1.05rem"})}</script>';
						$template .= $content_tmpl;
					}
				}
			}
		}
	}
}