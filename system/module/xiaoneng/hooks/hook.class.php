<?php
class module_xiaoneng_hook{
	public function __construct() {
		$this->status = model('xiaoneng/xiaoneng','service')->get_config('status');
		$this->xn_config = unserialize(authcode(config('__xiaoneng__','xiaoneng'),'DECODE'));
		if(!$this->xn_config) return FALSE;
		$this->server_config = model('xiaoneng/xiaoneng','service')->get_config('config');
		$this->member = model('member/member','service')->init();
		$this->isvip = $this->member['group_id'] > 0 ? 1 : 0;
	}

	public function global_footer() {
		if(!$this->status) return FALSE;
		//客服配置
		if(MODULE_NAME == 'goods' && CONTROL_NAME == 'index' && METHOD_NAME == 'detail'){
			$settingid = $this->server_config['goods'];
		}elseif (MODULE_NAME == 'order' && CONTROL_NAME == 'cart' && METHOD_NAME == 'index') {
			$settingid = $this->server_config['cart'];
		}elseif (MODULE_NAME == 'order' && CONTROL_NAME == 'order' && METHOD_NAME == 'settlement') {
			$settingid = $this->server_config['order_settle'];
		}elseif (MODULE_NAME == 'order' && CONTROL_NAME == 'order' && METHOD_NAME == 'detail') {
			$settingid = $this->server_config['order_success'];
		}elseif (MODULE_NAME == 'order' && CONTROL_NAME == 'order' && METHOD_NAME == 'pay_success') {
			$settingid = $this->server_config['pay_success'];
		}else{
			$settingid = $this->server_config['common'];
		}

		if (is_array($this->server_config['diy']['url']) && in_array('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $this->server_config['diy']['url'])) {
			$key = array_search((is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $this->server_config['diy']['url']);
			$settingid = $this->server_config['diy']['server'][$key];
		}
		//会员信息
		$html = '';
		$html .= '<script language="javascript" type="text/javascript">';
		$html .= 'NTKF_PARAM = {';
    	$html .= 'siteid:"' . $this->xn_config['siteid'] . '",';
    	$html .= 'settingid:"' . $settingid . '",';
    	$html .= 'uid:"' . $this->member['id'] . '",';
    	$html .= 'uname:"' . $this->member['username'] . '",';
    	$html .= 'isvip:"' . $this->isvip . '",';
    	$html .= 'userlevel:"' . $this->member['group_id'] . '",';
    	if(MODULE_NAME == 'goods' && CONTROL_NAME == 'index' && METHOD_NAME == 'detail'){
	    	$html .= 'itemid:"'. $_GET['sku_id'] .'",';
	    }
	    if(MODULE_NAME == 'goods' && CONTROL_NAME == 'index' && (METHOD_NAME == 'lists' || METHOD_NAME == 'brand_list')){
	    	$html .= 'ntalkerparam:{';
	    	if(METHOD_NAME == 'lists'){
	    		$category = model('goods/goods_category')->where(array('id'=>$_GET['id']))->getfield('name');
	    		$html .= 'category:"'. $category .'",';
	    	}
	    	if(METHOD_NAME == 'brand_list'){
	    		$brand = model('goods/brand')->where(array('id'=>$_GET['id']))->getfield('name');
	    		$html .= 'brand:"'. $brand .'",';
	    	}
	    	$html .= '}';
	    }
	    if(MODULE_NAME == 'order' && (CONTROL_NAME == 'cart' && METHOD_NAME == 'index') || (CONTROL_NAME == 'order' && METHOD_NAME == 'settlement')){
	    	$carts = model('xiaoneng/xiaoneng','service')->get_cart_format($this->member['id'],$_GET['skuids']);
	    	$html .= 'ntalkerparam:{';
	    	if(empty($_GET['skuids'])){
	    		$html .= 'cartprice:"' . $carts['cartprice'] . '",';
	    	}
	    	$html .= 'items:'.json_encode($carts['items']);
	    	$html .= '}';
	    }
	    if (MODULE_NAME == 'order' && CONTROL_NAME == 'order' && (METHOD_NAME == 'detail' || METHOD_NAME == 'pay_success')) {
	    	if(METHOD_NAME == 'detail') $orderprice = model('order/order')->where(array('sn' => $_GET['order_sn']))->getfield('real_amount');
	    	if(METHOD_NAME == 'pay_success') $orderprice = model('order/order')->where(array('sn' => $_GET['order_sn']))->getfield('paid_amount');
			$html .= 'orderid:"'.$_GET['order_sn'].'",';
			$html .= 'orderprice:"'.$orderprice.'"';
		}

		$html .= '};';
		$html .= '$(".hd-tbar-tab-custom").live("click",function(){';
		$html .= 'NTKF.im_openInPageChat();';
		$html .= '})';
		$html .= '</script>';
		$html .= '<script type="text/javascript" src="http://dl.ntalker.com/js/xn6/ntkfstat.js?siteid='.$this->xn_config['siteid'].'" charset="utf-8"></script>';
		return $html;
	}

	public function wap_goods_detail_footer() {
		$settingid = $this->server_config['goods'];
		$html = '<style>.ui-mobile-container{display:none;}</style>';
		$html .= '<script language="javascript" type="text/javascript">';
		$html .= ' $(function(){';
		$html .= '$(".ui-mobile-container").remove();';
    	$html .= 'var customIcoUrl = "'.__ROOT__.'system/module/xiaoneng/statics/images/wap_ico_custom_service.png";';
    	$html .= 'function replaceFooterCollect(){';
      	$html .= 'var collectHtml = $(".collect-btn").html();';
      	$html .= '$(".collect-btn").replaceWith(\'<a href="javascript:;" class="mui-col-xs-6 xiaonengkf"><span class="mui-icon"><img src="\'+ customIcoUrl +\'"></span><span class="mui-tab-label text-gray">客服</span></a>\');';
      	$html .= '$(".basic-info").addClass("basic-collect");';
      	$html .= '$(".basic-info .pro-title").before(\'<style>.basic-collect{position:relative}.basic-collect .pro-title{width:85%}.basic-collect .collect-btn{position:absolute;top:.2rem;right:0;width:15%;height:.8rem;border-left:1px solid #41454e;text-align:center;}.basic-collect .collect-btn img{margin-top:.08rem;width:.4rem;}.basic-collect .collect-btn .collect_text{display:block;}</style>\');';
      	$html .= '$(".basic-info .pro-title").after(\'<a href="javascript:;" class="collect-btn">\'+ collectHtml +\'</a><div class="mui-clearfix"></div>\');';
    	$html .= '}';
    	$html .= 'replaceFooterCollect();';
  		$html .= '});';
		$html .= 'NTKF_PARAM = {';
    	$html .= 'siteid:"' . $this->xn_config['siteid'] . '",';
    	$html .= 'settingid:"' . $settingid . '",';
    	$html .= 'uid:"' . $this->member['id'] . '",';
    	$html .= 'uname:"' . $this->member['username'] . '",';
    	$html .= 'isvip:"' . $this->isvip . '",';
    	$html .= 'userlevel:"' . $this->member['group_id'] . '",';
    	$html .= 'itemid:"'. $_GET['sku_id'] .'",';
    	$html .= '};';
		$html .= '$("body").on("tap",".xiaonengkf",function(){';
		if(!$this->status){
			$html .= '$.tips({';
			$html .= 'content:"客服功能未开启",';
			$html .= 'callback:function() {}';
			$html .= '});';
		}else{
			$html .= 'NTKF.im_openInPageChat();';
		}
		$html .= '})';
		$html .= '</script>';
		$html .= '<script type="text/javascript" src="http://dl.ntalker.com/js/xn6/ntkfstat.js?siteid='.$this->xn_config['siteid'].'" charset="utf-8"></script>';
		return $html;
	}
}