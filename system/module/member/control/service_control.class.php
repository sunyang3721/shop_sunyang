<?php
class service_control extends cp_control
{
	public function _initialize() {
		parent::_initialize();
		if($this->member['id'] < 1) {
			redirect(url('cp/index'));
		}
		$this->service = $this->load->service('order/order_server');
		$this->order_service = $this->load->service('order/order');
		$this->order_sku_service = $this->load->service('order/order_sku');
		$this->track_service = $this->load->service('order/order_track');
		$this->delivery_service = $this->load->service('order/delivery');
		$this->load->helper('attachment');
		$this->load->helper('order/function');
	}
	/**
	 * [index 售后列表]
	 * @return [type] [description]
	 */
	public function index(){
		if(!defined('MOBILE')){
			$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 10;
			$page = ($_GET['page']) ? $_GET['page'] : 1;
			$lists = $this->service->get_servers(0,$this->member['id'],$limit,$page);
			$servers = $lists['lists'];
			$pages = pages($lists['count'], $limit);
			$this->load->librarys('View')->assign('lists',$lists)->assign('servers',$servers)->assign('pages',$pages);
		}
		$SEO = seo('售后服务 - 会员中心');
		$this->load->librarys('View')->assign('SEO',$SEO)->display('sale_service');
	}

