SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hd_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `hd_admin_group`;
CREATE TABLE `hd_admin_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户组定义表';

-- ----------------------------
-- Table structure for hd_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `hd_admin_menu`;
CREATE TABLE `hd_admin_menu` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Table structure for hd_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `hd_admin_user`;
CREATE TABLE `hd_admin_user` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `encrypt` char(10) NOT NULL,
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_ip` char(15) NOT NULL DEFAULT '0',
  `login_num` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

-- ----------------------------
-- Table structure for hd_adv
-- ----------------------------
DROP TABLE IF EXISTS `hd_adv`;
CREATE TABLE `hd_adv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `position_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `loading` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hist` int(11) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='广告管理';

-- ----------------------------
-- Table structure for hd_adv_position
-- ----------------------------
DROP TABLE IF EXISTS `hd_adv_position`;
CREATE TABLE `hd_adv_position` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `defaultpic` varchar(255) NOT NULL DEFAULT '',
  `defaulttext` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='广告位管理';

-- ----------------------------
-- Table structure for hd_article
-- ----------------------------
DROP TABLE IF EXISTS `hd_article`;
CREATE TABLE `hd_article` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `content` text NOT NULL COMMENT '文章内容',
  `category_id` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '文章图片',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否推荐',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '外链',
  `dataline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `keywords` varchar(255) NOT NULL COMMENT '关键字',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '阅读量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Table structure for hd_article_category
-- ----------------------------
DROP TABLE IF EXISTS `hd_article_category`;
CREATE TABLE `hd_article_category` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '父分类',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- ----------------------------
-- Table structure for hd_attachment
-- ----------------------------
DROP TABLE IF EXISTS `hd_attachment`;
CREATE TABLE `hd_attachment` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `module` char(15) NOT NULL,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `filename` char(50) NOT NULL,
  `filepath` char(200) NOT NULL,
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `fileext` char(10) NOT NULL,
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `downloads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `datetime` int(10) unsigned NOT NULL DEFAULT '0',
  `clientip` char(15) NOT NULL,
  `use_nums` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用次数',
  `authcode` char(32) NOT NULL,
  `filetype` varchar(100) NOT NULL DEFAULT '',
  `md5` char(32) NOT NULL DEFAULT '',
  `sha1` varchar(100) NOT NULL DEFAULT '',
  `width` smallint(6) unsigned NOT NULL DEFAULT '0',
  `height` smallint(6) unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT '',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后台上传',
  `url` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `authcode` (`authcode`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Table structure for hd_attribute
-- ----------------------------
DROP TABLE IF EXISTS `hd_attribute`;
CREATE TABLE `hd_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `value` text NOT NULL COMMENT '属性值(逗号分隔)',
  `search` smallint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否参与筛选',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '输入控件的类型,radio:单选,checkbox:复选,input:输入',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='属性';

-- ----------------------------
-- Table structure for hd_brand
-- ----------------------------
DROP TABLE IF EXISTS `hd_brand`;
CREATE TABLE `hd_brand` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '品牌id',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `logo` varchar(200) NOT NULL DEFAULT '' COMMENT '品牌logo图片',
  `descript` text NOT NULL COMMENT '品牌描述',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '品牌的地址',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(是否显示，显示:1,隐藏:0)',
  `isrecommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品品牌';

-- ----------------------------
-- Table structure for hd_cart
-- ----------------------------
DROP TABLE IF EXISTS `hd_cart`;
CREATE TABLE `hd_cart` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `buyer_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '子商品ID',
  `nums` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='购物车';

-- ----------------------------
-- Table structure for hd_comment
-- ----------------------------
DROP TABLE IF EXISTS `hd_comment`;
CREATE TABLE `hd_comment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品sku_id',
  `spu_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品spu_id',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `content` text NOT NULL,
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所属会员',
  `datetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间戳',
  `clientip` char(15) NOT NULL DEFAULT '',
  `mood` enum('positive','neutral','negative') NOT NULL,
  `reply_content` text NOT NULL,
  `reply_time` int(10) unsigned NOT NULL,
  `imgs` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核结果',
  `is_shield` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品评价表';

-- ----------------------------
-- Table structure for hd_delivery
-- ----------------------------
DROP TABLE IF EXISTS `hd_delivery`;
CREATE TABLE `hd_delivery` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `identif` varchar(50) NOT NULL DEFAULT '' COMMENT '标识',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开关(1:开启,0:关闭)',
  `logo` varchar(200) NOT NULL DEFAULT '' COMMENT 'LOGO',
  `insure` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '保价',
  `tpl` text COMMENT '快递单模版内容',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `systime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='物流配送表';

