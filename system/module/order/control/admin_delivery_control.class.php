<?php
/**
 * 		后台物流 控制器
 */
hd_core::load_class('init', 'admin');
class admin_delivery_control extends init_control {

	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('order/delivery');
		$this->service_district = $this->load->service('admin/district');
		$this->load->helper('attachment');
	}

	/* 物流配置管理 */
	public function index() {
		$sqlmap = array();
		$pagesize = isset($_GET['limit']) ? $_GET['limit'] : 10;
		$deliverys = $this->service->get_list($_GET['page'],$pagesize);
		$count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $pagesize);
        $lists = array(
			'th' => array(
				'name' => array('title' => '配送方式','length' => 70,'style'=>'data'),
				'sort' => array('title' => '排序','length' => 10,'style'=>'double_click'),
 				'enabled' => array('title' => '状态','length' => 5,'style'=>'ico_up_rack')
			),
			'lists' => $deliverys,
            'pages' => $pages,
			);
        $this->load->librarys('View')->assign('lists',$lists)->display('delivery_index');
	}

	/* [添加|编辑] 物流 */
	public function update() {
		if (checksubmit('do_submit')) {
			unset($_GET['do_submit']);
			unset($_GET['formhash']);
			if(!empty($_FILES['logo']['name'])){
				$code = attachment_init(array('path'=>'delivery','mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','jpeg','gif','png')));
				$_GET['logo'] = $this->load->service('attachment/attachment')->setConfig($code)->upload('logo');
				if(!$_GET['logo']) showmessage($this->load->service('attachment/attachment')->error);
			}
			$result = $this->service->update($_GET);
			if (!$result) showmessage($this->service->error,'',0,'json');
			showmessage(lang('_operation_success_'),url('index'),1,'json');
		} else {
			$delivery = array();
			$id = (int) $_GET['id'];
			// 获取当前物流信息
			if ($id > 0) {
				$delivery = $this->service->get_by_id($id);
			}
			$this->load->librarys('View')->assign('delivery',$delivery)->display('delivery_update');
		}
	}

	public function ajax_district_select() {
		@ini_set('memory_limit','256M');
		/* 查询三级 */
		$districts = $this->service_district->fetch_all_by_tree(0, 3);
		$this->load->librarys('View')->assign('districts',$districts)->display('ajax_district_select');
	}

	/* 根据地区id获取下级地区 */
	public function ajax_get_district_childs() {
		$district_id = (int) $_GET['district_id'];
		$result = $this->service_district->get_children($district_id);
		$this->load->librarys('View')->assign('result',$result);
        $result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}

	/* 根据物流ID更改物流字段值 */
	public function update_field_by_id() {
		if (checksubmit('dosubmit')) {
			$result = $this->service->update_field_by_id($_GET['id'],$_GET['field'],$_GET['val']);
			if (!$result) showmessage($this->service->error);
			showmessage(lang('_operation_success_'),'',1,'json');
		} else {
			showmessage(lang('_error_action_'));
		}
	}

	/* 删除物流(支持多条) */
	public function deletes() {
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$ids = (array)$_GET['ids'];
		$result = $this->service->deletes($ids);
		if (!$result) showmessage($this->service->error);
		showmessage(lang('_operation_success_'),url('index'),1,'json');
	}

	/* 物流快递模版 */
	public function delivery_tpl() {
		if (checksubmit('dosubmit')) {
			if (empty($_GET['content']) || !isset($_GET['id'])) showmessage(lang('submit_parameters_error','order/language'));
			$result = $this->service->setField(array('tpl' => $_GET['content']), array('id' => $_GET['id']));
			if ($result === false) showmessage($this->service->getError());
			showmessage(lang('_operation_success_'),url('order/admin_delivery/delivery_tpl',array('id'=>$_GET['id'])),1,'json');
		} else {
			$delivery = $this->service->get_by_id($_GET['id']);
			if(empty($delivery['tpl'])) {
				$img_url = './statics/images/delivery_express/'.$delivery['identif'].'.jpg';
				if (!file_exists($img_url)) showmessage(lang('express_identifying_no_exist','order/language'));
				$load = hd_load::getInstance();
				$image = $load->librarys('image',$img_url);
				$content = array('width' => $image->width() ,'height' => $image->height());
			} else {
				$content = json_decode($delivery['tpl'], TRUE);
			}
			$this->load->librarys('View')->assign('img_url',$img_url)->assign('delivery',$delivery)->assign('content',$content)->display('delivery_tpl');
		}
	}

	/* 打印快递模版 */
	public function print_kd($sn = '') {
		$sn = (string) trim($_GET['sn']);
		if (empty($sn)) showmessage(lang('order_sn_error','order/language'));
		/* 查找该订单的快递图片名称 */
		$order = $this->load->service('order/order')->find(array('sn' => $sn));
		// 收货人地区
		$district = $this->load->service('admin/district')->fetch_position($order['delivery_address_district']);
		$district = implode(' ', $district);
		/* 读取该快递模版的编辑信息 */
		$delivery = $this->service->get_by_id($order['delivery_id']);
		$content = json_decode($delivery['tpl'], TRUE);
		// 替换值
		foreach ($content['list'] as $k => $v) {
			$str = str_replace('left','x',json_encode($v));
			$str = str_replace('{accept_name}',$order['delivery_address_name'],$v);	// 收货人
			$str = str_replace('{mobile}',$order['delivery_address_mobile'],$str);	// 收货人手机
			$str = str_replace('{address}',$district.' '.$order['delivery_address_address'],$str);	// 收货人地址
			$str = str_replace('{real_amount}',$order['real_amount'],$str);	// 实付金额
			$content['list'][$k] = $str;
		}
		$this->load->librarys('View')->assign('order',$order)->assign('district',$district)->assign('delivery',$delivery)->assign('content',$content)->display('print_kd');
	}

}