<?php
class member_service extends service {
    protected $result;

    public function _initialize() {
		$this->vcode_table = $this->load->table('vcode');
        $this->model = $this->load->table('member/member');
        $this->open = $this->load->table('member_open');
        $this->group_model = $this->load->table('member/member_group');
	}

    /**
     * 初始化
     * @return [type] [description]
     */
    public function init() {
        $_member = array(
            'id' => 0,
            'username' => '游客',
            'group_id' => 0,
            'email' => '',
            'mobile' => '',
            'money' => 0,
            'integral' => 0,
            'exp' => 0
        );
        $authkey = cookie('member_auth');
        if($authkey) {
	        list($mid, $rand) = explode("\t", authcode($authkey));
	        $_member = $this->model->setid($mid)->address()->group()->output();
        }
        $_member['avatar'] = getavatar($_member['id']);
        runhook('member_init', $_member);
        return $_member;
    }

    /**
     * 注册
     * @param  array  $params 表单信息
     * @return mixed
     */
    public function register($params = array()) {
		if(empty($params)) {
			$this->error = lang('_error_action_');
			return false;
		}
        foreach( $params as $k => $v ) {
        	$method = '_valid_'.$k;
			if($k == 'vcode'){
				if(method_exists($this,$method) && $this->$method($v,$params['mobile']) === false) return false;
			}else{
				if(method_exists($this,$method) && $this->$method($v) === false) return false;
			}
			$params[$k] = trim($v);
        }
        if($params['pwdconfirm'] != $params['password']) {
	        $this->error = lang('two_passwords_differ','member/language');
	        return false;
        }
        $setting = model('admin/setting','service')->get();
        $data = $params;

        $sms_enabled = model('notify')->where(array('code'=>'sms','enabled'=>1))->find();

        $sms_reg = false;
        if($sms_enabled){
            $sqlmap['id'] = 'sms';
            $sqlmap['enabled'] = array('like','%register_validate%');
            $sms_reg = model('notify_template')->where($sqlmap)->find();
        }
        if(in_array('phone',$setting['reg_user_fields']) && $sms_reg){
            $sqlmap = array();
            $sqlmap['mobile'] = $params['mobile'];
            $sqlmap['dateline'] = array('EGT',time()-1800);
            $vcode = $this->load->table('vcode')->where($sqlmap)->order('dateline desc')->getField('vcode');
            if($vcode != $params['vcode']){
                $this->error = lang('captcha_error','member/language');
                return false;
            }else{
                $data['mobilestatus'] = 1;
            }
        }
        $data['username'] = $params['username'];
        $data['email'] = $params['email'] ? $params['email'] : '';
        $data['mobile'] = $params['mobile'] ? $params['mobile'] : '';
		$data['encrypt'] = random(6);
        $data['group_id'] = 1;
        $data['money'] = 0;
        $data['exp'] = 0;
        $data['frozen_money'] = 0;
        runhook('before_register',$data);
        if($data['_callback'] === false){
            $this->error = $params['_message'];
            return false;
        }
		 if(!isset($data['id'])) {
            $data['password'] = md5(md5($params['password']).$data['encrypt']);
            $data['id'] = $this->model->update($data);
            if($data['id'] === false) {
                $this->error = $this->table->getError();
                return false;
            }
        }
		$this->dologin($data['id']);
        runhook('after_register',$data);
		return $data['id'];
    }

