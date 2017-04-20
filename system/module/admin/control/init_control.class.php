<?php
define('IN_ADMIN', TRUE);
class init_control extends control {
	public function _initialize() {
		parent::_initialize();
        $this->admin = model('admin/admin','service')->init();
        if($this->admin['id'] < 1 && CONTROL_NAME != 'public') {
            redirect(url('admin/public/login'));
        }
		define('ADMIN_ID', $this->admin['id']);
		define('FORMHASH', $this->admin['formhash']);
		$load = hd_load::getInstance();
        $load->librarys('View')->assign('admin',$this->admin);
        if(isset($_GET['formhash']) && $_GET['formhash'] !== FORMHASH) {
			hd_error::system_error('_request_tainting_');
		}
        if($this->admin['group_id'] > 1 && model('admin/admin','service')->auth($this->admin['rules']) === false) {
			showmessage(lang('no_promission_operate','admin/language'));
        }
	}

	/* 禁止重写的方法 */
	final public function admin_tpl($file, $module = '', $suffix = '.tpl.php', $halt = true) {
		$file = (!empty($file)) ? $file : CONTROL_NAME.'_'.METHOD_NAME;
		$module = (!empty($module)) ? $module : MODULE_NAME;
		$tpl_dir = APP_PATH.config('DEFAULT_H_LAYER').'/'.$module.'/template/';
		$tpl_file = $tpl_dir.$file.$suffix;
		if(!is_file($tpl_file) && $halt === TRUE) die(lang('_template_not_exist_').'：'.$tpl_file);
		return $tpl_file;
	}

    /**
     * 后台页面调用
     * @param int $totalrow 总记录数
     * @param int $pagesize 每页记录数
     * @param int $pagenum 	页码数量
     */
    final public function admin_pages($totalrow, $pagesize = 10, $pagenum = 5) {
        $totalPage = ceil($totalrow/$pagesize);
        $rollPage = floor($pagenum/2);

        $StartPage = $_GET['page'] - $rollPage;
        $EndPage = $_GET['page'] + $rollPage;
        if($StartPage < 1) $StartPage = 1;
        if($EndPage < $pagenum) $EndPage = $pagenum;

        if($EndPage >= $totalPage) {
            $EndPage = $totalPage;
            $StartPage = max(1, $totalPage - $pagenum + 1);
        }
        $string = '<ul class="fr">';
        $string .= '<li>共'.$totalrow.'条数据</li>';
        $string .= '<li class="spacer-gray margin-lr"></li>';
        $string .= '<li>每页显示<input class="input radius-none" type="text" name="limit" value="'.$pagesize.'"/>条</li>';
        $string .= '<li class="spacer-gray margin-left"></li>';

        /* 第一页 */
        if($_GET['page'] > 1) {
            $string .= '<li class="start"><a href="'.page_url(array('page' => 1)).'"></a></li>';
            $string .= '<li class="prev"><a href="'.page_url(array('page' => $_GET['page'] - 1)).'"></a></li>';
        } else {
            $string .= '<li class="default-start"></li>';
            $string .= '<li class="default-prev"></li>';
        }
        for ($page = $StartPage; $page <= $EndPage; $page++) {
            $string .= '<li '.(($page == $_GET['page']) ? 'class="current"' : '').'><a href="'.page_url(array('page' => $page)).'">'.$page.'</a></li>';
        }
        if($_GET['page'] < $totalPage) {
            $string .= '<li class="next"><a href="'.page_url(array('page' => $_GET['page'] + 1)).'"></a></li>';
            $string .= '<li class="end"><a href="'.page_url(array('page' => $totalPage)).'"></a></li>';
        } else {
            $string .= '<li class="default-next"></li>';
            $string .= '<li class="default-end"></li>';
        }
        $string .= '</ul>';
        return $string;
    }

    final public function _empty() {
        $tpl_file = $this->admin_tpl(METHOD_NAME, null, '.tpl.php', false);
        if(!is_file($tpl_file)) {
	        exit(lang('_template_not_exist_').'：'.$tpl_file);
	        //error::system_error(lang('_template_not_exist_').'：'.$tpl_file);
	    }
        include $tpl_file;
    }
}