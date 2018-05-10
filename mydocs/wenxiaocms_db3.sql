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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='程序应用名字管理';

-- 正在导出表  wenxiaocms_db.admin_appname_list 的数据：~2 rows (大约)
/*!40000 ALTER TABLE `admin_appname_list` DISABLE KEYS */;
INSERT INTO `admin_appname_list` (`id`, `appname`) VALUES
	(2, 'admin'),
	(1, 'index');
/*!40000 ALTER TABLE `admin_appname_list` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_control 结构
CREATE TABLE IF NOT EXISTS `admin_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='和sons表合并改完无限权限';

-- 正在导出表  wenxiaocms_db.admin_control 的数据：0 rows
/*!40000 ALTER TABLE `admin_control` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_control` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.admin_control_sons 结构
CREATE TABLE IF NOT EXISTS `admin_control_sons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_sons_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员子权限关系表 和admin_control合并改完无限权限配置';

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='admin_log操作日志日志改为计入redis，每2小时向数据库事务插入，操作日志保留最近默认三个月的，保留时间后台可设置。';

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='admin_permission 和 admin_permission_sons表合并改为无限权限分配表，权限内容存入redis，有变化则更新redis';

-- 正在导出表  wenxiaocms_db.admin_permission 的数据：2 rows
/*!40000 ALTER TABLE `admin_permission` DISABLE KEYS */;
INSERT INTO `admin_permission` (`id`, `name`, `m`, `a`, `op`, `data`, `disabled`, `display_order`, `appname`, `iconname`) VALUES
	(1, '主控', 'main', '', '', '', 0, 0, 'index', NULL),
	(2, '主控', 'main', '', '', '', 0, 0, 'admin', NULL);
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

-- 导出  表 wenxiaocms_db.wenxiaocms_header 结构
CREATE TABLE IF NOT EXISTS `wenxiaocms_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(20) NOT NULL DEFAULT '' COMMENT '属性',
  `value` varchar(200) NOT NULL DEFAULT '' COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='文晓CMS包含头配置';

-- 正在导出表  wenxiaocms_db.wenxiaocms_header 的数据：11 rows
/*!40000 ALTER TABLE `wenxiaocms_header` DISABLE KEYS */;
INSERT INTO `wenxiaocms_header` (`id`, `key`, `value`) VALUES
	(1, 'lang', 'en'),
	(2, 'charset', 'utf-8'),
	(3, 'http-equiv', 'X-UA-Compatible'),
	(4, 'http-equiv_content', 'IE=edge'),
	(5, 'viewport', 'width=device-width, initial-scale=1'),
	(6, 'author', '3'),
	(7, 'description', '4'),
	(8, 'title', 'wenxiaoCMS-时代的未来'),
	(9, 'body_css', ''),
	(10, 'logo_path', 'images/logo.png'),
	(11, 'logo_alt', 'wrapkit');
/*!40000 ALTER TABLE `wenxiaocms_header` ENABLE KEYS */;

-- 导出  表 wenxiaocms_db.wenxiaocms_topbar 结构
CREATE TABLE IF NOT EXISTS `wenxiaocms_topbar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL COMMENT '菜单名称',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '菜单等级',
  `father_id` int(11) NOT NULL DEFAULT '0' COMMENT '父菜单ID',
  `col-lg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '大屏显示列数',
  `col-md` tinyint(1) NOT NULL DEFAULT '0' COMMENT '中屏显示列数',
  `href` varchar(200) DEFAULT NULL COMMENT '菜单链接',
  `target` varchar(200) NOT NULL DEFAULT '_blank',
  `bg-img` varchar(200) NOT NULL DEFAULT '' COMMENT '菜单背景图',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '父菜单标题',
  `father_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单类型：0无，1带背景图片和标题 2列表样式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=utf8 COMMENT='CMS导航条';

