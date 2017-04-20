<?php
class member_table extends table
{
	protected $_validate = array(
		//array('username', 'require', '用户名不能为空'),
		//array('password', 'require', '登陆密码不能为空'),
		//array('pwdconfirm', 'require', '确认密码不能为空'),
		//array('mobile', 'mobile', '手机号格式错误'),
		//array('email', 'email', '电子邮箱格式错误'),
	);

	protected $_auto = array(
		//array(填充字段,填充内容,[填充条件,附加规则])
		array('register_time', TIMESTAMP, 1, 'string'),
		array('register_ip', 'get_client_ip', 1, 'function'),
	);

	protected $_where = array();
	protected $_result = array();

	public function fetch_by_id($id, $field = null) {
		if($id < 1) {
			$this->error = lang('_param_error_');
			return false;
		}
		$rs = $this->find($id);
		return (!is_null($field)) ? $rs[$field] : $rs;
	}

	public function fetch_by_username($username) {
		return $this->where(array('username' => $username))->find();
	}

	public function _after_select($result, $options) {
		$members = array();
        foreach($result AS $r) {
            $r['avatar'] = getavatar($r['id']);
            $r['group_name'] = $this->load->table('member/member_group')->fetch_by_id($r['group_id'], 'name');
            $r['has_address'] = $this->load->table('member/member_address')->where(array('mid'=>$r['id']))->getfield('id');
            $members[$r['id']] = $r;
        }
        return $members;
	}


	public function setid($id) {
		$this->_where['mid'] = $id;
		$this->_result = $this->fetch_by_id($id);
		return $this;
	}

	public function address() {
		$this->_result['_address'] = $this->load->table('member/member_address')->fetch_all_by_mid($this->_result['id'], 'isdefault DESC');
		return $this;
	}

	public function group() {
		$this->_result['_group'] = $this->load->table('member/member_group')->fetch_by_id($this->_result['group_id']);
		return $this;
	}

	public function output() {
		return $this->_result;
	}
}