<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= lang('后台管理') ?></title>
    <?php
    do_action("header");
    $debug = $_GET['debug'] ?? 0;
    add_js("
        $('#logout').click(function(){ 
            layer.confirm('" . lang('确定退出登录吗？') . "', {
                title: '" . lang('确认退出') . "',
                btn: ['" . lang('确认') . "', '" . lang('取消') . "'],
                icon: 3
            }, function(index){
                window.location.href = '/admin/logout'; 
            }, function(index){
                
            }); 
        });
        
    ");
    global $vue;
    $vue->data("password", "{}");
    $vue->method("savePassword()", " 
        if(!this.password.new){
            this.\$message.error('" . lang('新密码不能为空') . "');
            return;
        }
        if(!this.password.confirm){
            this.\$message.error('" . lang('确认新密码不能为空') . "');
            return;
        }
        if(this.password.new != this.password.confirm){
            this.\$message.error('" . lang('两次新密码输入不一致') . "');
            return;
        }
        ajax('/admin/password/change',{
            old:this.password.old,
            new:this.password.new,
            confirm:this.password.confirm
        },function(res){
            " . vue_message() . "
            if(res.code == 0){ 
                _this.password = {}; 
                $('#changePasswordModal').modal('hide');
            }
        });
    ");
    $vue->data("drawer", false);
    $vue->method("bind_account()", " 
        this.drawer = true;
    ");
    ?>
</head>

<body>
    <div id="app">
        <el-drawer
            :visible.sync="drawer"
            direction="rtl"
            title="<?= lang('帐号') ?>">

            <iframe src="/admin/user-bind" style="width:100%;height:100%;border:0"></iframe>
        </el-drawer>

        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <!-- 左侧部分 -->
                <div class="d-flex align-items-center">
                    <i class="bi bi-list toggle-sidebar-btn me-3" id="toggleSidebar"></i>
                    <a class="navbar-brand" href="#"></a>
                    <?php do_action("header_left") ?>
                </div>

                <!-- 中间部分 -->
                <div class="mx-auto">
                    <?php do_action("header_center") ?>
                </div>

                <!-- 右侧部分 -->
                <div class="d-flex align-items-center">
                    <?php do_action('header_right') ?>
                    <!-- 管理员头像下拉菜单 -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= cdn() ?>/misc/img/avatar.png" style="width:27px;height:27px;" alt="<?= lang('管理员头像') ?>">
                            <?= $user_info['username'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">

                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <?php if ($user_info['password']) { ?>
                                        <?= lang('修改密码') ?>
                                    <?php } else { ?>
                                        <?= lang('设置密码') ?>
                                    <?php } ?>
                                </a>
                            </li>


                            <li><span href="#" class="dropdown-item" @click="bind_account"><?= lang('绑定帐号') ?></span></li>
                            <li><span class="dropdown-item" href="#" id='logout'><?= lang('退出') ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 左侧菜单（展开式） -->
        <div class="sidebar">
            <div class="sidebar-title"><?= lang('控制台') ?></div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link" href="#/admin/welcome" data-has-submenu="false"><i class="bi bi-house"></i> <?= lang('控制面板') ?></a>
                </li>
                <?php
                foreach ($menu as $k => $v) {
                    $children = $v['children'] ?? "";
                    if ($children) {
                        foreach ($children as $kk => $vv) {
                            $url = create_new_url($vv['url']);
                            if (!has_access($url) && !$user_info['is_supper']) {
                                unset($menu[$k]['children'][$kk]);
                            }
                        }
                    } else {
                        $url = create_new_url($v['url']);
                        if (!has_access($url)  && !$user_info['is_supper']) {
                            unset($menu[$k]);
                        }
                    }
                }
                foreach ($menu as $v) {
                    $url = $v['url'];
                    $children = $v['children'] ?? "";
                    if (!$url && !$children) {
                        continue;
                    }
                ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#<?= $v['url'] ?>" data-has-submenu="true">
                            <i class="bi <?= $v['icon'] ?? '' ?>"></i>
                            <?= lang($v['title']) ?>
                            <?php if ($children) { ?>
                                <i class="bi bi-chevron-right float-end"></i>
                            <?php } ?>
                        </a>
                        <?php if ($children) { ?>
                            <ul class="nav flex-column sub-menu">
                                <?php foreach ($children as $vv) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#<?= $vv['url'] ?>"><?= lang($vv['title']) ?>
                                            <?php if ($debug) { ?> <?= $vv['name'] ?> <?= $vv['sort'] ?> <?php } ?>
                                        </a>
                                    </li>

                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <!-- 右侧内容区域 -->
        <div class="content">
            <div class="iframe-container">
                <iframe id="contentFrame" src="/admin/welcome"></iframe>
            </div>
        </div>

        <!-- 修改密码模态框 -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">
                            <?php if ($user_info['password']) { ?>
                                <?= lang('修改密码') ?>
                            <?php } else { ?>
                                <?= lang('设置密码') ?>
                            <?php } ?>

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <?php if ($user_info['password']) { ?>
                                <div class="mb-3">
                                    <label for="oldPassword" class="form-label required"><?= lang('旧密码') ?></label>
                                    <input type="password" v-model="password.old" class="form-control" id="oldPassword">

                                </div>
                            <?php } ?>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label required"><?= lang('新密码') ?></label>
                                <input type="password" v-model="password.new" class="form-control" id="newPassword">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label required"><?= lang('确认新密码') ?></label>
                                <input type="password" v-model="password.confirm" class="form-control" id="confirmPassword">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <el-button type="" data-bs-dismiss="modal"><?= lang('取消') ?></el-button>
                        <el-button type="primary" @click="savePassword"><?= lang('保存') ?></el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    do_action("footer");
    view_footer();
    ?>

</body>

</html>