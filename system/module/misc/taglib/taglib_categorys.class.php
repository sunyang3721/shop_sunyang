<?php
class taglib_categorys
{
	public function __construct() {
		$this->model = model('misc/article_category');
	}
	public function lists($sqlmap = array(), $options = array()) {
		$this->model->where($this->build_map($sqlmap));
		if(isset($sqlmap['order'])){
			$this->model->order($sqlmap['order']);
		}
		if(isset($options['limit'])){
			$this->model->limit($options['limit']);
		}
		// if($options['page']) {
		// 	$this->model->page($options['page']);
		// 	$pagefunc = $options['pagefunc'] ? $options['pagefunc'] : 'pages';
		// 	$this->pages = $pagefunc($count,$options['limit']);
		// }
		return $this->model->select();
	}
	public function build_map($data){
		$sqlmap = array();
		$sqlmap['display'] = 1;
		if (isset($data['id'])) {
			if(preg_match('#,#', $data['id'])) {	
				$sqlmap['parent_id'] = array("IN", explode(",", $data['id']));
			} else {
				$sqlmap['parent_id'] = $data['id'];
			}
		}
		if(isset($data['_string'])){
			$sqlmap['_string'] = $data['_string'];
		}
		return $sqlmap;
	}
}