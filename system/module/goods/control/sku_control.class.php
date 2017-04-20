<?php
hd_core::load_class('init', 'admin');
class sku_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('goods/goods_sku');
		$this->cate_service = $this->load->service('goods_category');
		$this->brand_service = $this->load->service('brand');
	}
	/**
	 * [select 商品弹窗选择]
	 * @return [type] [description]
	 */
	public function select(){
		$_GET['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 5;
		$skus = $this->service->get_lists($_GET);
		$pages = $this->admin_pages($skus['count'], $_GET['limit']);
		if($_GET['catid']){
			$cate = $this->cate_service->detail($_GET['catid'],'id,name');
			$this->load->librarys('View')->assign('cate',$cate);
		}
		if($_GET['brand_id']){
			$brand = $this->brand_service->detail($_GET['brand_id'],'id,name');
			$this->load->librarys('View')->assign('brand',$brand);
		}
		$cache = $this->cate_service->get();
		$category = $this->cate_service->get_category_tree($cache);
		$brands = $this->brand_service->get_lists(null,null);
		$this->load->librarys('View')->assign('skus',$skus)->assign('pages',$pages)->assign('category',$category)->assign('brands',$brands)->display('ajax_sku_list_dialog');
	}
	/**
	 * 商品列表弹窗
	 */
	public function ajax_lists(){
		$_GET['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 5;
		$lists = $this->service->get_lists($_GET);
		$lists['pages'] = $this->admin_pages($lists['count'], $_GET['limit']);
		$this->load->librarys('View')->assign('lists',$lists);
		$lists = $this->load->librarys('View')->get('lists');
		echo json_encode($lists);
	}
}