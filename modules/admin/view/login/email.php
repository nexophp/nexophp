<?php 
view_header(lang('邮箱验证码登录'));
?>

<div class="login-wrapper">
    <div class="login-bg-overlay"></div>
    <div class="login-container">
        <div class="login-header">
            <div class="login-title"><?=lang('邮箱验证码登录')?></div>
            <div class="login-subtitle"><?=lang('请输入邮箱和验证码')?></div>
        </div>
        
        <?php 
        element\form::$model = 'form';
        echo element('form',[ 
            ['type'=>'open','model'=>'form'],
            [
                'type'=>'input',
                'name'=>'email',
                'attr'=>['required'],
                'attr_element'=>[
                    'placeholder'=>lang('邮箱'),
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
                'type'=>'html',
                'html'=>'<el-form-item>
                    <div class="d-flex">
                        <el-button 
                            type="primary" 
                            @click="login_click"
                            style="flex: 1;"
                            :loading="loading"
                        >
                           '.lang('登录').'</el-button>
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
            <?php 
            include __DIR__.'/login-footer.php';
            ?>
        </div>
    </div>
</div>

<?php 
global $vue;
$vue->data("loading", false);
$vue->data("sendingCode", false);
$vue->data("countdown", 0);
$vue->data("form", [
    'email' => '',
    'code' => ''
]);
$vue->method("login_click()","
    this.login();
");
$vue->method("code_click()","
    this.sendCode();
");
$vue->method("login()", "
    if (!this.form.email) {
        this.\$message.error('" . lang('请输入邮箱') . "');
        return;
    }
    if (!this.form.code) {
        this.\$message.error('" . lang('请输入验证码') . "');
        return;
    }
    this.loading = true;
    ajax('/admin/login/email-login', this.form, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
           setTimeout(function() {
                if(isInIframe()){
                    window.parent.location.href = '/admin/site';
                }else{
                    window.location.href = '/admin/site';
                }
           }, 1000);
        }
        _this.loading = false;
    });
");

$vue->method("sendCode()", "
    if (!this.form.email) {
        this.\$message.error('" . lang('请输入邮箱') . "');
        return;
    }
    this.sendingCode = true;
    this.form.email = this.form.email;
    ajax('/admin/login/send-email-code', this.form, function(res) {
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