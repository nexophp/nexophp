CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='配置';
 

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `type` varchar(20) DEFAULT NULL COMMENT '类型',
  `ip` varchar(20) DEFAULT NULL COMMENT 'IP',
  `user_id` bigint(20) DEFAULT NULL COMMENT '用户ID',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='日志';


CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '唯一值',
  `icon` varchar(255) NOT NULL,
  `pid` int(11) DEFAULT '0' COMMENT '父id',
  `title` varchar(255) NOT NULL COMMENT '菜单名',
  `url` varchar(255) DEFAULT NULL COMMENT '路由',
  `level` int(11) DEFAULT NULL COMMENT '级别',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '唯一值',
  `path` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL COMMENT '版本',
  `title` varchar(255) NOT NULL COMMENT '插件名',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `module_info` json DEFAULT NULL COMMENT '数据',
  `level` int(11) DEFAULT NULL COMMENT '级别',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='模块';


CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `description` varchar(255) DEFAULT NULL COMMENT '角色描述',
  `permissions` text COMMENT '权限列表，JSON格式',
  `sys_tag` varchar(20) DEFAULT 'admin' COMMENT '系统标签',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';


CREATE TABLE `upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `url` varchar(255) NOT NULL COMMENT 'URL',
  `hash` varchar(255) NOT NULL COMMENT '唯一值',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `mime` varchar(255) NOT NULL COMMENT '类型',
  `size` decimal(20,2) NOT NULL COMMENT '大小',
  `ext` varchar(10) NOT NULL COMMENT '后缀',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='上传文件';

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '帐号',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `tag` varchar(10) NOT NULL DEFAULT 'user',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_verified_at` int(11) DEFAULT NULL,
  `is_supper` tinyint(1) NOT NULL DEFAULT '0' COMMENT '',
  `phone` varchar(20) DEFAULT NULL,
  `phone_verified_at` int(11) DEFAULT NULL,
  `sys_tag` varchar(20) DEFAULT 'admin' COMMENT '系统标签',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `nickname` varchar(20) DEFAULT NULL COMMENT '昵称', 

  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户角色关联表';



CREATE TABLE `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT '模板代码',
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `content` text NOT NULL COMMENT '邮件内容',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='邮件模板';


CREATE TABLE `sms_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drive_type` varchar(50) NOT NULL DEFAULT '' COMMENT '短信驱动',
  `code` varchar(50) NOT NULL COMMENT '模板代码',
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `subject` varchar(255) DEFAULT NULL COMMENT '短信标题',
  `content` text COMMENT '短信内容',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='短信模板';

CREATE TABLE `cache_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(255), 
  `data` json DEFAULT NULL COMMENT 'JSON数据',  
  `expire` int DEFAULT NULL COMMENT '过期时间',
  `created_at` int NOT NULL COMMENT '创建时间',
  `updated_at` int DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
 

CREATE TABLE `user_openid` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int  NULL COMMENT '用户ID',
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  `unionid` varchar(255)  NULL COMMENT 'unionid',
  `type` varchar(255) DEFAULT 'weixin' COMMENT '类型',

  `created_at` int NOT NULL COMMENT '创建时间',
  `updated_at` int DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户openid';


CREATE TABLE `user_info`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int  NULL COMMENT '用户ID', 
  `field` varchar(255)  NULL COMMENT '字段',
  `value` blob  NULL COMMENT '值',  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户信息';


CREATE TABLE `data_info`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `node_id` int  NULL COMMENT '节点ID',
  `node_type` varchar(255)  NULL COMMENT '节点类型',
  `field` varchar(255)  NULL COMMENT '字段',
  `value` blob  NULL COMMENT '值',  
  `user_id` int  NULL COMMENT '用户ID', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='对其他主表的扩展字段';

CREATE TABLE `user_login`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int  NULL COMMENT '用户ID',
  `token` varchar(2000)  NULL COMMENT 'token', 
  `secret` varchar(255)  NULL COMMENT 'secret',
  `ip` varchar(255)  NULL COMMENT 'IP',
  `device` varchar(255)  NULL COMMENT '设备', 
  `created_at` int NOT NULL COMMENT '创建时间', 
  `last_time` int  NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户登录记录';


CREATE TABLE `upload_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `url` varchar(255) NOT NULL COMMENT 'URL',
  `hash` varchar(255) NOT NULL COMMENT '唯一值',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `mime` varchar(255) NOT NULL COMMENT '类型',
  `size` decimal(20,2) NOT NULL COMMENT '大小',
  `ext` varchar(10) NOT NULL COMMENT '后缀',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户上传文件';