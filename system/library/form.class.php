<?php
class form {

    static public function input($type = 'text', $name, $value, $label='', $description='', $attribute = array()) {
	    $attribute = self::_parseAttribute($attribute);
		if($type == 'hidden'){
			$string .= self::$type($name, $value, $attribute);
		}else{
	        $string = '<div class="form-group"><span class="label">'.$label.'</span>';
	        $string .= '<div class="box">'.(self::$type($name, $value, $attribute)).'</div>';
	        if($description) {
	            $string .= '<p class="desc">'.$description.'</p>';
	        }
	        $string .= '</div>';
		}
        return $string;
    }

    /**
     * 单行文本
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param array $attribute  参数
        {
            color => 颜色值
            key   => 表单名
        }
     * @return string
     */
	static private function text($name, $value, $attribute) {
        $string = '<input class="input hd-input '.$attribute['css'].'" type="text" name="'.$name.'" value="'.$value.'" tabindex="0"'.self::_buildAttribute($attribute).' />';
        if(isset($attribute['color'])) {
            $color_name = (isset($attribute['key'])) ? $attribute['key'] : $name.'_color';
            $string .= '<input class="color-picker input_cxcolor" type="text" name="'.$color_name.'" value="'.$attribute['color'].'" style="background-color: '.$attribute['color'].';">';
            if(!defined('COLOR_INIT')) {
                $string .= '<script type="text/javascript" charset="utf-8" src="'.__ROOT__.'statics/js/cxcolor/jquery.cxcolor.min.js"></script>';
                $string .= '<link type="text/css" href="'.__ROOT__.'statics/js/cxcolor/jquery.cxcolor.css"  rel="stylesheet">';
                $string .= '<script>$(".input_cxcolor").cxColor();</script>';
                define('COLOR_INIT', TRUE);
            }
        }
        return $string;
	}
    /**
     * 隐藏域
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param array $attribute  参数
     * @return string
     */
	static private function hidden($name, $value, $attribute) {
        $string = '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
        return $string;
	}

    /**
     * 单行文本
     * @param string $name 表单名称
     * @param string $value 默认值
     * @param array $attribute 附加属性
     * @return string
     */
    static private function password($name, $value, $attribute) {
        $string = '<input class="input hd-input '.$attribute['css'].'" type="password" name="'.$name.'" value="'.$value.'" tabindex="0"';
        if($attribute['placeholder']) {
            $string .= ' placeholder="'.$placeholder.'"';
        }
		$string .= self::_buildAttribute($attribute);
        $string .= '>';
        return $string;
    }

    /**
     * 开启/关闭
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $attribute 附加属性
     * @return string
     */
    static public function enabled($name, $value = 1, $attribute = array()) {
        $radios = array(1 => '开启', 0 => '关闭');
        $string = '';
        foreach ($radios as $key => $radio) {
            $checked = ($value == $key) ? ' checked' : '';
            $string .= '<label class="select-wrap"><input class="select-btn" type="radio" name="'.$name.'" value="'.$key.'"'.$checked.'/>'.$radio.'</label>';
        }
        return $string;
    }

    /**
     * 单选框
     * @param string $name  表单名称
     * @param string|array $checked 默认值
     * @param array $default 选项列表
     * @param string|array $disabled 禁止项目
     * @param array $attribute 附加属性
     * @return mixed
     */
    static public function radio($name = '', $value, $attribute = array()) {
	    extract($attribute);
	    if(!is_array($items) || empty($items)) return false;
	    $colspan = max(1, intval($colspan));
	    $disabled = (!is_array($disabled) && !empty($disabled)) ? explode(",", $disabled) : (array) $disabled;
	    $i = 1;
	    foreach( $items as $key => $item ) {
		    $_disabled = (in_array($key, $disabled)) ? ' disabled' : '';
		    $_checked = ($value == $key) ? ' checked' : '';
		    $string .= '<label class="select-wrap"><input class="select-btn" type="radio" name="'.$name.'" value="'.$key.'"'.$_checked.$_disabled.' />'.$item.'</label>';
		    if($i % $colspan == 0) $string .= '<br/>';
		    $i++;
	    }
	    return $string;
    }

