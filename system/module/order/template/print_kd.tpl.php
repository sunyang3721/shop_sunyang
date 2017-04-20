<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8"> 
<title>快递单打印</title>
<script type="text/javascript" src="<?php echo __ROOT__ ?>statics/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo __ROOT__ ?>statics/js/jquery.print.js"></script>
<style type="text/css">
.button_search{
    background-image: -webkit-linear-gradient(top,#ffffff,#e6e6e6);
    background-image: -moz-linear-gradient(top,#ffffff,#e6e6e6);
    background-image: -o-linear-gradient(top,#ffffff,#e6e6e6);
    border: 1px solid #c7c7c7;
    cursor: pointer;
    width: 80px;
    height: 26px;
    line-height: 24px;
    margin-top: 20px;
    margin-bottom: 20px;
}
</style>
</head>
<body>
<div style="text-align:center;">
    <input type="button" class="button_search" value="开始打印" data-event="start_print" title="点击后会标识该快递单已打印" /> &nbsp;&nbsp;
    <input type="button" class="button_search" onclick="window.history.go(-1);" value="返回上一页" />
</div>
<div id="content" style="position: relative;background:url(<?php echo __ROOT__ ?>statics/images/delivery_express/<?php echo $_delivery['identif'];?>.jpg);width:<?php echo $_delivery['tpl']['width']?>px; height:<?php echo $_delivery['tpl']['height']; ?>px;">
    <?php foreach ($_delivery['tpl']['list'] as $key => $val): ?>
        <div style="position: absolute;width:<?php echo $val['width'] ?>; height:<?php echo $val['height']; ?>px; left:<?php echo $val['left'] ?>px; top: <?php echo $val['top'] ?>px;"><?php echo $val['txt'] ?></div>
    <?php endforeach ?>
</div>
<script type="text/javascript">
$("[data-event='start_print']").bind("click" ,function(){
    /* 标识为已打印 */
    if ('<?php echo $o_delivery[print_time] ?>' == 0) {
        $.post("<?php echo url('order/admin_order/print_kd') ?>", {o_id : '<?php echo $o_delivery[id] ?>'});
    }
    $("#content").jqprint();
})
</script>
<?php include template('footer','admin');?>