<?php

/**
 * 后台首页，注意兼容seller store
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;

use core\Menu;

class SiteController extends \core\AdminController
{
    /**
     * 是否加载admin.css
     */
    protected $with_admin_css = true;
    /**
     * 请求前，什么都不写则不检查权限
     */
    public function before()
    {
        $this->user_tag = $this->user_info['tag'] ?: 'admin';
        Menu::setGroup($this->user_tag);
        parent::before();
    }
    public function checkPermissions() {}
    /**
     * 后台首页
     */
    public function actionIndex()
    {
        $menu = Menu::get();
        $remove = [];
        /**
         * remove_admin_menu
         * admin_menu
         * remove_{$tag}_menu
         * {$tag}_menu
         */
        $tag = $this->user_tag;
        do_action("remove_{$tag}_menu", $remove);
        do_action("{$tag}_menu", $menu);
        if ($remove && $menu) {
            foreach ($menu as $k => $v) {
                foreach ($v['children'] as $kk => $vv) {
                    if (in_array($vv['name'], $remove)) {
                        unset($menu[$k]['children'][$kk]);
                    }
                }
            }
        }
        $this->view_data['user_info'] = $this->user_info;
        $this->view_data['user_tag'] = $this->user_tag;
        $this->view_data['menu'] = $menu;
    }
}
