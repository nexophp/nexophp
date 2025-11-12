<?php

/**
 * 退出
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;


class LogoutController extends \core\AppController
{
    /**
     * 退出
     */
    public function actionIndex()
    {
        //删除cookie
        cookie_delete('uid');
        //跳转至登录页
        redirect('/admin/login');
    }
}
