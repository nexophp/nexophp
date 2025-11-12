<?php

/**
 * 模块
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;


class ModuleController extends \core\AdminController
{
    /**
     * 模块列表 
     * @permission 模块.管理 模块.查看
     */
    public function actionIndex()
    {
        if (is_local()) {
            $list = get_all_modules();
            $content = '';
            foreach ($list as $v) {
                $path = get_dir($v);
                $doc_file = glob($path . '/doc/*.md');
                if ($doc_file) {
                    foreach ($doc_file as $file) {
                        $content .= file_get_contents($file);
                    }
                }
            }
            $doc_file = PATH . '/docs/guide/docs/module.md';
            file_put_contents($doc_file, $content);
        }
    }
    /**
     * 安装
     * @permission 模块.管理 模块.安装
     */
    public function actionInstall()
    {
        $id = $this->post_data['id'];
        $res = db_get_one('module', '*', ['id' => $id]);
        $depends = $res['module_info']['depends'];
        if ($depends) {
            foreach ($depends as $item) {
                $new_res = db_get_one('module', '*', ['name' => $item]);
                if (!$new_res) {
                    json_error(['msg' => lang('模块不存在')]);
                }
                $this->doInstall($new_res);
            }
        }
        $this->doInstall($res);
        json_success(['msg' => lang('安装成功')]);
    }

    protected function doInstall($res)
    {
        $module_info = $res['module_info'];
        $path = $res['path'];
        $name = $res['name'];
        $version = $module_info['version'];
        if (!$version || !$path) {
            json_error(['msg' => lang('模块不存在')]);
        }
        $path = PATH . $path;
        /**
         * 安装SQL文件
         */
        $sql_file = glob($path . '/sql/*.sql');
        $install_sql_file = [];
        if ($sql_file) {
            foreach ($sql_file as $file) {
                $sql_file_name = get_name($file);
                if ($sql_file_name <= $version) {
                    $install_sql_file[] = $file;
                }
            }
        }
        if ($install_sql_file) {
            foreach ($install_sql_file as $file) {
                install_sql($file, function ($sql) {
                    db()->query($sql);
                });
            }
        }
        //version/V1_0_0::install()   
        $version = str_replace(".", "_", $version);
        $version = 'V' . $version;
        $install_class = "\modules\\$name\\version\\$version";
        if (class_exists($install_class) && method_exists($install_class, 'install')) {
            $install_class::install();
        }
        db_update('module', ['status' => 1, 'updated_at' => time()], ['id' => $res['id']]);
    }
    /**
     * 卸载
     * @permission 模块.管理 模块.卸载
     */
    public function actionUninstall()
    {
        $id = $this->post_data['id'];
        db_update('module', ['status' => 0, 'updated_at' => time()], ['id' => $id]);
        $name = db_get_one('module', 'name', ['id' => $id]);
        $version = db_get_one('module', 'version', ['id' => $id]);
        $version = str_replace(".", "_", $version);
        $version = 'V' . $version;
        $install_class = "\modules\\$name\\version\\$version";
        if (class_exists($install_class) && method_exists($install_class, 'uninstall')) {
            $install_class::uninstall();
        }
        json_success(['msg' => lang('卸载成功')]);
    }
    /**
     * 模块列表
     * @permission 模块.管理 模块.查看
     */
    public function actionList()
    {
        $list = $this->loadFromVendor();
        $local_modules = array_keys($list);
        foreach ($list as $k => $v) {
            $module = db_get_one('module', '*', ['name' => $k]);
            if (!$module) {
                db_insert('module', [
                    'name' => $k,
                    'title' => $v['title'],
                    'version' => $v['version'],
                    'level' => $v['level']?:0, 
                    'path' => $v['path'],
                    'module_info' => $v['module_info'],
                    'created_at' => time(),
                ]);
            } else {
                if ($module['version'] != $v['version']) {
                    db_update('module', [
                        'title' => $v['title'],
                        'version' => $v['version'],
                        'level' => $v['level']?:0, 
                        'path' => $v['path'],
                        'module_info' => $v['module_info'],
                        'updated_at' => time(),
                    ], ['id' => $module['id']]);
                }
            }
        }
        if ($local_modules) {
            $db_modules = db_get('module', '*', []);
            $db_modules = array_column($db_modules, 'name');
            $uninstall_modules = array_diff($db_modules, $local_modules);
            if ($uninstall_modules) {
                db_delete('module', ['name' => $uninstall_modules]);
            }
        }
        $where = [];
        $title = $this->post_data['title'] ?? '';
        if ($title) {
            $where['OR'] = [
                'name[~]' => $title,
                'title[~]' => $title,
            ];
        }
        $where['ORDER'] = ['level' => 'DESC'];
        $list = db_pager('module', '*', $where);
        foreach ($list['data'] as $k => &$v) {
            $depends = $v['module_info']['depends'];
            $str = "";
            if ($depends) {
                foreach ($depends as $item) {
                    if (has_installed_module($item)) {
                        $str .= "<span class='el-tag el-tag--success mr-3 mb-2' style='margin-right:10px'>$item</span>";
                    } else {
                        $str .= "<span title='" . lang('模块未安装') . "' class='el-tag el-tag--danger mr-3' style='margin-right:10px'>$item</span>";
                    }
                }
            }
            $v['depends'] = $str;
        } 
        json($list);
    }
    /**
     * 加载模块
     * 支持从app、modules、vendor加载
     */
    protected function loadFromVendor()
    {
        global $modules;
        $output = [];
        $list = get_all_modules();
        foreach ($list as $file) {
            $name = get_module_name($file);
            if (!$name) {
                continue;
            }
            $module_info = $modules[$name];
            $path = get_module_path($file);
            $output[$name] = [
                'title' => $module_info['title'],
                'version' => $module_info['version'],
                'level' => $module_info['level']?:0, 
                'name' => $name,
                'path' => $path,
                'module_info' => $module_info,
            ];
            $title = $module_info['title'];
            $version = $module_info['version'];
            $id = db_get_one('module', 'id', ['name' => $name]);
            if (!$id) {
                db_insert('module', [
                    'name' => $name,
                    'title' => $title,
                    'version' => $version,
                    'level' => $module_info['level']?:0, 
                    'path' => $path,
                    'module_info' => $module_info,
                    'created_at' => time(),
                ]);
            } else {
                db_update('module', [
                    'title' => $title,
                    'version' => $version,
                    'level' => $module_info['level']?:0,  
                    'module_info' => $module_info,
                ], ['id' => $id]);
            }
        }
        return $output;
    }
}
