<?php
/**
 * 		后台订单 控制器
 */
hd_core::load_class('init', 'admin');
class admin_order_control extends init_control {

	public function _initialize() {
		parent::_initialize();
		/* 服务层 */
		$this->service = $this->load->service('order/order');
		$this->service_p_delivery = $this->load->service('order/delivery');
		$this->service_sub = $this->load->service('order/order_sub');
		$this->service_order_log = $this->load->service('order/order_log');
		$this->service_tpl_parcel = $this->load->service('order/order_tpl_parcel');
		$this->service_parcel = $this->load->service('order/order_parcel');
	}

	/* 订单列表管理 */
	public function index() {
		// 查询条件
		$sqlmap = array();
		$sqlmap = $this->service->build_sqlmap($_GET);
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$orders = $this->service->get_order_lists($sqlmap,$_GET['page'],$limit);
		$count  = $this->service->count($sqlmap);
		$pages  = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array(
				'sn' => array('title' => '订单号','length' => 15),
				'username' => array('title' => '会员帐号','length' => 10),
				'address_name' => array('length' => 10,'title' => '收货人'),
				'address_mobile' => array('title' => '收货电话','length' => 9),
				'system_time' => array('length' => 12,'title' => '下单时间','style'=>'date'),
				'real_amount' => array('length' => 8,'title' => '订单金额'),
				'_pay_type' => array('length' => 7,'title' => '支付方式'),
				'source' => array('length' => 8,'title' => '订单类型','style' => 'source'),
				'seller_ids' => array('length' => 8,'title' => '商家名称','style' => 'seller'),
				'_status' => array('length' => 7,'title' => '订单状态','style'=>'_status')
			),
			'lists' => $orders,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('index');
	}

	/* 订单详细页面 */
	public function detail() {
		$order = $this->service_sub->find(array('sub_sn' => $_GET['sub_sn']));
		if (!$order) showmessage(lang('order_not_exist','order/language'));
		// $order['_member'] = $this->load->service('member/member')->find($order['buyer_id']);
		$order['_member'] = $this->service->member_data($order['buyer_id']);
		$order['_main'] = $this->service->find(array('sn' => $order['order_sn']));
		foreach ($order['_skus'] as $key => $value) {
			if($key > 0){
				$status = 1;
			}
		}
		// 日志
		$order_logs = $this->service_order_log->get_by_order_sn($order['sub_sn'],'id DESC');
		$this->load->librarys('View')->assign('order',$order)->assign('order_logs',$order_logs)->assign('status',$status)->display('detail');
	}

	public function address_edit(){
		if(checksubmit('dosubmit')){
			$result = $this->service->edit_address($_GET);
			if($result===false){
				showmessage('修改订单收货信息失败');
			}
			showmessage(lang('修改订单收货信息成功'),'',1,'json');
		}else{
			$order = array();
			$order_sn = $_GET['order_sn'];
			$order['_main'] = $this->service->find(array('sn' =>$order_sn));
			$address_detail = end(explode(" ",$order['_main']['address_detail']));
			$cids = explode(",",$order['_main']['address_district_ids']);
		    array_pop($cids);
			$cid = end($cids);
			$order['cids']=$cids;
			$order['cid']=$cis;
			$order['sn']=$order_sn;
			$order['_main']['address_detail'] = $address_detail;
			$this->load->librarys('View')->assign('order',$order)->display('address');
		}
	}

	/* 发货单模版 */
	public function tpl_parcel() {
		if (checksubmit('dosubmit')) {
			$result = $this->service_tpl_parcel->update($_GET['content']);
			if (!$result) showmessage($this->service_tpl_parcel->error);
			showmessage(lang('_operation_success_'),url('tpl_parcel'),1,'json');
		} else {
			$info = $this->service_tpl_parcel->get_tpl_parcel_by_id(1);
			$this->load->librarys('View')->assign('info',$info)->display('tpl_parcel');
		}
	}

