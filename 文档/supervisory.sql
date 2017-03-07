/*
Navicat MySQL Data Transfer

Source Server         : 192.168.10.10
Source Server Version : 50716
Source Host           : 192.168.10.10:3306
Source Database       : supervisory

Target Server Type    : MYSQL
Target Server Version : 50716
File Encoding         : 65001

Date: 2017-03-07 23:51:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jk_cabinet
-- ----------------------------
DROP TABLE IF EXISTS `jk_cabinet`;
CREATE TABLE `jk_cabinet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cluster_id` int(11) unsigned NOT NULL,
  `name` varchar(60) NOT NULL COMMENT '机柜名称',
  `ip` int(11) unsigned NOT NULL COMMENT '机柜ip',
  `created_by` int(11) unsigned DEFAULT '0' COMMENT '创建者id',
  `updated_by` int(11) unsigned DEFAULT '0' COMMENT '更新者id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='机柜信息表';

-- ----------------------------
-- Table structure for jk_cluster
-- ----------------------------
DROP TABLE IF EXISTS `jk_cluster`;
CREATE TABLE `jk_cluster` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '中心为0，网点为中心的id',
  `name` varchar(60) NOT NULL COMMENT '机柜中心名称',
  `ip` int(11) unsigned NOT NULL COMMENT '中心ip地址',
  `city` varchar(60) DEFAULT NULL COMMENT '市',
  `county` varchar(60) DEFAULT NULL COMMENT '县',
  `district` varchar(100) DEFAULT NULL COMMENT '区域地址',
  `created_by` int(11) unsigned DEFAULT '0' COMMENT '创建者id',
  `updated_by` int(11) unsigned DEFAULT '0' COMMENT '更新者id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='中心/网点信息表';

-- ----------------------------
-- Table structure for jk_equipment
-- ----------------------------
DROP TABLE IF EXISTS `jk_equipment`;
CREATE TABLE `jk_equipment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cabinet_id` int(11) unsigned NOT NULL,
  `ip` int(11) unsigned NOT NULL,
  `port` varchar(100) NOT NULL,
  `created_by` int(11) unsigned DEFAULT '0' COMMENT '创建者id',
  `updated_by` int(11) unsigned DEFAULT '0' COMMENT '更新者id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设备信息表';

-- ----------------------------
-- Table structure for jk_pending
-- ----------------------------
DROP TABLE IF EXISTS `jk_pending`;
CREATE TABLE `jk_pending` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `obj_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '操作对象类型：0-中心；1-网点；2-机柜；3-设备；4-用户',
  `action_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '操作类型：0-增加；1-更新；2-删除',
  `content` text COMMENT '提交待审核的具体数据',
  `remarks` varchar(100) DEFAULT NULL COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='待审核事件表';

-- ----------------------------
-- Table structure for jk_role
-- ----------------------------
DROP TABLE IF EXISTS `jk_role`;
CREATE TABLE `jk_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '角色名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Table structure for jk_user
-- ----------------------------
DROP TABLE IF EXISTS `jk_user`;
CREATE TABLE `jk_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `username` varchar(60) NOT NULL COMMENT '用户名',
  `nickname` varchar(60) DEFAULT NULL,
  `password` varchar(60) NOT NULL COMMENT '密码',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `email` varchar(60) NOT NULL COMMENT '邮箱',
  `qq` varchar(20) DEFAULT NULL COMMENT 'QQ号',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别：0-男；1-女',
  `address` varchar(255) DEFAULT NULL COMMENT '住址',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0-禁用；1-待禁用；2-启用',
  `created_by` int(11) unsigned DEFAULT '0' COMMENT '创建者id',
  `updated_by` int(11) unsigned DEFAULT '0' COMMENT '更新者id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for jk_user_cluster
-- ----------------------------
DROP TABLE IF EXISTS `jk_user_cluster`;
CREATE TABLE `jk_user_cluster` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `cluster_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '中心id或网点id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户管理的中心/网点对应表';