	public function ajax_service(){
		$limit = 5;
		$page = ($_GET['page']) ? $_GET['page'] : 1;
		$status = $_GET['status'] ? $_GET['status'] : 0;
		$lists = $this->service->get_servers(0,$this->member['id'],$limit,$page,$status);
		$this->load->librarys('View')->assign('lists',$lists);
        $lists = $this->load->librarys('View')->get('lists');
		echo json_encode($lists);
	}
	/**
	 * [return_refund 售后详情]
	 * @return [type] [description]
	 */
	public function return_refund(){
		if(!$_GET['id']) redirect(url('goods/index/index'));
		$SEO = seo('售后服务 - 会员中心');
		$sku = $this->order_sku_service->find(array('id' => $_GET['id']));
		$spec = '';
		foreach ($sku['sku_spec'] AS $sku_spec) {
			$spec .= $sku_spec['name'] .':'.$sku_spec['value'].';';
		}
		$servers = $this->service->find(array('o_sku_id' => $_GET['id']),'return_id,refund_id,status');
		$this->load->librarys('View')->assign('SEO',$SEO)->assign('sku',$sku)->assign('spec',$spec)->assign('servers',$servers);
        //发起售后申请
		if(!$servers){
			$attachment_init = attachment_init(array('module' => 'member','path' => 'member', 'mid' => $this->member['id'],'allow_exts'=>array('bmp','jpg','jpeg','gif','png')));
			$price = $this->order_sku_service->getfield('real_price', array('id' => $_GET['id']));
			$this->load->librarys('View')->assign('attachment_init',$attachment_init)->assign('price',$price)->display('return_refund');
		}else{
			$sqlmap = array();
			$sqlmap['enabled'] = 1;
			$deliverys = $this->delivery_service->table_list($sqlmap);

			foreach ($deliverys as $key => $delivery) {
				$deliverys[$delivery['identif']] = $delivery['name'];
				unset($deliverys[$key]);
			}
			$this->load->librarys('View')->assign('deliverys',$deliverys);
			//发起退货流程
			if($servers['return_id'] && !$servers['refund_id'] && $servers['status'] == 1 && $_GET['type'] == 'delivery'){
				$this->load->librarys('View')->display('return_refund_3');
			//提交退货信息
			}elseif($servers['return_id'] && $servers['refund_id']){
				$server = $this->order_service->order_return_find(array('o_sku_id' => $_GET['id']), 'delivery_name,delivery_sn');
				$track = $this->track_service->kuaidi100($server['delivery_name'],$server['delivery_sn']);
				$this->load->librarys('View')->assign('server',$server)->assign('track',$track)->display('return_refund_4');
			//通过退货申请
			}else{
				$this->load->librarys('View')->display('return_refund_2');
			}
		}
	}
	/**
	 * [ajax_delivery 买家返货申请]
	 * @return [type] [description]
	 */
	public function ajax_delivery(){
		$operator = get_operator();	// 获取操作者信息
		$return_id = $this->order_service->order_return_field('id', array('o_sku_id'=>$_GET['id']));
		$result = $this->service->return_goods($return_id,$_GET['delivery_name'],$_GET['delivery_sn'], $operator['id'], $operator['operator_type']);
		$refund = $this->order_service->order_refund_find(array('o_sku_id'=>$_GET['id']));
		// 创建退款日志
		$log['refund_id']     = $refund['id'];
		$log['order_sn']      = $refund['order_sn'];
		$log['sub_sn']        = $refund['sub_sn'];
		$log['o_sku_id']      = $refund['o_sku_id'];
		$log['operator_id']   = $operator['id'];
		$log['operator_name'] = $operator['username'];
		$log['operator_type'] = $operator['operator_type'];
		$log['action']           = '用户发货完毕';
		$log['msg'] = $_GET['delivery_desc'];
		$this->order_service->order_refund_log_update($log);
		if(!$result){
			showmessage($this->service->error,'',0);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	public function ajax_return_cancel(){
		$return = $this->order_service->order_return_find(array('o_sku_id'=>$_GET['id']));
		$result = $this->order_service->order_return_update(array('status' => -1,'id'=>$return['id']));
		if($result === FALSE) showmessage($this->order_service->error,'',0);
		$this->service->setField(array('status'=>-1), array('return_id' => $return['id']));
		// 写入退货日志
		$operator = get_operator();	// 获取操作者信息
		$log['return_id']     = $return['id'];
		$log['order_sn']      = $return['order_sn'];
		$log['sub_sn']        = $return['sub_sn'];
		$log['o_sku_id']      = $return['o_sku_id'];
		$log['action']        = '用户取消退货退款申请';
		$log['operator_id']   = $operator['id'];
		$log['operator_name'] = $operator['username'];
		$log['operator_type'] = $operator['operator_type'];		
		$result = $this->order_service->order_return_log_update($log);
		if(!$result){
			showmessage($this->service->error,'',0);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	public function ajax_refund_cancel(){
		$refund = $this->order_service->order_refund_find(array('o_sku_id'=>$_GET['id']));
		$result = $this->order_service->order_refund_update(array('status' => -1,'id'=>$refund['id']));
		if($result === FALSE) showmessage($this->order_service->error,'',0);
		$this->service->setField(array('status' => -1), array('refund_id' => $refund['id']));
		// 创建退款日志
		$operator = get_operator();	// 获取操作者信息
		$log['refund_id']     = $refund['id'];
		$log['order_sn']      = $refund['order_sn'];
		$log['sub_sn']        = $refund['sub_sn'];
		$log['o_sku_id']      = $refund['o_sku_id'];
		$log['operator_id']   = $operator['id'];
		$log['operator_name'] = $operator['username'];
		$log['operator_type'] = $operator['operator_type'];
		$log['action']           = '用户取消退款申请';
		$this->order_service->order_refund_log_update($log);
		if(!$result){
			showmessage($this->service->error,'',0);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	/**
	 * [ajax_return 提交退货且退款申请]
	 * @return [type] [description]
	 */
	public function ajax_return(){
		$operator = get_operator();
		$result = $this->service->create_return($_GET['id'] ,$_GET['amount'] ,$_GET['cause'] ,$_GET['desc'],$_GET['imgs'],$operator['id'],$operator['operator_type']);
		if($result === FALSE){
			showmessage($this->service->error,'',0);
		}else{
			$this->load->service('attachment/attachment')->attachment($_GET['imgs'], '',false);
			showmessage(lang('_operation_success_'),'',1);
		}
	}
	/**
	 * [ajax_refund 提交仅退款申请]
	 * @return [type] [description]
	 */
	public function ajax_refund(){
		$operator = get_operator();
		$sn = $this->order_sku_service->getfield('sub_sn', array('id'=>$_GET['id']));
		$result = $this->service->create_refund($_GET['type'] ,$sn ,$_GET['amount'] ,$_GET['cause'] ,$_GET['desc'] ,$_GET['id'],$_GET['imgs'],$operator['id'],$operator['operator_type']);
		if($result === FALSE) showmessage($this->service->error,'',0);
		$this->load->service('attachment/attachment')->attachment($_GET['imgs'], '',false);
		showmessage(lang('_operation_success_'),url('index'),1);
	}

	public function delivery_detail(){
		$sqlmap = array();
		$sqlmap['enabled'] = 1;
		$deliverys = $this->load->service('order/delivery')->table_list($sqlmap);
		foreach ($deliverys as $key => $delivery) {
			$deliverys[$delivery['identif']] = $delivery['name'];
			unset($deliverys[$key]);
		}
		$track = $this->track_service->kuaidi100($_GET['delivery_name'],$_GET['delivery_sn']);
		$logo = './statics/images/deliverys/'.$_GET["delivery_name"].'.png';
		$this->load->librarys('View')->assign('deliverys',$deliverys)->assign('track',$track)->assign('logo',$logo)->display('delivery_detail');
	}
}