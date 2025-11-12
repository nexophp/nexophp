<?php
view_header(lang('登录'));

?>


<div class="login-wrapper">
    <div class="login-bg-overlay"></div>
    <div class="login-container">
        <div class="login-header">
            <div class="login-title"><?= lang('登录') ?></div>
            <div class="login-subtitle"><?= lang('请输入账号密码') ?></div>
        </div>

        <?php
        element\form::$model = 'form';
        echo element('form', [
            ['type' => 'open', 'model' => 'form'],
            [
                'type' => 'input',
                'name' => 'username',
                'attr' => ['required'],
                'attr_element' => [
                    'placeholder' => lang('账号|邮箱|手机号'),
                    'prefix-icon' => 'el-icon-user',
                    'value' => ''
                ],
            ],
            [
                'type' => 'input',
                'name' => 'password',
                'attr' => ['required'],
                'attr_element' => [
                    'placeholder' => lang('密码'),
                    'prefix-icon' => 'el-icon-lock',
                    'show-password' => true,
                    'type' => 'password'
                ],
            ],
            [
                'type' => 'html',
                'html' => '<el-form-item>
                    <el-button 
                        type="primary" 
                        @click="login_click"
                        style="width: 100%;"
                        :loading="loading"
                    >
                       ' . lang('登录') . '

                    </el-button>
                </el-form-item>'
            ],
            ['type' => 'close']
        ]);
        ?>


        <?php
        include __DIR__ . '/login-footer.php';
        ?>
    </div>
</div>

<?php
global $vue;
$vue->data("loading", false);
$vue->data("form", [
    'username' => '',
    'password' => ''
]);
$vue->method("login_click()", "
    this.login();
");
$vue->method("login()", "
    this.loading = true;
    ajax('/admin/login/account',this.form,function(res){
        if(res.jump){
            if(isInIframe()){
                window.parent.location.href = res.jump;
                return;
            }
            window.location.href = res.jump;
            return;
        }
        " . vue_message() . "
        if(res.code == 0){
           setTimeout(function(){
                if(isInIframe()){
                    window.parent.location.href = '/admin/site';
                }else{
                    window.location.href = '/admin/site';
                }
           },1000);
        }
        _this.loading = false;
    });
");
?>

<?php
view_footer();
?>