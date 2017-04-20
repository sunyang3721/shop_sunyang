<?php
/* 删除商品分类缓存 */
cache('app_lists',NULL);
model('admin/app','service')->clear_cache();