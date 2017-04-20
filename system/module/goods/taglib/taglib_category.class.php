<?php
class taglib_category
{
    public function __construct() {
        $this->service = model('goods/goods_category','service');
    }
    public function lists($sqlmap = array(), $options = array()) {
        return $this->service->lists($sqlmap, $options);
    }
}