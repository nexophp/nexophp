<?php 
view_header(lang('找回密码'));
?>

<div class="login-wrapper">
    <div class="login-bg-overlay"></div>
    <div class="login-container">
        <div class="login-header">
            <div class="login-title"><?=lang('找回密码')?></div>
            <div class="login-subtitle"><?=lang('请选择找回方式')?></div>
        </div>
        
        <?php 
        element\form::$model = 'form';
        echo element('form',[ 
            ['type'=>'open','model'=>'form'],
            [
                'type'=>'html',
                'html'=>'<el-form-item label="'.lang('找回方式').'">
                    <el-radio-group v-model="form.type">
                        <el-radio label="email">'.lang('邮箱验证码').'</el-radio>
                        <el-radio label="phone">'.lang('手机验证码').'</el-radio>
                    </el-radio-group>
                </el-form-item>'
            ],
            [
                'type'=>'input',
                'name'=>'account',
                'attr'=>['required'],
                'attr_element'=>[
                    'placeholder'=>lang('邮箱/手机号'),
                    'prefix-icon'=>'el-icon-message',
                    'value'=>''
                ],
            ],
            [
                'type'=>'input',
                'name'=>'code',
                'attr'=>['required'],
                'attr_element'=>[
                    'placeholder'=>lang('验证码'),
                    'prefix-icon'=>'el-icon-key',
                ],
            ],
            [
                'type'=>'input',
                'name'=>'password',
                'attr'=>['required'],
                'attr_element'=>[
                    'placeholder'=>lang('新密码'),
                    'prefix-icon'=>'el-icon-lock',
                    'show-password'=>true,
                    'type'=>'password'
                ],
            ],
            [
                'type'=>'input',
                'name'=>'confirm',
                'attr'=>['required'],
                'attr_element'=>[
                    'placeholder'=>lang('确认新密码'),
                    'prefix-icon'=>'el-icon-lock',
                    'show-password'=>true,
                    'type'=>'password'
                ],
            ],
            [
                'type'=>'html',
                'html'=>'<el-form-item>
                    <div class="d-flex">
                        <el-button 
                            type="primary" 
                            @click="login_click"
                            style="flex: 1;"
                            :loading="loading"
                        >
                           '.lang('重置密码').'</el-button>
                        <el-button 
                            type="default" 
                            @click="code_click"
                            style="margin-left: 10px;"
                            :loading="sendingCode"
                            :disabled="countdown > 0"
                        >
                           {{ countdown > 0 ? countdown + "秒后重新获取" : "'.lang('获取验证码').'" }}
                        </el-button>
                    </div>
                </el-form-item>'
            ],
            ['type'=>'close']
        ]);
        ?>
        
        <div class="login-footer">
            <a href="/admin/login"><?=lang('返回登录')?></a>
        </div>
    </div>
</div>

<?php 
global $vue;
$vue->data("loading", false);
$vue->data("sendingCode", false);
$vue->data("countdown", 0);
$vue->data("form", [
    'type' => 'email',
    'account' => '',
    'code' => '',
    'password' => '',
    'confirm' => ''
]);

$vue->method("code_click()","
    this.sendCode();
");
$vue->method("login_click()","
    this.resetPassword();
");

$vue->method("resetPassword()", "
    if (!this.form.account) {
        this.\$message.error('" . lang('请输入邮箱/手机号') . "');
        return;
    }
    if (!this.form.code) {
        this.\$message.error('" . lang('请输入验证码') . "');
        return;
    }
    if (!this.form.password) {
        this.\$message.error('" . lang('请输入新密码') . "');
        return;
    }
    if (!this.form.confirm) {
        this.\$message.error('" . lang('请确认新密码') . "');
        return;
    }
    if (this.form.password !== this.form.confirm) {
        this.\$message.error('" . lang('两次密码输入不一致') . "');
        return;
    }
    
    this.loading = true;
    ajax('/admin/login/reset-password', this.form, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
           setTimeout(function() {
                window.location.href = '/admin/login';
           }, 1000);
        }
        _this.loading = false;
    });
");

$vue->method("sendCode()", "
    if (!this.form.account) {
        this.\$message.error('" . lang('请输入邮箱/手机号') . "');
        return;
    }
    
    // 验证邮箱或手机号格式
    if (this.form.type === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.form.account)) {
            this.\$message.error('" . lang('邮箱格式不正确') . "');
            return;
        }
    } else {
        const phoneRegex = /^1\d{10}$/;
        if (!phoneRegex.test(this.form.account)) {
            this.\$message.error('" . lang('手机号格式不正确') . "');
            return;
        }
    }
    
    this.sendingCode = true;
    this.form.account = this.form.account;
    this.form.type = this.form.type;
    ajax('/admin/login/send-reset-code', this.form, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.countdown = 60;
            let timer = setInterval(function() {
                _this.countdown--;
                if (_this.countdown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        }
        _this.sendingCode = false;
    });
");
?>

<?php 
view_footer();
?>