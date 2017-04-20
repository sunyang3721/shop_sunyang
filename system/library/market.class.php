<?php
class market {
    public function __construct() {
        $this->api = 'market.xxxx.com/api.php';
        $this->cloud = unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
        $this->data['timestamp'] = TIMESTAMP;
        $this->data['token'] = $this->cloud['token'];
        $this->data['site_id'] = $this->cloud['identifier'];
    }
    /**
     * [check_sign description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function check_sign($params){
        if(empty($params['sign']) || empty($params['site_id'])){
            $this->code = -20001;
            $this->error = '签名或站点标识不能为空';
            return false;
        }

        if(get_sign($params,$this->cloud['key']) != $params['sign']){
            $this->code = -20002;
            $this->error = '通信密钥或站点标识错误';
            return false;
        }
        return true;
    }
    /**
     * [_notify 操作回调]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function _notify($branch_id = 0,$type = '',$version = ''){
        $this->data['method'] = 'shop.application.status';
        $this->data['branch_id'] = $branch_id;
        $this->data['type'] = $type; 
        $this->data['version'] = $version;
        $info = http::getRequest($this->api,$this->data);
        return TRUE;
    }
    /**
     * [get_plugin ]
     * @param  [type] $branch_id      [description]
     * @return [type]              [description]
     */
    public function get_plugin($branch_id = 0){
        $this->data['method'] = 'shop.application.update_download';
        $this->data['branch_id'] = $branch_id;
        return $this->data;
    }
    /**
     * [get_branch 获取插件版本]
     * @return [type] [description]
     */
    public function get_branch($branch_ids = array()){
        $this->data['method'] = 'shop.application.branch';
        $this->data['ishistory'] = 1;
        $this->data['branch_ids'] = implode(',',$branch_ids);
        $info = json_decode(http::getRequest($this->api,$this->data),TRUE);
        return $info;
    }
    /**
     * [get_branch_upgrade 获取插件更新]
     * @param  array  $branch_ids [description]
     * @return [type]             [description]
     */
    public function get_branch_upgrade($branch_ids = array()){
        $this->data['method'] = 'shop.application.upgrade';
        $this->data['branch_ids'] = $branch_ids;
        $info = json_decode(http::getRequest($this->api,$this->data),TRUE);
        return $info;
    }
    /**
     * [get_branch_auth 获取列表]
     * @return [type] [description]
     */
    public function get_branch_auth($type = '',$limit = 1000){
        $this->data['method'] = 'shop.application.auths';
        $this->data['type'] = $type;
        $this->data['limit'] = $limit;
        $lists = json_decode(http::getRequest($this->api,$this->data),TRUE);
        return $lists['result'];
    }
    /**
     * [synchro_status 同步列表]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function synchro_status($params){
        $this->data['method'] = 'shop.application.synchro_status';
        $this->data['data'] = $params;
        $info = json_decode(http::getRequest($this->api,$this->data),TRUE);
        return $info;
    }
}
	