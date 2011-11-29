/*
Navicat MySQL Data Transfer

Source Server         : 61.155.161.140
Source Server Version : 50152
Source Host           : 61.155.161.140:3306
Source Database       : www_27pu_com

Target Server Type    : MYSQL
Target Server Version : 50152
File Encoding         : 65001

Date: 2011-11-29 23:58:53
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `favorite_categorys`
-- ----------------------------
DROP TABLE IF EXISTS `favorite_categorys`;
CREATE TABLE `favorite_categorys` (
  `fc_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(56) DEFAULT NULL,
  `insert_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`fc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of favorite_categorys
-- ----------------------------

-- ----------------------------
-- Table structure for `favorite_goods`
-- ----------------------------
DROP TABLE IF EXISTS `favorite_goods`;
CREATE TABLE `favorite_goods` (
  `fg_id` int(11) NOT NULL AUTO_INCREMENT,
  `fc_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `num_iids` bigint(13) NOT NULL,
  `name` varchar(56) DEFAULT NULL,
  `insert_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`fg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of favorite_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `favorite_stores`
-- ----------------------------
DROP TABLE IF EXISTS `favorite_stores`;
CREATE TABLE `favorite_stores` (
  `fs_id` int(11) NOT NULL AUTO_INCREMENT,
  `fc_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `name` varchar(56) DEFAULT NULL,
  `insert_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`fs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of favorite_stores
-- ----------------------------

-- ----------------------------
-- Table structure for `good_categorys`
-- ----------------------------
DROP TABLE IF EXISTS `good_categorys`;
CREATE TABLE `good_categorys` (
  `gc_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(56) DEFAULT NULL,
  `rule` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of good_categorys
-- ----------------------------

-- ----------------------------
-- Table structure for `good_lists`
-- ----------------------------
DROP TABLE IF EXISTS `good_lists`;
CREATE TABLE `good_lists` (
  `num_iids` bigint(13) NOT NULL,
  `gc_id` int(11) NOT NULL,
  `commission_rate` decimal(7,2) DEFAULT NULL,
  `title` varchar(60) DEFAULT NULL,
  `pic_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `click_url` varchar(255) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `commission_num` int(8) DEFAULT NULL,
  `commission_volume` decimal(10,2) DEFAULT NULL,
  `shop_click_url` varchar(56) DEFAULT NULL,
  `seller_credit_score` int(3) DEFAULT NULL,
  `item_location` varchar(32) DEFAULT NULL,
  `volume` int(8) DEFAULT NULL,
  `weight` int(8) DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `fav_num` int(11) DEFAULT NULL,
  `insert_dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of good_lists
-- ----------------------------

-- ----------------------------
-- Table structure for `members`
-- ----------------------------
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(56) DEFAULT NULL,
  `mail` varchar(56) DEFAULT NULL,
  `from` tinyint(1) DEFAULT '0' COMMENT '0本站，1淘宝，2腾讯',
  `insert_dt` datetime DEFAULT NULL,
  `insert_ip` varchar(15) DEFAULT NULL,
  `last_dt` datetime DEFAULT NULL,
  `last_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of members
-- ----------------------------

-- ----------------------------
-- Table structure for `stores`
-- ----------------------------
DROP TABLE IF EXISTS `stores`;
CREATE TABLE `stores` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `gc_id` int(11) DEFAULT NULL,
  `store_name` varchar(56) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0',
  `fav_num` int(11) NOT NULL,
  `insert_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of stores
-- ----------------------------
