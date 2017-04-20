<?php
class account_control extends cp_control
{
	public function _initialize() {
		parent::_initialize();
		if($this->member['id'] < 1) {
			redirect(url('cp/index'));
		}
		$this->service = $this->load->service('member/member_address');
		$this->member_service = $this->load->service('member/member');
		$this->notify_service = $this->load->service('notify/notify');
		$this->notify_template_service = $this->load->service('notify/notify_template');
	}

	public function index() {

	}

	public function safe() {
		$safe_level = 33;
		$safe_level_email = !empty($this->member['email']) ? 33 : 0 ;
		$safe_level_mobile = !empty($this->member['mobile']) ? 33 : 0 ;
		$safe_level = $safe_level + $safe_level_email + $safe_level_mobile;
		$sms_enabled = $this->notify_service->find(array('code'=>'sms','enabled'=>1));
		$mobile_validate = false;
		if($sms_enabled){
			$sqlmap['id'] = 'sms';
			$sqlmap['enabled'] = array('like','%mobile_validate%');
			$mobile_validate = $this->notify_template_service->find($sqlmap);
		}

		$member = $this->member;
		$open = $this->member_service->member_open($this->member['id']);
		$SEO = seo('安全中心 - 会员中心');
		$coder = $this->member_service->resetweixin();
		$this->load->librarys('View')->assign('coder',$coder)->assign('open',$open)->assign('mobile_validate',$mobile_validate)->assign('safe_level',$safe_level)->assign('member',$member)->assign('SEO',$SEO)->display('account_safe');
	}

	//根据IP返回地区
	public function ajax_login_address(){
		$ip = $_GET['ip'];
		$url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
    	$ipinfo=file_get_contents($url);
    	echo  $ipinfo;
	}

	//修改密码
	public function resetpassword(){
		if(IS_POST){
			$newpassword = $newpassword1 = $oldpassword ='';
			extract($_GET,EXTR_IF_EXISTS);
			if(md5(md5($oldpassword).$this->member['encrypt']) !== $this->member['password']){
				showmessage(lang('password_error','member/language'),'',0);
			}
			if ($newpassword !== $newpassword1) {
				showmessage(lang('two_passwords_differ','member/language'),'',0);
			}
			$data['password'] = md5(md5($newpassword).$this->member['encrypt']);
			$data['id'] = $this->member['id'];
			$r = $this->member_service->update($data,FALSE);
			if(!$r){
				showmessage(lang('edit_password_error','member/language'),'',0);
			}
			$this->load->service('member/member')->logout();
			showmessage(lang('edit_password_success','member/language'),url('member/public/login'),1);
		}else{
			$SEO = seo('修改密码-会员中心');
			$this->load->librarys('View')->assign('SEO',$SEO)->display('resetpassword');
		}
	}

	//修改手机号码
	public function resetmobile(){
		if(IS_POST){
			$result = $this->member_service->resetmobile($_GET,$this->member['id']);
			if(!$result){
				showmessage($this->member_service->error);
			}
			showmessage(lang('edit_mobile_success','member/language'),url('safe'),1);
		}else{
			$sms_enabled = $this->notify_service->find(array('code'=>'sms','enabled'=>1));
			$mobile_validate = false;
			if($sms_enabled){
				$sqlmap['id'] = 'sms';
				$sqlmap['enabled'] = array('like','%mobile_validate%');
				$mobile_validate = $this->notify_template_service->find($sqlmap);
			}
			$this->load->librarys('View')->assign('mobile_validate',$mobile_validate)->display('resetmobile');
		}

	}

    /**
     * 检查号码是否被注册
     */
    public function checkmobile()
    {
        $mobile = $_GET['mobile'];
        $res = model('member/member','service')->_valid_mobile($mobile);
        if(!$res) showmessage('该手机号码已经被注册或者绑定!','',0,'','json');
		showmessage('success','',1,'','json');


    }

	//发送验证码
	public function checkansend(){
		$notify_template = $this->load->service('notify/notify_template');
		$template = $notify_template->fetch_by_code('sms');
		if(FALSE === $template || is_null($template['template']['mobile_validate'])) {
			showmessage(lang('cant_use_note','member/language'));
		}
		$this->member_service->vcode_delete(array('mid' => $this->member['id'],'action' =>'resetmobile','dateline'=>array('LT',TIMESTAMP)));
		$member = $_GET;
		$member['mid'] = $this->member['id'];
		$result = $this->load->service('member/member')->post_vcode($member,'resetmobile');
		if($result){
			showmessage(lang('send_msg_success','member/language'),'',1);
		}else{
			showmessage(lang('send_msg_error','member/language'),'',0);
		}
	}

	public function checkansend_email(){
		$result = $this->member_service->resetemail($_GET,$this->member['id']);
		if(!$result){
			showmessage($this->member_service->error);
		}
		showmessage(lang('send_email_success','member/language'),'',1);
	}

	public function avatar() {
		helper('attachment');
		if(checksubmit('dosubmit')) {
			if(empty($_GET['avatar'])) {
				showmessage(lang('head_portrait_empty','member/language'),'',0);
			}
			$avatar = $_GET['avatar'];
			runhook('oss_avatar',$avatar);
			$x = (int) $_GET['x'];
			$y = (int) $_GET['y'];
			$w = (int) $_GET['w'];
			$h = (int) $_GET['h'];
			if(is_file($avatar) && file_exists($avatar)) {
		        $ext = strtolower(pathinfo($avatar, PATHINFO_EXTENSION));
		        $name = basename($avatar, '.'.$ext);
		        $dir = dirname($avatar);
		        if(in_array($ext, array('gif','jpg','jpeg','bmp','png'))) {
		            $name = $name.'_crop_200_200.'.$ext;
		            $file = $dir.'/'.$name;
	                $image = new image($avatar);
	                $image->crop($w, $h, $x, $y, 200, 200);
	                $image->save($file);
		            if(file_exists($file)) {
		            	$avatar = getavatar($this->member['id'], false);
		            	dir::create(dirname($avatar));
		            	@rename($file, $avatar);
		            	showmessage(lang('change_head_portrait_success','member/language'),'',1);
		            } else {
		            	showmessage(lang('edit_head_portrait_error','member/language'),'',0);
		            }
		        } else {
		        	showmessage(lang('illegal_image','member/language'),'',0);
		        }
			} else {
				showmessage(lang('head_portrait_data_exception','member/language'),'',0);
			}
		} else {
			$attachment_init = attachment_init(array('module'=>'member', 'path' => 'member', 'mid' => $this->member['id'],'allow_exts'=>array('bmp','jpg','jpeg','gif','png')));
			$SEO = seo('上传头像 - 会员中心');
			$this->load->librarys('View')->assign('SEO',$SEO)->assign('attachment_init',$attachment_init)->display('account_avatar');
		}
	}
	public function bind_third_account(){
		$status = runhook('third_login_bind');
		if(!$status) showmessage('页面不存在');
		$this->load->librarys('View')->display('third_login_bind');
	}

}