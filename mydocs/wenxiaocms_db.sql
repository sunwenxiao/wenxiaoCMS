-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.7.19 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 wenxiaocms_db.admins 结构
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `login_name` char(20) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `salt` char(6) NOT NULL DEFAULT '',
  `department` varchar(50) NOT NULL DEFAULT '',
  `real_name` varchar(10) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `issendmaile` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以发送邮件1可以0不可以',
  `isfujian` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以发送邮件附件1可以0不可以',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0',
  `is_allwelfare_id` int(11) NOT NULL DEFAULT '0' COMMENT '是否审批号码0不是1是',
  `coderole` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可以生成可用兑换码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  wenxiaocms_db.admins 的数据：0 rows
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_appname_list 结构
CREATE TABLE IF NOT EXISTS `admin_appname_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `appname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appname` (`appname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='程序应用名字管理';

-- 正在导出表  wenxiaocms_db.admin_appname_list 的数据：~1 rows (大约)
/*!40000 ALTER TABLE `admin_appname_list` DISABLE KEYS */;
INSERT INTO `admin_appname_list` (`id`, `appname`) VALUES
	(1, 'index');
/*!40000 ALTER TABLE `admin_appname_list` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_control 结构
CREATE TABLE IF NOT EXISTS `admin_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  wenxiaocms_db.admin_control 的数据：0 rows
/*!40000 ALTER TABLE `admin_control` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_control` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_control_sons 结构
CREATE TABLE IF NOT EXISTS `admin_control_sons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_sons_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员子权限关系表';

-- 正在导出表  wenxiaocms_db.admin_control_sons 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `admin_control_sons` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_control_sons` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_log 结构
CREATE TABLE IF NOT EXISTS `admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0',
  `login_name` varchar(50) NOT NULL DEFAULT '',
  `real_name` varchar(20) NOT NULL DEFAULT '',
  `role_name` varchar(20) NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `object` varchar(200) NOT NULL DEFAULT '',
  `operation` varchar(200) NOT NULL DEFAULT '',
  `before` varchar(200) NOT NULL DEFAULT '',
  `after` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  wenxiaocms_db.admin_log 的数据：0 rows
/*!40000 ALTER TABLE `admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_log` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_permission 结构
CREATE TABLE IF NOT EXISTS `admin_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `m` varchar(20) NOT NULL DEFAULT '',
  `a` varchar(20) NOT NULL DEFAULT '',
  `op` varchar(20) NOT NULL DEFAULT '',
  `data` varchar(30) NOT NULL DEFAULT '',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` tinyint(4) NOT NULL DEFAULT '0',
  `appname` varchar(50) DEFAULT NULL,
  `iconname` varchar(50) DEFAULT NULL COMMENT '图标名字',
  PRIMARY KEY (`id`),
  KEY `appname` (`appname`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- 正在导出表  wenxiaocms_db.admin_permission 的数据：1 rows
/*!40000 ALTER TABLE `admin_permission` DISABLE KEYS */;
INSERT INTO `admin_permission` (`id`, `name`, `m`, `a`, `op`, `data`, `disabled`, `display_order`, `appname`, `iconname`) VALUES
	(1, '主控', 'main', '', '', '', 0, 0, 'index', NULL);
/*!40000 ALTER TABLE `admin_permission` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_permission_sons 结构
CREATE TABLE IF NOT EXISTS `admin_permission_sons` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `m` varchar(50) DEFAULT NULL,
  `a` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示0显示1不显示',
  `display_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `iconname` varchar(50) DEFAULT NULL COMMENT '图标名字',
  `additional` varchar(500) NOT NULL DEFAULT '&' COMMENT '附加参数',
  PRIMARY KEY (`id`),
  KEY `m` (`m`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='子权限';

-- 正在导出表  wenxiaocms_db.admin_permission_sons 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `admin_permission_sons` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_permission_sons` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_role 结构
CREATE TABLE IF NOT EXISTS `admin_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '权限名字',
  `des` varchar(255) NOT NULL DEFAULT '' COMMENT '权限描述',
  `display_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `delt` tinyint(1) NOT NULL DEFAULT '0',
  `action_url` varchar(200) DEFAULT NULL COMMENT '跳转路径',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台权限表';

-- 正在导出表  wenxiaocms_db.admin_role 的数据：0 rows
/*!40000 ALTER TABLE `admin_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_role` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.app_info_list 结构
CREATE TABLE IF NOT EXISTS `app_info_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(100) NOT NULL COMMENT '应用名字',
  `note` varchar(100) NOT NULL COMMENT '备注',
  `package_name` varchar(100) NOT NULL COMMENT '包名',
  `package_md5` varchar(100) NOT NULL COMMENT 'MD5',
  `download_address` varchar(500) NOT NULL COMMENT '下载地址',
  `file_name` varchar(500) NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用信息列表';

-- 正在导出表  wenxiaocms_db.app_info_list 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `app_info_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_info_list` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
