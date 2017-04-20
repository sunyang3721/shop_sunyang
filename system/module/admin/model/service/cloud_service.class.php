<?php
class cloud_service extends service {

    protected $_cloud = '';

    public function _initialize() {
        $this->_cloud = $this->load->librarys('cloud');
        $this->setting = $this->load->service('admin/setting');
        $this->config =  unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
    }
    /**
     * 获取绑定用户信息
     */
    public function get_account_info(){
        return $this->_cloud->get_account_info();
    }

    /**
     * 服务器通讯状态
     */
    public function getcloudstatus(){
        return $this->_cloud->getcloudstatus();
    }

    /**
     * 登录远程用户
     * @param type $account
     * @param type $password
     */
    public function getMemberLogin($account, $password) {
        //获取token
        $token = $this->get_access_token($account,$password);
        if($token['code'] != 200){
            $this->error = $token['msg'];
            return FALSE;
        }
        //获取站点列表
        $site_lists = $this->get_site_lists($token['result']['access_token']);
        if($site_lists['code'] != 200){
            $this->error = $site_lists['msg'];
            return FALSE;
        }
        if(empty($site_lists['result']['lists'])){
            $result = $this->bind_site($token['result']['access_token'],$token['result']['expires_in']);
            if(!$result){
                return FALSE;
            }
        }else{
            $sites = $site_lists['result']['lists'];
            foreach ($sites as $key => $site) {
                $sites[$key]['key'] = cut_str($site['key'], 3, 0).'****'.cut_str($site['key'], 3, -3);
                $sites[$key]['_install_time'] = date('Y-m-d h:i:s',$site['install_time']);
                if($this->config['identifier'] == $site['identifier']){
                    $sites[$key]['current'] = 1;
                }
            }
            cache('_cloud',$token['result']);
            $result['site'] = $sites;
        }
        return $result;
    }
    /**
     * [bind_site 绑定站点]
     * @param  [type] $access_token [description]
     * @param  [type] $expires_in   [description]
     * @return [type]               [description]
     */
    private function bind_site($access_token,$expires_in,$identifier = ''){
        $userinfo = $this->get_user_info($access_token);
        if(!$identifier){
            $site_info = $this->post_site_info($access_token);
        }else{
            $site_info = $this->get_site_info($access_token,$identifier);
        }

        if($site_info['code'] == 200 && !empty($site_info['result'])) {
            $_config = array(
                'username'   => $userinfo['result']['username'],
                'sms'        => $userinfo['result']['sms'],
                'coin'       => $userinfo['result']['coin'],
                'token'      => $access_token,
                'expires_in' => $expires_in,
                'identifier' => $site_info['result']['identifier'],
                'key'        => $site_info['result']['key'],
                'domain'     => $site_info['result']['domain'] ? $site_info['result']['domain'] : (is_ssl() ? 'https://' : 'http://').get_url(),
                'authorize'  => (int) $site_info['result']['authorize_status'],
                'authorize_endtime'     => $site_info['result']['authorize_endtime']
            );
            $config_text['__cloud__'] = authcode(serialize($_config),'ENCODE');
            $config = $this->load->librarys('hd_config');
            $r = $config->file('cloud')->note('云平台文件')->space(8)->to_require_one($config_text,null,1);
            if($r) {
                runhook('update_cache');
                return true;
            }
        }else{
            return FALSE;
        }
    }
    /**
     * [get_access_token 获取token]
     * @return [type] [description]
     */
    private function get_access_token($account = '',$password = ''){
        if(!$account){
            $this->error = '帐号不能为空';
            return false;
        }
        if(!$password){
            $this->error = '密码不能为空';
            return false;
        }
        $data = array();
        $data['account'] = $account;
        $data['password'] = $password;
        $result = $this->_cloud->type('get')->data($data)->api('member/account.access_token');
        return $result;
    }
    /**
     * [get_site_lists 获取站点列表]
     * @param  [type]  $token    [description]
     * @param  integer $pagesize [description]
     * @return [type]            [description]
     */
    public function get_site_lists($access_token,$page = 1,$pagesize = 200){
        if(!$access_token){
            $this->error = 'token不能为空,';
            return false;
        }
        $data = array();
        $data['access_token'] = $access_token;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        $info = $this->_cloud->type('get')->data($data)->api('site/get_site_list');
        return $info;
    }
    /**
     * [get_user_info 获取用户信息]
     * @param  [type] $access_token [description]
     * @return [type]               [description]
     */
    private function get_user_info($access_token = ''){
        if(!$access_token){
            $this->error = 'token不能为空';
            return false;
        }
        $data = array();
        $data['access_token'] = $access_token;
        $info = $this->_cloud->type('get')->data($data)->api('member/get_user_info');
        return $info;
    }
    /**
     * [post_site_info 绑定站点]
     * @param  [type] $access_token [description]
     * @return [type]               [description]
     */
    private function post_site_info($access_token = ''){
        if(!$access_token){
            $this->error = 'token不能为空';
            return false;
        }
        $setting = model('admin/setting','service')->get();
        $data = array();
        $data['access_token'] = $access_token;
        $data['domain'] = (is_ssl() ? 'https://' : 'http://').get_url();
        $data['site_name'] = $setting['site_name'];
        $data['version'] = HD_VERSION;
        $data['branch'] = HD_BRANCH;
        $data['server_ip'] = get_client_ip();
        $site_info = $this->_cloud->type('post')->data($data)->api('site/post_site_info');
        if($site_info['code'] == 200){
            return $site_info;
        }else{
            $this->code = $site_info['code'];
            $this->error = $site_info['msg'];
            return FALSE;
        }
    }
    /**
     * [get_site_info 获取站点信息]
     * @param  [type] $access_token [description]
     * @param  [type] $identifier   [description]
     * @return [type]               [description]
     */
    private function get_site_info($access_token = '',$identifier = ''){
        if(!$access_token){
            $this->error = 'token不能为空';
            return false;
        }
        if(!$identifier){
            $this->error = '站点标识不能为空';
            return false;
        }
        $data = array();
        $data['access_token'] = $access_token;
        $data['identifier'] = $identifier;
        $site_info = $this->_cloud->type('get')->data($data)->api('site/get_site_info');
        if($site_info['code'] == 200){
            return $site_info;
        }else{
            $this->code = $site_info['code'];
            $this->error = $site_info['msg'];
            return FALSE;
        }
    }

