<?php
class search_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('goods/goods_sku');
	}
	/**
	 * [search 商品搜索页]
	 * @return [type] [description]
	 */
	public function search(){
		$title = '搜索 '.$_GET['keyword'].' 的结果';
		$SEO = seo($title);
		$result = $this->service->search($_GET);
		$this->load->librarys('View')->assign('SEO',$SEO)->assign('result',$result)->display('search_lists');
	}
}