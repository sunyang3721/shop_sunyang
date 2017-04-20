<?php
hd_core::load_class('init', 'goods');
class index_control extends init_control {
	public function _initialize() {
		parent::_initialize();
		$this->category = $this->load->service('article_category');
		$this->article = $this->load->service('article');
	}
	/**
	 * [help_lists 前台帮助列表页]
	 */
	public function help_lists(){
		$this->load->librarys('View')->display('help_lists');
	}
	/**
	 * [help_dettail 前台帮助详情页]
	 */
	public function help_detail(){
		$id = (int) $_GET['id'];
		$row = $this->load->service('help')->get_help_by_id($id);
		if(!$row)
			showmessage(lang('_param_error_'));
		extract($row);
		$SEO = seo($title.' - 帮助中心');
		$this->load->librarys('View')->assign('SEO',$SEO)->assign($row,$row)->display('help_detail');
	}
	/**
	 * [article_lists 文章列表]
	 */
	public function article_lists(){
		$title = $this->category->get_category_by_id($_GET['category_id'],'name');
		$SEO = seo($title.' - 文章列表');
		$this->load->librarys('View')->assign('SEO',$SEO)->display('article_lists');
	}
	/**
	 * [article_detail 文章详情页]
	 */
	public function article_detail(){
		$id = (int) $_GET['id'];
		$row = $this->article->get_article_by_id($id);
		if(!$row) 
			showmessage(lang('article_not_exist','misc/language'));
		$this->article->hits($id);
		$row['hits'] += 1;
		extract($row);
		$SEO = seo($title.' - 文章详情',$keywords);
		$this->load->librarys('View')->assign('SEO',$SEO)->assign($row,$row)->display('article_detail');
	}
}