<?php
class attachment_service extends service {
	protected $_config = array();

	public function _initialize() {
		$this->entrydir = 'system/module/attachment/library/driver/';
		$this->upload_driver = $this->load->table('upload_driver');
		$this->table = $this->load->table('attachment/attachment');
	}

	public function setConfig($code = '') {
        $_config = unserialize(authcode($code, 'DECODE'));
		$this->_config = $_config;
		return $this;
	}

    /**
     * 附件上传接口
     * @param 变量名 $field
     * @param 密钥 $code
     * @return mixed
     */
	public function upload($field, $filed = null, $iswrite = TRUE) {
		if(empty($field)) {
			$this->error = lang('file_upload_empty','attachment/language');
			return FALSE;
		}
		if($this->_config['mid'] < 1) {
			$this->error = lang('no_promission_upload','attachment/language');
			return FALSE;
		}
		$attach_type = $this->load->service('admin/setting')->get('attach_type');
		$driver = $attach_type ? $attach_type : 'local';
		$upload = new upload($this->_config, $driver);
		$result = $upload->upload($field);
		if($result === FALSE) {
			$this->error = $upload->getError();
			return FALSE;
		}
		$this->file = $this->write($result, $iswrite);
		if(is_null($filed)) return $this->file['url'];
		return $this;
	}

	/**
	 * 替换上传
	 */
	public function replace($field, $aid = 0) {
		if(empty($field)) {
			$this->error = lang('file_upload_empty','attachment/language');
			return FALSE;
		}
		if($this->_config['mid'] < 1) {
			$this->error = lang('no_promission_upload','attachment/language');
			return FALSE;
		}
		$attach_type = $this->load->service('admin/setting')->get('attach_type');
		$driver = $attach_type ? $attach_type : 'local';
		$upload = new upload($this->_config, $driver);
		$result = $upload->upload($field);
		if($result === FALSE) {
			$this->error = $upload->getError();
			return FALSE;
		}
		$this->file = $this->write($result, $iswrite);
		if($this->file) {
			$this->file['aid'] = $aid;
			unset($this->file['module']);
			$this->load->table('attachment/attachment')->update($this->file);
		}
		return $this->file;
	}

	/**
     * 远程附件本地化
     * @param 变量名 $field
     * @param 密钥 $code
     * @return mixed
     */
	public function remote($file) {
		if(empty($file)) {
			$this->error = lang('file_upload_empty','attachment/language');
			return FALSE;
		}
		if($this->_config['mid'] < 1) {
			$this->error = lang('no_promission_upload','attachment/language');
			return FALSE;
		}
		$attach_type = $this->load->service('admin/setting')->get('attach_type');
		$driver = $attach_type ? $attach_type : 'local';
		$upload = new upload($this->_config, $driver);
		$result = $upload->remote($file);
		if($result === FALSE) {
			$this->error = $upload->getError();
			return FALSE;
		} else {
			return $this->write($result);
		}
	}

	public function remove_thumb($filepath){
		if(empty($filepath)) {
			$this->error = lang('catalog_empty','attachment/language');
			return FALSE;
		}
		if($this->_config['mid'] < 1) {
			$this->error = lang('no_promission_upload','attachment/language');
			return FALSE;
		}
		$attach_type = $this->load->service('admin/setting')->get('attach_type');
		$driver = $attach_type ? $attach_type : 'local';
		$upload = new upload($this->_config, $driver);
		$result = $upload->remove_thumb($filepath);
		if($result === FALSE) {
			$this->error = $upload->getError();
			return FALSE;
		}
		return true;
	}

	public function output($field = null) {
		return (is_null($field)) ? $this->file : $this->file[$field];
	}

