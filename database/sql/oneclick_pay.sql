/*
Navicat MySQL Data Transfer

Source Database       : oneclick_pay

Target Server Type    : MYSQL
Target Server Version : 50642
File Encoding         : 65001

Date: 2019-01-30 14:26:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for le_log
-- ----------------------------
DROP TABLE IF EXISTS `le_log`;
CREATE TABLE `le_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `out_trade_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '订单号',
  `return_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '支付异常结果',
  `add_time` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for le_order
-- ----------------------------
DROP TABLE IF EXISTS `le_order`;
CREATE TABLE `le_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '充值人',
  `out_trade_no` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '商户订单号',
  `recharge_status` enum('AWAIT','SUCCESS','FALL') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'AWAIT' COMMENT '订单状态  AWAIT待充值  SUCCESS充值成功  FALL充值失败',
  `pay_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '支付类型  wechatpay微信  alipay支付宝',
  `body` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品描述',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户输入的充值金额',
  `fee` decimal(10,2) DEFAULT '0.00' COMMENT '手续费',
  `remark` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '备注',
  `add_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '订单生成时间',
  `return_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '订单返回信息',
  `client_ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `trade_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '交易设备  phone  pc',
  `time_end` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '支付完成时间',
  `transaction_id` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '微信支付订单号',
  `bank_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '付款银行',
  `total_fee` decimal(10,0) DEFAULT NULL COMMENT '微信返回的订单金额',
  `cash_fee` decimal(10,0) DEFAULT NULL COMMENT '微信现金支付金额',
  `is_subscribe` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '是否关注公众账号 Y-关注，N-未关注',
  `err_code_des` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '充值错误描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='充值记录';

-- ----------------------------
-- Table structure for le_product
-- ----------------------------
DROP TABLE IF EXISTS `le_product`;
CREATE TABLE `le_product` (
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '产品名称',
  `price` decimal(10,2) NOT NULL COMMENT '套餐价格',
  `flag` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '产品标记'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of le_product
-- ----------------------------
INSERT INTO `le_product` VALUES ('1', '会员', '0.01', 'XXX-会员充值');

-- ----------------------------
-- Table structure for le_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `le_sms_log`;
CREATE TABLE `le_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '短信状态码描述',
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '状态码-返回OK代表请求成功,其他错误码详见错误码列表',
  `client_ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '发送短信的IP',
  `addtime` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '短信发送时间',
  `updatetime` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for le_user
-- ----------------------------
DROP TABLE IF EXISTS `le_user`;
CREATE TABLE `le_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sms_notify_status` int(2) DEFAULT '0' COMMENT '充值成功短信通知状态  0未通知  1已通知',
  `member_status` enum('非会员','会员','异常') COLLATE utf8_unicode_ci NOT NULL DEFAULT '非会员',
  `recharge_status` int(2) NOT NULL DEFAULT '0' COMMENT '充值状态  0未充值  1已充值',
  `client_ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'ip地址',
  `member_level` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '会员等级',
  `product_type` int(2) DEFAULT NULL COMMENT '套餐类型',
  `add_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户注册时间',
  `member_time` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '成为会员的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
