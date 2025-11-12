<?php

/**
 * 绑定用户邮箱、手机号
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\lib;

trait UserBind
{
    /**
     * 绑定邮箱
     */
    public function actionEmail()
    {
        $email = $this->post_data['email'];
        $code = $this->post_data['code'];
        $uid = $this->uid;

        // 验证邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_error(['msg' => lang('邮箱格式不正确')]);
        }

        // 验证验证码
        $cacheCode = cache("bind_account_{$email}");
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误')]);
        }

        // 检查邮箱是否已被其他用户绑定
        $existUser = db_get_one('user', '*', ['email' => $email, 'id[!]' => $uid]);
        if ($existUser) {
            return json_error(['msg' => lang('该邮箱已被其他用户绑定')]);
        }
        //添加日志
        add_log("用户绑定邮件", "用户{$this->uid}绑定了邮箱{$email}");
        // 更新用户邮箱
        $result = db_update('user', ['email' => $email, 'updated_at' => time(), 'email_verified_at' => time()], ['id' => $uid]);
        if ($result) {
            return json_success(['msg' => lang('邮箱绑定成功')]);
        } else {
            return json_error(['msg' => lang('邮箱绑定失败，请稍后重试')]);
        }
    }

    /**
     * 绑定手机号
     */
    public function actionPhone()
    {
        $phone = $this->post_data['phone'];
        $code = $this->post_data['code'];
        $uid = $this->uid;
        $vali = validate(
            [
                'phone' => lang('手机号'),
                'code' => lang('验证码'),
            ],
            $this->post_data,
            [
                'required' => [['phone']],
                'phonech' => [['phone']],
            ]
        );
        if ($vali) {
            json($vali);
        }

        // 获取用户当前手机号
        $user = db_get_one('user', ['phone'], ['id' => $uid]);
        $currentPhone = $user['phone'] ?? '';
        if ($currentPhone && $currentPhone == $phone) {
            return json_error(['msg' => lang('新号码不能与原号码相同')]);
        }

        // 验证验证码
        $cacheCode = cache("bind_account_{$phone}");
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误')]);
        }

        // 检查手机号是否已被其他用户绑定
        $existUser = db_get_one('user', '*', ['phone' => $phone, 'id[!]' => $uid]);
        if ($existUser) {
            return json_error(['msg' => lang('该手机号已被其他用户绑定')]);
        }
        //添加日志
        add_log("用户绑定手机号", "用户{$this->uid}绑定了手机号{$phone}");
        // 更新用户手机号
        db_update('user', ['phone' => $phone, 'updated_at' => time()], ['id' => $uid]);
        return json_success(['msg' => lang('手机号绑定成功')]);
    }

    /**
     * 发送邮箱验证码
     */
    public function actionSendMail()
    {
        $email = $this->post_data['email'] ?? '';
        $isOriginal = $this->post_data['isOriginal'] ?? false;
        $uid = $this->uid;

        // 如果是验证原邮箱，则使用用户当前绑定的邮箱
        if ($isOriginal) {
            $user = db_get_one('user', ['email'], ['id' => $uid]);
            $email = $user['email'] ?? '';

            if (empty($email)) {
                return json_error(['msg' => lang('您尚未绑定邮箱')]);
            }
        } else if (empty($email)) {
            return json_error(['msg' => lang('请输入邮箱地址')]);
        }

        // 验证邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_error(['msg' => lang('邮箱格式不正确')]);
        }
        $code = rand(100000, 999999);
        if ($isOriginal) {
            //发送邮箱验证码 
            add_mail_template('change_bind_account_email', "换绑邮箱", '换绑邮箱验证', '您正在换绑邮箱，您的验证码是  <b>{code}</b>,5分钟有效，如非本人请求请忽略。');
            cache("bind_account_{$email}", $code, 300);

            send_mail('change_bind_account_email', $email, [
                'code' =>  $code,
            ]);
        } else {
            //发送邮箱验证码 
            add_mail_template('bind_account_email', "绑定邮箱", '绑定邮箱验证', '您正在绑定邮箱，您的验证码是<b>{code}</b>,5分钟有效，如非本人请求请忽略。');
            cache("bind_account_{$email}", $code, 300);

            send_mail('bind_account_email', $email, [
                'code' =>  $code,
            ]);
        }



        return json_success(['msg' => lang('验证码已发送到邮箱')]);
    }

    /**
     * 发送手机验证码
     */
    public function actionSendPhone()
    {
        $phone = $this->post_data['phone'] ?? '';
        $isOriginal = $this->post_data['isOriginal'] ?? false;
        $uid = $this->uid;
        // 验证手机号格式
        $vali = validate(
            ['phone' => lang('手机号')],
            ['phone' => $phone],
            ['phonech' => [['phone']]]
        );
        if ($vali) {
            json($vali);
        }
        // 如果是验证原手机号，则使用用户当前绑定的手机号
        if ($isOriginal) {
            $user = db_get_one('user', ['phone'], ['id' => $uid]);
            $phone = $user['phone'] ?? '';

            if (empty($phone)) {
                return json_error(['msg' => lang('您尚未绑定手机号')]);
            }
            //发送邮箱验证码
            $code = rand(1000, 9999);
            add_sms_template('change_bind_account_phone', "换绑手机号", '换绑手机号验证', '您正在换绑手机号，您的验证码是{code},5分钟有效，如非本人请求请忽略。');
            cache("bind_account_{$phone}", $code, 300);
            send_sms('change_bind_account_phone', $phone, [
                'code' =>  $code,
            ]);
        } else {
            //发送邮箱验证码
            $code = rand(1000, 9999);
            add_sms_template('bind_account_phone', "绑定手机号", '绑定手机号验证', '您正在绑定手机号，您的验证码是{code},5分钟有效，如非本人请求请忽略。');
            cache("bind_account_{$phone}", $code, 300);
            send_sms('bind_account_phone', $phone, [
                'code' =>  $code,
            ]);
        }
        return json_success(['msg' => lang('验证码已发送到手机')]);
    }

    /**
     * 获取用户绑定信息
     */
    public function actionGetBindInfo()
    {
        $uid = $this->uid;
        $user = db_get_one('user', ['id', 'email', 'phone'], ['id' => $uid]);

        return json_success([
            'data' => [
                'email' => $user['email'] ?? '',
                'phone' => $user['phone'] ?? '',
                'hasEmail' => !empty($user['email']),
                'hasPhone' => !empty($user['phone'])
            ]
        ]);
    }

    /**
     * 验证原邮箱验证码
     */
    public function actionVerifyOriginalEmail()
    {
        $code = $this->post_data['code'] ?? '';
        $uid = $this->uid;

        if (empty($code)) {
            return json_error(['msg' => lang('请输入验证码')]);
        }

        // 获取用户当前邮箱
        $user = db_get_one('user', ['email'], ['id' => $uid]);
        $email = $user['email'] ?? '';

        if (empty($email)) {
            return json_error(['msg' => lang('您尚未绑定邮箱')]);
        }

        // 验证验证码
        $cacheCode = cache("bind_account_{$email}");
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误')]);
        }

        return json_success(['msg' => lang('验证成功'), 'data' => ['verified' => true]]);
    }

    /**
     * 验证原手机号验证码
     */
    public function actionVerifyOriginalPhone()
    {
        $code = $this->post_data['code'] ?? '';
        $uid = $this->uid;

        if (empty($code)) {
            return json_error(['msg' => lang('请输入验证码')]);
        }

        // 获取用户当前手机号
        $user = db_get_one('user', ['phone'], ['id' => $uid]);
        $currentPhone = $user['phone'] ?? '';

        if (empty($currentPhone)) {
            return json_error(['msg' => lang('您尚未绑定手机号')]);
        }

        // 验证验证码
        $cacheCode = cache("bind_account_{$currentPhone}");
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误')]);
        }

        return json_success(['msg' => lang('验证成功'), 'data' => ['verified' => true]]);
    }
}
