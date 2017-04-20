<?php
hd_core::load_class('init', 'admin');
class admin_control extends init_control {
	protected $service = '';
	protected $brand;
	public function _initialize() {
		parent::_initialize();
		$this->spu_service = $this->load->service('goods/goods_spu');
		$this->sku_service = $this->load->service('goods/goods_sku');
		$this->cate_service = $this->load->service('goods/goods_category');
		$this->brand_service = $this->load->service('goods/brand');
		helper('attachment');
	}
	/**
	 * [index 商品后台列表页]
	 * @return [type] [description]
	 */
	public function index(){
		$sqlmap = array();
		$_GET['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$info = $this->spu_service->get_lists($_GET);
		$goods = $info['lists'];
		$count = $info['count'];
		$cache = $this->cate_service->get();
		$category = $this->cate_service->get_category_tree($cache);
		if($_GET['catid']){
			$cate = $this->cate_service->detail($_GET['catid'],'id,name');
		}
		if($_GET['brand_id']){
			$brand = $this->brand_service->detail($_GET['brand_id'],'id,name');
		}
		$brands = $this->brand_service->get_lists(null,null);
		$pages = $this->admin_pages($count, $_GET['limit']);
		$goods_name_length = $_GET['label'] < 3 ? 25 : 30;
		$goods_number_length = $_GET['label'] == 1 || !isset($_GET['label']) ? 5 : 10;
		$lists = array(
			'th' => array(
				'sn' => array('title' => '商品货号','length' => 15,'style' => 'double_click'),
				'name' => array('title' => '商品名称','length' => $goods_name_length,'style' => 'goods'),
				'brand_name' => array('length' => 25,'title' => '品牌&分类','style' => 'cate_brand'),
				'price' => array('title' => '价格','length' => 10),
				'number' => array('title' => '库存','length' => $goods_number_length),
				'sort' => array('title' => '排序','style' => 'double_click','length' => 5),
				'status' => array('title' => '上架','style' => 'ico_up_rack','length' => 5),
			),
			'lists' => $goods,
			'pages' => $pages,
		);
		if($_GET['label'] != 1 && isset($_GET['label'])){
			unset($lists['th']['sort'],$lists['lists']['sort']);
		}
		if($_GET['label'] > 2){
			unset($lists['th']['status'],$lists['lists']['status']);
		}
		$this->load->librarys('View')->assign('lists',$lists)->assign('goods',$goods)->assign('category',$category)->assign('cate',$cate)->assign('brand',$brand)->assign('brands',$brands)->assign('pages',$pages)->display('goods_list');
	}


	public function ajax_spu_list(){
		$sqlmap = array();
		$_GET['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$lists = $this->spu_service->get_lists($_GET);
		$lists['pages'] = $this->admin_pages($lists['count'], $_GET['limit']);
		$this->load->librarys('View')->assign('lists',$lists);
		$lists = $this->load->librarys('View')->get('lists');
		echo json_encode($lists);
	}

	public function spu_select(){
		$sqlmap = array();
		$_GET['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;
		$lists = $this->spu_service->get_lists($_GET);
		$goods = $lists['lists'];
		$count = $lists['count'];
		$cache = $this->cate_service->get();
		$category = $this->cate_service->get_category_tree($cache);
		if($_GET['catid']){
			$cate = $this->cate_service->detail($_GET['catid'],'id,name');
		}
		if($_GET['brand_id']){
			$brand = $this->brand_service->detail($_GET['brand_id'],'id,name');
		}
		$brands = $this->brand_service->get_lists(null,null);
		$pages = $this->admin_pages($count, $_GET['limit']);
		$this->load->librarys('View')->assign('lists',$lists)->assign('goods',$goods)->assign('category',$category)->assign('cate',$cate)->assign('brand',$brand)->assign('brands',$brands)->assign('pages',$pages)->display('ajax_spu_list_dialog');
	}


	/**
	 * [goods_look_attr 查看子商品]
	 * @return [type] [description]
	 */
	public function sku_edit(){
		if(checksubmit('dosubmit')) {
			runhook('send_notice');
			$result = $this->sku_service->sku_edit($_GET);
			if(!$result){
				showmessage($this->sku_service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		}else{
			$info = $this->sku_service->fetch_by_id($_GET['sku_id'],'show_index');
			$attachment_init = attachment_init(array('module'=>'goods','path' => 'goods','mid' => $this->admin['id'],'allow_exts' => array('gif','jpg','peg','bmp','png')));
			$this->load->librarys('View')->assign('info',$info)->assign('attachment_init',$attachment_init)->display('sku_edit');
		}
	}
	/**
	 * [ajax_get_sku ajax获取主商品的子商品]
	 * @return [type] [description]
	 */
	public function ajax_get_sku(){
		$result['lists'] = $this->sku_service->get_sku($_GET['id']);
		$result['id'] = $_GET['id'];
		$this->load->librarys('View')->assign('result',$result);
		$result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}
	/**
	 * [goods_spec_modify 批量修改规格]
	 * @return [type] [description]
	 */
	public function goods_spec_modify(){
		$this->load->librarys('View')->display('goods_spec_modify');
	}
	/**
	 * [goods_spec_pop 编辑商品规格]
	 * @return [type] [description]
	 */
	public function goods_spec_pop(){
		$specs = $this->load->service('goods/spec')->get_spec_name();
		$attachment_init = attachment_init(array('module' => 'goods','path' => 'goods','mid' => $this->admin['id'],'allow_exts' => array('gif','jpg','jpeg','bmp','png')));
		$this->load->librarys('View')
				->assign('specs',$specs)
				->assign('selected',$selected)
				->assign('attachment_init',$attachment_init)
					->display('goods_spec_popup');
	}
	/**
	 * [goods_add 商品编辑]
	 * @return [type] [description]
	 */
	public function goods_add() {
		$id = (int) $_GET['id'];
		if(checksubmit('dosubmit')) {
			$result = $this->spu_service->goods_add($_GET);
			runhook('make_watermark',$_GET);
			if($result === false){
				showmessage($this->spu_service->error);
			}else{
				showmessage(lang('_operation_success_'),url('index'));
			}
		} else {
			$goods = array();
			if($id > 0) {
				$goods = (array) $this->spu_service->get_by_id($_GET['id']);
				if($goods) {
					$goods['extra']['attr'] = $this->load->service('goods/type')->get_type_by_catid($goods['spu']['catid']);
					$goods['extra']['attr']['types'][0] = '请选择商品类型';
					$goods['extra']['specs'] = $this->spu_service->get_goods_specs($goods['_sku']);
					$goods['extra']['album'] = $this->spu_service->get_goods_album($goods);
				}
			}
			$delivery_template = $this->load->service('order/delivery_template')->getField(array(),'id,name',true);
			$goods['extra']['attachment_init'] = attachment_init(array('path' => 'goods','mid' => $this->admin['id'],'allow_exts' => array('jpg','jpeg','bmp','png')));
			$this->load->librarys('View')
				->assign('goods',(array) $goods)
				->assign('delivery_template',$delivery_template)
				->assign('brands', $this->brand_service->get_lists(null,null))
				->display('goods_add');
		}
	}
	/**
	 * [ajax_brand ajax查询品牌]
	 * @return [type] [description]
	 */
	public function ajax_brand(){
		$result = $this->brand_service->ajax_brand($_GET['brandname']);
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	/**
	 *[ajax_sn ajax更改商品货号]
	 *@return [type] [description]
	 */
	public function ajax_sn(){
		$_GET['sn'] = $_GET['name'];
		unset($_GET['name']);
		$result = $this->spu_service->change_spu_info($_GET);
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_name ajax更改商品名称]
	 * @return [type] [description]
	 */
	public function ajax_name(){
		$result = $this->spu_service->change_spu_info($_GET);
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_recover 批量恢复商品]
	 * @param  [array] $id [要恢复的商品id]
	 * @return [type]     [description]
	 */
	public function ajax_recover(){
		$result = $this->spu_service->recover($_GET['id']);
		if(!$result){
			showmessage($this->spu_service->error);
		}else{
			showmessage(lang('_operation_success_'));
		}
	}
	/**
	 * 更改商品名称
	 */
	public function ajax_sku_name(){
		$_GET['sku_id'] = $_GET['id'];
		$_GET['sku_name'] = $_GET['name'];
		$result = $this->sku_service->change_sku_info($_GET);
		if(!$result){
			showmessage($this->spu_service->error);
		}
		showmessage(lang('_operation_success_'));
	}
	/**
	 * 更改商品
	 */
	public function ajax_sku_sn(){
		$_GET['sku_id'] = $_GET['id'];
		$_GET['sn'] = $_GET['name'];
		$result = $this->sku_service->change_sku_info($_GET);
		if(!$result){
			showmessage($this->spu_service->error);
		}
		showmessage(lang('_operation_success_'));
	}

	/**
	 * [ajax_name ajax更改商品上下架]
	 * @return [type] [description]
	 */
	public function ajax_status(){
		$result = $this->spu_service->change_status($_GET['id'],$_GET['type']);
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_name ajax更改商品上下架]
	 * @return [type] [description]
	 */
	public function ajax_show(){
		$result = $this->sku_service->change_show_in_lists($_GET['sku_id']);
		if(!$result){
			showmessage($this->sku_service->error,'',0);
		}else{
			showmessage(lang('_operation_success_'),'',1);
		}
	}
	/**
	 * [ajax_name ajax更改商品属性]
	 * @return [type] [description]
	 */
	public function ajax_sku(){
		$result = $this->sku_service->change_sku_info($_GET);
		if(!$result){
			showmessage($this->sku_service->error,'',0);
		}else{
			showmessage(lang('_operation_success_'),'',1);
		}
	}
	/**
	 * [ajax_name ajax更改排序]
	 * @return [type] [description]
	 */
	public function ajax_sort(){
		$result = $this->spu_service->change_spu_info($_GET);
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [ajax_del 删除商品，在商品列表里删除只改变状态，在回收站里删除直接删除]
	 * @return [type]         [description]
	 */
	public function ajax_del(){
		$result = $this->spu_service->delete($_GET);
		if(!$result){
			showmessage($this->spu_service->error);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	/**
	 * [ajax_del 删除商品，在商品列表里删除只改变状态，在回收站里删除直接删除]
	 * @return [type]         [description]
	 */
	public function ajax_del_sku(){
		$result = $this->sku_service->ajax_del_sku($_GET);
		if(!$result){
			showmessage($this->spu_service->error);
		}else{
			showmessage(lang('_operation_success_'),url('index'),1);
		}
	}
	/**
	 * [ajax_statusext ajax更改状态标签状态]
	 * @return [type] [description]
	 */
	public function ajax_statusext(){
		$result = $this->sku_service->ajax_statusext($_GET);
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			showmessage(lang('_operation_success_'),'',1,'','json');
		}
	}
	/**
	 * [upload 上传商品图片]
	 * @return [type] [description]
	 */
	public function upload(){
		$result['url'] = $this->load->service('attachment/attachment')->setConfig($_GET['code'])->upload('upfile');
		$this->load->service('attachment/attachment')->attachment($result['url'],'');
		$result['img_id'] = $_GET['img_id'];
		if(!$result){
			showmessage($this->spu_service->error,'',0,'','json');
		}else{
			$this->load->librarys('View')->assign('result',$result);
			$result = $this->load->librarys('View')->get('result');
			showmessage(lang('_operation_success_'),'',1,$result,'json');
		}
	}
	//获取类型数据
	public function ajax_get_attr(){
		$result = $this->load->service('goods/type')->get_type_by_catid($_GET['id']);
		showmessage(lang('_operation_success_'),'',1,$result);
	}
}