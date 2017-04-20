<?php
class cloud {

    //错误信息
    private $error = '出现未知错误 Cloud ！';
    //需要发送的数据
    private $data = array();
    //接口
    private $api = NULL;
	//配置文件
    private $config = NULL;
	

    //服务器地址
    const server_url = 'http://www.xxxx.com/api/v2/';
	
	public function __construct() {
        $this->config =  unserialize(authcode(config('__cloud__','cloud'),'DECODE'));
    }

	/**
	 * 获取绑定的用户信息
	 */
	public function get_account_info(){
		return $this->config?$this->config:FALSE;
	}
    /**
     * 连接云平台系统
     */
    public function getcloudstatus(){
		$url = preg_replace('/(.*)\/api.php/', "$1" ,self::server_url);
		$array = get_headers($url,1);
		if(preg_match('/200/',$array[0])){
			return true;
		}else{
			return false;
		}
	}

    /**
     * 获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
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
        $params['format'] = 'json';
        $params['timestamp'] = TIMESTAMP;
        $params = array_merge($params,$this->data);
        $params = array_filter($params);
        if($this->api == self::server_url.'product/downpack'){
            $result = array();
            $result['url'] = ''.$this->api.'';
            $result['params'] = ''.http_build_query($params).'';
            return $result;
        }
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
	