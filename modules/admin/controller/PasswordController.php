<?php

/**
 * 密码
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;


class PasswordController extends \core\AppController
{
    /**
     * 密码修改
     */
    public function actionChange()
    {
        if (!$this->uid) {
            json_error(['msg' => lang('用户不存在')]);
        }
        $old = $this->post_data['old'] ?? '';
        $new = $this->post_data['new'] ?? '';
        $confirm = $this->post_data['confirm'] ?? '';

        if (empty($new)) {
            json_error(['msg' => lang('新密码不能为空')]);
        }
        if (empty($confirm)) {
            json_error(['msg' => lang('确认新密码不能为空')]);
        }
        if ($new != $confirm) {
            json_error(['msg' => lang('两次新密码输入不一致')]);
        }
        if ($old == $new) {
            json_error(['msg' => lang('新密码不能与旧密码相同')]);
        }
        do_action("admin.change.password", $new);
        $user = db_get_one('user', '*', ['id' => $this->uid]);
        if ($user['password'] && !$old) {
            json_error(['msg' => lang('旧密码不能为空')]);
        }
        if (!$user) {
            json_error(['msg' => lang('用户不存在')]);
        }
        if ($user['password'] && !password_verify($old, $user['password'])) {
            json_error(['msg' => lang('旧密码错误')]);
        }
        $data = [
            'password' => password_hash($new, PASSWORD_DEFAULT)
        ];
        db_update('user', $data, ['id' => $this->uid]);
        json_success(['msg' => lang('密码修改成功')]);
    }
}
