<?php
hd_core::load_class('init', 'goods');
class public_control extends init_control
{
	public function _initialize() {
		parent::_initialize();
		if($this->member['id'] > 0 && METHOD_NAME !== 'logout' && METHOD_NAME !== 'resetemail' && METHOD_NAME !=='wechat_bind' && METHOD_NAME !=='ajax_verifi' && METHOD_NAME !=='binding') {
			redirect(url('index/index'));
		}
		$this->service = $this->load->service('member/member');
		$this->notify_service = $this->load->service('notify/notify');
		$this->notify_template_service = $this->load->service('notify/notify_template');
	}

	public function index() {
		hd_error::system_error('_data_type_invalid_');
	}

	public function register() {
		$setting = model('admin/setting','service')->get();
		if(!$setting['reg_allow']) {
			showmessage($setting['reg_closedreason']);
		}
		if(checksubmit('dosubmit')) {
			if(!$this->service->register($_GET)) {
				showmessage($this->service->error);
			}
			$url = $_GET['url_forward'] ? urldecode($_GET['url_forward']) : url('member/index/index');
			showmessage(lang('register_success','member/language'), $url, 1);
		} else {
			$SEO = seo('会员注册');
            $sms_reg = false;
            $sms_enabled = $this->notify_service->find(array('code'=>'sms','enabled'=>1));
            if($sms_enabled){
                $sqlmap['id'] = 'sms';
                $sqlmap['enabled'] = array('like','%register_validate%');
                $sms_reg = $this->notify_template_service->find($sqlmap);
            }
            $this->load->librarys('View')->assign('SEO',$SEO)->assign('sms_reg',$sms_reg)->display('register');
		}
	}

	public function login() {
		if(checksubmit('dosubmit')) {
			if(!$this->service->login($_GET['username'], $_GET['password'])) {
				showmessage($this->service->error);
			}
			$url = $_GET['url_forward'] ? urldecode($_GET['url_forward']) : url('member/index/index');
			showmessage(lang('login_success','member/language'), $url, 1);
		} else {
			$SEO = seo('会员登录');
			$this->load->librarys('View')->assign('SEO',$SEO)->display('login');
		}
	}

	public function logout() {
		$this->service->logout();
		redirect(url('member/public/login'));
	}

	/*找回密码 重构 */
	public function repwd(){
	    if(IS_POST){
	        if (!is_email($_GET['email'])) showmessage(lang('email_format_error','member/language'));
	         $this->service->femail($_GET['email']);
	    }else{
	        $SEO = seo(0,"找回密码");
	        $this->load->librarys('View')->assign('SEO',$SEO)->display('repwd');
	     }
	}

	/**
	 * 忘记密码
	 */
	public function forget_password()
	{
		if(IS_POST){
			$username = $_GET['username'];
            if(!is_email($username) && !is_mobile($username)){
                showmessage('邮箱或者电话格式错误','',0);
            }
            $member = $this->service->username_exist($username);
			if (!$member){
                showmessage($this->service->error,'',0);
            }
            $result = $this->service->send_message($username,$member);
            if($result){
                showmessage('验证短信已经发送，请注意查收。','',1);
            }

		}else{
			$SEO = seo('忘记密码');
			$this->load->librarys('View')->assign('SEO',$SEO)->display('forget_password');
		}
	}
	/*设置新密码*/
	public function setNpwd(){
	    if(IS_POST){
	        $pwd=$_GET['pwd'];
	        $repwd=$_GET['repwd'];
	        $mid=$_GET['mid'];
	        $encrypt=$this->service->fetch_by_id($mid);
	        $vcode=authcode(base64_decode($_GET['vcode']), 'DECODE', $encrypt['encrypt']) ;
	        $key = $this->service->get_vcode_field('vcode', "mid='$mid' AND vcode='$vcode'");
	        if($key == $vcode){
    	        if($pwd !=$repwd){
    	            showmessage(lang('second_password_different','member/language'));
    	        }else {
    	            $data['password']=md5(md5($pwd).$encrypt['encrypt']);
    	            $re=$this->service->setNpwd($_POST['mid'],$data);
    	            if($re){
    	            	$this->service->vcode_delete(array('mid' => $mid,'vcode' => $vcode));
    	                showmessage(lang('_operation_success_'));
					}else{
    	                showmessage(lang('_operation_fail_'));
    	            }
    	        }
	        }else if($key != $vcode || empty($key)){
	            showmessage(lang('error_link','member/language'));
	        }
	    }else{
	        $SEO = seo(0,'设置新密码');
	        $this->load->librarys('View')->assign('SEO',$SEO)->assign('mid',$_GET['mid'])->assign('key',$_GET['key'])->display('repwd');
	    }
	}

