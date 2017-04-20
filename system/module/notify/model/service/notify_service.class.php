<?php
class notify_service extends service
{
	protected $entrydir = '';

	public function _initialize() {
		$this->entrydir = APP_PATH.'module/notify/library/driver/';
		$this->table = $this->load->table('notify/notify');
		$this->member_service = $this->load->service('member/member');
	}

	public function fetch_all() {
		$folders = glob($this->entrydir.'*');
        foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $notifys[$xmldata['code']] = $xmldata;
            }
        }
        $notifys = array_merge_multi($notifys, $this->get_fech_all());
		return $notifys;
	}

	/* 根据标识 */
	public function fetch_by_code($code) {
		if(empty($code)) {
			$this->error = lang('_param_error_');
			return false;
		}
		if(!is_dir($this->entrydir.$code) || !file_exists($this->entrydir.$code)) {
			$this->error = lang('no_found_config_file','notify/language');
			return false;
		}
		$config = $this->entrydir.$code.'/config.xml';
        $importtxt = @implode('', file($config));
        $xmldata = xml2array($importtxt);
        return $xmldata;
	}

	public function import($params){
		$params['config'] = json_decode($params['config'],TRUE);
		return $this->config($params['config'],$params['code']);
	}

	public function config($vars, $code) {
		$notify = $this->fetch_by_code($code);
		if($notify === false) {
			return false;
		}
		$notify['config'] = unit::json_encode($vars);
		if($this->table->find($code)) {
			$rs = $this->table->update($notify);
		} else {
			$rs = $this->table->add($notify);
			$this->load->table('notify_template')->add(array('id'=>$code));
		}
		if($rs === false) {
			$this->error = lang('config_operate_error','notify/language');
			return false;
		}
		cache('notify_enable',NULL);
		return true;
	}

	public function get_fech_all() {
		$result = array();
		$notifys = $this->load->table('notify')->getField('code,enabled,config', TRUE);
		foreach ($notifys as $key => $value) {
			$value['configs'] = json_decode($value['config'], TRUE);
			$result[$value['code']] = $value;
		}
		return $result;
	}

	//根据code获取启用的通知方式
	public function get_fech_enable_code($code) {
		$result = $this->get();
		return $result[$code];
	}
	/**
	 * 获取缓存
	 */
	public function get($key = NULL){
		$notifys = $this->load->table('notify/notify')->cache('notify_enable',3600)->where(array('enabled'=>1))->select();
		return is_string($key) ? $notifys[$key] : $notifys;
	}
	/**
	 * [启用禁用支付方式]
	 * @param string $pay_code 支付方式标识
	 * @return TRUE OR ERROR
	 */
	public function change_enabled($code) {
		$result = $this->table->where(array('code' => $code))->save(array('enabled' => array('exp', '1-enabled'), 'dateline' => time()));
		if ($result == 1) {
			$result = TRUE;
			cache('notify_enable',NULL);
		} else {
			$result = $this->table->getError();
		}
		return $result;
	}

	/**
	 * [execute 通知执行接口]
	 */
	public function execute($type,$member,$level = 100){
		$wechat_member = $member;
		$hook_data = $this->load->service('notify/notify_template')->fetch_by_hook($type);
		if(!$hook_data) return FALSE;
		//组织模版内容替换
		$setting = model('admin/setting','service')->get();
		$replace = array(
			'{username}' => $member['username'] ? $member['username'] : ($member['member']['username'] ? $member['member']['username'] : ''),
			'{site_name}' => $setting['site_name'],
		);
		switch ($type) {
			case 'register_validate':
				unset($replace['{username}']);
				$replace['{mobile_validate}'] = $member['vcode'];
				break;
			case 'email_validate':
				$replace['{email_validate}'] = $member['email_validate'];
				break;
			case 'mobile_validate':
				$replace['{mobile_validate}'] = $member['vcode'];
				break;
			case 'forget_pwd':
				$replace['{email_validate}'] = $member['email_validate'];
                $replace['{mobile_validate}'] = $member['mobile_validate'];
				break;
			case 'money_change':
				$replace['{change_money}'] = $member['change_money'];
				$replace['{user_money}'] = $member['user_money'];
				break;
			case 'recharge_success':
				$replace['{recharge_money}'] = $member['recharge_money'];
				$replace['{user_money}'] = $member['user_money'];
				break;
			case 'order_delivery':
				$replace['{delivery_sn}'] = $member['delivery_sn'];
				$replace['{delivery_type}'] = $member['delivery_type'];
				break;
			case 'confirm_order':
				$replace['{order_sn}'] = $member['order_sn'];
				break;
			case 'pay_success':
				$replace['{real_amount}'] = $member['real_amount'];
				break;
			case 'create_order':
				$replace['{order_sn}'] = $member['order_sn'];
				break;
			case 'goods_arrival':
				$replace['{goods_name}'] = $member['goods_name'];
				$replace['{goods_spec}'] = $member['goods_spec'];
				break;
			default:
				break;
		}
		//遍历
		foreach ($hook_data as $key => $value) {
			$enabled = $this->table->where(array('code' => $value['id']))->getField('enabled');
			if($enabled == 0) continue;
			$data = array();
			switch ($value['id']) {
				case 'email':
					$replace['{email}'] = $member['email'] ? $member['email'] : $member['member']['email'];
					if(!$replace['{email}']) break;
					$data['to'] = $replace['{email}'];
					$data['subject'] = str_replace(array_keys($replace), $replace, $value['template']['title']);
					$data['body'] = str_replace(array_keys($replace), $replace, $value['template']['content']);
					$data['body'] = str_replace('./uploadfile/',(is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/uploadfile/',$data['body']);
					break;
				case 'message':
					$data['mid'] = $member['member']['id'];
					$data['title'] = str_replace(array_keys($replace), $replace, $value['template']['title']);
					$data['content'] = str_replace(array_keys($replace), $replace, $value['template']['content']);
					break;
				case 'wechat':
				    $open = $this->member_service->member_open($member['member']['id']);
					$data = $this->wechat_template($value,$wechat_member,$open['openid'],$type);
					$level = 0;
					break;
				case 'sms':
					$mobile = $member['mobile'] ? $member['mobile'] : $member['member']['mobile'];
					if(!$mobile) break;
					$template_replace = $this->template_replace();
					$format_data = array();
					foreach ($replace as $k => $v) {
						if(!empty($v)){
							$format_data[$template_replace[$k]] = $v;
						}
					}
					$data['mobile'] = $mobile;
					$data['tpl_id'] = $value['template']['template_id'];
					$data['tpl_vars'] = $this->format_sms_data($format_data);
					break;
				default:
					break;
			}
			if(!empty($data)){
				$params = unit::json_encode($data);
				$this->load->service('notify/queue')->add($value['id'],'send',$params,$level);
			}
		}
		return true;
	}
	/**
	 * [template_replace 替换模版内容]
	 * @return [type] [description]
	 */
	public function template_replace(){
		$replace = array(
			'{site_name}' => '{商城名称}',
			'{username}' => '{用户名}',
			'{mobile}' => '{用户手机}',
			'{email}' => '{用户邮箱}',
			'{goods_name}' => '{商品名称}',
			'{goods_spec}' => '{商品规格}',
			'{order_sn}' => '{主订单号}',
			'{order_amount}' => '{订单金额}',
			'{shop_price}' => '{商品金额}',
			'{real_amount}' => '{付款金额}',
			'{pay_type}' => '{支付方式}',
			'{change_money}' => '{变动金额}',
			'{delivery_type}' => '{配送方式}',
			'{delivery_sn}' => '{运单号}',
			'{user_money}' => '{用户可用余额}',
			'{recharge_money}' => '{充值金额}',
			'{email_validate}' => '{邮件验证链接}',
			'{mobile_validate}' => '{验证码}'
		);
		return $replace;
	}
	public function format_sms_data($data){
		foreach ($data as $k => $v) {
			if(preg_match('/\{(.+?)\}/', $k)){
				$_data[preg_replace('/\{(.+?)\}/','$1',$k)] = $v;
			}
		}
		return $_data;
	}
	/**
	* [删除]
	* @param array $ids 主键id
	*/
	public function delete($code) {
		if(empty($code)) {
			$this->error = lang('_param_error_');
			return false;
		}
		$_map = array();
		$_map['code'] = $code;
		$result = $this->table->where($_map)->delete();
		if($result === false) {
			$this->error = $this->table->getError();
			return false;
		}
		return true;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function fetch($sqlmap = array(), $limit = 20, $page = 1, $order = "") {
		$result = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		if($result===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	读取的字段
	 * @return [type]
	 */
	public function find($sqlmap = array(), $field = "") {
		$result = $this->table->where($sqlmap)->field($field)->find();
		if($result===false){
			$this->error = $this->table->getError();
			return false;
		}
		return $result;
	}

	/**微信模板解析
	 * @hooks  array 	模板字段
	 * @member  array 	模板信息
	 * @return [type]
	 */
	private function  wechat_template($hooks,$member,$openid,$type){
		foreach($hooks['template'] as $key => $value){
			if($key == 'template'){
				foreach(explode('.DATA}}',trim($value)) as $k => $v){
					$arr = explode("{{",$v);
					if($arr[1]) $template[$k] = str_replace('{{','',$arr[1]);
				}
			}
		}
		$data = $this->compilation($template,$member,$type,$openid,$hooks);
		// $template = array('touser' => $openid,
		// 				  'template_id' => $hooks['template']['template_id'],
		// 				  'url'=> $data['durl'],
		// 				  'topcolor' => "#7B68EE",
		// 				   'data' => $data['data'],
		// 				);
		return $data;
	}

	//微信模板编译
	private function compilation($params,$member,$type,$openid,$hooks){
		$setting = model('admin/setting','service')->get();
		$sub_sn = $this->load->table('order_sub')->where(array('order_sn'=>$member['order_sn']))->getField('sub_sn');
		$replaces = array(
			    		'sitename'=>$setting['site_name'],
						'username'=>$member['username'] ? $member['username'] : ($member['member']['username'] ? $member['member']['username'] : ''),
						'mobile' =>$member['mobile'],
						'email'=>$member['email'],
						'goodsname'=>$member['goods_name'],
						'goodsspec'=>$member['goods_spec'].' 元',
						'ordersn'=>$member['order_sn'],
						'orderamount'=>$member['order_amount'],
						'shopprice'=>$member['shop_price'],
						'realamount'=>$member['real_amount'],
						'paytype'=>$member['pay_type'],
						'changemoney'=>$member['change_money'].' 元',
						'deliverytype'=>$member['delivery_type'],
						'deliverysn'=>$member['delivery_sn'],
						'usermoney'=>$member['user_money'].'元',
						'rechargemoney'=>$member['recharge_money'].' 元',
						'time'   =>date("Y-m-d H:i:s", time()),
						);
		switch($type){
			case 'recharge_success'://充值成功
				$durl = url('member/money/log','',true);
				$template = array('touser' => $openid,
							'template_id' => $hooks['template']['template_id'],
							'url'=>$durl,
							'topcolor' => "#25619a",
							'data' => array('first'=>array(
													'value'=>"充值成功",
													'color'=>"#25619a"
													),
												'keyword1'=>array(
													'value'=>$replaces['username'],
													'color'=>"#25619a"
													),
												'keyword2'=>array(
													'value'=>$replaces['rechargemoney'],
													'color'=>"#25619a"
													),
												'keyword3'=>array(
													'value'=>$replaces['paytype'],
													'color'=>"#25619a"
													),
												'keyword4'=>array(
													'value'=>$replaces['time'],
													'color'=>"#25619a"
													),
												'remark'=>array(
													'value'=>"恭喜您已经成功充值余额",
													'color'=>"#25619a"
												)
											),
							);
			break;
			case 'money_change'://余额变动
				$durl = url('member/money/log','',true);
				$template = array('touser' => $openid,
							'template_id' => $hooks['template']['template_id'],
							'url'=>$durl,
							'topcolor' => "#25619a",
							'data' => array('first'=>array(
													'value'=>"余额变动",
													'color'=>"#25619a"
													),
												'keyword1'=>array(
													'value'=>$replaces['time'],
													'color'=>"#25619a"
													),
												'keyword2'=>array(
													'value'=>$replaces['changemoney'],
													'color'=>"#25619a"
													),
												'keyword3'=>array(
													'value'=>$replaces['usermoney'],
													'color'=>"#25619a"
													),
												
												'remark'=>array(
													'value'=>"您的余额发生变动，请核是否本人操作",
													'color'=>"#25619a"
												)
											),
							);
			break;
			case 'create_order'://下单成功
				$durl = url('member/order/detail',array('sub_sn'=>$sub_sn),true);
				$template = array('touser' => $openid,
							'template_id' => $hooks['template']['template_id'],
							'url'=>$durl,
							'topcolor' => "#25619a",
							'data' => array('first'=>array(
													'value'=>"下单成功",
													'color'=>"#25619a"
													),
												'keyword1'=>array(
													'value'=>$replaces['ordersn'],
													'color'=>"#25619a"
													),
												'keyword2'=>array(
													'value'=>$replaces['time'],
													'color'=>"#25619a"
													),
												'remark'=>array(
													'value'=>"您已经成功在本店下单，欢迎再次光临",
													'color'=>"#25619a"
												)
											),
							);
			break;
			case 'order_delivery'://订单发货
				$durl = url('member/order/detail',array('sub_sn'=>$sub_sn),true);
				$template = array('touser' => $openid,
							'template_id' => $hooks['template']['template_id'],
							'url'=>$durl,
							'topcolor' => "#25619a",
							'data' => array('first'=>array(
													'value'=>"订单发货",
													'color'=>"#25619a"
													),
												'keyword1'=>array(
													'value'=>$replaces['ordersn'],
													'color'=>"#25619a"
													),
												'keyword2'=>array(
													'value'=>$replaces['deliverytype'],
													'color'=>"#25619a"
													),
												'keyword3'=>array(
													'value'=>$replaces['deliverysn'],
													'color'=>"#25619a"
													),
												'remark'=>array(
													'value'=>"您的订单已经发货，请关注您的订单信息",
													'color'=>"#25619a"
												)
											),
							);
			break;
			case 'confirm_order'://确认订单
				$durl = url('member/order/detail',array('sub_sn'=>$sub_sn),true);
				$template = array('touser' => $openid,
						  'template_id' => $hooks['template']['template_id'],
						  'url'=>$durl,
						  'topcolor' => "#25619a",
						   'data' => array('first'=>array(
												'value'=>"确认订单",
												'color'=>"#25619a"
												),
											'keyword1'=>array(
												'value'=>$replaces['ordersn'],
												'color'=>"#25619a"
												),
											'keyword2'=>array(
												'value'=>$replaces['orderamount'],
												'color'=>"#25619a"
												),
											'keyword3'=>array(
												'value'=>$replaces['time'],
												'color'=>"#25619a"
												),
											'remark'=>array(
												'value'=>"您的订单已经确认，管理员正在安排发货",
												'color'=>"#25619a"
											)
						   				),
						);
			break;
			case 'pay_success'://付款成功
				$durl = url('member/order/detail',array('sub_sn'=>$sub_sn),true);
				$template = array('touser' => $openid,
							'template_id' => $hooks['template']['template_id'],
							'url'=>$durl,
							'topcolor' => "#25619a",
							'data' => array('first'=>array(
													'value'=>"付款成功",
													'color'=>"#25619a"
													),
												'orderMoneySum'=>array(
													'value'=>$replaces['orderamount'],
													'color'=>"#25619a"
													),
												'orderProductName'=>array(
													'value'=>$replaces['ordersn'],
													'color'=>"#25619a"
													),
												'remark'=>array(
													'value'=>"您已经付款成功，我们会尽快确认您的订单",
													'color'=>"#25619a"
												)
											),
							);
			break;
			default:
			break;
		}
		
		// $replace =	array_merge($types,$replaces);	
		// foreach($params as $key => $value){
		// 	if(array_key_exists($value,$replace)){
		// 		$var[$value] = array(
		// 						'value'=>$replace[$value],
		// 						'color'=>"#743A3A",
		// 						);
		// 	}
		// }
		// return array('data'=>$var,'durl'=>$durl);
		return $template;
	}


}