    /**
     * 传回调写入
     * @param array $files 文件信息
     * @return mixed
     */
	public function write($file = array(), $iswrite = true) {
		if(empty($file)) {
			$this->error = lang('_param_error_');
			return FALSE;
		}
		if(!isset($file['aid'])) {
			$data = array(
				'module'   => $this->_config['module'] ? $this->_config['module'] : MODULE_NAME,
				'catid'    => 0,
				'mid'      => (int) $this->_config['mid'],
				'name'     => $file['name'],
				'filename' => $file['savename'],
				'filepath' => $file['savepath'],
				'filesize' => $file['size'],
				'fileext'  => $file['ext'],
				'isimage'  => (int) $file['isimage'],
				'filetype' => $file['type'],
				'md5'      => $file['md5'],
				'sha1'     => $file['sha1'],
				'width'    => (int) $file['width'],
				'height'   => (int) $file['height'],
				'url'      => $file['url'],
			);
            if(!defined('IN_ADMIN')) {
                $data['issystem'] = 1;
                $data['mid'] = ADMIN_ID;
            }
            if($iswrite === true) $this->load->table('attachment/attachment')->update($data);
			return $data;
		}
		return $file;
	}

    /**
     * 变更附件使用状态
     * @param string $add_content 新内容
     * @param string $del_content 旧内容
     * @return boolean
     */
	public function attachment($add_content, $del_content = '', $ishtml = true) {
		if($ishtml === true){
			$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
	        $add_pics = $del_pics = array();
	        if($add_content) {
	            preg_match_all($pattern, $add_content, $add_match);
	            $add_pics = (array) $add_match[1];
	        }
	        if($del_content) {
	            preg_match_all($pattern, $del_content, $del_match);
	            $del_pics = (array) $del_match[1];
	        }
		}else{
			$add_pics = (array)$add_content;
			$del_pics = (array)$del_content;
		}

		$add_attachment = array_diff($add_pics, $del_pics);
		$del_attachment = array_diff($del_pics, $add_pics);
		/* 处理新增 */
		if($add_attachment) $this->load->table('attachment')->where(array('url' => array("IN", $add_attachment)))->setInc('use_nums', 1);
		/* 处理删除 */
		if($del_attachment) $this->load->table('attachment')->where(array('url' => array("IN",$del_attachment)))->setDec('use_nums');
		return true;
	}

	/* 删除图片 */
	public function delete($aid) {
		$r = $this->table->find($aid);
		if(!$r) return false;
		if($r['url'] && file_exists($r['url'])) {
			$ext = fileext($r['url']);
			$name = trim(str_replace($ext, '', basename($r['url'])), '.');
			$dir = dirname($r['url'].'/');
			$files = glob($dir.'/'.$name.'*'.$ext);
			foreach ($files as $file) {
				@unlink($file);
			}
		}
		$this->table->delete($aid);
		return true;
	}

