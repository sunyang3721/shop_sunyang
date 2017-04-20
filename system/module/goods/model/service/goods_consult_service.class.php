<?php
/**
 *		商品咨询数据层
 */

class goods_consult_service extends service {
	public function _initialize() {
		$this->model = $this->load->table('goods/goods_consult');
	}
	/**
	 * [delete 删除咨询]
	 * @$params [array] [数组id]
	 * @return [bool]      [description]
	 */
	public function delete($params){
		runhook('before_consult_delete',$params);
		$data = array();
		$data['id'] = array("IN",$params['id']);
		$result = $this->model->where($data)->delete();
		runhook('after_consult_delete',$params);
		if(!$result){
			$this->error = $this->model->getError();
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * [reply 回复咨询]
	 * @$params [array] [数组]
	 * @return [bool]      [description]
	 */
	 public function reply($params){
		 if((int)$params[id] < 1){
			 $this->error = lang('_param_error_');
			 return FALSE;
		 }
		 $data = array();
		 $data['id'] = $params['id'];
		 $data['reply_content'] = $params['reply_content'];
		 $data['reply_time'] = time();
		 $data['status'] = 1;
		 runhook('before_consult_reply',$data);
		 $result = $this->model->update($data);
		 runhook('after_consult_reply',$data);
		 if(!$result){
			 $this->error = $this->model->getError();
			 return FALSE;
		 }
		 return TRUE;
 	 }
	 /**
	 * [add 添加]
	 * @$params [array] [数组]
	 * @return [bool]      [description]
	 */
	 public function add($params){
		if((int)$params['sku_id'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		 if(empty($params['question'])){
			 $this->error = lang('consult_content_empty','goods/language');
			 return FALSE;
		 }
		$data = array();
		$data['mid'] = $params['mid'] ? $params['mid'] : '';
		$data['username'] = $params['username'] ? $params['username'] : '';
		$data['sku_id'] = $params['sku_id'];
		$data['spu_id'] = $params['spu_id'];
		$data['question'] = htmlspecialchars(remove_xss($params['question']));
		$data['dateline'] = time();
		$data['clientip'] = $_SERVER['REMOTE_ADDR'];
		runhook('before_consult_add',$data);
		$result = $this->model->update($data);
		runhook('after_consult_add',$data);
		if(!$result){
			$this->error = $this->model->getError();
			return FALSE;
		}
		return TRUE;
 	 }
	/**
	 * [add 添加]
	 * @$params [array] [数组]
	 * @return [bool]      [description]
	 */
	 public function see($params){
		if((int)$params['mid'] < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['id'] = $params['id'];
		$data['mid'] = $params['mid'];
		$data['status'] = 1;
		$save['see'] = 1;
		$result = $this->model->where($data)->save($save);
		if(!$result){
			$this->error = $this->model->getError();
			return FALSE;
		}
		return TRUE;
 	 }
 	 /**
 	  * [count 统计]
 	  * @$spu [条件]
 	  * @return [int]
 	  */
 	 public function seecont($spu){
		$data = array();
		$data['spu_id'] = $spu;
		$result = $this->model->where($data)->count();
		return $result;
 	 }
	/**
	 * [user_consult 会员咨询列表 ]
	 * @$params [array] [数组]
	 * @return [type]      [description]
	 */
	public function user_consult($mid,$page,$limit){
		$userinfo = $this->load->table('goods_consult')->where(array('mid' => array('eq',$mid)))->page($page)->limit(10)->order('id DESC')->select();
		foreach($userinfo as $k => $v){
			 $userinfo[$k]['goods_detail'] =  $this->load->table('goods/goods_sku')->detail($v['sku_id'])->output();
			 if($userinfo[$k]['goods_detail'] == null){
				 unset($userinfo[$k]);
			}
		}
		runhook('after_user_consult',$userinfo);
		return $userinfo;
	}
	/**
	 * [get_user_consult 添加]
	 * @$params [array] [数组]
	 * @return [type]      [description]
	 */
	public function get_user_consult($mid){
		$mid = (int)$mid;
		if($mid < 1){
			$this->error = lang('_param_error_');
			return FALSE;
		}
		$data = array();
		$data['mid'] = $mid;
		$data['status'] = 1;
		$data['see'] = 0;
		$count = $this->load->table('goods/goods_consult')->where($data)->count();
		if($count < 1){
			return FALSE;
		}
		return $count;
	}
	/**
	 * 统计咨询
	 */
	public function handle(){
		$data = array();
		$data['status'] = 0;
		$counts = $this->load->table('goods/goods_consult')->where($data)->count();
		return (int)$counts;
	}
	/**
	 * 商品咨询列表
	 */
	public function lists($sqlmap = array(),$options = array()){
		$lists = array();
		$lists['count'] = $this->model->where($sqlmap)->count();
		$this->model->where($sqlmap);
		if(isset($options['limit'])){
			$this->model->limit($options['limit']);
		}
		if($options['page']) {
			$this->model->page($options['page']);
		}
		$lists['lists'] = $this->model->order('id DESC')->select();
		foreach($lists['lists'] as $key => $value){
			$lists['lists'][$key]['_datetime'] = date("Y-m-d H:i:s",$value['dateline']);
			$lists['lists'][$key]['avatar'] = getavatar($value['mid']);
			$lists['lists'][$key]['username'] = $value['mid'] == 0 ?  $value['username'] : cut_str($value['username'], 1, 0).'**'.cut_str($value['username'], 1, -1);
		}
		runhook('after_consult_lists',$lists);
		return $lists;
	}
	/**
	 * 获取商品咨询
	 */
	public function get_consult($mid,$page,$limit){
		if(!$mid){
			$this->error = lang('_valid_access_');
			return false;
		}
		$data = array();
		$userinfo = '';
		$userinfo = $this->load->table('goods_consult')->where(array('mid' => array('eq',$mid)))->page($page)->limit($limit)->order('id DESC')->select();
		foreach($userinfo as $k => $v){
			 $userinfo[$k]['goods_detail'] =  $this->load->table('goods/goods_sku')->detail($v['sku_id'])->output();
			 if($userinfo[$k]['goods_detail'] == null){
				 unset($userinfo[$k]);
			}
		}
		foreach($userinfo as $k => $v){
			foreach($v['goods_detail']['spec'] as $key => $value){
				$data[$k]['goods_spec'] = $value['name'].' : '.$value['value'].'&nbsp;&nbsp;';
			}
			$data[$k]['sku_id'] = $v['sku_id'];
			$data[$k]['_sku_url'] = url('goods/index/detail',array('sku_id' => $v['sku_id']));
			$data[$k]['sku_name'] = $v['goods_detail']['sku_name'];
			$data[$k]['dateline'] = date('Y-m-d H-s-i',$v['dateline']);
			$data[$k]['question'] = $v['question'];
			$data[$k]['reply_content'] = $v['reply_content'];
			$data[$k]['status'] = $v['status'];
			$data[$k]['thumb'] = $v['goods_detail']['thumb'];
		}
		runhook('after_get_consult',$data);
		return $data;
	}
	/**
	 * 发表咨询
	 */
	public function add_consult($params){
		if(!$params['sku_id']){
			$sku_ids = $this->load->service('goods/goods_sku')->get_sku_ids($params['spu_id']);
			$params['sku_id'] = $sku_ids[0];
		}
		return $this->model->update($params);
	}
	/**
	 * 获取咨询列表（万能表格）
	 */
	public function get_lists($sqlmap, $limit = 5, $page = 1, $order = 'id DESC'){
		$result = $this->load->table('goods_consult')->where($sqlmap)->page($page)->limit($limit)->order($order)->select();
		$lists = array();
		foreach($result as $key => $value){
			 $result[$key]['goods_detail'] = $this->load->table('goods_sku')->detail($value['sku_id'],'sku_name,spec,thumb,spu_id')->output();
			 $lists[] = array(
			 	'id' => $value['id'],
			 	'sku_name' => $result[$key]['goods_detail']['sku_name'],
			 	'username' => empty($value['username']) ? '游客' : $value['username'],
			 	'dateline' => $value['dateline'],
			 	'sku_id' => $value['sku_id'],
			 	'thumb' => $result[$key]['goods_detail']['thumb'],
			 	'spec' => $result[$key]['goods_detail']['spec'],
			 	'question' => $value['question'],
			 	'status' => $value['status'],
			 	'reply_content' => $value['reply_content']
			 );
		}
		return $lists;
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
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getField($field = '', $sqlmap = array()) {
		//field是否存在','
		$exist = strpos($field, ',');
		if($exist === false){
			$result = $this->model->where($sqlmap)->getfield($field);
		}else{
			$result = $this->model->where($sqlmap)->field($field)->select();
		}
		if($result === false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
	}

}