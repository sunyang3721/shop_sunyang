<?php 
class member_message_service extends service
{
	public function _initialize(){
		$this->table = $this->load->table('member/member_message');
	}
	/**
	 * [ajax_update 改变状态]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function ajax_update($params) {
		if(count($params) < 1){
			$this->error = lang('choose_empty','member/language');
			return FALSE;
		}
		$data = array();
		$data['status'] = 1;
		foreach($params as $v){
			$data['id'] = (int)$v;
			$result = $this->table->update($data);
		}
		return $result;
	}
	/**
	 * [delete 批量删除]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete($params) {
		$data = array();
		$data['id'] =  implode(',', $params);
		runhook('member_message_delete',$params);
		$result = $this->table->where(array('id'=>array('IN',$data['id'])))->delete();
		if(!$result){
			$this->error = $this->table->getError();
			return FALSE;
		}
		return TRUE;
	}
	/**
     * 会员未读消息
	 *	$params [array] [数组]
     * @return [type] [description]
     */
	public function user_message($mid) {
		$data = array();
		$data['mid'] = (int)$mid;
		$data['status'] = 0;
		$rows = $this->table->where($data)->count();
		return (int)$rows;
	}
	/**
	 * [add 添加]
	 * @param  [array]  添加的数据
	 * @return [type]     [description]
	 */
	public function add($params) {
		if((int)$params['mid'] < 1){
			$this->error = lang('order_member_id_not_null','order/language');
			return FALSE;
		}
		if(!$params['title']){
			$this->error = lang('title_empty','member/language');
			return FALSE;
		}
		if(!$params['message']){
			$this->error = lang('content_empty','member/language');
			return FALSE;
		}
		$data = array();
		if($params['id']) $data['id'] = $$params['id'];
		$data['mid'] =  $params['mid'];
		$data['title'] = $params['title'];
		$data['message'] = $params['message'];
		$data['dateline'] = time();
		runhook('member_message_add',$data);
		$result = $this->table->update($data);
		if($result === FALSE){
			$this->error = $this->table->getError();
			return FALSE;
		}
		return $result;
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function lists($sqlmap = array(), $limit = 20, $page = 1, $order = "") {
		$count = $this->table->where($sqlmap)->count();
		$lists = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		if($count===false || $lists===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return array('lists'=>$lists,'count'=>$count);
	}
}