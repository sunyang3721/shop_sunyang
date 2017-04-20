<?php 
return array (
  'goods#index#lists' => 
  array (
    'page' => '商品列表页',
    'name' => 'goods#index#lists',
    'tab' => '{id}',
    'showurl' => 'lists-{id}.html',
    'rewrite' => 'index.php?m=goods&c=index&a=lists&id=$1&%1',
    'show' => '0',
  ),
  'goods#index#detail' => 
  array (
    'page' => '商品详情页',
    'name' => 'goods#index#detail',
    'tab' => '{sku_id}',
    'showurl' => 'goods-{sku_id}.html',
    'rewrite' => 'index.php?m=goods&c=index&a=detail&sku_id=$1&%1',
    'show' => '0',
  ),
  'goods#index#brand_list' => 
  array (
    'page' => '品牌列表页',
    'name' => 'goods#index#brand_list',
    'tab' => '{id}',
    'showurl' => 'brand-{id}.html',
    'rewrite' => 'index.php?m=goods&c=index&a=brand_list&id=$1&%1',
    'show' => '0',
  ),
  'member#index#index' => 
  array (
    'page' => '会员中心主页',
    'name' => 'member#index#index',
    'tab' => '',
    'showurl' => 'buyer.html',
    'rewrite' => 'index.php?m=member&c=index&a=index&%1',
    'show' => '0',
  ),
  'misc#index#help_lists' => 
  array (
    'page' => '帮助中心列表页',
    'name' => 'misc#index#help_lists',
    'tab' => '{id}',
    'showurl' => 'help-lists-{id}.html',
    'rewrite' => 'index.php?m=misc&c=index&a=help_lists&%1',
    'show' => '0',
  ),
  'misc#index#help_detail' => 
  array (
    'page' => '帮助中心内容页',
    'name' => 'misc#index#help_detail',
    'tab' => '{id}',
    'showurl' => 'help-detail-{id}.html',
    'rewrite' => 'index.php?m=misc&c=index&a=help_detail&id=$1&%1',
    'show' => '0',
  ),
  'misc#index#article_lists' => 
  array (
    'page' => '文章列表页',
    'name' => 'misc#index#article_lists',
    'tab' => '{category_id}',
    'showurl' => 'article-lists-{category_id}.html',
    'rewrite' => 'index.php?m=misc&c=index&a=article_lists&category_id=$1&%1',
    'show' => '0',
  ),
  'misc#index#article_detail' => 
  array (
    'page' => '文章内容页',
    'name' => 'misc#index#article_detail',
    'tab' => '{id}',
    'showurl' => 'article-detail-{id}.html',
    'rewrite' => 'index.php?m=misc&c=index&a=article_detail&id=$1&%1',
    'show' => '0',
  ),
);