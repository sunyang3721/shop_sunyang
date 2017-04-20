<?php
hd_core::load_class('init', 'goods');
class index_control extends init_control
{	
	public function _initialize() {
		parent::_initialize();
		$this->goods_service = $this->load->service('order/order_sku');
		$this->comment_service = $this->load->service('comment/comment');
		$this->load->helper('attachment');
		$this->load->helper('order/function');
	}

	public function index() {
		if($this->member['id'] < 1) {
			showmessage(lang('_not_login_'),url('member/public/login',array('url_forward'=>urlencode($_SERVER['REQUEST_URI']))),0);
		}
		$_sqlmap = array();
		$_sqlmap['buyer_id'] = $this->member['id'];
		$_sqlmap['delivery_status'] = 2;
		$_sqlmap['iscomment'] = $_GET['iscomment'] ? $_GET['iscomment'] : 0;
		$result = $this->goods_service->lists($_sqlmap, 10, 'id DESC', $_GET['page']);
		$nocomment = $this->member['counts']['load_comment'];
		extract($result);
		$pages = pages($count, 10);
		$iscomment = $this->goods_service->count(array('buyer_id'=>$this->member['id'],'iscomment'=>1));
		$iscomment = $iscomment ? $iscomment : 0;
		$attachment_init = attachment_init(array('module' => 'member','path' => 'member', 'mid' => $this->member['id'],'allow_exts'=>array('bmp','jpg','jpeg','gif','png')));
		$SEO = seo('待评价交易 - 会员中心');
		$this->load->librarys('View')->assign('SEO',$SEO)->assign('attachment_init',$attachment_init)->assign($result,$result)->assign('pages',$pages)->assign('iscomment',$iscomment)->assign('nocomment',$nocomment)->display('comment');
	}

	public function add() {
		if($this->member['id'] < 1) {
			redirect(url('member/public/login',array('url_forward'=>urlencode($_SERVER['REQUEST_URI']))));
		}
		if(checksubmit('dosubmit')) {
			$_GET['id'] = 0;
			$_GET['mid'] = $this->member['id'];
			$_GET['username'] = $this->member['username'];
			$result = $this->comment_service->add($_GET);
			if($result === false) {
				showmessage($this->comment_service->error,'',0);
			}
			$this->load->service('attachment/attachment')->attachment($_GET['imgs'], '',false);
			showmessage(lang('publish_estimate_success','comment/language'), url('index'), 1);
		} else {
			showmessage(lang('_error_action_'));
		}
	}
	
	/**
	 * Ajax 获取商品价格
	 * @return int $sku_id 商品SKU
	 */
	public function ajax_comment() {
		$spu_id = (int) $_GET['spu_id'];
		$_sqlmap = array();
		$_sqlmap['spu_id'] = $spu_id;
		$_sqlmap['is_shield'] = 1;
		if($_GET['mood']){
			$_sqlmap['mood'] = $_GET['mood'];
		}
		$result = $this->comment_service->lists($_sqlmap, 5, 'id DESC', $_GET['page']);
		$result['pages']  = pages($result['count'], 5);
		$this->load->librarys('View')->assign('result',$result);
		$result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}
	/**
	 * [count_iscomment ajax获取评价内容]
	 * @return [type] [description]
	 */
	public function ajax_content(){
		$sqlmap = array();
		$sqlmap['mid'] = $this->member['id'];
		$sqlmap['sku_id'] = $_GET['sku_id'];
		$sqlmap['order_sn'] = $_GET['order_sn'];
		$result = $this->comment_service->fetch($sqlmap);
		foreach ($result as $key => $value) {
			$result[$key]['_datetime'] = date('Y-m-d H:i:s',$value['datetime']);
		}
		$this->load->librarys('View')->assign('result',$result);
		$result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}
	public function ajax_comment_index(){
		if($this->member['id'] < 1) {
			showmessage(lang('_not_login_'),url('member/public/login',array('url_forward'=>$_GET['url_forward'])),0);
		}else{
			showmessage(lang('_operation_success_'),url('comment/index/index'),1);
		}
	}

	public function set_comment(){

		$goods = $this->load->service('order/order_sku')->detail($_GET['id']);
		$attachment_init = attachment_init(array('module' => 'goods', 'mid' => $this->member['id'],'allow_exts'=>array('bmp','jpg','gif','jpeg','png')));
		$SEO = seo('发表详情 - 会员中心');
		$this->load->librarys('View')->assign('goods',$goods)->assign('attachment_init',$attachment_init)->assign('SEO',$SEO)->display('set_comment');
	}

	public function comment_detail(){
		$goods = $this->load->service('order/order_sku')->detail($_GET['id']);
		$sqlmap = array();
		$sqlmap['sku_id'] = $goods['sku_id'];
		$sqlmap['order_sn'] = $goods['order_sn'];
		$result = $this->comment_service->fetch($sqlmap);
		$SEO = seo('评价详情 - 会员中心');
		$this->load->librarys('View')->assign('goods',$goods)->assign('result',$result[0])->assign('SEO',$SEO)->display('comment_detail');
	}

	public function ajax_iscomment(){
		if(!$this->member['id']) return false;
		$_sqlmap = array();
		$_sqlmap['buyer_id'] = $this->member['id'];
		$_sqlmap['delivery_status'] = 2;
		$_sqlmap['iscomment'] = $_GET['iscomment'] ? $_GET['iscomment'] : 0;
		$result = $this->goods_service->lists($_sqlmap, 5, 'id DESC', $_GET['page']);
		$this->load->librarys('View')->assign('result',$result);
		$result = $this->load->librarys('View')->get('result');
		echo json_encode($result);
	}
}