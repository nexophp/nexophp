<?php

/**
 * 域名
 */
$config['host'] = 'http://127.0.0.1:3000';
/**
 * 数据库配置
 */
$config['db_host'] = '127.0.0.1';
$config['db_user'] = 'root';
$config['db_pwd']  = '111111';
$config['db_name'] = 'nexo';
$config['db_port'] = '3306';

/**
 * redis
 */
$config['redis']['host'] = '127.0.0.1';
$config['redis']['port'] = '6379';
$config['redis']['auth'] = '';

/**
 * AES 
 * echo bin2hex(random_bytes(16));
 */
$config['aes'] = [
    //32位
    'key' => 'b720c8ea32c763e7b49f2313323e2e774a0f9dc94a366d0074fd10b4f55e2148',
    //16位
    'iv' => '86346e00b5cdceb3d42e2a1ea2d4d861',
];
/**
 * 后台列表高度
 */
$config['admin_table_height'] = 150;

/**
 * JWT
 */
$config['jwt_key'] = md5('nexo'); 
/**
 * JWT过期时间
 */
$config['jwt_exp_time'] = 86400*30;
/**
 * 图片处理 Gd Imagick
 * https://image.intervention.io/v3
 */
$config['image_drive'] = 'Gd';