    public function login($account, $password) {
		if(empty($account)) {
			$this->error = lang('login_username_empty','member/language');
			return false;
		}
		if(empty($password)) {
			$this->error = lang('login_password_empty','member/language');
			return false;
		}
        $member = array();
        runhook('before_login',$member);
        if(empty($member)) {
            $sqlmap = array();
            if(is_mobile($account)) {
                $sqlmap['mobile|username'] = $account;
                $sqlmap['mobile_status'] = 1;
            } elseif(is_email($account)) {
                $sqlmap['email|username'] = $account;
                $sqlmap['email_status'] = 1;
            } else {
                $sqlmap['username'] = $account;
            }
            $member = $this->model->where($sqlmap)->find();
            if(!$member || md5(md5($password).$member['encrypt']) != $member['password']) {
                $this->error = lang('username_not_exist','member/language');
                return false;
            }
            if($member['islock'] == 1) {
                $this->error = lang('user_ban_login','member/language');
                return false;
            }
        }
		$this->dologin($member['id']);
        runhook('after_login',$member);
		return $member['id'];
    }
    /**
     * 判断并实现会员自动升级
     * @param  int $mid 会员主键
     * @return [bool]
     */
    public function dologin($mid) {
        if((int) $mid < 1){
            $this->error = lang('_param_error_');
            return FALSE;
        }
        $rand = random(6);
		$auth = authcode($mid."\t".$rand, 'ENCODE');
		cookie('member_auth', $auth, 86400);
		$login_info = array(
			'id' => $mid,
			'login_time' => TIMESTAMP,
			'login_ip'	=> get_client_ip(),
            'login_num' => array('exp','login_num+1')
		);
		$this->model->update($login_info);
        return true;
    }
    /**
     * 退出登陆
     */
    public function logout() {
        runhook('after_logout');
	    return cookie('member_auth', null);
    }

    /**
     * 判断并实现会员自动升级
     * @param  int $mid 会员主键
     * @return [bool]
     */
    public function change_group($mid){
        $mid = (int) $mid;
        $exp = (int) $this->model->fetch_by_id($mid,'exp');
        $sqlmap = array();
        $sqlmap['min_points'] = array("ELT", $exp);
        $sqlmap['max_points'] = array("GT", $exp);
        $group_id = $this->group_model->where($sqlmap)->getField('id');
        if($group_id < 1) return FALSE;
        $this->model->where(array('id'=>$mid))->setField('group_id',$group_id);
        runhook('member_change_group',$mid);
        return TRUE;
    }
    /**
     * 查询单个会员信息
     * @param int $id
     * @return mixed
     */
    public function fetch_by_id($id) {
        $r = $this->model->find($id);
        if(!$r) {
            $this->error = '_select_not_exist_';
            return FALSE;
        }
        return $r;
    }
    /**
     * 删除指定会员
     * @param type $id
     */
    public function delete_by_id($id) {
        $ids = (array) $id;
        runhook('member_info_del',$ids);
        foreach($ids AS $id) {
            if($this->model->delete($id)) {
                /* 删除财务流水 */
                $this->load->table('member_log')->where(array("mid" => $id))->delete();
                /* 删除收货地址 */
                $this->load->table('member_address')->where(array("mid" => $id))->delete();
            }
        }
        return TRUE;
    }
	 /**
     * 锁定[解锁]指定会员
     * @param type $id
	 * $type int [是否锁定 1:锁定 0: 解锁]
     */
	 public function togglelock_by_id($id,$type) {
		 $ids = (array) $id;
		 $data = array();
		 $data['islock'] = ((int)$type) > 1 ? 1 : $type;
		 $result = $this->model->where(array('id'=>array('in',$ids)))->save($data);
		 if($result == false){
			 $this->error = $this->model->getError();
			 return FALSE;
		 }
		 return TRUE;
	 }
    /**
     * 处理保证金
     * @param int $mid
     * @param string $money
     * @param isfrozen 操作类别（true:冻结；false:解冻）
     * @return boolean 状态
     */
    public function action_frozen($mid, $money, $isfrozen = true, $msg = '') {
        if($isfrozen === true) {
            $member_money = $this->model->where(array('id' => $mid))->getField('money');
            if(!$member_money || $member_money < $money) {
                $this->error = lang('user_not_sufficient_funds','member/language');
                return false;
            }
            $_result = $this->model->where(array('id' => $mid))->setInc('frozen_money', $money);
            if(!$_result) {
                $this->error = $this->model->getError();
                return false;
            }
            $log_money = '-'.$money;
            $result = $this->change_account($mid, 'money', $log_money, $msg);
            if(!$result) $this->model->where(array('id' => $mid))->setDec('frozen_money', $money);
        } else {
            $result = $this->model->where(array('id' => $mid))->setDec('frozen_money', $money);
            $log_money = $money;
        }
        if($result === false) {
            $this->error = $this->model->getError();
            return false;
        }
        return true;
    }

