<?php
$login_type = get_config('login_type');
$only = g('only');
if($only){
    return;
}
?>
<div class="login-footer">
    <a href="/admin/login/index"><?= lang('帐号密码') ?></a> |

    <?php if ($login_type && in_array('email', $login_type)) { ?>
        <a href="/admin/login/email"><?= lang('邮箱') ?></a> |

    <?php } ?>
    <?php if ($login_type && in_array('phone', $login_type)) { ?>
        <a href="/admin/login/phone"><?= lang('手机号') ?></a> |
    <?php } ?>
    <a href="/admin/login/forgot"><?= lang('忘记密码') ?></a><br>

    <?php
    do_action("login.footer");
    ?>

</div>

<?php
do_action("login.third");
?>