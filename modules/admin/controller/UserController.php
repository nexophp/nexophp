<?php

/**
 * 用户
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;
use modules\admin\model\UserModel;

class UserController extends \core\AdminController
{
    /**
     * 列表 
     * @permission 用户.管理 用户.查看
     */
    public function actionIndex() {}

    /**
     * 分页
     * @permission 用户.管理 用户.查看
     */
    public function actionList()
    {
        $where = [];
        $title = $this->post_data['title'] ?? '';
        if ($title) {
            $where['OR'] = [
                'username[~]' => $title,
            ];
        }
        $where['ORDER'] = ['id' => 'DESC'];
        $list = db_pager('user', '*', $where);

        // 获取用户角色信息
        if (!empty($list['data'])) {
            $admin_tags = get_user_table_tags();
            do_action("admin_tags",$admin_tags);
            foreach ($list['data'] as &$user) {
                $tag = $admin_tags[$user['tag']]??'';
                $user['tag_new'] = $tag['title']??'';
                $user['tag_new_color'] = $tag['color']??'';
                if($user['id'] == 1){
                    $user['tag_new'] = lang('超级管理员');
                    $user['tag_new_color'] = '#D53F8C';
                }
                $user['roles'] = $this->getUserRoles($user['id']);
                $user['password'] = '';
            }
        }

        json($list);
    }

    /**
     * 添加用户
     * @permission 用户.管理 用户.添加
     */
    public function actionAdd()
    {
        $username = $this->post_data['username'] ?? '';
        $password = $this->post_data['password'] ?? '';
        $tag = $this->post_data['tag'] ?? 'user';
        $roles = $this->post_data['roles'] ?? [];

        if (empty($username)) {
            json_error(['msg' => lang('用户名不能为空')]);
        }

        if (empty($password)) {
            json_error(['msg' => lang('密码不能为空')]);
        }
        $username = strtolower($username);
        // 检查用户名是否已存在
        $exists = db_get_one('user', 'id', ['username' => $username]);
        if ($exists) {
            json_error(['msg' => lang('用户名已存在')]);
        }

        $data = [
            'username' => $username,
            'password' => UserModel::model()->genPassword($password),
            'tag' => $tag,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $id = db_insert('user', $data);
        if ($id) {
            // 添加用户角色关联
            if (!empty($roles)) {
                $this->updateUserRoles($id, $roles);
            }
            json_success(['msg' => lang('添加成功'), 'id' => $id]);
        } else {
            json_error(['msg' => lang('添加失败')]);
        }
    }

    /**
     * 编辑用户
     * @permission 用户.管理 用户.修改
     */
    public function actionEdit()
    {
        $id = $this->post_data['id'] ?? 0;
        $username = $this->post_data['username'] ?? '';
        $password = $this->post_data['password'] ?? '';
        $tag = $this->post_data['tag'] ?? 'user';
        $roles = $this->post_data['roles'] ?? [];

        if (empty($id)) {
            json_error(['msg' => lang('用户不能为空')]);
        }

        if (empty($username)) {
            json_error(['msg' => lang('用户名不能为空')]);
        }
        $username = strtolower($username);
        // 检查用户名是否已存在（排除当前用户）
        $exists = db_get_one('user', 'id', ['username' => $username, 'id[!]' => $id]);
        if ($exists) {
            json_error(['msg' => lang('用户名已存在')]);
        }

        $data = [
            'username' => $username,
            'tag' => $tag,
            'updated_at' => time()
        ];

        // 如果提供了新密码，则更新密码
        if (!empty($password)) {
            $data['password'] = UserModel::model()->genPassword($password);
        }

        $result = db_update('user', $data, ['id' => $id]);
        if ($result) {
            // 更新用户角色关联
            $this->updateUserRoles($id, $roles);
            json_success(['msg' => lang('更新成功')]);
        } else {
            json_error(['msg' => lang('更新失败')]);
        }
    }

    /**
     * 获取用户详情
     * @permission 用户.管理 用户.查看
     */
    public function actionDetail()
    {
        $id = $this->post_data['id'] ?? 0;

        if (empty($id)) {
            json_error(['msg' => '用户不能为空']);
        }

        $user = db_get_one('user', '*', ['id' => $id]);
        if ($user) {
            // 移除密码字段
            unset($user['password']);
            // 获取用户角色
            $user['roles'] = $this->getUserRoles($id);
            json_success(['data' => $user]);
        } else {
            json_error(['msg' => '用户不存在']);
        }
    }

    /**
     * 获取用户的角色列表
     * @param int $userId 用户ID
     * @return array 角色ID数组
     */
    protected function getUserRoles($userId)
    {
        return db_get('user_role', 'role_id', ['user_id' => $userId]);
    }

    /**
     * 更新用户的角色关联
     * @param int $userId 用户ID
     * @param array $roleIds 角色ID数组
     */
    protected function updateUserRoles($userId, $roleIds)
    {
        // 先删除原有关联
        db_delete('user_role', ['user_id' => $userId]);

        // 添加新关联
        if (!empty($roleIds)) {
            foreach ($roleIds as $roleId) {
                $data = [
                    'user_id' => $userId,
                    'role_id' => $roleId
                ];
                db_insert('user_role', $data);
            }
        }
    }
}
