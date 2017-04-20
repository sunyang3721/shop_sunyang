<?php
/**
 *		子商品数据层
 */
class goods_sku_table extends table {
    protected $result = array();
	protected $_validate = array(

    );
    protected $_auto = array(
        array('up_time','time',1,'function'),
        array('update_time','time',2,'function')
    );
    /**
     * 获取sku数据
     */
    public function detail($id = 0,$field = TRUE){
        if((int)$id < 1) return FALSE;
        $this->result['sku'] = $this->field($field)->find($id);
        if(empty($this->result['sku'])) return $this;
        if($this->result['sku']['imgs']) $this->result['sku']['img_list'] = $this->result['sku']['imgs'];
        $this->result['sku']['thumb'] = $this->result['sku']['thumb'] ? $this->result['sku']['thumb'] : $this->load->table('goods/goods_spu')->where(array('id'=>$this->result['sku']['spu_id']))->getField('thumb');
        $this->result['sku']['url'] = url('goods/index/detail',array('sku_id' => $id));
        return $this;
    }
    /**
     * 获取spu数据
     */
    public function spu(){
        $this->result['goods'] = $this->load->table('goods/goods_spu')->find($this->result['sku']['spu_id']);
        if(empty($this->result['sku']['imgs'])){
            $this->result['sku']['img_list'] = array_merge($this->result['goods']['imgs'] ? $this->result['goods']['imgs'] : array(),$this->result['sku']['img_list'] ? $this->result['sku']['img_list'] : array());
        }
        unset($this->result['goods']['thumb']);
        unset($this->result['goods']['sn']);
        if(!$this->result['sku']['subtitle']) unset($this->result['sku']['subtitle'],$this->result['sku']['style']);
        if(!$this->result['sku']['warn_number']) unset($this->result['sku']['warn_number']);
        if(!$this->result['sku']['keyword']) unset($this->result['sku']['keyword']);
        if(!$this->result['sku']['description']) unset($this->result['sku']['description']);
        if(!$this->result['sku']['content']) unset($this->result['sku']['content']);
        if($this->result['sku']['weight'] == 0) unset($this->result['sku']['weight']);
        if($this->result['sku']['volume'] == 0) unset($this->result['sku']['volume']);
        $this->result['sku'] = array_merge($this->result['goods'],$this->result['sku']);
        return $this;
    }
    /**
     * 计算实际售价
     */
    public function price(){
        $prom_price = 0;
        if ($this->result['sku']['prom_type'] == 'time' && $this->result['sku']['prom_id'] > 0 ) {
            $pro_map = array();
            $pro_map['id'] = $this->result['sku']['prom_id'];
            $pro_map['start_time'] = array("LT", time());
            $pro_map['end_time'] = array("GT", time());
            $promotion = $this->load->table('promotion/promotion_time')->where($pro_map)->find();
            if ($promotion) {
                $sku_prom = json_decode($promotion['sku_info'],TRUE);
                $prom_price = sprintf("%.2f", $sku_prom[$this->result['sku']['sku_id']]);
                $this->result['sku']['prom_time'] = $promotion['end_time'] - time();
            }else{
                $prom_price = sprintf("%.2f", $this->result['sku']['shop_price']);
            }
        }else{
            $member = $this->load->service('member/member')->init();
            if (!$member['id']) {
                $prom_price = sprintf("%.2f", $this->result['sku']['shop_price']);
            } else {
                $discount = (!$member['_group']['discount']) ? 100 : $member['_group']['discount'] ;
                $prom_price = sprintf("%.2f", $this->result['sku']['shop_price']/100*$discount);
            }
        }
        $this->result['sku']['prom_price'] = $prom_price;
        return $this;
    }
    /**
     * 获取品牌信息
     */
    public function brand(){
        if(empty($this->result['goods'])) $this->result['goods'] = $this->load->table('goods/goods_spu')->find($this->result['sku']['spu_id']);
        $this->result['sku']['brand'] = $this->load->table('goods/brand')->where(array('id'=>$this->result['goods']['brand_id']))->find();
        return $this;
    }
    /**
     * 获取分类名称
     */
    public function cat_name(){
        if(empty($this->result['goods'])) $this->result['goods'] = $this->load->table('goods/goods_spu')->find($this->result['sku']['spu_id']);
        $this->result['sku']['cat_name'] = $this->load->table('goods/goods_category')->where(array('id'=>$this->result['goods']['catid']))->getField('name');
        return $this;
    }
    /**
     * 获取索引商品表信息
     */
    public function show_index(){
        $this->result['index'] = $this->load->table('goods/goods_index')->find($this->result['sku']['sku_id']);
        $sales = $this->load->table('goods/goods_index')->where(array('spu_id'=>$this->result['sku']['spu_id']))->Field('sales')->select();
        $sales_total = 0;
        foreach ($sales as $num) {
            $sales_total += $num['sales'];
        }
        $this->result['sku']['sales'] = $sales_total;
        $this->result['sku']['hits'] = $this->result['index']['hits'];
        $this->result['sku']['favorites'] = $this->result['index']['favorites'];
        $this->result['sku']['show_in_lists'] = $this->result['index']['show_in_lists'];
        return $this;
    }
    /**
     * 输出商品数据
     */
    public function output(){
        runhook('goods_sku_detail', $this->result['sku']);
        return $this->result['sku'];
    }
    /**
     * find后执行
     */
    public function _after_find(&$result,$option = array()){
        if($result['spec']) $result['spec'] = json_decode($result['spec'],TRUE);
        if($result['imgs']) $result['imgs'] = json_decode($result['imgs'],TRUE);
        return $result;
    }
    /**
     * select后执行
     */
    public function _after_select(&$result){
        foreach ($result as &$record) {
            $this->_after_find($record, $options);
        }
        return $result;
    }
}