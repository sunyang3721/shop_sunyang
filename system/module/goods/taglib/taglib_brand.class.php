<?php
class taglib_brand
{
	public function __construct() {
		$this->service = model('goods/brand', 'service');
		$this->goods_spu_model = model('goods/goods_spu');
		$this->cate_service = model('goods/goods_category','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
        return $this->service->lists($sqlmap, $options);
	}
}