-- 正在导出表  wenxiaocms_db.wenxiaocms_topbar 的数据：124 rows
/*!40000 ALTER TABLE `wenxiaocms_topbar` DISABLE KEYS */;
INSERT INTO `wenxiaocms_topbar` (`id`, `name`, `level`, `father_id`, `col-lg`, `col-md`, `href`, `target`, `bg-img`, `title`, `father_type`) VALUES
	(1, 'Demos', 0, 0, 3, 0, '#', '_blank', 'images/mega-bg.jpg', 'Most Powerfull Bootstrap 4 UI Kit', 1),
	(2, 'Niche Homepages', 1, 1, 2, 6, '#', '_blank', '', '', 0),
	(3, 'Business', 2, 2, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(4, 'Creative', 2, 2, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(5, 'Health & Medical', 2, 2, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(6, 'Software Application', 2, 2, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(7, 'Real Estate', 2, 2, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(8, 'Restaurant', 2, 2, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(9, '&nbsp;', 1, 1, 2, 6, '#', '_blank', '', '', 0),
	(10, 'Web-Agency', 2, 9, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(11, 'Fitness', 2, 9, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(12, 'Accounting', 2, 9, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(13, 'One Page', 2, 9, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(14, 'Marketing / SEO', 2, 9, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(15, 'Industrial', 2, 9, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(16, 'Landing Pages', 1, 1, 2, 6, '#', '_blank', '', '', 0),
	(17, 'App Landing', 2, 16, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(20, 'Form Landing', 2, 16, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(18, 'Health & Medical', 2, 16, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(19, 'Personal Landing', 2, 16, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(21, 'Appointment Landing', 2, 16, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(22, 'More Coming soon...', 2, 16, 0, 0, '#', '_blank', '', '', 0),
	(23, 'Freelancer / Portfolio', 1, 1, 2, 6, '#', '_blank', '', '', 0),
	(24, 'Freelancer 1', 2, 23, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(25, 'Freelancer 2', 2, 23, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(26, 'Photographer Light', 2, 23, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(27, 'Photographer Dark', 2, 23, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(28, 'Comming Soon', 2, 23, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(29, 'Sections', 0, 0, 4, 0, '#', '_blank', 'images/mega-bg2.jpg', 'Create anything <br/>with our amazing <br/>sections', 1),
	(30, 'Headers &amp; Footers', 1, 29, 2, 4, '#', '_blank', '', '', 0),
	(31, 'Banners', 2, 30, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(32, 'Navigation 1-10', 2, 30, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(33, 'Navigation 11-20', 2, 30, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(34, 'Footers', 2, 30, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(35, 'Call to Actions', 2, 30, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(36, 'Sliders', 1, 29, 1, 4, '#', '_blank', '', '', 0),
	(37, 'Slider1', 1, 36, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(38, 'Slider2', 1, 36, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(39, 'Slider3', 1, 36, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(40, 'Slider4', 1, 36, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(41, 'Slider5', 1, 36, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(42, '&nbsp;', 1, 29, 1, 4, '#', '_blank', '', '', 0),
	(43, 'Slider6', 1, 42, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(44, 'Slider7', 1, 42, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(45, 'Slider8', 1, 42, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(46, 'Slider9', 1, 42, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(47, 'Slider10', 1, 42, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(48, 'Other Sections', 1, 29, 2, 4, '#', '_blank', '', '', 0),
	(49, 'Contacts', 1, 48, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(50, 'Blogs', 1, 48, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(51, 'Pricing', 1, 48, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(52, 'Popups / Modals', 1, 48, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(53, 'Teams', 1, 48, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(54, 'Testimonials', 1, 48, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(55, 'Features', 1, 29, 2, 4, '#', '_blank', '', '', 0),
	(56, 'Features 1-10', 1, 55, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(57, 'Features 11-20', 1, 55, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(58, 'Features 21-30', 1, 55, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(59, 'Features 31-40', 1, 55, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(60, 'Features 41-50', 1, 55, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(61, 'Pages', 0, 0, 0, 0, '#', '_blank', '', '', 2),
	(62, 'About Us', 1, 61, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(63, 'Pricing', 1, 61, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(64, 'Services', 1, 61, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(65, '', 1, 61, 0, 0, '#', '_blank', '', '', 0),
	(66, 'Portfolio', 1, 61, 0, 0, 'javascript:void(0)', '_blank', '', '', 2),
	(67, 'Portfolio 1 Column', 2, 66, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(68, 'Portfolio 2 Column', 2, 66, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(69, 'Portfolio 3 Column', 2, 66, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(70, '', 2, 66, 0, 0, '#', '_blank', '', '', 0),
	(71, 'Portfolio with Masonry', 2, 66, 0, 0, 'javascript:void(0)', '_blank', '', '', 2),
	(72, 'Portfolio with Popup', 2, 66, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(73, 'Portfolio Detail', 2, 66, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(74, 'Portfolio Detail', 1, 61, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(75, '', 1, 61, 0, 0, '#', '_blank', '', '', 0),
	(76, 'Blog', 1, 61, 0, 0, '#', '_blank', '', '', 0),
	(77, 'Blog Single', 1, 61, 0, 0, '#', '_blank', '', '', 0),
	(78, 'Contact Us', 1, 61, 0, 0, '#', '_blank', '', '', 0),
	(79, 'Shop', 0, 0, 0, 0, '#', '_blank', '', '', 2),
	(80, 'Shop Listing', 1, 79, 0, 0, 'javascript:void(0)', '_blank', '', '', 2),
	(81, 'With Sidebar', 2, 80, 0, 0, '#', '_blank', '', '', 0),
	(82, '2 Columns', 2, 80, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(83, '3 Columns', 2, 80, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(84, '', 2, 80, 0, 0, '', '', '', '', 0),
	(85, 'Without Sidebar', 2, 80, 0, 0, '#', '_blank', '', '', 0),
	(86, '3 Columns', 2, 80, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(87, '4 Columns', 2, 80, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(88, 'Shop Details', 1, 79, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(89, 'Shopping Cart', 1, 79, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(90, 'Checkout', 1, 79, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(91, 'Elements', 0, 0, 5, 0, '#', '_blank', 'images/mega-bg2.jpg', '<h3 class="text-white font-light">Create anything <br/>with our amazing <br/>Elements</h3>', 1),
	(92, '', 1, 91, 2, 4, NULL, '_blank', '', '', 0),
	(93, 'Breadcrumb', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(94, 'Buttons', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(95, 'Bootstrap Ui', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(96, 'Cards', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(97, 'Carousel', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(98, 'Counter', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(99, 'Typography', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(100, 'Dropdowns', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(101, 'Overlays', 2, 92, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(102, NULL, 1, 91, 2, 4, NULL, '_blank', '', '', 0),
	(103, 'Custom Modal', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(104, 'Forms', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(105, 'Grid', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(106, 'List media', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(107, 'Modals', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(108, 'Tables', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(109, 'Videos', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(110, 'Animations', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(111, 'Iconmind', 2, 102, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(112, NULL, 1, 91, 2, 4, NULL, '_blank', '', '', 0),
	(113, 'Notifications', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(114, 'Progressbar', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(115, 'Tabs', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(116, 'Timeline', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(117, 'Tooltip / Popover', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(118, 'Typed Text', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(119, 'Utility Classes', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(120, 'Accordions', 2, 112, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(121, 'Documentation', 0, 0, 5, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(122, 'Portfolio with Masonry1', 3, 71, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0),
	(123, 'Portfolio with Masonry2', 3, 71, 0, 0, 'javascript:void(0)', '_blank', '', '', 2),
	(124, 'Portfolio with Masonry22', 4, 123, 0, 0, 'http://www.baidu.com', '_blank', '', '', 0);
/*!40000 ALTER TABLE `wenxiaocms_topbar` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
