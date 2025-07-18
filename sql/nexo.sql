-- Adminer 4.8.1 MySQL 8.0.29 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='配置';

TRUNCATE `config`;
INSERT INTO `config` (`id`, `title`, `body`) VALUES
(1,	'app_name',	'首页网站名称'),
(2,	'timezone',	'Asia/Shanghai'),
(3,	'menu_bg',	'#36366d'),
(4,	'menu_active',	'#7775c7'),
(5,	'menu_color_active',	'#f4f0f0'),
(6,	'app_ga_beian',	'京公网安备11010802020134号'),
(7,	'app_beian',	'京ICP备10046444号'),
(8,	'app_footer',	'111'),
(9,	'niutrans_apikey',	'428c8551c3f47a7fa96dd23ab31f6ae5');

DROP TABLE IF EXISTS `demo_module_v1`;
CREATE TABLE `demo_module_v1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE `demo_module_v1`;

DROP TABLE IF EXISTS `demo_module_v2`;
CREATE TABLE `demo_module_v2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE `demo_module_v2`;

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '唯一值',
  `icon` varchar(255) NOT NULL,
  `pid` int DEFAULT '0' COMMENT '父id',
  `title` varchar(255) NOT NULL COMMENT '菜单名',
  `url` varchar(255) DEFAULT NULL COMMENT '路由',
  `level` int DEFAULT NULL COMMENT '级别',
  `sort` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE `menu`;
INSERT INTO `menu` (`id`, `name`, `icon`, `pid`, `title`, `url`, `level`, `sort`) VALUES
(11,	'module',	'',	10,	'模块',	'/admin/module',	2,	100),
(10,	'system',	'bi-gear',	0,	'系统管理',	'',	1,	50),
(12,	'setting',	'',	10,	'设置',	'/admin/setting',	2,	50),
(13,	'user',	'',	10,	'用户管理',	'/admin/user',	2,	30),
(14,	'role',	'',	10,	'角色管理',	'/admin/role',	2,	20),
(15,	'language',	'',	10,	'语言',	'/language/site',	2,	1000);

DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '唯一值',
  `path` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL COMMENT '版本',
  `title` varchar(255) NOT NULL COMMENT '插件名',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `module_info` json DEFAULT NULL COMMENT '数据',
  `level` int DEFAULT NULL COMMENT '级别',
  `created_at` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='模块';

TRUNCATE `module`;
INSERT INTO `module` (`id`, `name`, `path`, `version`, `title`, `status`, `module_info`, `level`, `created_at`, `updated_at`) VALUES
(11,	'demo_module',	NULL,	'1.0.2',	'测试模块',	1,	'{\"url\": \"https://www.google.com\", \"email\": \"68103403@qq.com\", \"title\": \"测试模块\", \"author\": \"sunkangchina\", \"version\": \"1.0.2\", \"description\": \"这是一个测试模块\"}',	NULL,	1752801499,	1752801509),
(12,	'language',	NULL,	'1.0.0',	'多语言',	1,	'{\"url\": \"\", \"email\": \"68103403@qq.com\", \"title\": \"多语言\", \"author\": \"sunkangchina\", \"version\": \"1.0.0\", \"description\": \"翻译\"}',	NULL,	1752801499,	1752801512),
(13,	'mp',	NULL,	'1.0.0',	'公众号',	1,	'{\"url\": \"\", \"email\": \"68103403@qq.com\", \"title\": \"公众号\", \"author\": \"sunkangchina\", \"version\": \"1.0.0\", \"description\": \"\"}',	NULL,	1752801499,	1752801512);

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `description` varchar(255) DEFAULT NULL COMMENT '角色描述',
  `permissions` text COMMENT '权限列表，JSON格式',
  `created_at` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='角色表';

TRUNCATE `role`;
INSERT INTO `role` (`id`, `name`, `description`, `permissions`, `created_at`, `updated_at`) VALUES
(1,	'test',	'1',	'[\"admin\\/module\\/index\",\"admin\\/module\\/install\",\"admin\\/module\\/uninstall\",\"admin\\/module\\/list\"]',	1752758288,	1752758288);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '帐号',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `tag` varchar(10) NOT NULL DEFAULT 'user',
  `created_at` int NOT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE `user`;
INSERT INTO `user` (`id`, `username`, `password`, `tag`, `created_at`, `updated_at`) VALUES
(1,	'admin',	'$2y$10$Qtv5otAH/v5C9.44xTmdtOhlbtj4FJqmlt516uy4cc.XrjYa/z04.',	'admin',	1752564304,	NULL);

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL COMMENT '用户ID',
  `role_id` int NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户角色关联表';

TRUNCATE `user_role`;

-- 2025-07-18 02:35:27