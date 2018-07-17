/*
 Navicat MySQL Data Transfer

 Source Server         : Vagrant
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : geolocation

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 17/07/2018 14:51:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for geolocation
-- ----------------------------
DROP TABLE IF EXISTS `geolocation`;
CREATE TABLE `geolocation`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `country_code` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_eu` int(1) NULL DEFAULT 0,
  `created_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for geomapping
-- ----------------------------
DROP TABLE IF EXISTS `geomapping`;
CREATE TABLE `geomapping`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `country_code` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `store_id`(`store_id`) USING BTREE,
  CONSTRAINT `store_id` FOREIGN KEY (`store_id`) REFERENCES `geostores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for geostores
-- ----------------------------
DROP TABLE IF EXISTS `geostores`;
CREATE TABLE `geostores`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `store_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_enabled` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