    /**
     * 复选框
     * @param string $name  表单名称
     * @param string|array $checked 默认值
     * @param array $default 选项列表
     * @param string|array $disabled 禁止项目
     * @param array $attribute 附加属性
     * @return mixed
     */
    static public function checkbox($name = '', $value = '', $attribute = array()) {
	    extract($attribute);
	    if(!is_array($items) || empty($items)) return false;
	    $colspan = max(1, intval($colspan));
	    $disabled = (!is_array($disabled) && !empty($disabled)) ? explode(",", $disabled) : (array) $disabled;
	    $checked = (!is_array($value) && !empty($value)) ? explode(",", $value) : (array) $value;

	    $i = 1;
	    foreach( $items as $key => $item ) {
		    $_disabled = (in_array($key, $disabled)) ? ' disabled' : '';
		    $_checked = (in_array($key, $checked)) ? ' checked' : '';
		    $string .= '<label class="select-wrap"><input class="select-btn" type="checkbox" name="'.$name.'" value="'.$key.'"'.$_checked.$_disabled.' />'.$item.'</label>';
		    if($i % $colspan == 0) $string .= '<br/>';
		    $i++;
	    }
	    return $string;
    }

    /**
     * 多行文本
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $attribute 附加属性
     * @return string
     */
    static public function textarea($name, $value, $attribute = array()) {
        extract($attribute);
        return '<textarea class="textarea hd-input" name="'.$name.'" placeholder="'. $placeholder .'" '.self::_buildAttribute($attribute).'>'.$value.'</textarea>';
    }

    /**
     * 文件框
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param array $attribute 附加属性
     * @return string
     */
    static public function file($name = '', $value = '', $attribute = array()) {
        $string = '<div class="input hd-input file-box clearfix">';
        $string .= '<input type="text" class="file-txt" value="'.$value.'" tabindex="0"/>';
        $string .= '<input type="button" class="file-btn" value="浏览" />';
        $string .= '<input type="file" class="file file-view" name="'.$name.'" value=""/>';
        if($value) {
            $string .= '<div class="file-preview"><i class="ico_pic_show no"></i><span class="file-pic"><img src="'.$value.'" /></span></div>';
        }
        $string .= '</div>';
        return $string;
    }

    /**
     * 下拉框
     * @param string $name  表单名称
     * @param string|array $selected 默认值
     * @param array $default 选项列表
     * @param array $attribute  附加属性
     * @return string
     */
    static public function select($name, $value, $attribute = array()) {
	    extract($attribute);
	    if(!is_array($items) || empty($items)) return false;
	    $string = '<div class="form-select-edit"><div class="form-buttonedit-popup"><input class="input" type="text" value="'.$items[$value].'" readonly="readonly"><span class="ico_buttonedit"></span></div><div class="listbox-items" style="display: none;">';
	    foreach( $items as $key => $item ) {
		    $selected = ($value == $key) ? true : false;
		    $string .= '<span class="listbox-item'.($selected ? ' listbox-item-selected' : '').'" data-val="'.$key.'">'.$item.'</span>';
	    }
	    $string .= '</div><input class="form-select-name" type="hidden" name="'.$name.'" value="'.$value.'"></div>';
        return $string;
    }

    /**
     * 日历控件
     * @param string $name  表单名称
     * @param string $value 默认值
     * @param string $format 时间格式
     * @param array $attribute  附加属性
     * @return string
     */
    static public function calendar($name, $value, $attribute = array()) {
	    extract($attribute);
	    $format = (!empty($format)) ? $format : 'YYYY-MM-DD hh:mm:ss';
	    $placeholder = (!empty($placeholder)) ? $placeholder : $format;
	    $skin = (!empty($skin)) ? $skin : 'danlan';
	    $string = '<input class="input laydate-icon hd-input" type="text" name="'.$name.'" value="'.$value.'" placeholder="'.$placeholder.'" tabindex="0" '.self::_buildAttribute($attribute).' onclick="laydate({istime: true, format: \''.$format.'\' })">';
	    if(!defined( 'INIT_CALENDAR')) {
		    $string .= '<script type="text/javascript" src="'.__ROOT__.'statics/js/laydate/laydate.js"></script>';
		    $string .= '<script type="text/javascript">laydate.skin(\''.$skin.'\');</script>';
		    define('INIT_CALENDAR', true);

	    }
	    return $string;
    }

