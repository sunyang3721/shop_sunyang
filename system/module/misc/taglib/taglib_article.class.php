<?php
class taglib_article
{
	public function __construct() {
		$this->service = model('misc/article','service');
	}
	
	public function lists($sqlmap = array(), $options = array()) {	
		$lists = $this->service->article_lists($sqlmap,$options);
		$pagefunc = $options['pagefunc'] ? $options['pagefunc'] : 'pages';
		$this->pages = $pagefunc($lists['count'],$options['limit']);
		return $lists['lists'];
	}
}