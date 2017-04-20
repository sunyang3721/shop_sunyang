<?php
/**
 *      捆绑营销控制器
 */
hd_core::load_class('init', 'admin');
class group_control extends init_control {
    public function _initialize() {
        parent::_initialize();
        $this->service = $this->load->service('promotion_group');
        $this->sku_service = $this->load->service('goods/goods_sku');
    }
    /**
     * [index 列表]
     * @return [type] [description]
     */
    public function index(){
        $limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
        $info = $this->service->lists($sqlmap);
        $count = $this->service->count($sqlmap);
        $pages = $this->admin_pages($count, $limit);
        $lists = array(
            'th' => array(
                'title' => array('title' => '组合标题','length' => 30,'style' => 'double_click'),
                'subtitle' => array('title' => '组合名称','length' => 25,'style' => 'double_click'),
                'count'=>array('title' => '商品数量','length' => 15),
                'status' => array('title' => '状态','length' => 15,'style' => 'ico_up_rack')
            ),
            'lists' => $info ['lists'],
            'pages' => $pages,
            );
        $this->load->librarys('View')->assign('lists',$lists)->display('group_lists');
    }
    /**
     * [add 编辑]
     * @return [type] [description]
     */
    public function edit(){
       if((int)$_GET['id'] > 0){
            $info = $this->service->fetch_by_id($_GET['id']);
            if(!empty($info['sku_ids'])){
                $sku_ids = explode(',', $info['sku_ids']);
                $skus = $this->sku_service->sku_detail($sku_ids);
                $lists = array();
                foreach ($skus as $key => $sku) {
                    $item = array();
                    $item['id'] = $sku['sku_id'];
                    $item['pic'] = $sku['thumb'];
                    $item['price'] = $sku['shop_price'];
                    $item['number'] = $sku['number'];
                    $item['title'] = $sku['sku_name'];
                    $item['spec'] = $sku['spec'];
                    $lists[$sku['sku_id']] = $item;
                }
                $this->load->librarys('View')->assign('lists',$lists);
            }
            $this->load->librarys('View')->assign('info',$info);
        }
        if(checksubmit('dosubmit')) {
            $result = $this->service->update($_GET);
            if($result === false) {
                showmessage($this->service->error);
            } else {
                showmessage(lang('add_bound_activity_success','promotion/language'), url('index'), 1);
            }
        } else {
            $this->load->librarys('View')->display('group_add');
        }
    }
    /**
     * [delete 删除]
     * @return [type] [description]
     */
    public function delete() {
        $ids = (array) $_GET['id'];
        if(empty($ids)) {
            showmessage(lang('_param_error_'));
        }
        $result = $this->service->delete($ids);
        if($result === false) {
            showmessage($this->service->error);
        } else {
            showmessage(lang('delete_activity_success','promotion/language'), url('index'), 1);
        }
    }
    /**
     * [ajax_status 规格列表内更改规格状态]
     */
    public function ajax_status(){
        $result = $this->service->change_status($_GET['id']);
        if(!$result){
            showmessage($this->service->error,'',0,'','json');
        }else{
            showmessage(lang('_operation_success_'),'',1,'','json');
        }
    }
    /**
     * [ajax_name ajax更改名称]
     * @return [type] [description]
     */
    public function ajax_name(){
        $result = $this->service->change_name($_GET);
        if(!$result){
            showmessage($this->service->error,'',0,'','json');
        }else{
            showmessage(lang('_operation_success_'),'',1,'','json');
        }
    }
     /**
     * [ajax_name ajax更改名称]
     * @return [type] [description]
     */
    public function ajax_subtitle(){
        $result = $this->service->change_subtitle($_GET);
        if(!$result){
            showmessage($this->service->error,'',0,'','json');
        }else{
            showmessage(lang('_operation_success_'),'',1,'','json');
        }
    }
}
