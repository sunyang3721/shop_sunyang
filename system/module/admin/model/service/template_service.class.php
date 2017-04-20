<?php
class template_service extends service
{
	protected $dir;

	public function _initialize() {
		$this->dir = TPL_PATH;
	}

	/* 获取所有模板目录 */
	public function fetch_all() {
		$dir = @opendir($this->dir);
		$tpls = array();
		while (($file = @readdir($dir)) !== false) {
			if($file == '.' || $file == '..' || !is_dir($this->dir.$file)) continue;
			if (file_exists($this->dir.$file.DIRECTORY_SEPARATOR.'config.xml')) {
				$config = @file_get_contents($this->dir.$file.DIRECTORY_SEPARATOR.'config.xml');
				$xml = xml2array($config);
				if (file_exists($this->dir.$file.DIRECTORY_SEPARATOR.'thumb.png')) {
					$thumb_dir = $this->dir.$file.'/';
				} else {
					$thumb_dir = $this->dir.'/';
				}
				$xml['thumb'] = str_replace(DOC_ROOT, __ROOT__, $thumb_dir.'thumb.png');
				$tpls[$file] = $xml;
			}
		}
		@closedir($dir);
		foreach ($tpls as $key => $value ) {
			asort($tpls[$key] );
		}
		asort($tpls);
		return $tpls;
	}
}