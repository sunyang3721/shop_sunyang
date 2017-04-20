<?php
hd_core::load_class('init', 'admin');
class goods_consult_control extends init_control {
	protected $service = '';
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('goods_consult');
	}
	/**
	 * [index 咨询列表]
	 */
	public function index(){
		$sqlmap = array();
		if($_GET['start']) $time[] = array("GT", strtotime($_GET['start']));
		if($_GET['end']) $time[] = array("LT", strtotime($_GET['end']));
		if($time) $sqlmap['dateline'] = $time;
		if($_GET['keywords']) $sqlmap['username'] = array('like', "%".$_GET['keywords']."%");
		$sqlmap['status'] = !empty($_GET['status']) ? $_GET['status'] : 0;
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
		$result = $this->service->get_lists($sqlmap,$limit,$_GET['page']);
		$count = $this->service->count($sqlmap);
		$pages = $this->admin_pages($count, $limit);
		$lists = array(
            'th' => array(
                'sku_name' => array('title' => '咨询商品','length' => 50,'style' => 'goods'),
                'username' => array('title' => '会员账号','length' => 20),
                'dateline' => array('length' => 20,'title' => '咨询时间','style' => 'date'),
            ),
            'lists' => $result,
            'pages' => $pages,
        );
		$this->load->librarys('View')->assign('lists',$lists)->assign('pages',$pages)->display('goods_consult_lists');
	}
	/**
	 * [delete 删除]
	 */
	public function delete(){
		$result = $this->service->delete($_GET);
		if(!$result){
			showmessage($this->service->error);
		}
		showmessage(lang('_operation_success_'),url('index',array('status' => (int) $_GET['status'])),1);
	}
	/**
	 * [reply 回复咨询]
	 */
	public function reply(){
		$result = $this->service->reply($_GET);
	    if(!$result){
			showmessage($this->service->error);
		}else{
			showmessage(lang('_operation_success_'),'',1);
		}
	}
	/**
	 * [add 添加]
	 */
	public function add(){
		$goods_detail = $this->load->service('goods/goods_sku')->fetch_by_id($_GET['sku_id']);
		if(checksubmit('dosubmit')){
			$result = $this->service->add($_GET);
			if(!$result){
				showmessage($this->service->error);
			}else{
				showmessage(lang('_operation_success_'),'',1);
			}
		}
		$this->load->librarys('View')->assign($goods_detail,$goods_detail)->display('consult');
	}
	/**
	 * [excel 导出]
	 */
	public function excel(){
		$data = array();
		$field = 'id,sku_id,question,username,reply_content';
		$data[] = $this->service->getField($field,array('id'=>array('IN',$_GET['id'][0])));
		$header = array(
			  'id' =>'咨询id',
			  'question'=>'问题',
			  'username'=>'用户名',
			  'reply_content'=>'回复内容'
			);
		$this->DownloadCSV($data[0],$header);
	}
	/*
	* 通用方法
	* 导出大数据为CSV
	* 参数依次传入 查询对象,CSV文件列头(键是数据表中的列名,值是csv的列名),文件名.对数据二次处理的函数;
	*/
	private function DownloadCSV($selectObject,$head){
		$fileName=time();
		set_time_limit(0);
		//下载头.
		header ('Content-Type: application/vnd.ms-excel;charset=gbk');
		header ('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
		header ('Cache-Control: max-age=0');
		//输出流;
		$file = 'php://output';
		$fp = fopen ( $file, 'a' );
		function changCode( $changArr ) {
			// 破Excel2003中文只支持GBK编码;
			foreach ( $changArr as $k => $v ) {
				$changArr [$k] = iconv ( 'utf-8', 'gbk', $v );
			}
			//返回一个 索引数组;
			return array_values( $changArr );
		};
		//写入头部;
		fputcsv ( $fp, changCode( $head ) );
		//写入数据;
		foreach($selectObject as $key => $value){
			fputcsv ( $fp, changCode($value) );
			flush();
		}
		exit();
	}
}