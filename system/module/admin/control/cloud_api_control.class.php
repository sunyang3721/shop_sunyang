<?php
class cloud_api_control extends control {

	protected $_params = array();
	protected $_result = array();

	public function __construct() {
		parent::_initialize();
		unset($_GET['page']);
		$this->_params = $_GET;
		$this->_result = array('code' => -99999,'msg'=> '接口异常','result' =>  false);
		$this->app_service = $this->load->service('admin/app');
	}

	public function index() {
		list($server, $module, $function) = explode(".", $this->_params['method']);
		if (!$function) {
			$this->_result['code'] = -99998;
			$this->_result['msg']    = '接口请求方法有误';
			$this->_result['result'] = false;
		} else {
			$this->_result['result'] = $this->app_service->$function($this->_params);
			$this->_result['code'] = $this->app_service->code;
			$this->_result['msg'] = $this->app_service->error;
		}
		/* 根据请求返回数据 */
		echo json_encode($this->_result);
	}
}