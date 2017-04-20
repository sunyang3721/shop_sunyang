<?php
class api_control extends control {
	protected $_result = array();

	public function __construct() {
		parent::_initialize();
		unset($_GET['page']);
		$this->_params = $_GET;
		$this->_result = array('code' => -99999,'msg'=> '接口异常','result' =>  false);
		$this->service = $this->load->service('xiaoneng/xiaoneng');
	}

	public function index() {
		$this->_result = $this->service->sku_format($this->_params['id']);
		/* 根据请求返回数据 */
		echo json_encode($this->_result);
	}
}