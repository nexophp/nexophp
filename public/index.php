<?php

/**
 * 上线后改为false
 */
define('DEBUG', true);
/**
 * 加载配置
 */
include __DIR__ . '/../config.ini.php';
/**
 * 启动项目
 */
include __DIR__ . '/../vendor/nexophp/boot/boot.php';