<?php
class index_control extends cp_control
{
	public function _initialize() {
		parent::_initialize();
	}

	public function index(){
		$SEO = seo('会员中心');
		//收藏的商品
		$favorite = $this->load->service('member/member_favorite')->set_mid($this->member['id'])->lists(array(), 10);
		//待评价商品
		$notcommentgoods = $this->load->service('order/order_sku')->fetch(array('buyer_id' => $this->member['id'],'iscomment'=>0,'delivery_status'=>2),10);
		//进行中的订单
		// $counts = model('order/order','table')->member_id($this->member['id'])->out_counts();
		//咨询回复
		$counts['consult'] = (int)$this->load->service('goods/goods_consult')->get_user_consult($this->member['id']);
		//站内信
		$counts['message'] = (int)$this->load->service('member/member_message')->user_message($this->member['id']);
		//配置文件
		$_config = model('admin/setting','service')->get();
		$this->load->librarys('View')->assign('_config',$_config)->assign('favorite',$favorite)->assign('notcommentgoods',$notcommentgoods)->assign('counts',$counts)->assign('SEO',$SEO)->display('index');
	}
	public function get_rec_data(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$data = array();
		$data = $this->load->service('goods/goods_sku')->lists(array('order'=>'rand()'),array('limit'=>10));
		$result = $data['lists'];
		foreach ($result as $key => $value) {
			$result[$key]['goods_url'] = url('goods/index/detail',array('sku_id'=>$value['sku_id']));
			$result[$key]['format_thumb'] = thumb($value['thumb'],500,500);
		}
		showmessage('success','',1,$result);
	}
	public function clear_history(){
		if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
		$r = $this->load->service('goods/goods_sku')->clear_history();
	}
}