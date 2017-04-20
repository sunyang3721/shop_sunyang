<?php
hd_core::load_class('init', 'admin');
class admin_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		$this->service = $this->load->service('notify/notify');
		$this->temlage_service = $this->load->service('notify/notify_template');
	}

	public function index() {
		$notifys = $this->service->fetch_all();
		$this->load->librarys('View')->assign('notifys',$notifys)->display('notify_index');
	}

	/* 配置参数 */
	public function config() {
		$notify = $this->service->fetch_by_code($_GET['code']);
		if($notify === FALSE) {
			showmessage($this->service->error);
		}
		if(checksubmit('dosubmit')) {
			$r = $this->service->config($_GET['vars'], $_GET['code']);
			if($r === false) {
				showmessage($this->service->error);
			}
			showmessage(lang('_operation_success_'), url('index'), 1);
		} else {
			$_setting = $this->service->get_fech_all();
			$_config = $_setting[$_GET['code']]['configs'];
			$this->load->librarys('View')->assign('notify',$notify)->assign('_setting',$_setting)->assign('_config',$_config)->display('notify_config');
		}
	}

	/* 配置模板 */
	public function template() {
		$notify = $this->service->fetch_by_code($_GET['code']);
		$hooks = array(
			'after_register'=>'注册成功',
			'register_validate'=>'注册验证',
			'mobile_validate'=>'手机验证',
			'email_validate'=>'邮箱验证',
			'forget_pwd'=>'找回密码',
			'money_change'=>'余额变动',
			'recharge_success'=>'充值成功',
			'order_delivery'=>'订单发货',
			'confirm_order'=>'确认订单',
			'pay_success'=>'付款成功',
			'create_order'=>'下单成功',
			'goods_arrival'=>'商品到货',
		);
		$replace = $this->service->template_replace();
		$ignore = explode(',', $notify['ignore']);
		foreach ($ignore as $k => $v) {
			unset($hooks[$v]);
		}
		if(checksubmit('dosubmit')) {
			if($_GET['code'] == 'wechat'){
				$template = array();
				$enabled = array();
				foreach ($hooks as $k => $v) {
					$template[$k] = array('template_id'=>$_GET[$k.'template_id'],'template'=>$_GET[$k.'template']);
				}
				$enabled = $_GET['enabled'];
				$_GET['template'] = unit::json_encode($template);
				$_GET['enabled'] = json_encode($enabled);
				$_GET['name'] = $notify['name'];
			}else{
				$template = array();
				$enabled = array();
				foreach ($hooks as $k => $v) {
					$template[$k] = str_replace($replace,array_keys($replace),$_GET[$k]);
				}
				$enabled = $_GET['enabled'];
				$_GET['template'] = unit::json_encode($template);
				$_GET['enabled'] = json_encode($enabled);
				$_GET['name'] = $notify['name'];
			}
			$result = $this->temlage_service->update($_GET);
			if($result === false) showmessage($this->temlage_service->error);
			showmessage(lang('upload_message_success','notify/language'),url('index'));
		}else{
			$template = $this->temlage_service->fetch_by_code($_GET['code']);
			foreach ($template['template'] as $k => $temp) {
				$template['template'][$k] = str_replace(array_keys($replace),$replace,$temp);
			}
			//单独处理短信
			if($_GET['code'] == 'sms'){
				$cloud = $this->load->service('admin/cloud');
				$data = $cloud->getsmstpl();
				$template = $data['result'] ? array_merge($template,$data['result']) : $template;
				$sms_num = $cloud->getsmsnum();
				$this->load->librarys('View')->assign('sms_num',$sms_num);
			}
			//单独处理微信
			if($_GET['code'] == 'wechat'){
				$_setting = $this->service->get_fech_all();
				$_config = $_setting[$_GET['code']]['configs'];
				$wechat = new we($_config);
				$templat = $wechat->getAllTemplatelists();
				$template = $this->temlage_service->fetch_by_code($_GET['code']);
				$notify['ignore'] = str_replace("reg_success,","", $notify['ignore']);
				unset ($hooks['after_register']);

			}
			$this->load->librarys('View')->assign('notify',$notify)->assign('templat',$templat)->assign('hooks',$hooks)->assign('replace',$replace)->assign('ignore',$ignore)->assign('template',$template)->display('notify_template');
		}
	}

    /**
     *  设置通知模板数据入库【ajax】
     */
    public function set_notify()
    {
        $data = $_GET['data']['result'][0];
        $data['enabled'] = json_encode($data['enabled']);
        model('notify_template')->update($data);
        showmessage('ok','',1);
	}

	// 根据模板id获取模板配置信息
    public function get_notify()
    {
        $map['id'] = $_GET['code'];
        $data = model('notify_template')->where($map)->select();
        showmessage('ok','',1,$data);
    }

	/* 开启或关闭 */

    public function ajax_enabled() {
    	if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
        $code = $_POST['code'];
        if ($this->service->change_enabled($code)) {
            showmessage(lang('_operation_success_'), '', 1);
        } else {
            showmessage(lang('_operation_fail_'), '', 0);
        }
    }

	/**
     * 卸载支付方式
     */
    public function uninstall() {
    	if(empty($_GET['formhash']) || $_GET['formhash'] != FORMHASH) showmessage('_token_error_');
        $code = $_GET['code'];
        $this->service->delete($code);
        $this->temlage_service->delete($code);
        showmessage(lang('uninstall_success','notify/language'), url('index'), 1);
    }
}