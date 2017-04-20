<?php include template('header','admin');?>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>admin/plugin.css">
<div class="fixed-nav layout">
    <ul>
        <li class="first"><?php echo $plugin['name']?><a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
        <li class="spacer-gray"></li>
        <li class="fixed-nav-tab"><a class="current" href="javascript:;">设置</a></li>
    </ul>
    <div class="hr-gray"></div>
</div>
<div class="content padding-big have-fixed-nav">
    <div class="tips margin-tb">
        <div class="tips-info border">
            <h6>温馨提示</h6>
            <a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
        </div>
        <div class="tips-txt padding-small-top layout">
            <p>- <?php echo $plugin['description']?></p>
        </div>
    </div>
    <div class="hr-gray"></div>
    <form class="addfrom" name="form1" id="form1" action="<?php echo url('setting',array('id' => $_GET['id']))?>" method="post">
    <dl class="gzzt clearfix mt10" style="margin-top:10px;">
        <dd>
            <div class="time fl">
                <?php foreach ($vars as $var): ?>
                    <?php
                        $array = array();
                        if(in_array($var['type'], array('radio', 'checkbox', 'select', 'selects'))) {
                            $extra = explode("\r\n", $var['extra']);
                            foreach ($extra as $key => $value) {
                                list($k, $v) = explode("=", $value);
                                $array[$k] = $v;
                            }
                        }
                            echo form::input($var['type'],$var['variable'],$var['value'],$var['title'],$var['description'],array('items' => $array));
                        ?>
                <?php endforeach ?>
            </div>
        </dd>
    </dl>

        <div class="padding">
            <input type="submit" class="button bg-main" value="设置" />
            <input type="button" class="button margin-left bg-gray" value="返回" />
        </div>
    </form>
</div>

<?php include template('footer','admin');?>
