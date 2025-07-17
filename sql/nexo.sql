-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2025-07-16 17:33:40
-- 服务器版本： 5.7.26
-- PHP 版本： 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `nexo`
--

-- --------------------------------------------------------

--
-- 表的结构 `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置';

--
-- 转存表中的数据 `config`
--

INSERT INTO `config` (`id`, `title`, `body`) VALUES
(1, 'app_name', '首页网站名称'),
(2, 'timezone', 'Asia/Shanghai'),
(3, 'menu_bg', '#303031'),
(4, 'menu_active', '#787b74'),
(5, 'menu_color_active', '#f4f0f0'),
(6, 'app_ga_beian', '京公网安备11010802020134号'),
(7, 'app_beian', '京ICP备10046444号'),
(8, 'app_footer', '111');

-- --------------------------------------------------------

--
-- 表的结构 `demo_module_v1`
--

CREATE TABLE `demo_module_v1` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `demo_module_v2`
--

CREATE TABLE `demo_module_v2` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '唯一值',
  `icon` varchar(255) NOT NULL,
  `pid` int(11) DEFAULT '0' COMMENT '父id',
  `title` varchar(255) NOT NULL COMMENT '菜单名',
  `url` varchar(255) DEFAULT NULL COMMENT '路由',
  `level` int(11) DEFAULT NULL COMMENT '级别',
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `menu`
--

INSERT INTO `menu` (`id`, `name`, `icon`, `pid`, `title`, `url`, `level`, `sort`) VALUES
(11, 'module', '', 10, '模块', '/admin/module', 2, 100),
(10, 'system', 'bi-gear', 0, '系统管理', '', 1, 50),
(12, 'setting', '', 10, '设置', '/admin/setting', 2, 50),
(13, 'user', '', 10, '用户管理', '/admin/user', 2, 30),
(14, 'role', '', 10, '角色管理', '/admin/role', 2, 20);

-- --------------------------------------------------------

--
-- 表的结构 `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '唯一值',
  `path` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL COMMENT '版本',
  `title` varchar(255) NOT NULL COMMENT '插件名',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `module_info` json DEFAULT NULL COMMENT '数据',
  `level` int(11) DEFAULT NULL COMMENT '级别',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模块';

--
-- 转存表中的数据 `module`
--

INSERT INTO `module` (`id`, `name`, `path`, `version`, `title`, `status`, `module_info`, `level`, `created_at`, `updated_at`) VALUES
(1, 'demo_module', '/vendor/nexophp/demo_module/src', '1.0.2', '测试模块', 1, '{\"url\": \"https://www.google.com\", \"email\": \"68103403@qq.com\", \"title\": \"测试模块\", \"author\": \"sunkangchina\", \"version\": \"1.0.2\", \"description\": \"这是一个测试模块\"}', NULL, 1752570529, 1752655525);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL,
  `username` varchar(20) NOT NULL COMMENT '帐号',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `tag` varchar(10) NOT NULL DEFAULT 'user',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `tag`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$Qtv5otAH/v5C9.44xTmdtOhlbtj4FJqmlt516uy4cc.XrjYa/z04.', 'admin', 1752564304, NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `demo_module_v1`
--
ALTER TABLE `demo_module_v1`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `demo_module_v2`
--
ALTER TABLE `demo_module_v2`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `demo_module_v1`
--
ALTER TABLE `demo_module_v1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `demo_module_v2`
--
ALTER TABLE `demo_module_v2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
