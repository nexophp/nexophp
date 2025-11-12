<?php

/**
 * 管理员登录
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;
use modules\admin\model\UserModel;

class LoginController extends \core\AppController
{
    /**
     * 登录页面
     */
    public function actionIndex()
    {
        //加载css
        add_css('/assets/admin/admin_login.css');
    }
    
    /**
     * 邮箱验证码登录页面
     */
    public function actionEmail()
    {
        //加载css
        add_css('/assets/admin/admin_login.css');
    }
    
    /**
     * 手机验证码登录页面
     */
    public function actionPhone()
    {
        //加载css
        add_css('/assets/admin/admin_login.css');
    }
    
    /**
     * 找回密码页面
     */
    public function actionForgot()
    {
        //加载css
        add_css('/assets/admin/admin_login.css');
    }
    
    /**
     * 账号密码登录
     */
    public function actionAccount()
    {
        $input = $this->post_data;
        $username = $input['username'];
        $password = $input['password'];
        $vali = validate(
            [
                'username' => lang('帐号'),
                'password' => lang('密码')
            ],
            $input,
            ['required' => [['username']]]
        );
        if ($vali) {
            json($vali);
        }
        $where = ['username' => $username];
        //判断帐号是否是邮箱或手机号
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $where = ['email' => $username];
        } else if(is_numeric($username)) {
            $where = ['phone' => $username];
        }
        $find = db_get('user', 'id', ['id' => 1]);
        if (!$find) {
            db_insert('user', [
                'username' => $username,
                'password' => UserModel::model()->genPassword($password),
                'tag' => 'admin',
                'created_at' => time()
            ]);
        }
        $find = db_get_one('user', '*', $where);
        if (!$find) {
            json_error(['msg' => lang('帐号不存在')]);
        }
        if(!$find['password']){
            json_error(['msg' => lang('帐号未设置密码'),'jump'=>'/admin/login/phone']);
        }
        
        if (password_verify($password, $find['password'])) {
            do_action("admin.login",$find);
            $time = get_admin_login_cookie_time();
            cookie('uid', $find['id'], $time);
            add_log('登录成功',[
                'username' => $username,
            ],'success');
            json_success(['msg' => lang('登录成功')]);
        } else {
            add_log('登录失败',[
                'username' => $username,
            ],'error');
            json_error(['msg' => lang('密码错误')]);
        }
    }
    
    /**
     * 发送邮箱验证码
     */
    public function actionSendEmailCode()
    {
        $email = $this->post_data['email'] ?? '';
        
        // 验证邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_error(['msg' => lang('邮箱格式不正确')]);
        }
        
        // 检查邮箱是否存在
        $user = db_get_one('user', '*', ['email' => $email]);
        if (!$user) {
            return json_error(['msg' => lang('该邮箱未注册')]);
        }
        
        // 生成验证码
        $code = rand(100000, 999999);
        
        // 保存验证码到缓存
        cache("login_email_{$email}", $code, 300); // 5分钟有效
        
        // 发送验证码邮件
        add_mail_template('login_email_code', "登录验证码", '登录验证码', '您的登录验证码是<b>{code}</b>，5分钟内有效，如非本人操作请忽略。');
        send_mail('login_email_code', $email, [
            'code' => $code,
        ]);
        
        return json_success(['msg' => lang('验证码已发送到邮箱')]);
    }
    
    /**
     * 邮箱验证码登录
     */
    public function actionEmailLogin()
    {
        $email = $this->post_data['email'] ?? '';
        $code = $this->post_data['code'] ?? '';
        
        // 验证邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_error(['msg' => lang('邮箱格式不正确')]);
        }
        
        // 验证验证码
        $cacheCode = cache("login_email_{$email}");
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误或已过期')]);
        }
        
        // 查找用户
        $user = db_get_one('user', '*', ['email' => $email]);
        if (!$user) {
            return json_error(['msg' => lang('该邮箱未注册')]);
        }
        do_action("admin.login",$user);
        // 登录成功，设置cookie
        $time = get_admin_login_cookie_time();
        cookie('uid', $user['id'], $time);
        
        // 清除验证码缓存
        cache("login_email_{$email}", null);
        add_log('登录成功',[
            'email' => $email,
        ],'success');
        
        return json_success(['msg' => lang('登录成功')]);
    }
    
    /**
     * 发送手机验证码
     */
    public function actionSendPhoneCode()
    {
        $phone = $this->post_data['phone'] ?? '';
        
        // 验证手机号格式
        $vali = validate(
            ['phone' => lang('手机号')],
            ['phone' => $phone],
            ['phonech' => [['phone']]]
        );
        if ($vali) {
            json($vali);
        }
        
        // 检查手机号是否存在
        $user = db_get_one('user', '*', ['phone' => $phone]);
        if (!$user) {
            return json_error(['msg' => lang('该手机号未注册')]);
        }
        
        // 生成验证码
        $code = rand(1000, 9999);
        
        // 保存验证码到缓存
        cache("login_phone_{$phone}", $code, 300); // 5分钟有效
        
        // 发送验证码短信
        add_sms_template('login_phone_code', "登录验证码", '登录验证码', '您的登录验证码是{code}，5分钟内有效，如非本人操作请忽略。');
        send_sms('login_phone_code', $phone, [
            'code' => $code,
        ]);
        
        return json_success(['msg' => lang('验证码已发送到手机')]);
    }
    
    /**
     * 手机验证码登录
     */
    public function actionPhoneLogin()
    {
        $phone = $this->post_data['phone'] ?? '';
        $code = $this->post_data['code'] ?? '';
        
        // 验证手机号格式
        $vali = validate(
            ['phone' => lang('手机号')],
            ['phone' => $phone],
            ['phonech' => [['phone']]]
        );
        if ($vali) {
            json($vali);
        } 
        // 验证验证码
        $cacheCode = cache("login_phone_{$phone}");
        if(is_local()){
            $cacheCode = '123456'; 
        }
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误或已过期')]);
        }
        
        // 查找用户
        $user = db_get_one('user', '*', ['phone' => $phone]);
        if (!$user) {
            return json_error(['msg' => lang('该手机号未注册')]);
        }
        do_action("admin.login",$user);
        // 登录成功，设置cookie
        $time = get_admin_login_cookie_time();
        cookie('uid', $user['id'], $time);
        
        // 清除验证码缓存
        cache("login_phone_{$phone}", null);

        add_log('登录成功',[
            'phone' => $phone,
        ],'success');

        
        return json_success(['msg' => lang('登录成功')]);
    }
    
    /**
     * 发送找回密码验证码
     */
    public function actionSendResetCode()
    {
        $account = $this->post_data['account'] ?? '';
        $type = $this->post_data['type'] ?? 'email'; // email或phone
        
        if ($type == 'email') {
            // 验证邮箱格式
            if (!filter_var($account, FILTER_VALIDATE_EMAIL)) {
                return json_error(['msg' => lang('邮箱格式不正确')]);
            }
            
            // 检查邮箱是否存在
            $user = db_get_one('user', '*', ['email' => $account]);
            if (!$user) {
                return json_error(['msg' => lang('该邮箱未注册')]);
            }
            
            // 生成验证码
            $code = rand(100000, 999999);
            
            // 保存验证码到缓存
            cache("reset_password_{$account}", $code, 300); // 5分钟有效
            
            // 发送验证码邮件
            add_mail_template('reset_password_code', "重置密码验证码", '重置密码验证码', '您的重置密码验证码是<b>{code}</b>，5分钟内有效，如非本人操作请忽略。');
            send_mail('reset_password_code', $account, [
                'code' => $code,
            ]);
            
            return json_success(['msg' => lang('验证码已发送到邮箱')]);
        } else {
            // 验证手机号格式
            $vali = validate(
                ['phone' => lang('手机号')],
                ['phone' => $account],
                ['phonech' => [['phone']]]
            );
            if ($vali) {
                json($vali);
            }
            
            // 检查手机号是否存在
            $user = db_get_one('user', '*', ['phone' => $account]);
            if (!$user) {
                return json_error(['msg' => lang('该手机号未注册')]);
            }
            
            // 生成验证码
            $code = rand(1000, 9999);
            
            // 保存验证码到缓存
            cache("reset_password_{$account}", $code, 300); // 5分钟有效
            
            // 发送验证码短信
            add_sms_template('reset_password_code', "重置密码验证码", '重置密码验证码', '您的重置密码验证码是{code}，5分钟内有效，如非本人操作请忽略。');
            send_sms('reset_password_code', $account, [
                'code' => $code,
            ]);
            
            return json_success(['msg' => lang('验证码已发送到手机')]);
        }
    }
    
    /**
     * 重置密码
     */
    public function actionResetPassword()
    {
        $account = $this->post_data['account'] ?? '';
        $code = $this->post_data['code'] ?? '';
        $password = $this->post_data['password'] ?? '';
        $confirm = $this->post_data['confirm'] ?? '';
        $type = $this->post_data['type'] ?? 'email'; // email或phone
        
        // 验证密码
        if (empty($password)) {
            return json_error(['msg' => lang('新密码不能为空')]);
        }
        if ($password != $confirm) {
            return json_error(['msg' => lang('两次密码输入不一致')]);
        }
        
        // 验证验证码
        $cacheCode = cache("reset_password_{$account}");
        if (empty($cacheCode) || $cacheCode != $code) {
            return json_error(['msg' => lang('验证码错误或已过期')]);
        }
        
        // 查找用户
        $where = [];
        if ($type == 'email') {
            if (!filter_var($account, FILTER_VALIDATE_EMAIL)) {
                return json_error(['msg' => lang('邮箱格式不正确')]);
            }
            $where = ['email' => $account];
        } else {
            $vali = validate(
                ['phone' => lang('手机号')],
                ['phone' => $account],
                ['phonech' => [['phone']]]
            );
            if ($vali) {
                json($vali);
            }
            $where = ['phone' => $account];
        }
        
        $user = db_get_one('user', '*', $where);
        if (!$user) {
            return json_error(['msg' => lang('用户不存在')]);
        }
        
        // 更新密码
        $data = [
            'password' => UserModel::model()->genPassword($password),
            'updated_at' => time()
        ];
        db_update('user', $data, ['id' => $user['id']]);
        
        // 清除验证码缓存
        cache("reset_password_{$account}", null);

        add_log('重置密码',[
            'account' => $account,
        ],'success');
        
        return json_success(['msg' => lang('密码重置成功，请使用新密码登录')]);
    }
}
