<?php
require_cache(MODULE_PATH . 'library/xiaoneng.class.php');
class xiaoneng_service extends service {
	/**
	 * 开通
	 * @param  array	$params
	 * @return [boolean]
	 */
	public function apply($params){
		$this->xneng = new xiaoneng();
		if(empty($params['company_name'])){
			$this->error = '公司名称不能为空';
			return FALSE;
		}
		if(empty($params['link_man'])){
			$this->error = '公司联系人不能为空';
			return FALSE;
		}
		if(empty($params['mobile_phone_no'])){
			$this->error = '公司联系人手机不能为空';
			return FALSE;
		}
		if(empty($params['email'])){
			$this->error = '公司邮箱不能为空';
			return FALSE;
		}
		if(empty($params['addr'])){
			$this->error = '公司地址不能为空';
			return FALSE;
		}
		if(empty($params['url'])){
			$this->error = '公司网址不能为空';
			return FALSE;
		}
		if(empty($params['version'])){
			$this->error = '小能账号的版本标识不能为空';
			return FALSE;
		}
		$data = array();
		$data['company_name'] = $params['company_name'];
		$data['link_man'] = $params['link_man'];
		$data['mobile_phone_no'] = $params['mobile_phone_no'];
		$data['email'] = $params['email'];
		$data['addr'] = $params['addr'];
		$data['url'] = $params['url'];
		$data['version'] = $params['version'];
		$data['expire_time'] = 10;
		unset($params);
		$result = $this->xneng->type('post')->data($data)->api('xiaoneng/register');
		if($result['code'] == 200 && !empty($result['result'])){
			$_info = array(
				'siteid' => $result['result']['siteid'],
				'admin_userid' => $result['result']['admin_userid'],
				'admin_pass' => $result['result']['admin_pass'],
				'version'	=> $data['version'],
				'start_time' => $result['result']['start_time'],
				'end_time' => $result['result']['end_time'],
				'client_download_url' => $result['result']['client_download_url'],
				'doc_download_url' => $result['result']['doc_download_url']
			);
			$info_text['__xiaoneng__'] = authcode(serialize($_info),'ENCODE');
            $config = $this->load->librarys('hd_config');
            $r = $config->file('xiaoneng')->note('小能客服文件')->space(8)->to_require_one($info_text,null,1);
            if($r) return true;
        }else{
			$this->code = $result['code'];
			$this->error = $result['msg'];
			return FALSE;
		}
	}
	/**
	 * 详情
	 * @param  array	$params
	 * @return [array]
	 */
	public function detail(){
		$config =  unserialize(authcode(config('__xiaoneng__','xiaoneng'),'DECODE'));
		if(empty($config)){
			$result = $this->get_xiaoneng_info();
			if($result){
				$config =  unserialize(authcode(config('__xiaoneng__','xiaoneng'),'DECODE'));
			}else{
				return FALSE;
			}
		}
		$reception_num = array('PCWAP1' => 3,'PCWAP2' => 5,'PCWAP3' => 10);
		$config['reception_num'] = $reception_num[$config['version']];
		return $config;
	}
	/**
	 * sku格式
	 * @param  array	$params
	 * @return [array]
	 */
	public function sku_format($sku_id = 0){
		if($sku_id < 1){
			$this->error = '商品id不能为空！';
			return FALSE;
		}
		$sku = $this->load->service('goods/goods_sku')->fetch_by_id($sku_id);
		if(empty($sku)){
			$this->error = '商品不存在！';
			return FALSE;
		}
		$info = array(
			'item' => array(
				'id' => $sku['sku_id'],
				'name' => $sku['sku_name'],
				'imageurl' => $sku['thumb'],
				'url' => str_replace('/api/xiaoneng.php','',$_SERVER['SCRIPT_NAME']).'/index.php?m=goods&c=index&a=detail&sku_id='.$sku['sku_id'],
				'currency' => '￥',
				'siteprice' => $sku['prom_price'],
				'marketprice' => $sku['market_price'],
				'category' => $sku['cat_name'],
				'brand' => $sku['brand']['name']
			)
		);
		$specs_arr = array();
		foreach ($sku['specs'] AS $spec) {
			$specs_arr[] = array($spec['name'],implode('/', $spec['value']));
		}
		foreach ($specs_arr as $key => $spec_arr) {
			$info['item']['custom'.($key+1)] = $spec_arr;
		}
		return $info;
	}
	/**
	 * 获取配置
	 * @return [boolean]
	 */
	private function get_xiaoneng_info(){
		$this->xneng = new xiaoneng();
		$result = $this->xneng->type('get')->api('xiaoneng/access');
		if($result['code'] == 200 && !empty($result['result'])){
			$_info = array(
				'siteid' => $result['result']['siteid'],
				'admin_userid' => $result['result']['admin_userid'],
				'admin_pass' => $result['result']['admin_pass'],
				'version'	=> $data['version'],
				'start_time' => $result['result']['start_time'],
				'end_time' => $result['result']['end_time'],
				'client_download_url' => $result['result']['client_download_url'],
				'doc_download_url' => $result['result']['doc_download_url']
			);
			$info_text['__xiaoneng__'] = authcode(serialize($_info),'ENCODE');
            $config = $this->load->librarys('hd_config');
            $r = $config->file('xiaoneng')->note('小能客服文件')->space(8)->to_require_one($info_text,null,1);
            return TRUE;
		}
		return FALSE;
	}
	/**
	 * 保存分配配置
	 * @param  array	$params
	 * @return [boolean]
	 */
	public function update($params){
		$data = $params;
		if(isset($params['config'])){
			$data['config'] = json_encode($params['config']);
		}
		$result = model('xiaoneng/xiaoneng_service')->where(array('id' => 1))->save($data);
		if($result === FALSE){
			$this->error = $this->load->table('xiaoneng_service')->getError();
			return FALSE;
		}
		cache('xiaoneng_service',NULL);
		return TRUE;
	}
	/**
	 * 保存设置配置
	 * @param  array	$params
	 * @return [boolean]
	 */
	public function edit($config){
		$result = model('xiaoneng/xiaoneng_config')->where(1)->delete();
		foreach ($config['name'] as $key => $name) {
			if(empty($name)) continue;
			$data = array();
			$data['name'] = $name;
			$data['identifier'] = $config['identifier'][$key];
			$result = $this->load->table('xiaoneng/xiaoneng_config')->update($data);
			if($result === FALSE){
				$this->error = $this->load->table('xiaoneng/xiaoneng_config')->getError();
				return FALSE;
			}
		}
		return TRUE;
	}
	/**
	 * 获取设置配置
	* @return [array]
	 */
	public function get_server_config(){
		return model('xiaoneng/xiaoneng_config')->select();
	}
	/**
	 * 生成缓存
	 * @return [boolean]
	 */

	public function get(){
		return model('xiaoneng/xiaoneng_service')->where(array('id' => 1))->cache('xiaoneng_service',3600)->find();
	}
	/**
	 * 获取缓存
	 * @param  array	$params
	 * @return [array]
	 */
	public function get_config($param){
		$config = $this->get();
		return $config[$param];
	}
	/**
	 * 获取购物车信息
	 * @return [array]
	 */
	public function get_cart_format($mid = 0,$skuids = ''){
		$carts = $this->load->service('order/cart')->get_cart_lists($mid,$skuids);
		$data = array();
		if(empty($skuids))$data['cartprice'] = $carts['all_prices'];
		foreach ($carts['skus'] as $sku_id => $sku_list) {
			$data['items'][] = array('id' => $sku_id,'count' => $sku_list['number']);
		}
		return $data;
	}
	/**
	 * 查询接待组信息
	 * @return [array]
	 */
	public function fetch_all_config(){
		$info = model('xiaoneng/xiaoneng_config')->getfield('identifier,name',TRUE);
		return $info;
	}
}
