<?php
class xiaoneng {

    //错误信息
    private $error = '出现未知错误！';
    //需要发送的数据
    private $data = array();
    //接口
    private $api = NULL;
	//配置文件
    private $config = NULL;


    //服务器地址
    const server_url = 'http://www.kuwuya.com/api/v2/';

	public function __construct() {
        $this->config =  unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
        if(empty($this->config)){
            return array('code' => -10001, 'msg' => '请先绑定云平台！');
        }
    }

    /**
     * 需要发送的数据
     */
    public function data($data) {
        $this->data = $data;
        return $this;
    }
    /**
     * [api 请求接口]
     * @param  [type] $api [description]
     * @return [type]      [description]
     */
    public function api($api) {
        $this->api = self::server_url.$api;
        return $this->run($data);
    }
    /**
     * [type 请求类型]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function type($type){
        $this->type = $type;
        return $this;
    }
    /**
     * [run 执行接口]
     * @return [type] [description]
     */
    private function run(){
        $params = array();
        $params['format'] = 'json';
        $params['timestamp'] = TIMESTAMP;
        $params['unique_key'] = $this->config['identifier'];
        $params['access_token'] = $this->config['token'];
        $params = array_merge($params,$this->data);
        $params = array_filter($params);
         if($this->type == 'get'){
            $result = http::getRequest($this->api, $params);
        }elseif ($this->type == 'post') {
            $result = http::postRequest($this->api, $params);
        }
        return self::_response($result);
    }
	/**
     * 组装数据返回格式
     */
    private function _response($result) {
        if(!$result) {
            return array('code' => -10000, 'msg' => $this->error?$this->error:'接口网络异常，请稍后。');
        } else {
            return json_decode($result, TRUE);
        }
    }
}
