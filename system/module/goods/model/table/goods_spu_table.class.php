<?php
/**
 *		商品数据层
 */

class goods_spu_table extends table {
    protected $result = array();
    protected $_validate = array(
        array('name','require','{goods/goods_name_require}',table::MUST_VALIDATE),
        array('catid','number','{goods/classify_id_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
        array('warn_number','number','{goods/stock_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
        array('status','number','{goods/state_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
        array('sort','number','{goods/sort_require}',table::EXISTS_VALIDATE,'regex',table:: MODEL_BOTH),
    );
    protected $_auto = array(
    );


    public function status($status){
        if(is_null($status)){
            return $this;
        }
        $this->where(array('status'=>$status));
        return $this;
    }

    public function category($catid){
        if(!$catid){
            return $this;
        }
        $this->where(array('catid'=>array('IN',$catid)));
        return $this;
    }

    public function brand($brand_id){
        if(!$brand_id){
            return $this;
        }
       $this->where(array('brand_id'=>$brand_id));
        return $this;
    }

    public function keyword($keyword){
        if(!$keyword){
            return $this;
        }
        $this->where(array('name'=>array('LIKE','%'.$keyword.'%')));
        return $this;
    }




    protected function _after_find(&$result,$options) {
        return $result = $this->_output($result);
    }

    /**
     * 获取拓展品牌
     * @param  array $spu SPU数组
     * @author xuewl <master@xuewl.com>
     * @return array
     */
    public function get_extra_brand($spu) {
        $spu_id = (int) $spu['id'];
        $brand_id = (int) $spu['brand_id'];
        if($spu_id > 0 && $brand_id > 0) {
            return $this->load->table('goods/brand')->find($spu['brand_id']);
        }
        return false;
    }

    /**
     * 获取拓展分类
     * @param  array $spu SPU数组
     * @author xuewl <master@xuewl.com>
     * @return array
     */
    public function get_extra_category($spu) {
        $catid = (int) $spu['catid'];
        if($catid > 0) {
            return $this->load->service('goods/goods_category')->get_category_by_id($spu['catid'],false);
        }
        return false;
    }

    /**
     * 获取拓展SKU列表
     * @param  array $spu SPU数组
     * @author xuewl <master@xuewl.com>
     * @return array
     */
    public function get_extra_sku($spu) {
        $spu_id = (int) $spu['id'];
        if($spu_id > 0) {
            return $this->load->service('goods/goods_sku')->get_sku($spu_id);
        }
        return false;
    }


    public function get_extra_type($spu) {
        $spu_id = (int) $spu['id'];
        if($spu_id > 0) {
            return $this->load->service('goods/type')->get_type_by_goods_id($spu_id);
        }
        return false;
    }


    protected function _output($result) {
        /* 默认主图 */
        $result['imgs'] = json_decode($result['imgs'],true);
        if($result['specs']) {
            $specs = json_decode($result['specs'], true);
            foreach ($specs as $id => $spec) {
                $specs[$id]['value'] = explode(",", $spec['value']);
                $specs[$id]['img'] = explode(",", $spec['img']);
                // $specs[$id]['md5'] = md5($spec['name'].':'.$spec['value']);
            }
            $result['specs'] = $specs;
        }

        return $result;
    }
}