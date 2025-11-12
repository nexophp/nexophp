<?php

use modules\admin\model\UserModel;
use core\Menu;

// 设置分组（可选，默认为'admin'）
Menu::setGroup('admin');

// 添加顶级菜单
Menu::add('system', '系统管理', '', 'bi-gear', 50);

Menu::add('system-module', '模块', '/admin/module', '', 100, 'system');
Menu::add('system-setting', '设置', '/admin/setting', '', 50, 'system');
Menu::add('system-user', '用户管理', '/admin/user', '', 30, 'system');
Menu::add('system-role', '角色管理', '/admin/role', '', 20, 'system');
Menu::add('system-log', '操作日志', '/admin/log', '', 10, 'system');
/**
 * 发布资源
 */
publish_assets('admin', __DIR__);


/**
 * 添加日志
 */
function add_log($title, $content, $type = 'info')
{
    global $uid;
    db_insert('log', [
        'title' => $title,
        'user_id' => $uid,
        'ip' => get_ip(),
        'content' => $content,
        'type' => $type,
        'url' => $_SERVER['REQUEST_URI'] ?: $_SERVER['PATH_INFO'],
        'created_at' => time(),
    ]);
}

/**
 * 日志类型
 */
function get_log_types($type = '')
{
    $types = [
        'debug' => lang('调试'),
        'info' => lang('一般'),
        'error' => lang('错误'),
        'warning' => lang('警告'),
        'success' => lang('成功'),
    ];
    if ($type) {
        return $types[$type] ?? '';
    }
    return $types;
}
/**
 * 上传文件类型
 */
add_action("upload.mime", function ($mime) {
    if (is_admin()) {
        return;
    }
    $upload_mime = get_config('upload_mime');
    if ($upload_mime) {
        if (is_array($upload_mime)) {
            $upload_mime = implode(',', $upload_mime);
        }
        $allow = lib\Mime::get($upload_mime, true);
        if (!$allow || !in_array($mime, $allow)) {
            json_error(['msg' => lang('上传文件类型错误')]);
        }
    }
});
/**
 * 上传文件大小
 */
add_action("upload.size", function ($size) {
    if (is_admin()) {
        return;
    }
    $upload_size = get_config('upload_size');
    if ($upload_size) {
        $upload_size = $upload_size * 1024 * 1024;
        if ($size > $upload_size) {
            json_error(['msg' => lang('上传文件大小错误')]);
        }
    }
});

/**
 * 添加到首页 
 */
function add_to_home($title, $url)
{
    global $homepages;
    $url = str_replace("\\", "/", $url);
    $homepages[] = [
        'title' => $title,
        'url' => $url,
    ];
}

function get_user_form_tags()
{
    $list = [
        'admin' => lang('管理员'),
        'user' => lang('会员'),
        'seller' => lang('商家'),
        'store' => lang('门店'),
        'customer' => lang('客户'),
    ];
    do_action("user_form_tag", $list);
    return $list;
}

function get_user_table_tags()
{
    $list = [
        'user' => ['title' => lang('会员'), 'color' => '#4A90E2'],      // 柔和的蓝色
        'seller' => ['title' => lang('商家'), 'color' => '#FF6B6B'],    // 温暖的珊瑚红
        'store' => ['title' => lang('门店'), 'color' => '#48BB78'],     // 清新的绿色
        'admin' => ['title' => lang('管理员'), 'color' => '#805AD5'],   // 高贵的紫色
        'supplier' => ['title' => lang('供应商'), 'color' => '#F6AD55'], // 活力的橙色
    ];
    do_action("user_table_tag", $list);
    return $list;
}



/**
 * 创建用户
 * @param array $arr ['username' => '','phone' => '','email' => '','password' => '']
 * @param string $tag 
 * @return int
 */
function create_user($arr = [], $tag = 'user', $is_supper = 0, $err = false)
{
    unset($arr['tag']);
    $username = $arr['username'] ?? '';
    $phone = $arr['phone'] ?? '';
    $email = $arr['email'] ?? '';
    $password = $arr['password'] ?? '';
    $where = [];
    if ($username) {
        $where['username'] = $username;
    }
    if ($phone) {
        $where['phone'] = $phone;
    }
    if ($email) {
        $where['email'] = $email;
    }
    $res = db_get_one("user", '*', [
        'OR' => $where,
    ]);
    $id = $res['id'];
    if ($id) {
        if ($err) {
            json_error(['msg' => lang('用户名已存在')]);
        }
        if (in_array($res['tag'], ['admin', 'seller', 'store'])) {
            if ($err) {
                json_error(['msg' => lang('用户名已存在')]);
            }
            return $id;
        }
        db_update("user", ['tag' => $tag, 'is_supper' => $is_supper], ['id' => $id]);
        return $id;
    }
    $data = $where;
    if ($password) {
        $data['password'] = UserModel::model()->genPassword($password);
    }
    $data['is_supper'] = $is_supper;
    $data['tag'] = $tag;
    $data['created_at'] = time();
    $data['updated_at'] = time();
    $user_id = db_insert("user", $data);
    return $user_id;
}
/**
 * 是否登录页面
 */
function is_login_page()
{
    $uri = get_uri();
    if (in_array($uri, [
        'admin/login/index',
        'admin/login/email',
        'admin/login/phone',
        'admin/login/forgot',
    ])) {
        return true;
    }
}
/**
 * is_login_action
 */
function is_login_action()
{
    $uri = get_uri();
    if (in_array($uri, [
        'admin/login/account',
        'admin/login/send-email-code',
        'admin/login/email-login',
        'admin/login/send-phone-code',
        'admin/login/phone-login',
        'admin/login/send-reset-code',
        'admin/login/reset-password',
    ])) {
        return true;
    }
}

/**
 * 本地文件上传到数据库
 */
function local_file_to_db($url)
{
    global $user_id;
    $file = WWW_PATH . $url;
    $md5 = md5_file($file);
    $mime = mime_content_type($file);
    $size = filesize($file);
    $file_ext = pathinfo($file, PATHINFO_EXTENSION);
    $ori_name = pathinfo($file, PATHINFO_BASENAME);
    $insert = [
        'url' => $url,
        'hash' => $md5,
        'user_id' => $user_id,
        'mime' => $mime,
        'size' => $size,
        'ext' => $file_ext,
        'name' => $http_opt['name'] ?? $ori_name,
        'created_at' => date('Y-m-d H:i:s')
    ];
    if (!$url || !$md5) {
        return false;
    }
    $data = db_get_one('upload', '*', ['hash' => $md5]);
    if (!$data) {
        db_insert('upload', $insert);
    }
    $res = db_get_one('upload_user', '*', ['hash' => $md5, 'user_id' => $user_id]);
    if (!$res) {
        db_insert('upload_user', $insert);
    }
    return true;
}

/**
 * 登录COOKIE时长
 */
function get_admin_login_cookie_time()
{
    return time() + 86400 * 365 * 5;
}
