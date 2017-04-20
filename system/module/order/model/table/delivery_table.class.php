<?php
/**
 * 		物流模型
 */
class delivery_table extends table {

	protected $_validate = array(
        /* array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]), */
        // 物流名称
        array('name', 'require', '{order/logistics_name_not_empty}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('name', '', '{order/logistics_name_exist}', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        // 物流标识
        array('identif', 'require', '{order/logistics_identifi_not_empty}', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // 保价
        array('insure', 'currency', '{order/insure_money_require}', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        // 排序
        array('sort', 'number', '{order/sort_require}', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        // array(完成字段1,完成规则,[完成条件,附加规则]),
        array('systime','time',1,'function'),
    );
    
    public function _after_select($datas) {
        foreach ($datas as $k => $val) {
            $datas[$k]['method'] = json_decode($val['method'],TRUE);
            $datas[$k]['pays'] = json_decode($val['pays'],TRUE);
        }        
        return $datas;
    }
    //获取一条记录
    public function fetch_by_name($value,$field){
        $data = array();
        $data['name'] = $value;
        $data['enabled'] = 1;
        $result = $this->where($data)->find();
        if($field) return $result[$field];
        return $result;
    }
}