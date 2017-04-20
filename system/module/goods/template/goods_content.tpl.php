<script type="text/javascript" src="./statics/js/template.js" ></script>
<script type="text/javascript" charset="utf-8" src="./statics/js/editor/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="./statics/js/editor/umeditor.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="./statics/js/editor/umeditor.js"> </script>
<script type="text/javascript" charset="utf-8" src="./statics/js/wap/jquery-ui.min.js"> </script>

<div class="margin-top">
	<?php echo form::editor('spu[content]', $goods['spu']['content'], '', '', array('mid' => $admin['id'], 'path' => 'goods','module'=>'goods','allow_exts'=>array('bmp','jpg','jpeg','gif','png'))); ?>
</div>