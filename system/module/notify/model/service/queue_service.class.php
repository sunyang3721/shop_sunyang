<?php
class queue_service extends service
{
	protected $entrydir = '';
	public function _initialize() {
		$this->entrydir = APP_PATH.'module/notify/library/driver/';
		$this->notify = $this->load->service('notify/notify');
		$this->queue = $this->load->librarys('queue');
	}
	public function config($queue){
		$_config = $this->notify->get_fech_enable_code($queue['type']);
		$_config = array_merge($_config,$queue);
		if(FALSE === $_config || empty($_config['configs']) || $_config['enabled']==0) return $this;
		$class_name = $queue['type'];
		$class_file = $this->entrydir.$class_name.'/'.$class_name.'.class.php';
		if (!file_exists($class_file)) return $this;
		require_cache($class_file);
		$this->queue = new $class_name($_config);
		return $this->queue;
	}

	public function add_queue($params){
		return $this->queue->add($params);
	}

	public function __call($method_name, $method_args){
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->queue)
			&& method_exists($this->queue, $method_name)
		)
		return call_user_func_array(array(& $this->queue, $method_name), $method_args);
	}

}