    /**
     * 实时更新站点用户信息
     * @param type $username    [平台用户名]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function update_site_userinfo() {
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['domain'] = (is_ssl() ? 'https://' : 'http://').get_url();
        $data['branch'] = HD_BRANCH;
        $data['version']    = HD_VERSION;
        $data['server_ip'] = get_client_ip();
        $_result = $this->_cloud->type('post')->data($data)->api('site/put_site_info');
        if($_result['code'] == 200 && !empty($_result['result'])) {
            $this->config['authorize']  = (int) $_result['result']['authorize_status'];
            $config_text['__cloud__'] = authcode(serialize($this->config),'ENCODE');
            $config = $this->load->librarys('hd_config');
            $r = $config->file('cloud')->note('云平台文件')->space(8)->to_require_one($config_text);
            if($r) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取短信模版
     * @param type $tpl_type    [模版标识]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function getsmstpl() {
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['version'] = '2.0';
        return $this->_cloud->type('get')->data($data)->api('sms/template.groups');
    }
    /**
     * [getsmsnum 获取短信条数]
     * @return [type] [description]
     */
    public function getsmsnum(){
        $data = array();
        $data['access_token'] = $this->config['token'];
        return $this->_cloud->type('get')->data($data)->api('sms/balance');
    }
    /**
     * 发送短信
     * @param type $tpl_id      [模版ID]
     * @param type $mobile      [手机号码]
     * @param type $sms_sign    [短信签名]
     * @param type $tpl_vars    [模版变量(array)]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function send_sms($params) {
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['tpl_id']  = $params['tpl_id'];
        $data['sms_sign'] = $params['sms_sign'];
        $data['tpl_vars'] = $params['tpl_vars'];
        $data['mobile']  = $params['mobile'];
        return $this->_cloud->type('post')->data($data)->api('sms/send');
    }



    //获取最新版本
    public function api_product_version(){
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['version']      = HD_VERSION;
        $data['branch']       = HD_BRANCH;
        $data['allow_type']   = 'domain';
        return $this->_cloud->type('get')->data($data)->api('product/version');
    }

    //获取指定版本文件流
    public function api_product_downpack($branch=HD_BRANCH){
        $data = array();
        $data['access_token'] = $this->config['token'];
        $data['identifier'] = $this->config['identifier'];
        $data['version']      = HD_VERSION;
        $data['branch']      = $branch;
        $data['allow_type']   = 'domain';
        return $this->_cloud->type('get')->data($data)->api('product/downpack');
    }

    //获取最新通知
    public function api_product_notify($version = HD_VERSION,$branch=HD_BRANCH){
        $data['version']      = $version;
        $data['branch']      = $branch;
        $result =  $this->_cloud->data($data)->api('api.member.notify');
        if($result['code'] == 200 && !empty($result['result'])) {
            cache('product_notify',$result['result']);
            return true;
        }
        cache('product_notify',null);
        return false;
    }
    /**
     * [bind_site 绑定站点]
     * @param  [type] $identifier [description]
     * @return [type]             [description]
     */
    public function site_bind($identifier = ''){
        $token = cache('_cloud');
        $access_token = $token['access_token'];
        $expires_in = $token['expires_in'];
        $result = $this->bind_site($access_token,$expires_in,$identifier);
        cache('_cloud',null);

        //绑定成功返回 获取绑定用户信息
        $this->_cloud->__construct();
        $cloud = $this->_cloud->get_account_info();
        $cloud['site_isclosed'] = (int)$this->setting->get('site_isclosed');//获取是否关闭站点
        $cloud_status = $this->_cloud->getcloudstatus(); // 服务器通讯状态
        $cloud['cloud_status'] = $cloud_status ? $cloud_status : false ;
        return $cloud;
    }
}