-- ----------------------------
-- Table structure for hd_delivery_district
-- ----------------------------
DROP TABLE IF EXISTS `hd_delivery_district`;
CREATE TABLE `hd_delivery_district` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` int(8) unsigned NOT NULL DEFAULT '0',
  `price` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '配送金额',
  `district_id` text NOT NULL COMMENT '地区ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='物流地区设置';

-- ----------------------------
-- Table structure for hd_district
-- ----------------------------
DROP TABLE IF EXISTS `hd_district`;
CREATE TABLE `hd_district` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` int(10) NOT NULL COMMENT '父栏目',
  `name` varchar(50) NOT NULL DEFAULT '',
  `zipcode` int(10) NOT NULL DEFAULT '0',
  `pinyin` varchar(100) NOT NULL DEFAULT '',
  `lng` varchar(20) NOT NULL DEFAULT '',
  `lat` varchar(20) NOT NULL DEFAULT '',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_focus
-- ----------------------------
DROP TABLE IF EXISTS `hd_focus`;
CREATE TABLE `hd_focus` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `width` int(4) unsigned NOT NULL DEFAULT '100' COMMENT '宽',
  `height` int(4) unsigned NOT NULL DEFAULT '100' COMMENT '高',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否新窗口打开',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='焦点图';

-- ----------------------------
-- Table structure for hd_friendlink
-- ----------------------------
DROP TABLE IF EXISTS `hd_friendlink`;
CREATE TABLE `hd_friendlink` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '友链名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否新窗口打开',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接';

-- ----------------------------
-- Table structure for hd_goods_attribute
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_attribute`;
CREATE TABLE `hd_goods_attribute` (
  `sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `attribute_id` int(10) unsigned NOT NULL COMMENT '属性id',
  `attribute_value` varchar(255) NOT NULL COMMENT '属性值',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '商品属性种类：1为规格，2为属性',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(是否显示，显示:1,隐藏:0)',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品属性';

-- ----------------------------
-- Table structure for hd_goods_category
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_category`;
CREATE TABLE `hd_goods_category` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '父级分类id',
  `type_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品模型id',
  `keywords` varchar(200) NOT NULL,
  `descript` varchar(200) NOT NULL,
  `show_in_nav` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在导航中显示,状态:0:关闭，1:开启',
  `grade` text NOT NULL COMMENT '价格分级',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:0:关闭，1:开启',
  `sort` int(8) NOT NULL DEFAULT '100' COMMENT '排序',
  `img` varchar(200) NOT NULL DEFAULT '' COMMENT '分类前面的小图标',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '外部链接',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品分类';

