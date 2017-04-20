<?php
/**
 * 		订单商品模型
 */
class order_sku_table extends table {

	protected $_validate = array(
        /* array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]), */

        // 会员ID
        array('buyer_id', 'require', '{order/order_member_id_not_null}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buyer_id', 'number', '{ORDER_MEMBER_ID_ERROR}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 子商品ID
        array('sku_id', 'require', '{order/sku_id_not_null}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('sku_id', 'number', '{order/sku_id_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 购买数量
        array('buy_nums', 'require', '{order/buy_nums_require}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buy_nums', 'number', '{order/buy_nums_number}', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    //自动完成
    protected $_auto = array(
        // array(完成字段1,完成规则,[完成条件,附加规则]),
        array('dateline','time',1,'function'), //新增数据时插入系统时间
    );

    public function _after_select($skus ,$options) {
        foreach ($skus as $k => $sku) {
            $_spec = '';
            if (!empty($sku['sku_spec'])) {
                $sku['sku_spec'] = json_decode($sku['sku_spec'] ,TRUE);
                foreach ($sku['sku_spec'] as $spec) {
                    $_spec .= $spec['name'].'：'.$spec['value'].'&nbsp;';
                }
            }
            $sku['_sku_spec'] = $_spec;
            $sku['_sku_url'] = url('goods/index/detail',array('sku_id' => $sku['sku_id']));
            if ($sku['promotion']) $sku['promotion'] = json_decode($sku['promotion'] ,TRUE);
            $skus[$k] = $sku;
        }
        return $skus;
    }

    public function _after_find($sku, $options) {
        if ($sku['sku_spec']) $sku['sku_spec'] = json_decode($sku['sku_spec'] ,TRUE);
        if ($sku['promotion']) $sku['promotion'] = json_decode($sku['promotion'] ,TRUE);
        $this->data = $sku;
        return $this->data;
    }
}