<?php

/**
 * 权限管理
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;

class PermissionController extends \core\AdminController
{
    /**
     * 权限列表页面
     * @permission 权限.管理 权限.查看
     */
    public function actionIndex() {}
    
    /**
     * 获取所有权限列表
     * @permission 权限.管理 权限.查看 角色.查看
     */
    public function actionList()
    {
        $permission = new \core\Permission();
        $permissions = $permission->scanPermissions();
        
        // 将权限列表转换为树形结构
        $permissionTree = $this->buildPermissionTree($permissions);
        
        foreach ($permissionTree as $k => &$v) {
            // 1. 将"管理"权限移到数组首位
            usort($v['children'], function($a, $b) {
                if ($a['permission'] === '管理') return -1;
                if ($b['permission'] === '管理') return 1;
                return 0;
            });
            
            // 2. 处理分组显示（从第二个元素开始清空group）
            foreach ($v['children'] as $index => &$vv) {
                if ($index > 0) {
                    $vv['group'] = ' ';
                }
                 
            }
            unset($vv); // 解除引用
        }

        json_success(['data' => $permissionTree]);
    }
    
    /**
     * 获取权限详情
     * @permission 权限.管理 权限.查看
     */
    public function actionDetail()
    {
        $name = $this->post_data['name'] ?? '';
        if (empty($name)) {
            json_error(['msg' => lang('权限名称不能为空')]);
        }
        
        $permission = new \core\Permission();
        $permissions = $permission->scanPermissions();
        $permissionData = null;
        
        foreach ($permissions as $p) {
            if ($p['name'] === $name) {
                $permissionData = $p;
                break;
            }
        }
        
        if ($permissionData) {
            json_success(['data' => $permissionData]);
        } else {
            json_error(['msg' => lang('权限不存在')]);
        }
    }
    
    /**
     * 构建权限树形结构
     * @param array $permissions 权限列表
     * @return array 树形结构的权限列表
     */
    protected function buildPermissionTree($permissions)
    {
        $groups = [];
        
        // 按组分类权限
        foreach ($permissions as $permission) {
            $groupName = $permission['group'];
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = [
                    'name' => $groupName,
                    'children' => []
                ];
            }
            $groups[$groupName]['children'][] = $permission;
        }
        
        // 转换为数组
        return array_values($groups);
    }
}