    /**
     * 编辑器
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param string $width 宽度
     * @param string $height 高度
     * @return string
     */
    static public function editor($name, $value='', $width ='100%', $height = '500',$allow_image = FALSE,$umsuffix='',$diytag = FALSE) {
        $id = random(8);
        $string = '<script type="text/plain" id="'.$id.'" style="width:'.$width.';height:'.$height.';">'.$value.'</script>';
        if(!defined('INIT_EDITOR')) {
            $string .= '<script type="text/javascript" charset="utf-8" src="'.__ROOT__.'statics/js/editor/umeditor.config.js"></script>';
            $string .= '<script type="text/javascript" charset="utf-8" src="'.__ROOT__.'statics/js/editor/umeditor.js"></script>';
			$string .= '<script type="text/javascript" charset="utf-8" src="'.__ROOT__.'statics/js/editor/haidaotag.js"></script>';
            $string .= '<script type="text/javascript" charset="utf-8" src="'.__ROOT__.'statics/js/editor/lang/zh-cn/zh-cn.js"></script>';
            $string .= '<link href="'.__ROOT__.'statics/js/editor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">';
            define('INIT_EDITOR', true);
        }

        $upload_init = '';
        if(is_array($allow_image)) {
            helper('attachment');
            $upload_init = attachment_init($allow_image);
        }

        $width = (!empty($width)) ? $width : '100%';
        $height = (!empty($height)) ? $height : '500';

        $string .= '<script type="text/javascript">';

        $string .= 'var um'.$umsuffix.' = UM.getEditor(\''.$id.'\', {
            textarea : \''.$name.'\'
            ,initialFrameWidth:\''.$width.'\'
            ,initialFrameHeight:\''.$height.'\'
            ,imageUrl:\''.url('attachment/index/editor', array('upload_init' => $upload_init)).'\'
            ,catcherUrl:\''.url('attachment/index/remote', array('upload_init' => $upload_init)).'\'
            ,imageFieldName:\'editor\'';
            if($diytag){
	            $string .= ',toolbar:[\'source | undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |\',
	            \'insertorderedlist insertunorderedlist | selectall cleardoc paragraph | fontfamily fontsize\' ,
	            \'| justifyleft justifycenter justifyright justifyjustify |\',
	            \'link unlink | emotion image video  | map\',
	            \'| horizontal print preview fullscreen\', \'drafts\', \'formula\',
	            \'换行 商城名称 用户名 用户手机 用户邮箱 商品名称 商品规格 主订单号 订单金额 商品金额 付款金额 支付方式 充值金额 邮件验证链接 验证码 配送方式 运单号 用户可用余额 变动金额\']';
			}
        $string .= '});';
        $string .= '</script>';
        runhook('editor',$data = array('value'=>$value,'string'=>&$string,'upload_init'=>$upload_init));
        return $string;
    }


    /**
     * 颜色选框
     * @param string $name   表单名称
     * @param string $value  默认值
     * @param array $attribute  参数
        {
            color => 颜色值
            key   => 表单名
        }
     * @return string
     */
    static public function color($name, $value, $attribute = array()) {
        $string = '<input class="color-choose input_cxcolor" type="text" name="'.$name.'" value="'.$value.'" style="background-color: '.$value.';" readonly="readonly" '.self::_buildAttribute($attribute).'>';
        if(!defined('COLOR_INIT')) {
            $string .= '<script type="text/javascript" charset="utf-8" src="'.__ROOT__.'statics/js/cxcolor/jquery.cxcolor.min.js"></script>';
            $string .= '<link type="text/css" href="'.__ROOT__.'statics/js/cxcolor/jquery.cxcolor.css"  rel="stylesheet">';
            $string .= '<script>$(".input_cxcolor").cxColor();</script>';
            define('COLOR_INIT', TRUE);
        }
        return $string;
    }

    /**
     *
     * @param type $options
     * @return string|boolean
     */
    static private function _parseAttribute($options = array()) {
	    $igrnore = array('name', 'value', 'label', 'descript');
	    foreach( $options as $key => $option ) {
		    if(in_array($key, $igrnore)) unset($options[$key]);
	    }
	    return $options;
    }

    static private function _buildAttribute($options = array()) {
	    $string = '';
	    foreach( $options as $key => $option ) {
	    	$string .= ' '.$key.'="'.$option.'"';
	    }
	    return $string;
    }
}