    /**
     * 变更用户账户
     * @param int $mid
     * @param string $type
     * @param int $num
     * @param boolean $iswritelog
     * @return boolean 状态
     */
    public function change_account($mid, $field = 'money',$num, $msg = '',$iswritelog = TRUE) {
        $fields = array('money', 'exp', 'integral');
        if(!in_array($field, $fields)) {
            $this->error = '_param_error_';
            return FALSE;
        }
        if(strpos($num, '-') === false && strpos($num, '+') === false) $num = '+'.$num;
        if(strpos($num, '-') === false){
             $result = $this->model->where(array('id' => $mid))->setField($field, array("exp", $field.$num));
        }else{
             $value = $this->model->where(array('id'=>$mid))->getField($field);
            if(abs($num) > $value){
                 $result = $this->model->where(array('id' => $mid))->setField($field,0);
            }else{
                $result = $this->model->where(array('id' => $mid))->setInc($field,$num);
            }
        }
        if($result === FALSE) {
            $this->error = '_operation_wrong_';
            return FALSE;
        }
        if($iswritelog === TRUE) {
            $_member = $this->model->setid($mid)->output();
            $log_info = array(
                'mid'      => $mid,
                'value'    => $num,
                'type'     => $field,
                'msg'      => $msg,
                'dateline' => TIMESTAMP,
                'admin_id' => (defined('IN_ADMIN')) ? ADMIN_ID : 0,
                'money_detail' => json_encode(array($field => sprintf('%.2f' ,$_member[$field])))
            );
            $this->load->table('member_log')->update($log_info);
        }
        if($field == 'exp') $this->change_group($mid);
        if($field == 'money'){
            $member = array();
            $member['member'] = $_member;
            $member['change_money'] = $num;
            $member['user_money'] = $_member['money'] > 0 ? $_member['money'] : 0;
            runhook('money_change',$member);
        }
        return TRUE;
    }
	 /**
     * 注册验证
     * @field string 验证字段
	 * @value string 值
     * @return boolean 返回值
     */
	public function valid($name,$value) {
        if(!$name || !$value){
            $this->error = lang('_param_error_');
            return false;
        }
        $method = '_valid_'.$name;
        if(!method_exists($this,$method)) {
            $this->error = lang('_param_error_');
            return false;
        }
        $valid = array('name' => $name, 'value' => $value);
        runhook('before_member'.$method, $valid);
        if($this->$method($value) === false){
            return false;
        }
        if($valid['_callback'] === false) {
            $this->error = $valid['_message'];
            return false;
        }
        runhook('after_member'.$method, $valid);
        return true;
	}