	/* 确认付款 */
	public function pay() {
		if (checksubmit('dosubmit')) {
			$params = array();
			$params['pay_status'] = remove_xss(strtotime($_GET['pay_status']));
			$params['paid_amount']= sprintf("%0.2f", (float) $_GET['paid_amount']);
			$params['pay_method'] = remove_xss($_GET['pay_method']);
			$params['pay_sn']     = remove_xss($_GET['pay_sn']);
			$params['msg']        = remove_xss($_GET['msg']);
			if ($params['pay_method'] != 'other' && !$params['pay_sn']) {
				showmessage(lang('pay_deal_sn_empty','order/language'));
			}
			$result = $this->service_sub->set_order($_GET['order_sn'] ,'pay' ,'',$params);
			if (!$result) showmessage($this->service_sub->error);
			showmessage(lang('pay_success','order/language'),'',1,'json');
		} else {
			// 获取所有已开启的支付方式
			$pays = model('pay/payment','service')->get();
			foreach ($pays as $k => $pay) {
				$pays[$k] = $pay['pay_name'];
			}
			$pays['other'] = '其它付款方式';
			$order = $this->service->find(array('sn' => $_GET['order_sn']));
			$this->load->librarys('View')->assign('pays',$pays)->assign('order',$order)->display('alert_pay');
		}
	}

	/* 确认订单 */
	public function confirm() {
		if (checksubmit('dosubmit')) {
			$result = $this->service_sub->set_order($_GET['sub_sn'] ,'confirm' ,'',array('msg' => $_GET['msg']));
			if (!$result) showmessage($this->service_sub->error);
			showmessage(lang('确认订单成功'),'',1,'json');
		} else {
			$this->load->librarys('View')->display('alert_confirm');
		}
	}

	/* 确认发货 */
	public function delivery() {
		if (checksubmit('dosubmit')) {
			$params = array();
			$params['is_choise']     = remove_xss($_GET['is_choise']);
			$params['delivery_id']   = remove_xss($_GET['delivery_id']);
			$params['delivery_sn']   = remove_xss($_GET['delivery_sn']);
			$params['sub_sn']   	 = remove_xss($_GET['sub_sn']);
			$params['msg']           = remove_xss($_GET['msg']);
			$o_sku_ids = remove_xss($_GET['o_sku_id']);
			$params['o_sku_ids']	 = implode(',', $o_sku_ids);
			$result = $this->service_sub->set_order($_GET['sub_sn'] ,'delivery' ,$_GET['status'],$params);
			if (!$result) showmessage($this->service_sub->error);
			showmessage(lang('确认发货成功'),'',1,'json');
		} else {
			// 获取已开启的物流
			$sqlmap = $deliverys = array();
			$sqlmap['enabled'] = 1;
			$deliverys = $this->load->service('order/delivery')->getField('id,name' ,$sqlmap);
			// 获取子订单下的skus
			$o_skus = $this->service_sub->sub_delivery_skus($_GET['sub_sn']);
			if (!$o_skus) {
				showmessage($this->service_sub->error);
			}
			$this->load->librarys('View')->assign('deliverys',$deliverys)->assign('o_skus',$o_skus)->display('alert_delivery');
		}
	}

	/* 确认完成 */
	public function finish() {
		if (checksubmit('dosubmit')) {
			$result = $this->service_sub->set_order($_GET['sub_sn'] ,'finish' ,'',array('msg'=>$_GET['msg']));
			if (!$result) showmessage($this->service_sub->error);
			showmessage(lang('确认完成成功'),'',1,'json');
		} else {
			$this->load->librarys('View')->display('alert_finish');
		}
	}

	/* 取消订单 */
	public function cancel() {
		if (checksubmit('dosubmit')) {
			$order_sn = $this->load->service('order/order_sub')->getField('order_sn', array('sub_sn'=>$_GET['sub_sn']));
			$this->load->service('order/order_trade')->setField(array('status'=>-1), array('order_sn'=>$order_sn));

			$result = $this->service_sub->set_order($_GET['sub_sn'] ,'order' ,2,array('msg'=>$_GET['msg'],'isrefund' => 1));
			if (!$result) showmessage($this->service_sub->error,'',0,'json');
			showmessage(lang('cancel_order_success','order/language'),'',1,'json');
		} else {
			$order = $this->service_sub->find(array('sub_sn' => $_GET['sub_sn']));
			$this->load->librarys('View')->assign('order',$order)->display('alert_cancel');
		}
	}

	/* 作废 */
	public function recycle() {
		if (checksubmit('dosubmit')) {
			$result = $this->service_sub->set_order($_GET['sub_sn'] ,'order' ,3,array('msg'=>$_GET['msg']));
			if (!$result) showmessage($this->service_sub->error);
			showmessage(lang('cancellation_order_success','order/language'),'',1,'json');
		} else {
			$this->load->librarys('View')->display('alert_recycle');
		}
	}

