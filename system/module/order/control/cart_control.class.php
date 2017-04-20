<?php
/**
 * 		前台购物车控制器
 */
hd_core::load_class('init', 'goods');
class cart_control extends init_control {

	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('order/cart');
		$this->sku_service = $this->load->service('goods/goods_sku');
		$this->service_delivery = $this->load->service('order/delivery');
		$this->member = $this->load->service('member/member')->init();
	}

	/* 购物车页面 */
	public function index() {
		$SEO = seo('购物车信息');
		$carts = $this->get_carts(TRUE);
		$this->load->librarys('View')->assign('SEO',$SEO)->assign('carts',$carts)->display('cart');
	}

	/**
	 * 获取购物车
	 * @param boolean $group 是否根据商家分组
	 * @return result
	 */
	public function get_carts($group = FALSE) {
		if ($_GET['group'] == TRUE) $group = TRUE;
		$result = $this->service->get_cart_lists((int) $this->member['id'] ,'',$group);
		if (!$result) showmessage($this->service->error);
		$this->load->librarys('View')->assign('result',$result);
        $result = $this->load->librarys('View')->get('result');
		if ($_GET['format'] == 'json') {
			echo json_encode($result);
		} else {
			return $result;
		}
	}

	/**
	 * 添加加购物车 (支持批量添加)
	 * @param  array	$params ：array('sku_id' => 'nums'[,'sku_id' => 'nums'...])
	 * @param  int    $nums 	数量
	 * @return [boolean]
	 */
	public function cart_add(){
		runhook('before_cart_add');
		$params = array_filter($_GET['params']);
		if (empty($params)) showmessage(lang('parameter_empty','order/language'));
		$result = $this->service->cart_add($params , (int) $this->member['id'] ,$_GET['buynow']);
		if (!$result) showmessage($this->service->error);
		$forward = array();
		foreach ($params as $skuid => $number) {
			$forward[] = (int) $skuid.','.(int) $number;
		}
		showmessage(lang('购物车添加商品成功'), url('order/order/settlement', array('skuids' => implode(";", $forward))), 1);
	}

	/**
	 * 设置购物车商品数量
	 * @param int 	$sku_id 商品sku_id
	 * @param int 	$nums 	要设置的数量
	 * @return [boolean]
	 */
	public function set_nums() {
		$result = $this->service->set_nums($_GET['sku_id'] , $_GET['nums'] , (int) $this->member['id']);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),'',1,'json');
	}

	/**
	 * 删除购物车商品
	 * @param  int 	$sku_id 商品sku_id
	 * @return [boolean]
	 */
	public function delpro() {
		$result = $this->service->delpro($_GET['sku_id'], (int) $this->member['id']);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),'',1,'json');
	}

	/**
	 * 清空购物车
	 * @return [boolean]
	 */
	public function clear() {
		$result = $this->service->clear((int) $this->member['id']);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('清空购物车成功'),'',1,'json');
	}

	/* 加入购物车成功 */
	public function success() {
		$carts = $this->service->get_cart_lists((int) $this->member['id'] ,'',false);
		// 加入购物车后跳转设置
		$cart_jump = $this->load->service('admin/setting')->get('cart_jump');
		$SEO = seo('加入购物车成功');
		$this->load->librarys('View')->assign('carts',$carts)->assign('cart_jump',$cart_jump)->assign('SEO',$SEO)->display('cart_success');
	}

	/* 购物车列表页更换商品规格 */
	public function goods_spec() {
		$goods = $this->sku_service->detail($_GET['sku_id']);
		$this->load->librarys('View')->assign('goods',$goods)->display('cart_popup');
	}

	/* 修改购物车skuid */
	public function change_skuid() {
		$result = $this->service->change_skuid($_GET['old_skuid'],$_GET['new_skuid'] ,(int) $this->member['id']);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),'',1,'json');
	}

	/* 清除已售馨商品 */
	public function clear_sold_out() {
		$result = $this->service->clear_sold_out((int) $this->member['id']);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),'',1,'json');
	}

	/* 获取物流信息 */
	public function get_delivery_log() {
		$o_d_id = (int) $_GET['o_d_id'];
		$result = $this->service_delivery->get_delivery_log($o_d_id);
		if (!$result) {
			showmessage($this->service_delivery->error);
			return FALSE;
		}
		showmessage(lang('_operation_success_'),'',1,$result,'json');
	}
	public function ajax_order_goods(){
        $id = $_GET['id'];
	 	//$sid = $_GET['sku_id'];
	 	$isspu = TRUE;
	 	$all = TRUE;
	 	$options['limit'] = 10;
	 	$options['page'] = (int)$_GET['page'];
		$result = $this->load->service('order/order_sku')->records($id,$isspu,$all,$options);
		$result['pages'] = pages($result['count'],10);
		$this->load->librarys('View')->assign('result',$result);
        $result = $this->load->librarys('View')->get('result');
	 	echo json_encode($result);
	}
	public function add_group_cart(){
		$lists = array();
		foreach ($_GET['sku_id'] AS $sku_id) {
			$item = array();
			$item['sku_id'] = $sku_id;
			$lists[] = $item;
		}
		$result = $this->service->add($lists);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('购物车添加商品成功'),'',1,'json');
	}
}