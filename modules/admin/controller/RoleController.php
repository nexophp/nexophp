<?php

/**
 * 角色管理
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;

class RoleController extends \core\AdminController
{
    /**
     * 角色列表页面
     * @permission 角色.管理 
     */
    public function actionIndex() {}
    
    /**
     * 获取角色列表
     * @permission 角色.管理 角色.查看
     */
    public function actionList()
    {
        $where = [];
        $name = $this->post_data['name'] ?? '';
        if ($name) {
            $where['OR'] = [
                'name[~]' => $name,
                'description[~]' => $name
            ];
        }
        $where['sys_tag'] = 'admin';
        $list = db_pager('role', '*', $where);
        json($list);
    }
    
    /**
     * 添加角色
     * @permission 角色.管理 角色.添加 
     */
    public function actionAdd()
    {
        $name = $this->post_data['name'] ?? '';
        $description = $this->post_data['description'] ?? '';
        $permissions = $this->post_data['permissions'] ?? [];
        
        if (empty($name)) {
            json_error(['msg' => lang('角色名称不能为空')]);
        }
        
        // 检查角色名是否已存在
        $exists = db_get_one('role', 'id', ['name' => $name]);
        if ($exists) {
            json_error(['msg' => lang('角色名已存在')]);
        }
        
        $data = [
            'name' => $name,
            'description' => $description,
            'permissions' => is_array($permissions) ? json_encode($permissions) : $permissions,
            'created_at' => time(),
            'updated_at' => time(),
            'sys_tag' => 'admin'

        ];
        
        $id = db_insert('role', $data);
        if ($id) {
            json_success(['msg' => lang('add_success'), 'id' => $id]);
        } else {
            json_error(['msg' => lang('add_failed')]);
        }
    }
    
    /**
     * 编辑角色
     * @permission 角色.管理 角色.修改 
     */
    public function actionEdit()
    {
        $id = $this->post_data['id'] ?? 0;
        $name = $this->post_data['name'] ?? '';
        $description = $this->post_data['description'] ?? '';
        $permissions = $this->post_data['permissions'] ?? [];
        
        if (empty($id)) {
            json_error(['msg' => lang('角色不能为空')]);
        }
        
        if (empty($name)) {
            json_error(['msg' => lang('角色名称不能为空')]);
        }
        
        // 检查角色名是否已存在（排除当前角色）
        $exists = db_get_one('role', 'id', ['name' => $name, 'id[!]' => $id]);
        if ($exists) {
            json_error(['msg' => lang('角色名已存在')]);
        }
        
        $data = [
            'name' => $name,
            'description' => $description,
            'permissions' => is_array($permissions) ? json_encode($permissions) : $permissions,
            'updated_at' => time()
        ];
        
        $result = db_update('role', $data, ['id' => $id]);
        if ($result) {
            json_success(['msg' => lang('修改成功')]);
        } else {
            json_error(['msg' => lang('修改失败')]);
        }
    }
    
    /**
     * 删除角色
     * @permission 角色.管理 角色.删除 
     */
    public function actionDelete()
    {
        $id = $this->post_data['id'] ?? 0;
        
        if (empty($id)) {
            json_error(['msg' => lang('角色不能为空')]);
        }
        
        // 检查是否有用户关联此角色
        $hasUsers = db_get_one('user_role', 'id', ['role_id' => $id]);
        if ($hasUsers) {
            json_error(['msg' => lang('角色下有用户，不能删除')]);
        }
        
        $result = db_delete('role', ['id' => $id]);
        if ($result) {
            json_success(['msg' => lang('删除成功')]);
        } else {
            json_error(['msg' => lang('删除失败')]);
        }
    }
    
    /**
     * 获取角色详情
     * @permission 角色.管理 角色.查看
     */
    public function actionDetail()
    {
        $id = $this->post_data['id'] ?? 0;
        
        if (empty($id)) {
            json_error(['msg' => lang('角色不能为空')]);
        }
        
        $role = db_get_one('role', '*', ['id' => $id]);
        if ($role) {
            $role['permissions'] = $role['permissions']??[];
            json_success(['data' => $role]);
        } else {
            json_error(['msg' => lang('角色不存在')]);
        }
    }
    
    /**
     * 获取所有角色
     * @permission 角色.管理 用户.查看
     */
    public function actionAll()
    {
        $roles = db_get('role', "*",['sys_tag'=>'admin']);
        json_success(['data' => $roles]);
    }
}