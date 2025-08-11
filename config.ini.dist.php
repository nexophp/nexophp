<?php

/**
 * 域名
 */
$config['host'] = '{host}';
/**
 * 数据库配置
 */
$config['db_host'] = '{db_host}';
$config['db_user'] = '{db_user}';
$config['db_pwd']  = '{db_pwd}';
$config['db_name'] = '{db_name}';
$config['db_port'] = '{db_port}';

/**
 * redis
 */
$config['redis']['host'] = '{redis_host}';
$config['redis']['port'] = '{redis_port}';
$config['redis']['auth'] = '{redis_auth}';

/**
 * AES 
 * echo bin2hex(random_bytes(16));
 */
$config['aes'] = [
    //32位
    'key' => '{aes_key}',
    //16位
    'iv' => '{aes_iv}',
];
/**
 * 后台列表高度
 */
$config['admin_table_height'] = 150;

/**
 * JWT
 */
$config['jwt_key'] = '{jwt_key}'; 
/**
 * JWT过期时间
 */
$config['jwt_exp_time'] = 86400*30;
/**
 * 图片处理 Gd Imagick
 * https://image.intervention.io/v3
 */
$config['image_drive'] = 'Gd';

/**
 * 每页显示条数
 */
$config['per_page'] = 20;

/**
 * 缓存
 */
$config['cache_pre'] = 'www';
/**
 * 缓存驱动 file redis
 */
$config['cache_drive'] = 'redis';
/**
 * CDN图片地址
 * 如 https://cdn.yourname.com
 */
$config['cdn'] = [ 
    
]; 
/**
 * CDN CSS JS 
 */
$config['cdn_css'] = [
     
];