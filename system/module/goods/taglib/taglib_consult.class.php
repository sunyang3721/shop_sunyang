<?php
class taglib_consult
{	
	public function __construct() {
		$this->model = model('goods/goods_consult');
		$this->service = model('goods/goods_consult','service');
	}
	public function count($sqlmap = array(), $options = array()){
		$count = $this->model->where($sqlmap)->count();
		return $count;
	}
	public function lists($sqlmap = array(), $options = array()) {
		$result = $this->service->lists($sqlmap,$options);
		$pagefunc = $options['pagefunc'] ? $options['pagefunc'] : 'pages';
		$this->pages = $pagefunc($result['count'],$options['limit']);
		return $result['lists'];
	}
}