    /**
     * 检查邮箱
     */
    public function valid_email()
    {
        $email = $_GET['email'];
        $result = $this->service->_valid_email($email);
        if(!$result){
            showmessage($this->service->error,'',0);
        }else{
            showmessage('邮箱正常可用','',1);
        }
    }

    /**
     * 重置新密码
     */
    public function reset_password()
    {
        $mid = $_GET['mid'];
        $key = $_GET['key'];
        if(IS_POST){
            $pwd = $_GET['pwd'];
            $repwd = $_GET['repwd'];
            if(!$pwd || !$repwd){
                $username = $_GET['username'];
                $vcode = $_GET['vcode'];
                $result = $this->service->valid_vcode($vcode,$username);
                if(!$result){
                    showmessage($this->service->error,'',0);
                }else{
                    showmessage('验证码正确',url('member/public/reset_password',$result['params']));
                }
            }
            if($pwd != $repwd){
                showmessage('确认密码错误，请重新输入','',0);
            }

            $reset_result = $this->service->reset_password($mid,$key,$pwd);
            if(!$reset_result){
                showmessage($this->service->error);
            }else{
                showmessage('密码已重置',url('member/public/login'),1);
            }
        }else{
            $SEO = seo('重置密码');
            $this->load->librarys('View')->assign('SEO',$SEO)->display('repwd');
        }
    }

	public function ajax_register_check() {
		$result = $this->service->valid($_GET['name'],$_GET['param']);
		if($result === false){
			showmessage($this->service->error);
		}
		showmessage('', '', 1, array(), 'json');
	}
	public function ajax_register_vcode_check() {
		$result = $this->service->_valid_vcode($_GET['param'],$_GET['mobile']);
		if($result === false){
			showmessage($this->service->error);
		}
		showmessage('', '', 1, array(), 'json');
	}
	/*邮箱验证*/
	public function resetemail(){
		$mid = $vcode = $email ='';
		extract($_GET,EXTR_IF_EXISTS);
		list($mid,$vcode,$email) = json_decode(authcode(base64_decode($vcode),'DECODE'),TRUE);

		$sqlmap = array();
		$sqlmap['mid'] = $mid;
		$sqlmap['action'] = 'resetemail';
		$sqlmap['vcode'] = $vcode;
		$sqlmap['dateline'] = array('EGT',time()-1800);
		$_vcode = $this->service->get_vcode_field('vcode', $sqlmap);

		if ($_vcode !== $vcode) showmessage(lang('captcha_error','member/language'),'',0);

		$data['id'] = $mid;
		$data['email'] = $email;

		$r = $this->service->update($data,FALSE);
		if($r){
			showmessage(lang('edit_email_success','member/language'),url('member/index/index'),1);
		}else{
			showmessage(lang('edit_email_error','member/language'),url('member/index/index'),0);
		}
	}
	/* 手机验证 */
	public function register_validate(){
		$this->service->vcode_delete(array('mobile' => $_GET['mobile'],'action' =>'register_validate','dateline'=>array('LT',TIMESTAMP)));
		$result = $this->service->post_vcode($_GET,'register_validate');
		if($result){
			showmessage(lang('send_success','member/language'),'',1);
		}else{
			showmessage(lang('send_error','member/language'),'',0);
		}
	}

	
	/**
	*	@param text $openid 
	*/
	public function wechat_bind(){
		$openid = $_GET['openid'];
        $this->load->librarys('View')->assign('openid',$openid)->display('wechat_bind');
	}

	/**
	*	检查是否已经绑定
	*	@param text $openid 
	*/
	public function ajax_verifi(){
		$result  = 	$this->service->weixin_valid($_GET['openid']);
		if(!$result){
			showmessage('', '', 0);
		}
			showmessage('', '', 1);
	}

	/**
	*	绑定微信公众号
	*/
	public function binding(){
		$result  = 	$this->service->valid_openid($_GET['username'], $_GET['password'], $_GET['openid']);
		if(!$result){
			showmessage($this->service->error,'',0);
		}else{
			$_setting = model('notify/notify','service')->get_fech_all();
			$config = $_setting['wechat']['configs'];
			$weixin = new we($config);
			$text = array(
				'touser'=>$_GET['openid'],
				'msgtype'=>'text',
				'text'=>array(
					'content'=>"恭喜您绑定成功",
							)
					);
			$token = $weixin->sendCustomMessage($text);
			$url = $_GET['url_forward'] ? urldecode($_GET['url_forward']) : url('member/index/index');
			showmessage('绑定成功', $url, 1);
		}
	}
}