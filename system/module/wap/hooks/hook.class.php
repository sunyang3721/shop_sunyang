<?php
class module_wap_hook
{
	public function pre_system() {
		$load = hd_load::getInstance();
		$mobile = $load->librarys('mobile');
		if(config('wap_enabled' ,'wap') && $mobile->isMobile() === TRUE) {
            define('MOBILE', TRUE);
            if(config('is_jump','wap') && config('wap_domain','wap') && stripos(strtolower($_SERVER['HTTP_HOST']), config('wap_domain','wap')) === FALSE && !defined('IN_ADMIN')) {
				$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$url = str_replace($_SERVER['HTTP_HOST'],config('wap_domain','wap'),$url);
                redirect((is_ssl() ? 'https://' : 'http://').$url);
            }
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') != false ){
                define('IS_WECHAT',TRUE);
            }
            config('TPL_THEME', null, 'wap');
        }
	}
	public function tmpl_compile(&$template){
		preg_match_all('/diy\s+(.+)}/',$template,$arr);
		if(empty($arr[0])){
			return $template;
		}
		$compile_tmpl = '';
		$content_tmpl = '';
		foreach ($arr[0] AS $base_tpl) {
			$tpl = explode(' ',$base_tpl);
			$tmpl = $tpl[1] == 'content' ? base64_decode($tpl[2]) : json_decode(base64_decode($tpl[2]),TRUE);
			switch ($tpl[1]) {
				case 'global':
					$content_tmpl = '';
					$content_tmpl .= '<body><style>body{background-color: '.$tmpl['bgcolor'].';}</style><header class="mui-bar mui-bar-nav header" style="background-color: '.$tmpl['headbg'].'">
<div class="logo mui-pull-left"><a href="{url(\'goods/index/index\')}"><img src="'.$tmpl['logo'].'" height="30" /></a></div><h1 class="mui-title">'.$tmpl['title'].'</h1><a href="{url(\'goods/index/category_lists\')}" class="hd-menu"><span class="mui-icon mui-icon-more"></span></a></header>';
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/','',$template,1);
					$template = preg_replace('/<body>/',$content_tmpl,$template,1);
					break;
				case 'ads':
					$content_tmpl = '';
					if($tmpl['show'] == 0){
						$content_tmpl .= '<div class="mui-slider"><div class="mui-slider-group">';
						foreach ($tmpl['imgs'] as $key => $value) {
							$content_tmpl .= '<a class="mui-slider-item" href="';
							if($value['url']){
								$content_tmpl .= $value['url'] .= '">';
							}else{
								$content_tmpl .= 'javascript:;">';
							}
							if($value['src']){
								$content_tmpl .= '<img src="'.$value['src'].'" />';
							}
							if($value['title']){
								$content_tmpl .= '<p class="mui-slider-title">'.$value['title'].'</p>';
							}
							$content_tmpl .= '</a>';
						}
						$content_tmpl .= '</div>';
						$content_tmpl .= '<div class="mui-slider-indicator">';
						foreach ($tmpl['imgs'] as $i => $val) {
							if($i == 0){
								$content_tmpl .= '<div class="mui-indicator mui-active"></div>';
							}else{
								$content_tmpl .= '<div class="mui-indicator"></div>';
							}
						}
						$content_tmpl .= '</div></div>';
					}else{
						$content_tmpl .= '<ul class="custom-image mui-clearfix">';
						foreach ($tmpl['imgs'] as $key => $value) {
							$content_tmpl .= '<li';
							if($tmpl['size']==1){
								$content_tmpl .= ' class="custom-image-small"';
							}
							$content_tmpl .= '><a href="';
							if($value['url']){
								$content_tmpl .= $value['url'].'">';
							}else{
								$content_tmpl .= 'javascript:;'.'">';
							}
							if($value['src']){
								$content_tmpl .= '<img src="'.$value['src'].'" />';
							}
							if($value['title']){
								$content_tmpl .= '<p class="hd-slider-title">'.$value['title'].'</p>';
							}
							$content_tmpl .= '</a></li>';
						}
						$content_tmpl .= '</ul>';
					}
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'goods':
					$content_tmpl = '';
					$content_tmpl .= '<ul class="custom-goods-items';
					switch ($tmpl['size']) {
						case 0:
							$content_tmpl .= ' custom-goods-single';
							break;
						case 2:
							$content_tmpl .= ' custom-goods-blend';
							break;
						case 3:
							$content_tmpl .= ' custom-goods-row';
							break;
						default:
							break;
					}
					$sku_url = '{url("goods/index/detail",array("sku_id" => $r[sku_id]))}';
					$content_tmpl .= ' mui-clearfix">';
					$content_tmpl .= '{hd:goods tagfile="goods" method="lists" catid="'.$tmpl['category'].'" num="'.$tmpl['goods_number'].'"}';
					$content_tmpl .= '{loop $data $r}';
					$content_tmpl .= '<li class="goods-item-list"><a class="list-item" href="'.$sku_url.'"><div class="list-item-pic"><div class="square-item"><img class="lazy" src="{SKIN_PATH}statics/images/loading.gif" data-original="{thumb($r[thumb],500,500)}"></div></div><div class="list-item-bottom"><div class="list-item-title"><span>{$r[sku_name]}</span></div><div class="list-item-text"><span class="price-org">￥{$r[prom_price]}</span></div></div></a></li>';
					$content_tmpl .= '{/loop}';
					$content_tmpl .= '{/hd}';
					$content_tmpl .= '</ul>';
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'search':
					$content_tmpl = '';
					$content_tmpl .= '<div class="hd-search"><form name="form_search" action="{__ROOT__}" method="get"><input type="hidden" name="m" value="goods"><input type="hidden" name="c" value="search"><input type="hidden" name="a" value="search"><input type="search" placeholder="搜索商品名称" name="keyword"></form></div>';
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'spacing':
					$content_tmpl = '';
					$content_tmpl .= '<div class="custom-white" style="height: '.$tmpl['height'].'px;"></div>';
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'cube':
					$content_tmpl = '';
					$table = '';
					foreach (json_decode($tmpl['layout'],TRUE) AS $layouts) {
						$table .= '<tr>';
						foreach ($layouts AS $layout) {
							if($layout['flog']){
								$table .= '<td class="empty"></td>';
							}else{
								if($layout['width'] && $layout['height']){
									$imgs = '';
									$img = $tmpl['imgs'][$layout['index']];
									if($img['src']){
										$imgs = $img['src'] ? '<img src="'.$img['src'].'" />' : '';
										if($img['href']){
											$imgs = '<a href="'.$img['href'].'">'.$imgs.'</a>';
										}
									}
									$table .= '<td class="no-empty cols-'.$layout['width'].' rows-'.$layout['height'].'" colspan="'.$layout['width'].'" rowspan="'.$layout['height'].'">'.$imgs.'</td>';
								}
							}
						}
						$table .= '</tr>';
					}
					$content_tmpl .= '<table class="cube-table"><tbody>'.$table.'</tbody></table>';
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'content':
					$content_tmpl = '';
					$content_tmpl .= $tmpl;
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'notice':
					$content_tmpl = '';
					if($tmpl['style'] == 0){
						$content_tmpl .= '<a class="custom-notice custom-notice-jd" href="'.$tmpl["upload"][0]["url"].'"><div class="notice-img"><img src="'.$tmpl["upload"][0]["src"].'" /></div><div class="notice-text mui-ellipsis" style="color: '.$tmpl["color"].';">'.$tmpl["title"].'</div></a>';
					}else if($tmpl['style'] == 1){
						$content_tmpl .= '<a class="custom-notice custom-notice-tmall" href="'.$tmpl["upload"][0]["url"].'"><div class="notice-img"><img src="'.$tmpl["upload"][0]["src"].'" /></div><div class="notice-text mui-ellipsis"><div class="notice-title"><div class="main-title" style="color: '.$tmpl["color"].';">'.$tmpl["title"].'</div><div class="sub-title" style="color: '.$tmpl["subcolor"].';">'.$tmpl["subtitle"].'</div></div></div></a>';
					}
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				case 'nav';
					$content_tmpl = '';
					$className = 'hd-col-xs-e4 ';
					if($tmpl["type"] == 1 || $tmpl["type"] == 3){
						$className = 'hd-col-xs-e5 ';
					}
					$content_tmpl .= '<nav class="quick-entry-nav hd-grid">';
					$uploads = $tmpl["upload"];
					for($i= 0;$i< count($uploads); $i++){
						$content_tmpl .= '<a href="'.$uploads[$i]["url"].'" class="'.$className.'quick-entry-link"><span class="nav-img"><img src="'.$uploads[$i]["src"].'" /></span><span class="title">'.$uploads[$i]["title"].'</span></a>';
					}
					$content_tmpl .= '</nav>';
					$template = preg_replace('/<!--{diy '.$tpl[1].'+(.+)}-->/',$content_tmpl,$template,1);
					break;
				default:
					break;
			}

		}
		$template = preg_replace('/<\/header>/','</header><div class="mui-content">',$template);
		$compile_tmpl .= '</div>';
		$compile_tmpl .= '<footer class="footer">';
		$compile_tmpl .= SITE_AUTHORIZE == 0? '<div class="copyright"></div>':"";
		$compile_tmpl .= '</footer>';
		$compile_tmpl .= '<script>$("[name=form_search]").submit(function(){if($("[type=search]").val() == ""){return false;}});</script>';
		$compile_tmpl .= '</body>';
		$template = preg_replace('/<\/body>/',$compile_tmpl,$template);
	}
}