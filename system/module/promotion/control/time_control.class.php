<?php
/**
 *      限时营销控制器
 */
hd_core::load_class('init', 'admin');
class time_control extends init_control {
    public function _initialize() {
        parent::_initialize();
        $this->service = $this->load->service('promotion_time');
        $this->sku_service = $this->load->service('goods/goods_sku');
	}
	/**
	 * [index 列表]
	 * @return [type] [description]
	 */
	public function index(){
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
        $info = $this->service->lists();
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $limit);
		$lists = array(
			'th' => array(
				'name' => array('title' => '促销名称','length' => 40,'style' => 'double_click'),
				'time' => array('title' => '促销时间','length' => 40),
 				'status' => array('title' => '状态','length' => 10)
			),
			'lists' => $info['lists'],
            'pages' => $pages,
			);
		$this->load->librarys('View')->assign('lists',$lists)->display('time_lists');
	}



	/**
	 * [add 添加]
	 * @return [type] [description]
	 */
	public function edit(){
		if((int)$_GET['id'] > 0){
			$info = $this->service->fetch_by_id($_GET['id']);
			if(!empty($info['sku_info'])){
				$sku_ids = array_keys(json_decode($info['sku_info'],TRUE));
				$price = array_values(json_decode($info['sku_info'],TRUE));
				$skus = $this->sku_service->sku_detail($sku_ids);
				$lists = array();
				foreach ($skus as $key => $sku) {
					$item = array();
					$item['id'] = $sku['sku_id'];
					$item['pic'] = $sku['thumb'];
					$item['price'] = $sku['shop_price'];
					$item['number'] = $sku['number'];
					$item['title'] = $sku['sku_name'];
					$item['spec'] = $sku['spec'];
					$item['prom_price'] = $price[$key];
					$lists[] = $item;
				}
				$this->load->librarys('View')->assign('lists',$lists);
			}
			$this->load->librarys('View')->assign('info',$info);
		}
		if(checksubmit('dosubmit')) {
			$result = $this->service->update($_GET);
			if($result === false) {
				showmessage($this->service->error);
			} else {
				showmessage(lang('add_activity_success','promotion/language'), url('index'), 1);
			}
		} else {
			$this->load->librarys('View')->display('time_add');
		}
	}
	/**
	 * [delete 删除]
	 * @return [type] [description]
	 */
	public function delete() {
		$ids = (array) $_GET['id'];
		if(empty($ids)) {
			showmessage(lang('_param_error_'));
		}
		$result = $this->service->delete($ids);
		if($result === false) {
			showmessage($this->service->error);
		} else {
			showmessage(lang('delete_activity_success','promotion/language'), url('index'), 1);
		}
	}
	/**
	 * [ajax_name ajax更改名称]
	 * @return [type] [description]
	 */
	public function ajax_name(){
		$result = $this->service->change_name($_GET);
		if(!$result){
			showmessage($this->service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
}
