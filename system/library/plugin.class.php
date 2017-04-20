<?php
class plugin extends hd_base
{
	protected $plugin_dir;
	protected $plugin_path;
	protected $config;
	protected $vars;
	protected $identifier;

	public function __construct($identifier = '') {
		$this->getInstance($identifier);
	}

	public function getInstance($identifier) {
		$plugins = model('admin/app','service')->get_plugins();
		$this->identifier = $identifier;
		$this->plugin_dir = PLUGIN_PATH.$identifier;
		$this->plugin_path = str_replace(DOC_ROOT, __ROOT__, $this->plugin_dir);
		$this->vars = $plugins[$identifier]['vars'];
		$this->config = $plugins[$identifier];
	}
}