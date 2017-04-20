<?php
class taglib_goods
{
	public function __construct() {
		$this->model = model('goods/goods_spu');
		$this->index_model = model('goods/goods_index');
		$this->sku_service = model('goods/goods_sku','service');
		$this->sku_model = model('goods/goods_sku');
		$this->cate_service = model('goods/goods_category','service');
	}
	public function lists($sqlmap = array(), $options = array()) {
		$result = $this->sku_service->lists($sqlmap,$options);
		$pagefunc = $options['pagefunc'] ? $options['pagefunc'] : 'pages';
		$this->pages = $pagefunc($result['count'],$options['limit']);
		return $result['lists'];
	}
	/**
	 * [page 构造商品分页]
	 * @param  array  $sqlmap  [description]
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function page($sqlmap = array(), $options = array()){
		return $this->sku_service->page($sqlmap,$options);
	}
	/**
	 * [history 调用历史记录]
	 * @param  [type] $attr [description]
	 * @return [type]       [description]
	 */
	public function history($sqlmap = array(), $options = array()) {
		$_history = cookie('_history');
		if(empty($_history)) return FALSE;
		$history = array_filter(explode(',',$_history));
		foreach ($history as $key => $value) {
    		if($key < $options['limit']){
    			$_historys[$key] = $this->sku_service->fetch_by_id($value,'price');
    			if(empty($_historys[$key])){
    				unset($_historys[$key]);
    			}
    		}
		}
		return $_historys;
	}
}