<?php
class site_control extends init_control{
    public function _initialize() {
        parent::_initialize();
		$this->service = $this->load->service('admin/setting');
		helper('attachment');
    }
    /* 基本设置 */
    public function base() {
		$array = array('exp_rate','cart_jump');
        if(checksubmit('dosubmit')) {
			if(!empty($_FILES['site_logo']['name'])) {
				$code = attachment_init(array('mid'=>$this->admin['id'],'allow_exts'=>array('bmp','jpg','gif','jpeg','png')));
				$_GET['site_logo'] =  $this->load->service('attachment/attachment')->setConfig($code)->upload('site_logo');
				if(!$_GET['site_logo']){
					showmessage($this->load->service('attachment/attachment')->error);
				}
			}
			$_GET['pays'] = isset($_GET['pays']) ? $_GET['pays'] : array();
			$_GET['balance_deposit'] = isset($_GET['balance_deposit']) ? $_GET['balance_deposit'] : array();
			$_GET['invoice_content'] = explode("\r\n", $_GET['invoice_content']);
			$result = $this->service->update($_GET);
			if(!$result){
				showmessage($this->service->error);
			}
			showmessage(lang('_operation_success_'),url('base'),1);
		} else {
			$setting = $this->service->get();
			// 获取所有已开启的支付方式
			$payment = model('pay/payment','service')->get();
			foreach ($payment as $k => $pay) {
				$payment[$k] = $pay['pay_name'];
			}
			$this->load->librarys('View')->assign('setting',$setting)->assign('payment',$payment)->display('site_base');
        }
    }
	/* 注册与访问 */
    public function reg() {
      if(checksubmit('dosubmit')){
      		runhook('before_set_reg');
			$_GET['reg_user_fields'] = isset($_GET['reg_user_fields']) ? $_GET['reg_user_fields'] : array();
			$_GET['reg_pass_complex'] = isset($_GET['reg_pass_complex']) ? $_GET['reg_pass_complex'] : array();
      		$_GET['reg_user_censor'] = trim(preg_replace("/\s*(\r\n|\n\r|\n|\r)\s*/", "\r\n", $_GET['reg_user_censor']));
			$result = $this->service->update($_GET);
			if(!$result) showmessage($this->service->error);
			showmessage(lang('_operation_success_'),url('reg'),1);
		}else{
			$setting = $this->service->get();
			$this->load->librarys('View')->assign('setting',$setting)->display('site_reg');
		}
    }

	/* 优化设置 -> SEO设置 */
    public function seo() {
    	if(checksubmit('dosubmit')){
      		$data['seos'] = (array) $_GET['seos'];
      		$result = $this->service->update($data);
			if(!$result) showmessage($this->service->error);
			showmessage(lang('_operation_success_'),url('seo'),1);
		} else {
			$seos = $this->service->get('seos');
			$this->load->librarys('View')->assign('seos',$seos)->display('site_seo');
		}
    }

	/* 优化设置 */
    public function rewrite() {
        $rewrites = @include CONF_PATH.'rewrite.php';
        $this->load->librarys('View')->assign('rewrites',$rewrites)->display('site_rewrite');
	}

    /* 更改伪静态下的值 */
    public function ajax_update_rewrite() {
    	$field = remove_xss($_GET['field']);
    	if (!in_array($field, array('show','showurl'))) showmessage(lang('_error_action_'));
    	$id = remove_xss($_GET['id']);
    	$val = remove_xss($_GET['val']);
    	$rewrites = @include CONF_PATH.'rewrite.php';
    	if (!$rewrites[$id]) showmessage(lang('record_no_exist','admin/language'));
    	$rewrites[$id][$field] = $val;
    	$data = "<?php \nreturn ".stripslashes(var_export(daddslashes($rewrites), true)).";";
		@file_put_contents(CONF_PATH.'rewrite.php',$data);
		showmessage(lang('_operation_success_'), url('admin/site/rewrite'), 1);
    }
}