	/* 删除订单 */
	public function delete() {
		if (checksubmit('dosubmit')) {
			$result = $this->service_sub->set_order($_GET['sub_sn'] ,'order' ,4);
			if (!$result) showmessage($this->service_sub->error);
			showmessage(lang('删除订单成功'),url('order/admin_order/index'),1,'json');
		} else {
			showmessage(lang('_error_action_'));
		}
	}

	/* 修改订单应付总额 */
	public function update_real_price() {
		if (checksubmit('dosubmit')) {
			$result = $this->service->update_real_price($_GET['sub_sn'] ,$_GET['real_price']);
			if (!$result) {
				showmessage($this->service->error);
			}
			showmessage(lang('修改订单应付总额成功'),'',1,'json');
		} else {
			$order = $this->service_sub->find(array('sub_sn' => $_GET['sub_sn']));
			$this->load->librarys('View')->assign('order',$order)->display('alert_update_real_price');
		}
	}

	/* 发货单管理 */
	public function parcel() {
		$sqlmap = array();
		if (isset($_GET['status'])) {
			$sqlmap['status'] = $_GET['status'];
		}
		if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
			$sqlmap['order_sn|member_name|address_mobile'] = array('LIKE','%'.$_GET['keyword'].'%');
		}
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 10;
		$parcels = $this->service_parcel->get_lists($sqlmap,$_GET['page'],$_GET['limit']);
		$count  = $this->load->service('order/order_parcel')->count();
		$pages  = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array(
				'order_sn' => array('title' => '订单号','length' => 15),
				'member_name' => array('title' => '会员账号','length' => 10),
				'address_name' => array('length' => 10,'title' => '收货人姓名'),
				'address_detail' => array('title' => '联系地址','length' => 30,'style'=>'left_text'),
				'address_mobile' => array('length' => 10,'title' => '联系电话'),
				'status' => array('length' => 10,'title' => '配送状态','style' => 'status'),
			),
			'lists' => $parcels,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('parcel');
	}

	/*确认配送*/
	public function complete_parcel() {
		if((int)$_GET['id'] < 1) showmessage(lang('_error_action_'));
		if (checksubmit('dosubmit')) {
			$result = $this->service_parcel->complete_parcel($_GET);
			if(!$result){
				showmessage($this->service_parcel->error);
			}
			showmessage(lang('_operation_success_'),url('parcel'),1);
		}else{
			$parcelinfo = $this->load->service('order/order_parcel')->fetch_by_id($_GET['id']);
			$this->load->librarys('View')->assign('parcelinfo',$parcelinfo)->display('alert_complete_parcel');
		}
	}

	/*删除发货单*/
	public function delete_parcel() {
		if((int) $_GET['id'] < 1) showmessage(lang('_error_action_'));
		$result = $this->service_parcel->delete_parcel($_GET['id']);
		if(!$result){
			showmessage($this->service_parcel->error);
		}
		showmessage(lang('_operation_success_'),url('order/admin_order/parcel'),1);
	}

	/*打印发货单*/
	public function prints(){
		if((int)$_GET['id'] < 1) showmessage(lang('_error_action_'));
		$info = $this->service_tpl_parcel->get_tpl_parcel_by_id(1);
		//订单信息
		$sub_sn = $this->load->service('order/order_parcel')->fetch_by_id($_GET['id'],'sub_sn');
		$order_sn = $this->load->service('order/order_parcel')->fetch_by_id($_GET['id'],'order_sn');
		//收货人信息
		$userinfo = $this->load->service('order/order_parcel')->find(array('sub_sn'=>$sub_sn));
		//商品信息
		$goods = $this->load->service('order/order_sku')->fetch(array('sub_sn'=>$sub_sn));
		$info['content'] = str_replace('{order_sn}',$order_sn,$info['content']);
		$info['content'] = str_replace('{address}',$userinfo['address_detail'],$info['content']);
		$info['content'] = str_replace('{print_time}',date('Y-m-d H:i:s',time()),$info['content']);
		$info['content'] = str_replace('{accept_name}',$userinfo['address_name'],$info['content']);
		$info['content'] = str_replace('{mobile}',$userinfo['address_mobile'],$info['content']);
		$info['content'] = str_replace('{delivery_txt}',$userinfo['delivery_name'],$info['content']);
		$field_start = substr($info['content'],strpos($info['content'],'<tr id="goodslist">'));
		$field_end = substr($info['content'],strpos($info['content'],'<tr id="goodslist">'),strpos($field_start, '<tr>'));
		$total_num = 0;
		$total_price = 0;
		foreach($goods as $k => $v){
			$str = str_replace('{sort_id}',$k+1,$field_end);
			$sku = $this->load->service('goods/goods_sku')->fetch_by_id($v['sku_id']);
			$str = str_replace('{products_sn}',$sku['sn'],$str);
			$str = str_replace('{goods_name}',$v['sku_name'],$str);
			$str = str_replace('{goods_spec}',$v['_sku_spec'],$str);
			$str = str_replace('{shop_price}',$v['sku_price'],$str);
			$str = str_replace('{number}',$v['buy_nums'],$str);
			$str = str_replace('{total_goods_price}',$v['real_price'],$str);
			$goods[$k] = $str;
			$total_num = $total_num + $v['buy_nums'];
			$total_price =  $total_price + $v['real_price'];
		}
		$goods=implode('', $goods);
		$info['content']=str_replace($field_end, $goods, $info['content']);
		$info['content']=str_replace('{total_num}', $total_num, $info['content']);
		$info['content']=str_replace('{total_price}', number_format($total_price,2), $info['content']);
		$this->load->librarys('View')->assign('info',$info)->assign('sub_sn',$sub_sn)->display('prints_parcel');
	}


	/* 快递单管理 */
	public function deliverys() {
		$sqlmap = array();
		$sqlmap['delivery_id'] = array("GT" ,0);
		if (isset($_GET['isprint'])) {
			if ($_GET['isprint'] == 1) {
				$sqlmap['print_time'] = array("GT" ,0);
			} else if($_GET['isprint'] == 0) {
				$sqlmap['print_time'] = array("EQ" , 0);
			}
		} else {
			$sqlmap['print_time'] = array("EQ" , 0);
		}
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 10;
		$o_deliverys = $this->service_p_delivery->get_lists($sqlmap,$page,$limit);
		$count = $this->service->order_delivery_count($sqlmap);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array(
				'order_sn' => array('title' => '订单号','length' => 15),
				'delivery_name' => array('title' => '物流名称','length' => 15),
				'delivery_sn' => array('length' => 20,'title' => '物流编号'),
				'delivery_time' => array('title' => '发货时间','length' => 20,'style'=>'date'),
				'receive_time' => array('length' => 10,'title' => '是否收货'),
				'print_time' => array('length' => 10,'title' => '打印状态'),
			),
			'lists' => $o_deliverys,
			'pages' => $pages,
		);
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('deliverys');
	}

	/* 打印快递单 */
	public function print_kd() {
		if (checksubmit('dosubmit')) {
			/* 标记快递单为已打印 */
			$o_id = (int) $_GET['o_id'];
			$this->service->order_delivery_update(array('print_time' => time()), array('id' => $_GET['o_id']));
			return TRUE;
		} else {
			$o_delivery = $this->service->order_delivery_find(array('id' => $_GET['o_id']));
			$sub_order = $this->load->service('order/order_sub')->sub_detail($o_delivery['sub_sn']);
			$main_order = $this->service->find(array('sn' => $sub_order['order_sn']));
			$setting = $this->load->service('admin/setting')->get();
			$_delivery = $this->load->service('order/delivery')->find(array('id' => $o_delivery['delivery_id']));
			// 替换值
			if ($_delivery['tpl']) {
				$_delivery['tpl'] = json_decode($_delivery['tpl'] ,TRUE);
				$str = '';
				foreach ($_delivery['tpl']['list'] as $k => $v) {
					$str = str_replace('left','x',json_encode($v));
					$str = str_replace('{address_name}',$main_order['address_name'],$v);
					$str = str_replace('{address_mobile}',$main_order['address_mobile'],$str);
					$str = str_replace('{address_detail}',$main_order['address_detail'],$str);
					$str = str_replace('{sender_name}',$setting['sender_name'],$str);
					$str = str_replace('{sender_mobile}',$setting['sender_mobile'],$str);
					$str = str_replace('{sender_address}',$setting['sender_address'],$str);
					$str = str_replace('{real_amount}',$main_order['real_amount'],$str);
					$str = str_replace('{paid_amount}',$main_order['paid_amount'],$str);
					$str = str_replace('{remark}',$sub_order['remark'],$str);
					$str = str_replace('{dateline}',date('Y-m-d H:i:s' , time()),$str);
					$_delivery['tpl']['list'][$k] = $str;
				}
			}
			$this->load->librarys('View')->assign('o_delivery',$o_delivery)->assign('_delivery',$_delivery)->display('print_kd');
		}
	}
}