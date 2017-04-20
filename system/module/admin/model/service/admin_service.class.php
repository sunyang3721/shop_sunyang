<?php
class admin_service extends service {
    public function _initialize() {
		$this->model = model('admin_user');
    }

    public function init() {
    	$_admin = array(
    		'id' => 0,
    		'group_id' => 0,
    		'username'	=> '',
    		'avatar'	=> __ROOT__.'statics/images/head.jpg',
    		'rules'		=> '',
    		'formhash'	=> random(6)
		);        
        $authkey = session('authkey');
        if($authkey) {
            list($admin_id, $authkey) = explode("\t", authcode($authkey, 'DECODE'));			
            $_admin = model('admin/admin_user')->find($admin_id);
			if($_admin) {
				$_admin['avatar'] = $this->getthumb($_admin['id']);
			}
			$_admin['rules'] = model('admin/admin_group')->where(array('id' => $_admin['group_id']))->getField('rules');
			$_admin['formhash'] = $authkey;
        }
        runhook('admin_init',$_admin);
        return $_admin;
    }
	
	/* 权限验证 */
	public function auth($rules) {
		$rules = explode(",", $rules);		
		$_map = array();
		$_map['m'] = MODULE_NAME;
		$_map['c'] = CONTROL_NAME;
		$_map['a'] = METHOD_NAME;
		$rule_id = model('node')->where($_map)->getField('id');
		if($rule_id && !in_array($rule_id, $rules) && !defined('AUTH_IGNORE')) {
			return false;
		}
		return true;
	}

    /**
     * 登录
     * @param string $username
     * @param string $password
     */
    public function login($username, $password) {
       if(empty($username)) {
            $this->error = lang('username_not_exist','admin/language');
            return FALSE;
        }
        if(empty($password)) {
            $this->error = lang('password_not_exist','admin/language');
            return FALSE;
        }
        $admin_user = $this->model->fetch_by_username($username);
        if(!$admin_user) {
            $this->error = lang('admin_user_not_exist','admin/language');
            return FALSE;
        }
        if($admin_user['password'] !== $this->_create_password($password, $admin_user['encrypt'])) {
            $this->error = lang('password_checked_error','admin/language');
            return FALSE;
        }
        runhook('admin_login',$admin_user);
        $this->admin_user = $admin_user;
        return $this->_dologin($admin_user['id']);
    }
	/**
     * 生成登陆密码
     * @param string $pwd 原始密码
     * @param string $encrypt 混淆字符
     * @return string
     */
    public function _create_password($pwd, $encrypt = '') {
        if(empty($encrypt)) $encrypt = random (6);
        return md5($pwd.$encrypt);
    }
    
    
    private function _dologin($id) {		
        $auth = authcode($id."\t".  random(6), 'ENCODE');
		$this->model->where(array('id'=>$id))->save(array('last_login_time'=>time(),'login_num'=>array('exp','`login_num`+1')));
        session('authkey', $auth);
        return TRUE;
    }
    /**
     * 退出登录
     * @return boolean
     */
    public function logout() {
        session('authkey', NULL);
        return TRUE;
    }
	/**
     * 修改管理员资料
	 * @params array 用户资料
	 * @files array 头像
     * @return bool
     */
	public function update($params,$files){
		if(empty($params['newpassword'])) return true;
		if(!$this->ajax_password($params['oldpassword'])) return false;
		if($params['newpassword'] !== $params['pwpassword']){
			$this->error = lang('two_passwords_differ','member/language');
			return false;
		}
		$data = array();
		$data['id'] = ADMIN_ID;
		$data['encrypt'] = random(10);
		$data['password'] = create_password($params['newpassword'], $data['encrypt']);
		$data['username'] = $this->model->where(array('id'=>ADMIN_ID))->getField('username');
		$result = $this->model->update($data);
		if($result === FALSE){
			$this->error = $this->model->getError();
			return false;
		}
		if(!$this->avatar($files)) return false;
		return true;
	}
	/**
     * 修改头像
	 * @file array 图片信息
     * @return result
     */
	public function avatar($file){
		if(empty($file['portrait']['name'])) return true;
		$ext = strtolower(pathinfo($file['portrait']['name'], PATHINFO_EXTENSION));
		$portrait = './uploadfile/avatar/'.ADMIN_ID.'.'.$ext;
		rename($file['portrait']['tmp_name'],$portrait);
		if(is_file($portrait) && file_exists($portrait)){
			$ext = strtolower(pathinfo($portrait, PATHINFO_EXTENSION));
			$name = basename($portrait, '.'.$ext);
		    $dir = dirname($portrait);
			if(in_array($ext, array('gif','jpg','jpeg','bmp','png'))){
				$name = $name.'_admin_44_44.'.$ext;
				$file = $dir.'/'.$name;
				$image = $this->load->librarys('image',$portrait);
	            $image->thumb(44,44);
				$image->save($file);
				if(file_exists($file)) {
					$thumb = getavatar(ADMIN_ID,FALSE);
					$thumb = dirname($thumb)."/".ADMIN_ID."_admin.".$ext;
					dir::create(dirname($thumb));
					@rename($file, $thumb);
					@unlink($portrait);
					return true;
		        } else { 
					$this->error = lang('head_portrait_save_error','admin/language');
					return false;
		        }
			} else {
				$this->error = lang('illegal_image_upload','admin/language');
				return false;
			}
		} else {
			$this->error = lang('head_protrait_data_error','admin/language');
			return false;
		}
	}
	/**
     * 获取头像
     * @return path
     */
	 public function getthumb($id){
		 $thumb = getavatar($id,FALSE);
		 $ext = strtolower(pathinfo($thumb, PATHINFO_EXTENSION));
		 $dir = dirname($thumb);
		 $file = $dir.'/'.$id.'_admin.'.$ext;
		 if(file_exists($file)){
			 return $file;
		 }
		 return false;
	 }
    /**
     * ajax验证密码
     * @param string $password
     */
    public function ajax_password($password) {
        if(!$password){
			$this->error = lang('original_password_empty','admin/language');
			return false;
		}
		$admin_user = $this->model->fetch_by_id(ADMIN_ID);
		if($admin_user['password'] !==  $this->_create_password($password, $admin_user['encrypt'])){
			$this->error = lang('original_password_error','admin/language');
			return false;
		}
		return true;
    }
}