-- ----------------------------
-- Table structure for hd_goods_consult
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_consult`;
CREATE TABLE `hd_goods_consult` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品咨询id',
  `spu_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品spu_id',
  `sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '子商品id',
  `question` text NOT NULL COMMENT '咨询内容',
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '咨询人会员ID，非会员为空',
  `username` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '咨询时间',
  `reply_content` text NOT NULL COMMENT '咨询回复',
  `reply_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '评论时的用户IP',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '评论状态：0：未审核 1: 已审核',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `see` int(1) NOT NULL DEFAULT '0' COMMENT '是否查看',
  PRIMARY KEY (`id`),
  KEY `spu_id` (`spu_id`) USING BTREE,
  KEY `spu_id, status` (`spu_id`,`status`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品咨询表';

-- ----------------------------
-- Table structure for hd_goods_index
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_index`;
CREATE TABLE `hd_goods_index` (
  `sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `spu_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `catid` mediumint(8) unsigned NOT NULL,
  `brand_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品状态',
  `sales` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '销量',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '人气',
  `show_in_lists` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在列表中显示',
  `status_ext` tinyint(1) NOT NULL,
  `shop_price` decimal(10,2) NOT NULL,
  `favorites` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '收藏',
  `attr_ids` text NOT NULL,
  `spec_ids` text NOT NULL,
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `prom_type` varchar(200) NOT NULL DEFAULT '' COMMENT '促销类型',
  `prom_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '促销类型ID',
  PRIMARY KEY (`sku_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_goods_sku
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_sku`;
CREATE TABLE `hd_goods_sku` (
  `sku_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '子商品id',
  `spu_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品id,取值goods的goods_id',
  `sku_name` varchar(200) NOT NULL DEFAULT '' COMMENT '子商品名称',
  `subtitle` varchar(200) NOT NULL DEFAULT '' COMMENT '副标题',
  `style` varchar(50) NOT NULL,
  `sn` varchar(200) NOT NULL DEFAULT '' COMMENT '商品货号',
  `barcode` varchar(60) NOT NULL DEFAULT '' COMMENT '商品条形码',
  `spec` text NOT NULL COMMENT '商品所属规格类型id，取值spec的id',
  `imgs` text NOT NULL COMMENT '商品图册',
  `thumb` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `status_ext` tinyint(1) NOT NULL COMMENT '商品标签状态',
  `number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存数量',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场售价',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `shop_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售售价',
  `keyword` text NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `show_in_lists` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在列表显示',
  `warn_number` tinyint(3) NOT NULL DEFAULT '5',
  `prom_type` varchar(200) NOT NULL DEFAULT '' COMMENT '促销类型',
  `prom_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '促销类型ID',
  `up_time` int(10) NOT NULL DEFAULT '0' COMMENT '上架时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `edition` int(10) NOT NULL DEFAULT '1' COMMENT '版本号',
  `weight` numeric(8,2) NOT NULL DEFAULT '0.00' COMMENT '体重',
  `volume` numeric(8,2) NOT NULL DEFAULT '0.00' COMMENT '体积',
  PRIMARY KEY (`sku_id`),
  UNIQUE KEY `sn` (`sn`) USING BTREE,
  KEY `goods_id` (`spu_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='子商品表';

-- ----------------------------
-- Table structure for hd_goods_spu
-- ----------------------------
DROP TABLE IF EXISTS `hd_goods_spu`;
CREATE TABLE `hd_goods_spu` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品名称,商品标题',
  `sn` varchar(200) NOT NULL DEFAULT '' COMMENT '商品货号',
  `subtitle` varchar(200) NOT NULL DEFAULT '' COMMENT '副标题，广告语',
  `style` varchar(50) NOT NULL COMMENT '商品标题的html样式',
  `catid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品品牌id',
  `keyword` varchar(200) NOT NULL COMMENT '商品关键词，利于搜索引擎优化',
  `description` varchar(200) NOT NULL COMMENT '商品描述，利于搜索引擎优化',
  `content` text NOT NULL COMMENT '商品的详细描述',
  `imgs` text NOT NULL COMMENT '商品图册',
  `thumb` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图',
  `min_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '最小价格',
  `max_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '最大价格',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态(是否上架，删除:-1,上架:1,下架:0)',
  `specs` text NOT NULL COMMENT '规格数据 json',
  `sku_total` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品总数',
  `give_point` int(11) NOT NULL DEFAULT '-1' COMMENT '积分',
  `warn_number` tinyint(3) NOT NULL DEFAULT '2' COMMENT '库存警告数量',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `spec_id` int(10) NOT NULL DEFAULT '0' COMMENT '上传图片时与规格关联的id',
  `type_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联类型id',
  `weight` decimal(8,2) NOT NULL COMMENT '重量',
  `volume` decimal(8,2) NOT NULL COMMENT '体积',
  `delivery_template_id` mediumint(8) NOT NULL COMMENT '运费模板id',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品表';

-- ----------------------------
-- Table structure for hd_help
-- ----------------------------
DROP TABLE IF EXISTS `hd_help`;
CREATE TABLE `hd_help` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '超链接',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `keywords` varchar(50) NOT NULL DEFAULT '' COMMENT '帮助关键字',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='站点帮助';

-- ----------------------------
-- Table structure for hd_log
-- ----------------------------
DROP TABLE IF EXISTS `hd_log`;
CREATE TABLE `hd_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` char(15) NOT NULL DEFAULT '0' COMMENT '执行行为者ip',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '操作URL',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`) USING BTREE,
  KEY `user_id_ix` (`user_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='行为日志表';

-- ----------------------------
-- Table structure for hd_member
-- ----------------------------
DROP TABLE IF EXISTS `hd_member`;
CREATE TABLE `hd_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '登录密码',
  `group_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '会员等级',
  `encrypt` char(6) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '电子邮件',
  `integral` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用积分',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '可用余额',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `register_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `register_ip` char(15) NOT NULL DEFAULT '' COMMENT '注册IP',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `login_ip` char(15) NOT NULL DEFAULT '' COMMENT '登录IP',
  `login_num` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `islock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `frozen_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '冻结资金',
  `exp` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `emailstatus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '邮箱认证状态',
  `mobilestatus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '手机认证状态',
   `weixin_open` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for hd_member_address
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_address`;
CREATE TABLE `hd_member_address` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所属会员',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '收货人',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '联系电话',
  `district_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '区划ID',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `zipcode` char(6) NOT NULL DEFAULT '' COMMENT '邮编',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户收货地址';

-- ----------------------------
-- Table structure for hd_member_deposit
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_deposit`;
CREATE TABLE `hd_member_deposit` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `order_sn` char(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `order_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单生成时间',
  `pay_code` varchar(50) NOT NULL DEFAULT '' COMMENT '支付方式',
  `trade_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方交易号',
  `trade_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '交易状态（第三方）',
  `trade_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '交易时间',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态（是否已入账）',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`) USING BTREE,
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员充值记录表';

-- ----------------------------
-- Table structure for hd_member_favorite
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_favorite`;
CREATE TABLE `hd_member_favorite` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所属会员',
  `sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'SKU编号',
  `sku_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `datetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `sku_name` varchar(200) NOT NULL DEFAULT '' COMMENT '产品名称',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_member_group
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_group`;
CREATE TABLE `hd_member_group` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `min_points` int(10) unsigned NOT NULL DEFAULT '0',
  `max_points` int(10) unsigned NOT NULL DEFAULT '0',
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '等级描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员等级组';

-- ----------------------------
-- Table structure for hd_member_log
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_log`;
CREATE TABLE `hd_member_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL,
  `value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `msg` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT '',
  `admin_id` int(10) unsigned DEFAULT '0',
  `money_detail` tinytext NOT NULL COMMENT '余额明细',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='财务变动记录表';

-- ----------------------------
-- Table structure for hd_member_message
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_message`;
CREATE TABLE `hd_member_message` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '(0：未阅；1：已阅)',
  `delstatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内信';


-- ----------------------------
-- Table structure for hd_navigation
-- ----------------------------
DROP TABLE IF EXISTS `hd_navigation`;
CREATE TABLE `hd_navigation` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '导航名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',
  `sort` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否新窗口打开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='导航设置';

-- ----------------------------
-- Table structure for hd_node
-- ----------------------------
DROP TABLE IF EXISTS `hd_node`;
CREATE TABLE `hd_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `name` char(40) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `m` char(20) NOT NULL DEFAULT '' COMMENT '模块',
  `c` char(20) NOT NULL DEFAULT '' COMMENT '控制器',
  `a` char(20) NOT NULL DEFAULT '' COMMENT '操作',
  `param` char(100) NOT NULL DEFAULT '' COMMENT '参数',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `url` char(255) NOT NULL DEFAULT '',
  `appid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `split` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分割线',
  PRIMARY KEY (`id`),
  KEY `listorder` (`sort`) USING BTREE,
  KEY `parentid` (`parent_id`) USING BTREE,
  KEY `module` (`c`,`m`,`a`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='菜单&权限节点表';

-- ----------------------------
-- Table structure for hd_notify
-- ----------------------------
DROP TABLE IF EXISTS `hd_notify`;
CREATE TABLE `hd_notify` (
  `code` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `enabled` varchar(250) NOT NULL DEFAULT '1' COMMENT '启用状态',
  `config` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `ignore` text NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知系统配置信息';

-- ----------------------------
-- Table structure for hd_notify_template
-- ----------------------------
DROP TABLE IF EXISTS `hd_notify_template`;
CREATE TABLE `hd_notify_template` (
  `id` varchar(100) NOT NULL DEFAULT '' COMMENT '嵌入点名称',
  `enabled` text NOT NULL COMMENT '开启的通知方式',
  `template` text NOT NULL COMMENT '通知模板',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模版注释',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知模版设置';

-- ----------------------------
-- Table structure for hd_order
-- ----------------------------
DROP TABLE IF EXISTS `hd_order`;
CREATE TABLE `hd_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `sn` char(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家id',
  `seller_ids` varchar(200) NOT NULL DEFAULT '0' COMMENT '卖家ids',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单来源(1：标准，2：wap，3：wechat)',
  `pay_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '支付类型(1：在线支付，2：货到付款)',
  `sku_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总额',
  `delivery_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '物流总额',
  `real_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应付总额',
  `paid_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实付总额',
  `balance_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额付款总额',
  `pay_method` varchar(200) NOT NULL DEFAULT '' COMMENT '支付方式',
  `pay_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方支付号',
  `address_name` varchar(100) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `address_mobile` varchar(100) NOT NULL DEFAULT '' COMMENT '收货人电话',
  `address_detail` varchar(100) NOT NULL DEFAULT '' COMMENT '收货详细地址',
  `invoice_tax` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '发票税率',
  `invoice_title` varchar(200) NOT NULL DEFAULT '' COMMENT '发票抬头',
  `invoice_content` varchar(200) NOT NULL DEFAULT '' COMMENT '发票内容',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态(1：正常，2：全部取消，3：全部回收，4：全部删除)',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支付(布尔值)',
  `confirm_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '确认状态(0：待确认，1：部分确认，2：已确认)',
  `delivery_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态(0：待发货，1：部分发货，2：已发货)',
  `finish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '完成状态(0：待完成，1：部分完成，2：已完成)',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  `promot_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '所有优惠总额',
  `address_district_ids` varchar(100) NOT NULL DEFAULT '' COMMENT '收货地区ids(统计索引)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单主表';

-- ----------------------------
-- Table structure for hd_order_delivery
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_delivery`;
CREATE TABLE `hd_order_delivery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `o_sku_ids` varchar(200) NOT NULL DEFAULT '0' COMMENT '订单商品列表(多个用","逗号分割)',
  `sub_sn` char(20) NOT NULL COMMENT '订单号',
  `delivery_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '物流id',
  `delivery_name` varchar(20) NOT NULL DEFAULT '' COMMENT '所属物流',
  `delivery_sn` varchar(50) NOT NULL COMMENT '运单号',
  `delivery_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `isreceive` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否确认收货(布尔值)',
  `receive_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '确认收货时间',
  `print_time` int(10) unsigned DEFAULT '0' COMMENT '打印时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单物流表';

-- ----------------------------
-- Table structure for hd_order_log
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_log`;
CREATE TABLE `hd_order_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作类型',
  `operator_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `operator_name` varchar(60) NOT NULL DEFAULT '' COMMENT '操作者名称',
  `operator_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '操作者类型(1:后台管理员,2:会员3:商家(预留))',
  `msg` text NOT NULL COMMENT '日志详情',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP地址',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单日志表';

-- ----------------------------
-- Table structure for hd_order_parcel
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_parcel`;
CREATE TABLE `hd_order_parcel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（-1：配送失败，0：待配货；1：配送中，2：配送完成）',
  `member_name` varchar(100) NOT NULL DEFAULT '' COMMENT '会员账号',
  `address_name` varchar(100) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `address_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '收货人手机',
  `address_detail` varchar(200) NOT NULL DEFAULT '' COMMENT '收货人详细地址',
  `delivery_name` varchar(100) NOT NULL DEFAULT '' COMMENT '物流名称',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='发货单管理';

-- ----------------------------
-- Table structure for hd_order_parcel_log
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_parcel_log`;
CREATE TABLE `hd_order_parcel_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parcel_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联发货表id',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `sub_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `buyer_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `member_name` varchar(100) NOT NULL DEFAULT '' COMMENT '会员帐号',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作明细',
  `msg` text NOT NULL COMMENT '操作日志备注',
  `operator_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '操作管理员ID',
  `operator_name` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单发货单日志';

-- ----------------------------
-- Table structure for hd_order_refund
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_refund`;
CREATE TABLE `hd_order_refund` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `return_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退货id',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号(空为整个订单)',
  `o_sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品ID(0为整个订单)',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '退款类型(1：退货并退款，2：仅退款)',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `cause` varchar(200) NOT NULL DEFAULT '' COMMENT '原因',
  `desc` tinytext NOT NULL COMMENT '退款描述',
  `images` text NOT NULL COMMENT '售后传图',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态(-2：未通过，-1：已取消，0：待审核，1：通过)',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `admin_id` decimal(8,0) unsigned NOT NULL DEFAULT '0' COMMENT '审核管理员ID',
  `admin_desc` varchar(200) NOT NULL DEFAULT '' COMMENT '后台审核描述',
  `admin_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE,
  KEY `order_goods_id` (`o_sku_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单退款';

-- ----------------------------
-- Table structure for hd_order_refund_log
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_refund_log`;
CREATE TABLE `hd_order_refund_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `refund_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款id',
  `order_sn` char(20) NOT NULL DEFAULT '',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `o_sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作类型',
  `operator_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `operator_name` varchar(60) NOT NULL DEFAULT '' COMMENT '操作者名称',
  `operator_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '操作者类型(1:后台管理员,2:会员3:商家(预留))',
  `msg` text NOT NULL COMMENT '日志详情',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP地址',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单退款日志';

-- ----------------------------
-- Table structure for hd_order_return
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_return`;
CREATE TABLE `hd_order_return` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `o_sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  `buyer_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '买家ID',
  `cause` varchar(200) NOT NULL DEFAULT '' COMMENT '退货原因',
  `number` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '退货数量',
  `amount` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `desc` tinytext NOT NULL COMMENT '会员退货描述',
  `images` text NOT NULL COMMENT '会员退货传图',
  `delivery_name` varchar(50) NOT NULL DEFAULT '' COMMENT '快递名称',
  `delivery_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '快递号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态(-2：未通过，-1：已取消，0：待审核，1：通过，2：已退货)',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `admin_id` decimal(8,0) unsigned NOT NULL DEFAULT '0' COMMENT '审核管理员ID',
  `admin_desc` varchar(200) NOT NULL DEFAULT '' COMMENT '后台审核描述',
  `admin_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE,
  KEY `o_sku_id` (`o_sku_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单退货';

-- ----------------------------
-- Table structure for hd_order_return_log
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_return_log`;
CREATE TABLE `hd_order_return_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `return_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退货ID',
  `order_sn` char(20) NOT NULL DEFAULT '',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `o_sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作类型',
  `operator_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '操作者ID',
  `operator_name` varchar(60) NOT NULL DEFAULT '' COMMENT '操作者名称',
  `operator_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '操作者类型(1:后台管理员,2:会员3:商家(预留))',
  `msg` text NOT NULL COMMENT '日志详情',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP地址',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单退货日志';

-- ----------------------------
-- Table structure for hd_order_server
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_server`;
CREATE TABLE `hd_order_server` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '售后类型(1：退货并退款，2：仅退款)',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家id',
  `return_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退货表主键id',
  `refund_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款表主键id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '售后状态',
  `o_sku_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单售后索引表';

-- ----------------------------
-- Table structure for hd_order_sku
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_sku`;
CREATE TABLE `hd_order_sku` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `buyer_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `seller_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '卖家ID',
  `spu_id` mediumint(8) NOT NULL,
  `sku_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `sku_thumb` varchar(200) NOT NULL DEFAULT '' COMMENT '产品缩略图',
  `sku_barcode` varchar(30) NOT NULL DEFAULT '' COMMENT '产品的唯一条码(写入相对应商品条码)',
  `sku_name` varchar(200) NOT NULL DEFAULT '' COMMENT '产品名称',
  `sku_spec` text NOT NULL COMMENT '产品规格',
  `sku_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售售价',
  `sku_edition` int(10) NOT NULL DEFAULT '1' COMMENT 'sku版本号',
  `sku_info` text NOT NULL COMMENT '订单快照数据',
  `real_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  `buy_nums` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `return_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '退货id',
  `refund_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '退款ID',
  `delivery_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单物流关联id',
  `delivery_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态(0：待发货，1：已发货，2：已收货)',
  `iscomment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否评论',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间',
  `promotion` text NOT NULL COMMENT '促销活动详情',
  `is_give` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `delivery_template_id` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '运费模板id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单商品表';

-- ----------------------------
-- Table structure for hd_order_sub
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_sub`;
CREATE TABLE `hd_order_sub` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主订单id',
  `order_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `pay_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '支付方式(1：在线支付，2：货到付款)',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家id',
  `seller_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '卖家id',
  `delivery_name` varchar(50) NOT NULL DEFAULT '' COMMENT '物流名称',
  `sku_price` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总额',
  `delivery_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '物流费用',
  `real_price` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应付总额',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态(1：正常，2：取消，3：回收，4：删除)',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态',
  `confirm_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '确认状态(0：待确认，2：已确认)',
  `delivery_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态(0：待发货，1：部分发货，2：已发货)',
  `finish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '完成状态(0：待完成，1：部分完成，2：已完成)',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间(时间戳)',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '确认订单(时间戳)',
  `delivery_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间(时间戳)',
  `finish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间(时间戳)',
  `system_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统时间(时间戳)',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `promotion` text NOT NULL COMMENT '促销活动',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='子订单表';

-- ----------------------------
-- Table structure for hd_order_tpl_parcel
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_tpl_parcel`;
CREATE TABLE `hd_order_tpl_parcel` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '模版名称',
  `content` text NOT NULL COMMENT '内容',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='发货单模板';

-- ----------------------------
-- Table structure for hd_order_track
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_track`;
CREATE TABLE `hd_order_track` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单物流关联ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `sub_sn` char(20) NOT NULL DEFAULT '' COMMENT '子订单号',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '追踪时间',
  `msg` text NOT NULL COMMENT '内容',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `clientip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单跟踪';

-- ----------------------------
-- Table structure for hd_payment
-- ----------------------------
DROP TABLE IF EXISTS `hd_payment`;
CREATE TABLE `hd_payment` (
  `pay_code` varchar(50) NOT NULL DEFAULT '',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `pay_fee` varchar(5) NOT NULL DEFAULT '',
  `pay_desc` text NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态',
  `config` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `isonline` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否在线支付',
  `applie` varchar(10) NOT NULL DEFAULT 'pc' COMMENT '客户端类型',
  PRIMARY KEY (`pay_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付方式配置信息';


-- ----------------------------
-- Table structure for hd_promotion_goods
-- ----------------------------
DROP TABLE IF EXISTS `hd_promotion_goods`;
CREATE TABLE `hd_promotion_goods` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '活动名称',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `sku_ids` text NOT NULL COMMENT '商品列表',
  `rules` text NOT NULL COMMENT '活动规则',
  `share_order` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否参与订单促销活动',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品促销表';

-- ----------------------------
-- Table structure for hd_promotion_group
-- ----------------------------
DROP TABLE IF EXISTS `hd_promotion_group`;
CREATE TABLE `hd_promotion_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '促销标题，用于后台标识',
  `subtitle` varchar(50) NOT NULL DEFAULT '' COMMENT '捆绑销售名称，用于前台显示',
  `sku_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '参与捆绑销售的商品sku_id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开启状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_promotion_order
-- ----------------------------
DROP TABLE IF EXISTS `hd_promotion_order`;
CREATE TABLE `hd_promotion_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自动递增',
  `name` varchar(250) NOT NULL DEFAULT '' COMMENT '促销名称',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `type` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '促销类型(0：满额立减；1：满额免邮；2：满额赠礼)',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '满足金额',
  `discount` varchar(10) NOT NULL DEFAULT '' COMMENT '优惠项目',
  `dateline` int(10) NOT NULL COMMENT '添加时间',
  `sort` mediumint(8) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='订单促销';

-- ----------------------------
-- Table structure for hd_promotion_time
-- ----------------------------
DROP TABLE IF EXISTS `hd_promotion_time`;
CREATE TABLE `hd_promotion_time` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `sku_info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_queue
-- ----------------------------
DROP TABLE IF EXISTS `hd_queue`;
CREATE TABLE `hd_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '类型/驱动',
  `method` varchar(255) NOT NULL DEFAULT '' COMMENT '方法/操作',
  `params` text NOT NULL COMMENT '参数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（-1：失败；0：待执行；1：已完成）',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间戳',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '100' COMMENT '排序（越小越先执行）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='队列表';

-- ----------------------------
-- Table structure for hd_setting
-- ----------------------------
DROP TABLE IF EXISTS `hd_setting`;
CREATE TABLE `hd_setting` (
  `key` varchar(200) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设置表';

-- ----------------------------
-- Table structure for hd_spec
-- ----------------------------
DROP TABLE IF EXISTS `hd_spec`;
CREATE TABLE `hd_spec` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格id',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '规格名称',
  `value` text NOT NULL,
  `img` text NOT NULL COMMENT '规格图片',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '规格状态',
  `sort` mediumint(5) unsigned NOT NULL DEFAULT '100' COMMENT '规格排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品规格';

-- ----------------------------
-- Table structure for hd_type
-- ----------------------------
DROP TABLE IF EXISTS `hd_type`;
CREATE TABLE `hd_type` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '模型名称',
  `content` text NOT NULL,
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品模型';

-- ----------------------------
-- Table structure for hd_vcode
-- ----------------------------
DROP TABLE IF EXISTS `hd_vcode`;
CREATE TABLE `hd_vcode` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `vcode` text NOT NULL,
  `action` varchar(100) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_order_trade
-- ----------------------------
DROP TABLE IF EXISTS `hd_order_trade`;
CREATE TABLE `hd_order_trade` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单支付ID',
  `order_sn` char(20) NOT NULL DEFAULT '' COMMENT '主订单号',
  `trade_no` char(20) NOT NULL DEFAULT '' COMMENT '支付单号',
  `total_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态[-1:取消,0:未支付,1:完成支付]',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `method` varchar(200) NOT NULL DEFAULT '' COMMENT '支付方式',
  `pay_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方支付号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_app
-- ----------------------------
DROP TABLE IF EXISTS `hd_app`;
CREATE TABLE `hd_app` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) NOT NULL DEFAULT '' COMMENT '应用标示',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '应用名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '应用简介',
  `available` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启状态',
  `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '版权信息',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '应用版本',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '官网地址',
  `author` varchar(50) NOT NULL DEFAULT '' COMMENT '应用作者',
  `branch_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '应用分支',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `is_system` tinyint(1) NOT NULL,
  `menu` text NOT NULL,
  `server_version` varchar(200) NOT NULL DEFAULT '' COMMENT '服务版本',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_appvar
-- ----------------------------
DROP TABLE IF EXISTS `hd_appvar`;
CREATE TABLE `hd_appvar` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `appid` smallint(6) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `variable` varchar(40) NOT NULL,
  `extra` text NOT NULL,
  `displayorder` int(8) NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_wap_template
-- ----------------------------
DROP TABLE IF EXISTS `hd_wap_template`;
CREATE TABLE `hd_wap_template` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `identifier` varchar(100) NOT NULL DEFAULT '' COMMENT '应用标示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_xiaoneng_config
-- ----------------------------
DROP TABLE IF EXISTS `hd_xiaoneng_config`;
CREATE TABLE `hd_xiaoneng_config` (
  `name` varchar(200) NOT NULL DEFAULT '',
  `identifier` varchar(200) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_xiaoneng_service
-- ----------------------------
DROP TABLE IF EXISTS `hd_xiaoneng_service`;
CREATE TABLE `hd_xiaoneng_service` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启',
  `config` text NOT NULL COMMENT '客服配置情况',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hd_upload_driver
-- ----------------------------
DROP TABLE IF EXISTS `hd_upload_driver`;
CREATE TABLE `hd_upload_driver` (
  `code` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(8) unsigned NOT NULL DEFAULT '100',
  `config` text NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='附件驱动';

-- ----------------------------
-- Table structure for `hd_delivery_template`
-- ----------------------------
DROP TABLE IF EXISTS `hd_delivery_template`;
CREATE TABLE `hd_delivery_template` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '地区id',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '物流名称',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '类型',
  `delivery_info` text NOT NULL COMMENT '地区模板',
  `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '开关(1:开启,0:关闭)',
  `sort` int(8) NOT NULL DEFAULT '100' COMMENT '排序',
  `isdefault` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='运费模板表';


-- ----------------------------
-- Table structure for `hd_delivery_template`
-- ----------------------------
DROP TABLE IF EXISTS `hd_member_open`;
CREATE TABLE `hd_member_open` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `mid` int(10) NOT NULL DEFAULT '0' COMMENT '会员id',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '类型',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '关联值',
  `dateline` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知关联';