	/* 校验用户名 */
    private function _valid_username($value) {
        if(strlen($value) < 3 || strlen($value) > 15) {
            $this->error = lang('username_length_require','member/language');
            return false;
        }
        $setting = $this->load->service('admin/setting')->get();
        $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($setting['reg_user_censor'] = trim($setting['reg_user_censor'])), '/')).')$/i';
        if($setting['reg_user_censor'] && @preg_match($censorexp, $value)) {
            $this->error = lang('username_disable_keyword','member/language');
            return false;
        }
        /* 检测重名 */
        if($this->model->where(array("username" => $value,))->count() || $this->model->where(array("email" => $value,))->count() || $this->model->where(array("mobile" => $value,))->count()) {
            $this->error = lang('username_exist','member/language');
            return false;
        }
        return true;
    }

    /* 校验密码 */
    private function _valid_password($value) {
        $setting = $this->load->service('admin/setting')->get();
	    $reg_pass_lenght = max(3, (int) $setting['reg_pass_lenght']);
	    $reg_pass_complex = $setting['reg_pass_complex'];
	    if(strlen($value) < $reg_pass_lenght ) {
		    $this->error = '密码至少为 '. $reg_pass_lenght. ' 位字符';
		    return false;
	    }
	    if($reg_pass_complex) {
		    $strongpws = array();
			if(in_array('num',$reg_pass_complex) && !preg_match("/\d/",$value)){
				$strongpws[] = '数字';
			}
			if(in_array('small',$reg_pass_complex) && !preg_match("/[a-z]/",$value)){
				$strongpws[] = '小写字母';
			}
			if(in_array('big',$reg_pass_complex) && !preg_match("/[A-Z]/",$value)){
				$strongpws[] = '大写字母';
			}
			if(in_array('sym',$reg_pass_complex) && !preg_match("/[^a-zA-z0-9]+/",$value)){
				$strongpws[] = '特殊字符 ';
			}
			if($strongpws){
				$this->error = '密码必须包含'.implode(',', $strongpws);
				return false;
			}
	    }
	    return true;
    }
    /**
     * 校验邮箱
     */
    public function _valid_email($value) {
        if(!is_email($value)) {
            $this->error = lang('email_format_error','member/language');
            return false;
        }

        $_map = array();
        $_map['email'] = $value;
	    if($this->model->where($_map)->count()) {
            $this->error = lang('email_format_exist','member/language');
            return false;
        }
        return true;
    }
    /**
     * 校验手机号
     */
    public function _valid_mobile($value) {
	    if(!is_mobile($value)) {
		    $this->error = lang('mobile_format_error','member/language');
		    return false;
	    }
	    $_map = array();
	    $_map['mobile'] = $value;
        if($this->model->where($_map)->count()) {
            $this->error = lang('mobile_format_exist','member/language');
            return false;
        }
        return true;
    }

	public function _valid_vcode($code,$mobile){
        if(empty($code) || !$this->_valid_mobile($mobile)){
			$this->error = lang('captcha_empty','member/language');
            return false;
		}
		$setting = model('admin/setting','service')->get();
        if(in_array('phone',$setting['reg_user_fields'])){
            $sqlmap = array();
            $sqlmap['mobile'] = $mobile;
            $sqlmap['dateline'] = array('EGT',time()-1800);
            $vcode = $this->load->table('vcode')->where($sqlmap)->order('dateline desc')->getField('vcode');
            if($vcode != $code){
                $this->error = lang('captcha_error','member/language');
                return false;
            }
			return true;
        }
		return false;
	}
    public function post_vcode($params,$action = ''){
        $data = array();
        $data['vcode'] = random(4,1);
        $data['mobile'] = $params['mobile'];
        $data['mid'] = $params['mid'] ? $params['mid'] : 0;
        $data['action'] = $action;
        $data['dateline'] = TIMESTAMP;
        $result = $this->load->table('vcode')->update($data);
        if(!$result){
            return false;
        }else{
            if($action == 'register_validate'){
                runhook('register_validate',$data);
            }else{
                runhook('mobile_validate',$data);
            }
            return true;
        }
    }

	//重置邮箱
	public function resetemail($params,$mid){
		$vcode = random(5,1);
		extract($params,EXTR_IF_EXISTS);
        if(!is_email($params['email'])) return false;
		$notify_template = $this->load->service('notify/notify_template');
        $template = $notify_template->fetch_by_code('email');
		if(FALSE === $template || is_null($template['template']['n_email_validate'] ||empty($params['email']))) {
			$this->error = lang('unusable_email_info','member/language');
			return false;
		}
		$data = array();
		$data['vcode'] = $vcode;
		$data['email'] = $params['email'];
        $data['mid'] = $mid;
		runhook('email_validate',$data);
		$this->vcode_table->where(array('mid' => $mid,'action' => 'resetemail','dateline'=>array('LT',TIMESTAMP)))->delete();
		$this->vcode_table->add(array('mid' => $mid,'vcode'=>$vcode,'action'=>'resetemail','dateline'=>time()));
		return true;
	}
	//重置手机
	public function resetmobile($params,$mid){
        $sms_enabled = model('notify')->where(array('code'=>'sms','enabled'=>1))->find();
        $mobile_validate = false;
        if($sms_enabled){
            $sqlmap['id'] = 'sms';
            $sqlmap['enabled'] = array('like','%mobile_validate%');
            $mobile_validate = model('notify_template')->where($sqlmap)->find();
        }
        if(!$this->_valid_mobile($params['mobile'])) return false;
        if($mobile_validate){
            extract($params,EXTR_IF_EXISTS);
            $sqlmap = $data = array();
            $sqlmap['mid'] = $mid;
            $sqlmap['action'] = 'resetmobile';
            $sqlmap['vcode'] = $params['vcode'];
            $sqlmap['dateline'] = array('EGT',time()-1800);
            $_vcode = $this->vcode_table->where($sqlmap)->getField('vcode');
            if ($_vcode !== $params['vcode']){
                $this->error = lang('captcha_error','member/language');
                return false;
            }
        }

		$data['id'] = $mid;
		$data['mobile'] = $params['mobile'];
		$r = $this->load->table('member')->update($data,FALSE);
		if($r === FALSE){
			$this->error = lang('edit_mobile_error','member/language');
			return false;
		}
		return true;
	}

    /**
     * [add_member 增加会员]
     * @param [type] $params [description]
     */
    public function add_member($params){
        $data = $this->model->create($params);
        return $this->model->add($data);
    }

    /**
     * 检查登录账号是否存在或绑定
     * @param string $username 登录账号
     * @return mixed
     */
    public function username_exist($username='')
    {
        $type = '';
        if(is_mobile($username)) $type = 'mobile';
        if(is_email($username)) $type = 'email';
        if(!$type) {
            $this->error = '邮箱或者电话号码格式错误';
            return false;
        }
        $member =  $this->model->where("$type = '$username'")->find();
        if(!$member) {
            $this->error = '邮箱或者电话号码不存在或者未绑定';
            return false;
        }
        return $member;
    }

    /**
     * 发送系统信息
     * @param string $type 信息类型
     * @param array $member 用户信息
     */
    public function send_message($username = '',$member = array())
    {
        //todo 校验email，mobile 发送间隔时间

        $code = random(6,1);
        if(is_mobile($username)) $code_data['mobile'] = $username;
        $code_data['mid'] = $member['id'];
        $code_data['vcode'] = $code;
        $code_data['action'] = 'forget_pwd';
        $code_data['dateline'] = time();
        $this->vcode_table->where(array('mid' => $member['id'],'action' => 'forget_pwd','dateline'=>array('LT',TIMESTAMP)))->delete();
        $this->vcode_table->add($code_data);

        $data = array();
        if(is_email($username)){
            $key = base64_encode(authcode($code, 'ENCODE', $member['encrypt'], 3600 * 5)) ;
            $mobile_validate = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].url('member/public/reset_password',array('mid' => $member['id'],'key' => $key));
            $data['email_validate'] = $mobile_validate;
            $data['email'] = $member['email'];
        }elseif(is_mobile($username)){
            $data['mobile_validate'] = $code;
            $data['mobile'] = $member['mobile'];
        }
        runhook('forget_pwd',$data);
        return true;
    }

    /**
     * 重置密码
     * @param $mid
     * @param $username
     * @param $key
     * @param $pwd
     */
    public function reset_password($mid,$key,$pwd)
    {

        $map['mid'] = $mid;
        $map['action'] = 'forget_pwd';
        $vcode = model('vcode')->where($map)->order('dateline DESC')->limit(1)->find();
        $member =  $this->model->where("id = '$mid'")->find();
        $vkey = authcode(base64_decode($key), 'DECODE', $member['encrypt']) ;
        if($vcode['vcode'] != $vkey){
            $this->error = '非法操作！';
            return false;
        }
        $data['id'] = $mid;
        $data['password']=md5(md5($pwd).$member['encrypt']);

        $this->model->update($data);
        return true;
    }



    /**
     * 验证短信验证码
     * @param $code
     * @param $mobile
     * @return bool
     */
    public function valid_vcode($code,$mobile){
        if(empty($code) || !is_mobile($mobile)){
            $this->error = '验证码不能为空或手机号错误';
            return false;
        }
        $setting = model('admin/setting','service')->get();
        $sqlmap = array();
        $sqlmap['mobile'] = $mobile;
        $sqlmap['dateline'] = array('EGT',time()-1800);
        $vcode = model('vcode')->where($sqlmap)->order('dateline desc')->getField('vcode');
        if($vcode != $code){
            $this->error = '验证码错误';
            return false;
        }
        $member =  $this->model->where("mobile = '$mobile'")->find();
        $mid = model('vcode')->where($sqlmap)->order('dateline desc')->getField('mid');
        $key = base64_encode(authcode($code, 'ENCODE', $member['encrypt'], 3600 * 5));
        return array('params'=>array('mid'=>$mid,'key'=>$key));
    }

    public function get_lists($sqlmap,$page,$limit){
        $members = $this->load->table('member/member')->where($sqlmap)->page($page)->order('id DESC')->limit($limit)->select();
        $lists = array();
        foreach ($members AS $member) {
            $lists[] = array(
                'id' => $member['id'],
                'username' => $member['username'],
                'member_level' => $member['group_name'],
                'money' => money($member['money']),
                'login' => date('Y-m-d H:i:s', $member['register_time']),
                'lock' => $member['islock'] == 1 ? '锁定' : '正常',
                'exp' => $member['exp'],
                'frozen_money' => money($member['frozen_money']),
                'login_time' => date('Y-m-d H:i:s', $member['login_time']),
                'avatar' => $member['avatar'],
                'login_num' => $member['login_num'],
                'email' => $member['email'],
                'mobile' => $member['mobile'],
                'has_address' => $member['has_address']
            );
        }
        return $lists;
    }
    /**
     * [update 更新数据]
     * @param  [type] $params [参数]
     * @return [type]         [description]
     */
    public function update($params ,$bool = true){
        if(empty($params)){
            $this->error = lang('_params_error_');
            return false;
        }
        $result = $this->model->update($params ,$bool);
        if($result === false){
            $this->error = $this->model->getError();
            return false;
        }
        return $result;
    }
    public function vcode_delete($sqlmap){
        return $this->vcode_table->where($sqlmap)->delete();
    }
    /**
     * 条数
     * @param  [arra]   sql条件
     * @return [type]
     */
    public function count($sqlmap = array()){
        $result = $this->model->where($sqlmap)->count();
        if($result === false){
            $this->error = $this->model->getError();
            return false;
        }
        return $result;
    }
    /**
     * @param  string  获取的字段
     * @param  array    sql条件
     * @return [type]
     */
    public function get_vcode_field($field = '', $sqlmap = array()) {
        if(substr_count($field, ',') < 2){
            $result = $this->vcode_table->where($sqlmap)->getfield($field);
        }else{
            $result = $this->vcode_table->where($sqlmap)->field($field)->select();
        }
        if($result===false){
            $this->error = $this->vcode_table->getError();
            return false;
        }
        return $result;
    }
    /**
     * @param  array    sql条件
     * @param  integer  读取的字段
     * @return [type]
     */
    public function find($sqlmap = array(), $field = "") {
        $result = $this->model->where($sqlmap)->field($field)->find();
        if($result===false){
            $this->error = $this->model->getError();
            return false;
        }
        return $result;
    }

    public function user($mid){
        $_member = $this->model->setid($mid)->address()->group()->output();
        $_member['avatar'] = getavatar($_member['id']);
        return $_member;
    }

    public function avatar($sqmap){
        $site_url = model('admin/setting','service')->get('site_url');
        $avatar = $sqmap['avatar'];
        runhook('oss_avatar',$avatar);
        $x = (int) $sqmap['x'];
        $y = (int) $sqmap['y'];
        $w = (int) $sqmap['w'];
        $h = (int) $sqmap['h'];
        $avatar = str_replace($site_url.'/', DOC_ROOT, $avatar);
        if(!is_file($avatar) || !file_exists($avatar)) {
            $this->error = lang('head_portrait_data_exception','member/language');
            return false;
        }

        $ext = strtolower(pathinfo($avatar, PATHINFO_EXTENSION));
        $name = basename($avatar, '.'.$ext);
        $dir = dirname($avatar);

        if(!in_array($ext, array('gif','jpg','jpeg','bmp','png'))){
            $this->error = lang('illegal_image','member/language');
            return false;
        }

        $name = $name.'_crop_200_200.'.$ext;
        $file = $dir.'/'.$name;
        $image = new image($avatar);
        $image->crop($w, $h, $x, $y, 200, 200);
        $image->save($file);

        if(!file_exists($file)) {
            $this->error = lang('edit_head_portrait_error','member/language');
            return false;
        }

        $avatar = getavatar($sqmap['mid'], false);
        $avatar = str_replace($site_url.'/', DOC_ROOT, $avatar);
        dir::create(dirname($avatar));
        @rename($file, $avatar);
        return true;
    }
    //生成公众号二维码
    public function resetweixin(){
    $_setting = model('notify/notify','service')->get_fech_all();
    $config = $_setting['wechat']['configs'];
    $weixin = new we($config);
    $result = $weixin->getQRCode('123');
    if(!$result){
        return false;
    }
    $result = $weixin->	getQRUrl($result['ticket']);
    return $result;
    }
    
    //检测是否已近绑定公众号
    public function weixin_valid($openid){
        $where['openid'] = $openid;
        if($this->open->where($where)->count()){
            $this->error = '此微信已经绑定有相关账号';
			return false;
        }
        return true;

    }
     //绑定公众号验证
    public function valid_openid($username,$password,$openid){
        if(empty($username)) {
			$this->error = lang('login_username_empty','member/language');
			return false;
		}
		if(empty($password)) {
			$this->error = lang('login_password_empty','member/language');
			return false;
		}
        $member = array();
        runhook('before_login',$member);
        if(empty($member)) {
            $sqlmap = array();
            if(is_mobile($username)) {
                $sqlmap['mobile|username'] = $username;
                $sqlmap['mobile_status'] = 1;
            } elseif(is_email($username)) {
                $sqlmap['email|username'] = $username;
                $sqlmap['email_status'] = 1;
            } else {
                $sqlmap['username'] = $username;
            }
            $member = $this->model->where($sqlmap)->find();
           
            if(!$member || md5(md5($password).$member['encrypt']) != $member['password']) {
                $this->error = lang('username_not_exist','member/language');
                return false;
            }
            if($member['islock'] == 1) {
                $this->error = lang('user_ban_login','member/language');
                return false;
            }
            $member_open = $this->open->where(array('mid'=>$member['id']))->find();
            if(false == $member_open){
                $data=array();
                $data['mid']=$member['id'];
                $data['type'] = 'wechat';
                $data['openid'] = $openid;
                $data['dateline'] = time();
                if(!$this->open->add($data)){
                    $this->error = '绑定失败';
                    return false;
                }
            }elseif(!$member_open['openid']){
                $data=array();
                $data['openid'] = $openid;
                $data['dateline'] = time();
                if(!$this->open->where(array('mid'=>$member['id']))->save($data)){
                    $this->error = '绑定失败';
                    return false;
                };
            }else{
                $this->error = '账号已绑定,请勿重复操作';
                return false;
            }
        }
        $this->dologin($member['id']);
        runhook('after_login',$member);
        return $member['id'];

    }
    //自动解除绑定
    public function cancel_weixin($openid){
        $where['openid'] = $openid;
        if(empty($where['openid'])){
            return false;
        }else{
            $data['openid']='';
             return  $this->open->where($where)->save($data);
        }
    }

    public function member_open($mid){
            $result = $this->open->where(array('mid'=>$mid))->find();
            if(!$result){
                return false;
            }
            return $result;
    }
}