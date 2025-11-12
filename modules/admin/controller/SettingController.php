<?php

/**
 * 设置
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;


class SettingController extends \core\AdminController
{
    /**
     * 设置 
     * @permission 设置.管理 设置.查看
     */
    public function actionIndex() {}
    /**
     * 获取所有配置信息
     * 注意仅限管理员
     * @permission 设置.管理 设置.查看
     */
    public function actionAjax()
    {
        $all = db_get('config', '*');
        $list = [];
        foreach ($all as $v) {
            $list[$v['title']] = $v['body'];
        }
        if(!$list){
            json_error(lang('未获取到配置信息'));
        }
        $list['menu_bg'] = $list['menu_bg'] ?? '#000000';
        $list['menu_active'] = $list['menu_active'] ?? '#0d6efd';
        $list['menu_color_active'] = $list['menu_color_active'] ?? '#FFFFFF';
        json_success(['data' => $list]);
    }
    /**
     * 保存配置信息
     * 注意仅限管理员
     * @permission 设置.管理
     */
    public function actionSave()
    {
        $input = $this->post_data;
        $data = $input['data'];
        foreach ($data as $k => $v) {
            set_config($k, $v);
        }
        json_success(['msg' => lang('保存成功')]);

    }
}