	public function fetch_attachment() {
		$folders = glob($this->entrydir.'*');
        foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $notifys[$xmldata['code']] = $xmldata;
            }
        }
        $notifys = array_merge_multi($notifys, $this->get_attachemnt());
		return $notifys;
	}

	public function get_attachemnt() {
		$result = array();
		$notifys = $this->upload_driver->getField('code,config', TRUE);
		foreach ($notifys as $key => $value) {
			$result[$key]['configs'] = json_decode($value, TRUE);
		}
		return $result;
	}

	/* 根据标识 */
	public function fetch_by_code($code) {
		if(empty($code)) {
			$this->error = lang('_param_error_');
			return false;
		}
		if(!is_dir($this->entrydir.$code) || !file_exists($this->entrydir.$code)) {
			$this->error = lang('no_found_config_file','notify/language');
			return false;
		}
		$config = $this->entrydir.$code.'/config.xml';
        $importtxt = @implode('', file($config));
        $xmldata = xml2array($importtxt);
        return $xmldata;
	}

	public function config($vars, $code) {
		$notify = $this->fetch_by_code($code);
		if($notify === false) {
			return false;
		}
		$notify['config'] = unit::json_encode($vars);
		if($this->upload_driver->find($code)) {
			$rs = $this->upload_driver->update($notify);
		}else{
			$rs = $this->upload_driver->add($notify);
		}
		if($rs === false) {
			$this->error = lang('config_operate_error','notify/language');
			return false;
		}
		$this->get_fech_enable_code();
		return true;
	}

	//根据code获取启用的通知方式
	public function get_fech_enable_code($code) {
		$result = $this->get();
		return $result[$code];
	}
	/**
	 * 获取附件配置缓存
	 */
	public function get($key = NULL){
		$result = array();
		$result = $this->upload_driver->cache('attachment_enable',3600)->getField('code,name',true);
		return is_string($key) ? $result[$key] : $result;
	}

	//获取附件上传
	public function get_attach_name(){
		return $this->get();
	}

	/* 图片统计 */
	public function all_img() {
		$pays_count = array();
		$i = 0;
		$modules = model('admin/app','service')->get_module();
		foreach ($modules as $k => $code) {
			$pays_count[$i]['code'] = $k;
			$pays_count[$i]['name'] = $modules[$k];
			$pays_count[$i]['value'] = (int) $this->table->where(array('module' => $k))->count();
			$i += 1;
		}
		$this->result['all_img'] = $pays_count;
		$filesize = (int) $this->table->sum("filesize");
		$this->result['filesize'] = sizecount($filesize);
		$this->result['unused_img'] = $this->table->where(array('use_nums'=>0))->count();
		$this->result['used_img'] = $this->table->where(array('use_nums'=>array('gt',0)))->count();
		$this->result['img_total'] = $this->table->count();
		return $this;
	}

	/**
     * 输出统计结果
     * @param  string $fun_name 要统计的方法名（多个用 ，分割），默认统计所有结果
     * @return [result]
     */
    public function attach_output($fun_name = '') {
    	$this->result = array();
        if (empty($fun_name)) {
            $this->all_img();
        } else {
        	$fun_names = explode(',', $fun_name);
        	foreach ($fun_names as $name) {
        		if (method_exists($this,$name)) {
        			$this->$name();
        		}
        	}
        }
        return $this->result;
    }

    public function get_lists() {
		$spaces = array('goods' => '商品图库','article' => '文章图库','member' => '会员图库','common' => '其它图库');
		$attachments = array();
		foreach ($spaces as $key => $value) {
			$v = array();
			$v['datetime'] = (int) $this->table->where(array("module" => $key))->order("aid DESC")->getField('datetime');
			$v['count'] = $this->table->where(array("module" => $key))->count();
			$filesize = (int) $this->table->where(array("module" => $key))->sum("filesize");
			$v['filesize'] = sizecount($filesize);
			list($v['filesize'], $v['fileunit']) = explode(" ", $v['filesize']);
			$attachments[$key] = $v;
		}
		return array('spaces'=>$spaces, 'attachments'=>$attachments);
	}
	/**
	 * @param  array 	sql条件
	 * @param  integer 	条数
	 * @param  integer 	页数
	 * @param  string 	排序
	 * @return [type]
	 */
	public function lists($sqlmap = array(), $limit = 10, $page = 1, $order = "") {
		$count = $this->table->where($sqlmap)->count();
		$lists = $this->table->where($sqlmap)->limit($limit)->page($page)->order($order)->select();
		if($count===false || $lists===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return array('lists'=>$lists,'count'=>$count);
	}
	/**
	 * @param  string  获取的字段
	 * @param  array 	sql条件
	 * @return [type]
	 */
	public function getField($field = '', $sqlmap = array()) {
		$result = $this->table->where($sqlmap)->getfield($field);
		if($result===false){
			$this->error = lang('_param_error_');
			return false;
		}
		return $result;
	}
	/**
	 * @param  string  模块名
	 * @param  string  是否使用
	 * @param  string  筛选时间
	 * @return [type]
	 */
	public function picture_space($module = 'goods',$isused,$time){
		$sqlmap = array();
		$sqlmap['module']  = $module;
		$sqlmap['isimage'] = 1;
		if(isset($isused) && $isused == 'unused') {
			$sqlmap['use_nums'] = 0;
		}
		if(isset($isused) && $isused == 'used') {
			$sqlmap['use_nums']  = array('gt',0);
		}
		switch ($time) {
			case '1':
				$sqlmap['datetime'] = array('gt',(time()-(7*60*60*24)));//一周之内
				break;
			case '2':
				$sqlmap['datetime'] = array('gt',(time()-(15*60*60*24)));//半月之内
				break;
			case '3':
				$sqlmap['datetime'] = array('gt',(time()-(30*60*60*24)));//一月之内
				break;
			case '4':
				$sqlmap['datetime'] = array('gt',(time()-(183*60*60*24)));//半年之内
				break;
		}
		return $sqlmap;
	}
}