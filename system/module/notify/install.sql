/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : com.dmibox.pro.haidao_v2

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2015-12-11 16:44:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hd_notify
-- ----------------------------
DROP TABLE IF EXISTS `hd_notify`;
CREATE TABLE `hd_notify` (
  `code` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态',
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
-- Table structure for hd_vcode
-- ----------------------------
DROP TABLE IF EXISTS `hd_vcode`;
CREATE TABLE `hd_vcode` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `vcode` text NOT NULL,
  `action` varchar(100) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=gbk;
