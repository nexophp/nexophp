<?php

add_css("
    .btn-get-code {
        background-color: #0d6efd;
        color: white;
        border: none;
        transition: background-color 0.2s;
        width: 120px !important;
        min-width: 120px !important;
    }
    .el-button.is-disabled, .el-button.is-disabled:focus, .el-button.is-disabled:hover{
        background-color: #0d6efd;
        color: white;
        border: none;
        transition: background-color 0.2s;
        width: 120px !important;
        min-width: 120px !important;
    }
    .bind-card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        border: none;
        transition: transform 0.3s, box-shadow 0.3s;
        max-width: 600px;
        margin: 0 auto;
    } 
    .bind-list {
        padding: 0;
        list-style: none;
        margin-bottom: 0;
    }
    .bind-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        transition: background-color 0.2s;
    }
    .bind-item:last-child {
        border-bottom: none;
    } 
    .bind-icon {
        font-size: 1.75rem;
        margin-right: 1rem;
        color: #0d6efd;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
    }
    .bind-info {
        flex-grow: 1;
    }
    .bind-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #212529;
        font-size: 1.1rem;
    }
    .bind-status {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .bind-status.bound {
        color: #198754;
        font-weight: 500;
    }
    .bind-action {
        margin-left: 1rem;
    }
    .verification-section {
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem; 
    }
    .btn-get-code {
        background-color: #0d6efd;
        color: white;
        border: none;
        transition: background-color 0.2s;
    } 
    .modal-header, .modal-footer {
        border-color: #dee2e6;
    }
    .modal-content {
        border-radius: 0.5rem;
    }
");

view_header(lang('账号绑定'));


global $vue;
$vue->data("form", "{
    phone: '',
    phoneCode: '',
    email: '',
    emailCode: '',
    originalPhoneCode: '',
    originalEmailCode: '',
    bindInfo: {
        hasPhone: false,
        hasEmail: false,
        phone: '',
        email: ''
    },
    phoneVerified: false,
    emailVerified: false,
    originalPhoneCountdown: 0,
    phoneCodeCountdown: 0,
    originalEmailCountdown: 0,
    emailCodeCountdown: 0
}");

// Vue methods
$vue->created(['load()']);
$vue->method("load()", "
    ajax('/admin/user-bind/get-bind-info', {}, function(res) {
        if (res.code == 0) {
            _this.form.bindInfo = res.data;
        }
    });
");

$vue->method("openPhoneDialog()", "
    this.form.phone = '';
    this.form.phoneCode = '';
    this.form.originalPhoneCode = '';
    this.form.phoneVerified = false;
    new bootstrap.Modal(document.getElementById('phoneModal')).show();
");

$vue->method("openEmailDialog()", "
    this.form.email = '';
    this.form.emailCode = '';
    this.form.originalEmailCode = '';
    this.form.emailVerified = false;
    new bootstrap.Modal(document.getElementById('emailModal')).show();
");

$vue->method("bindPhone()", "
    if (!this.form.phone) {
        this.\$message.error('" . lang('请输入手机号码') . "');
        return;
    }
    if (!this.form.phoneCode) {
        this.\$message.error('" . lang('请输入验证码') . "');
        return;
    }
    if (this.form.bindInfo.hasPhone && !this.form.phoneVerified) {
        this.\$message.error('" . lang('请先验证原手机号') . "');
        return;
    }
    let params = {
        phone: this.form.phone,
        code: this.form.phoneCode
    };
    if (this.form.bindInfo.hasPhone) {
        params.originalCode = this.form.originalPhoneCode;
    }
    ajax('/admin/user-bind/phone', params, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.form.phone = '';
            _this.form.phoneCode = '';
            _this.form.originalPhoneCode = '';
            _this.form.phoneVerified = false;
            bootstrap.Modal.getInstance(document.getElementById('phoneModal')).hide();
            ajax('/admin/user-bind/get-bind-info', {}, function(res) {
                if (res.code == 0) {
                    _this.form.bindInfo = res.data;
                }
            });
        }
    });
");

$vue->method("bindEmail()", "
    if (!this.form.email) {
        this.\$message.error('" . lang('请输入邮箱地址') . "');
        return;
    }
    if (!this.form.emailCode) {
        this.\$message.error('" . lang('请输入验证码') . "');
        return;
    }
    if (this.form.bindInfo.hasEmail && !this.form.emailVerified) {
        this.\$message.error('" . lang('请先验证原邮箱') . "');
        return;
    }
    let params = {
        email: this.form.email,
        code: this.form.emailCode
    };
    if (this.form.bindInfo.hasEmail) {
        params.originalCode = this.form.originalEmailCode;
    }
    ajax('/admin/user-bind/email', params, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.form.email = '';
            _this.form.emailCode = '';
            _this.form.originalEmailCode = '';
            _this.form.emailVerified = false;
            bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
            ajax('/admin/user-bind/get-bind-info', {}, function(res) {
                if (res.code == 0) {
                    _this.form.bindInfo = res.data;
                }
            });
        }
    });
");

$vue->method("getOriginalEmailCode()", "
    ajax('/admin/user-bind/send-mail', { isOriginal: true }, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.form.originalEmailCountdown = 60;
            let timer = setInterval(function() {
                _this.form.originalEmailCountdown--;
                if (_this.form.originalEmailCountdown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        }
    });
");

$vue->method("getEmailCode()", "
    if (!this.form.email) {
        this.\$message.error('" . lang('请输入邮箱地址') . "');
        return;
    }
    ajax('/admin/user-bind/send-mail', { email: this.form.email }, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.form.emailCodeCountdown = 60;
            let timer = setInterval(function() {
                _this.form.emailCodeCountdown--;
                if (_this.form.emailCodeCountdown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        }
    });
");

$vue->method("getOriginalPhoneCode()", "
    ajax('/admin/user-bind/send-phone', { isOriginal: true }, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.form.originalPhoneCountdown = 60;
            let timer = setInterval(function() {
                _this.form.originalPhoneCountdown--;
                if (_this.form.originalPhoneCountdown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        }
    });
");

$vue->method("getPhoneCode()", "
    if (!this.form.phone) {
        this.\$message.error('" . lang('请输入手机号码') . "');
        return;
    }
    ajax('/admin/user-bind/send-phone', { phone: this.form.phone }, function(res) {
        " . vue_message() . "
        if (res.code == 0) {
            _this.form.phoneCodeCountdown = 60;
            let timer = setInterval(function() {
                _this.form.phoneCodeCountdown--;
                if (_this.form.phoneCodeCountdown <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        }
    });
");

$vue->method("verifyOriginalPhone()", "
    if (!this.form.originalPhoneCode) {
        this.\$message.error('" . lang('请输入验证码') . "');
        return;
    }
    ajax('/admin/user-bind/verify-original-phone', { code: this.form.originalPhoneCode }, function(res) {
        " . vue_message() . "
        if (res.code == 0 && res.data.verified) {
            _this.form.phoneVerified = true; 
        }
    });
");

$vue->method("verifyOriginalEmail()", "
    if (!this.form.originalEmailCode) {
        this.\$message.error('" . lang('请输入验证码') . "');
        return;
    }
    ajax('/admin/user-bind/verify-original-email', { code: this.form.originalEmailCode }, function(res) {
        " . vue_message() . "
        if (res.code == 0 && res.data.verified) {
            _this.form.emailVerified = true;
            _this.\$message.success('" . lang('验证成功，请输入新邮箱') . "');
        }
    });
");
?>

<div class="container ">
    <div class="card bind-card">
        <div class="card-body">

            <ul class="bind-list">
                <li class="bind-item">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-phone bind-icon"></i>
                        <div class="bind-info">
                            <div class="bind-title"><?= lang('手机号绑定') ?></div>
                            <div class="bind-status" :class="{'bound': form.bindInfo.hasPhone}">
                                <span v-if="form.bindInfo.hasPhone">
                                    <?= lang('已绑定') ?>: {{ form.bindInfo.phone }}
                                </span>
                                <span v-else><?= lang('未绑定') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="bind-action">
                        <el-button type="primary" class="btn btn-primary" @click="openPhoneDialog">
                            <i class="bi" :class="form.bindInfo.hasPhone ? 'bi-arrow-repeat' : 'bi-link'"></i>
                            {{ form.bindInfo.hasPhone ? '<?= lang('换绑') ?>' : '<?= lang('绑定') ?>' }}
                        </el-button>
                    </div>
                </li>
                <li class="bind-item">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-envelope bind-icon"></i>
                        <div class="bind-info">
                            <div class="bind-title"><?= lang('邮箱绑定') ?></div>
                            <div class="bind-status" :class="{'bound': form.bindInfo.hasEmail}">
                                <span v-if="form.bindInfo.hasEmail">
                                    <?= lang('已绑定') ?>: {{ form.bindInfo.email }}
                                </span>
                                <span v-else><?= lang('未绑定') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="bind-action">
                        <el-button type="primary" @click="openEmailDialog">
                            <i class="bi" :class="form.bindInfo.hasEmail ? 'bi-arrow-repeat' : 'bi-link'"></i>
                            {{ form.bindInfo.hasEmail ? '<?= lang('换绑') ?>' : '<?= lang('绑定') ?>' }}
                        </el-button>
                    </div>
                </li>

                <?php do_action("bind_account") ?>
            </ul>
        </div>
    </div>
    <div class="modal fade" id="phoneModal" tabindex="-1" aria-labelledby="phoneModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="phoneModalLabel">{{ form.bindInfo.hasPhone ? '<?= lang('换绑手机号') ?>' : '<?= lang('绑定手机号') ?>' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Verify Original Phone -->
                    <div v-if="form.bindInfo.hasPhone && !form.phoneVerified" class="verification-section">
                        <h5 class="mb-3"><?= lang('验证原手机号') ?></h5>
                        <p class="mb-3"><?= lang('当前绑定手机号') ?>: <strong>{{ form.bindInfo.phone }}</strong></p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" v-model="form.originalPhoneCode" placeholder="<?= lang('请输入验证码') ?>" required>
                            <el-button class="btn btn-get-code" @click="getOriginalPhoneCode" :disabled="form.originalPhoneCountdown > 0">
                                {{ form.originalPhoneCountdown > 0 ? form.originalPhoneCountdown + 's' : '<?= lang('获取验证码') ?>' }}
                                </el>
                        </div>
                        <div class="d-grid gap-2 ">
                            <el-button type="primary" @click="verifyOriginalPhone"><?= lang('下一步') ?></el-button>
                        </div>
                    </div>

                    <div v-if="!form.bindInfo.hasPhone || form.phoneVerified">
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label"><?= lang('新手机号码') ?></label>
                            <input type="tel" class="form-control" id="phoneNumber" v-model="form.phone" placeholder="<?= lang('请输入11位手机号') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneCode" class="form-label"><?= lang('新手机号验证码') ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="phoneCode" v-model="form.phoneCode" placeholder="<?= lang('请输入验证码') ?>" required>
                                <el-button class="btn btn-get-code" @click="getPhoneCode" :disabled="form.phoneCodeCountdown > 0">
                                    {{ form.phoneCodeCountdown > 0 ? form.phoneCodeCountdown + 's' : '<?= lang('获取验证码') ?>' }}
                                </el-button>
                            </div>
                            <div class="d-grid gap-2  mb-3 mt-4">
                                <el-button type="primary" class="btn btn-primary" @click="bindPhone"><?= lang('确定') ?></el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">{{ form.bindInfo.hasEmail ? '<?= lang('换绑邮箱') ?>' : '<?= lang('绑定邮箱') ?>' }}</h5>
                    <div type="primary" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></div>
                </div>
                <div class="modal-body">

                    <div v-if="form.bindInfo.hasEmail && !form.emailVerified" class="verification-section">
                        <h5 class="mb-3"><?= lang('验证原邮箱') ?></h5>
                        <p class="mb-3"><?= lang('当前绑定邮箱') ?>: <strong>{{ form.bindInfo.email }}</strong></p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" v-model="form.originalEmailCode" placeholder="<?= lang('请输入验证码') ?>" required>
                            <el-button class="btn btn-get-code" @click="getOriginalEmailCode" :disabled="form.originalEmailCountdown > 0">
                                {{ form.originalEmailCountdown > 0 ? form.originalEmailCountdown + 's' : '<?= lang('获取验证码') ?>' }}
                            </el-button>
                        </div>
                        <div class="d-grid gap-2 ">
                            <el-button type="primary" class="btn btn-primary" @click="verifyOriginalEmail"><?= lang('下一步') ?></el-button>
                        </div>
                    </div>

                    <div v-if="!form.bindInfo.hasEmail || form.emailVerified">
                        <div class="mb-3">
                            <label for="emailAddress" class="form-label"><?= lang('新邮箱地址') ?></label>
                            <input type="email" class="form-control" id="emailAddress" v-model="form.email" placeholder="<?= lang('请输入邮箱地址') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailCode" class="form-label"><?= lang('新邮箱验证码') ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="emailCode" v-model="form.emailCode" placeholder="<?= lang('请输入验证码') ?>" required>
                                <el-button class="btn btn-get-code" @click="getEmailCode" :disabled="form.emailCodeCountdown > 0">
                                    {{ form.emailCodeCountdown > 0 ? form.emailCodeCountdown + 's' : '<?= lang('获取验证码') ?>' }}
                                </el-button>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <el-button type="primary" class="btn btn-primary" @click="bindEmail"><?= lang('确认更换邮箱') ?></el-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view_footer(); ?>