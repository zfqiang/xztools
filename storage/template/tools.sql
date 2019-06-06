/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : tools

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-06-06 09:08:29
*/
CREATE DATABASE tools;
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for daka_normal
-- ----------------------------
DROP TABLE IF EXISTS `daka_normal`;
CREATE TABLE `daka_normal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '1：正常上班确实法定假期 2：周末正常上班',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for members
-- ----------------------------
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `department` varchar(255) DEFAULT NULL COMMENT '所属部门',
  PRIMARY KEY (`id`),
  KEY `department` (`department`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for members_daka
-- ----------------------------
DROP TABLE IF EXISTS `members_daka`;
CREATE TABLE `members_daka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL COMMENT '部门',
  `sxw` int(11) DEFAULT NULL COMMENT '1：上午 2：下午',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `date_time` (`date_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
