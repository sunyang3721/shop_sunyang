<?php
class taglib_order
{
	public function __construct() {
		$this->service_order_sku = model('order/order_sku','service');
	}

    /**
     * 获取订单商品的成交记录
     * @param int   $sid    商品sku_id||spu_id
     * @param bool  $isspu  是否spu_id (当前为TRUE时，$all为TRUE)
     * @param bool  $all    是否查找所有sku的记录
     * @param array $options    附加条件
     */
	public function records($sqlmap = array(), $options = array()) {
        $result = $this->service_order_sku->records($sqlmap['sid'], $sqlmap['isspu'] , $sqlmap['all'] ,$options);
        if($options['page']) {
            $pagefunc = $options['pagefunc'] ? $options['pagefunc'] : 'pages';
            $this->pages = $pagefunc($result['count'],$options['limit']);
        }
        return $result;
	}

}