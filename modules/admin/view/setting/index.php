<?php
view_header(lang('系统设置'));

global $vue;
$vue->data('form', "{}");
$vue->created(['load()']);
$vue->method("load()", "
ajax('/admin/setting/ajax',{},function(res){
  if(res.code==0){
    _this.form = res.data;
  }
});
");
$vue->method("save()", " 
ajax('/admin/setting/save',{data:this.form},function(res){
  " . vue_message() . "
  if(res.code==0){
    _this.load();
  }
});
");
$mime = [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'bmp',
    'webp',
    'mp4',
    'csv',
    'pdf',
    'doc',
    'docx',
    'ppt',
    'pptx',
    'xls',
    'xlsx',
];
do_action("mime", $mime);
global $homepages;
?>
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="bi bi-gear me-2"></i><?= lang('系统设置') ?>
        </h5>
    </div>
    <div class="card-body">
        <form>
            <div class="mb-4">
                <div class="col-md-4">
                    <label for="" class="form-label"><?= lang('网站Logo') ?></label>
                    <div>
                        <?= vue_upload_image($name = 'logo', $top = 'form') ?>
                    </div>
                </div>
            </div>
            <!-- 基础设置 -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-sliders me-2"></i><?= lang('基础设置') ?>
                </h6>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="" class="form-label"><?= lang('站点名称') ?></label>
                        <input type="text" v-model="form.app_name" class="form-control" id="" value="">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><?= lang('PC端首页') ?></label>
                        <div>
                            <el-select v-model="form.home_class" placeholder="请选择">
                                <?php foreach ($homepages as $v) { ?>
                                    <el-option label="<?= $v['title'] ?>" value="<?= $v['url'] ?>"></el-option>
                                <?php } ?>
                            </el-select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="timezone" class="form-label"><?= lang('时区设置') ?></label>
                        <div>
                            <el-select class="" id="timezone" name="timezone" v-model="form.timezone" style="width: 90%;">
                                <?php
                                // 常用时区列表
                                $timezones = [
                                    'Asia/Shanghai' => lang('中国标准时间 (北京)'),
                                ];
                                /**
                                 * 时区列表
                                 */
                                do_action("timezones", $timezones);
                                $vue->data("timezones", json_encode($timezones));
                                ?>
                                <el-option v-for="(item, index) in timezones" :value="item" :label="item"></el-option>
                            </el-select>
                        </div>
                    </div>



                </div>
            </div>


            <div class="row g-3">
                

                <div class="col-md-4">
                    <label for="" class="form-label"><?= lang('网站备案号') ?></label>
                    <input type="text" v-model="form.app_beian" class="form-control" value="">
                </div>

                <div class="col-md-4">
                    <label for="" class="form-label"><?= lang('公安备案号') ?></label>
                    <input type="text" v-model="form.app_ga_beian" class="form-control" value="">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="" class="form-label"><?= lang('联系电话') ?></label>
                    <input v-model="form.app_phone" class="form-control"></input>
                </div>
                <div class="col-md-6">
                    <label for="" class="form-label"><?= lang('网站统计代码') ?></label>
                    <textarea v-model="form.app_footer" class="form-control">{{form.app_footer}}</textarea>
                </div>
            </div>

            <div class="mb-4 mt-4">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="" class="form-label"><?= lang('上传文件类型') ?></label>
                        <div>
                            <el-select class="" multiple v-model="form.upload_mime" style="width: 90%;">
                                <?php
                                $vue->data('mimeOptions', json_encode($mime));
                                ?>
                                <el-option

                                    v-for="(item, index) in mimeOptions"
                                    :value="item">
                                    {{ item }}
                                </el-option>
                            </el-select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="" class="form-label"><?= lang('上传文件大小') ?>/MB</label>
                        <input type="text" v-model="form.upload_size" class="form-control" value="MB">
                    </div>

                    <div class="col-md-4">
                        <label for="" class="form-label"><?= lang('每页显示条数') ?></label>
                        <input type="text" v-model="form.per_page" class="form-control" value="">
                    </div>
                </div>
            </div>

            <!-- 显示设置 -->
            <div class="mb-4 mt-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-display me-2"></i><?= lang('显示设置（修改颜色需刷新页面）') ?>
                </h6>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label"><?= lang('菜单背景颜色') ?></label>
                        <input
                            type="color"
                            class="form-control form-control-color"
                            v-model="form.menu_bg"
                            title="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?= lang('菜单选中背景颜色') ?></label>
                        <input
                            type="color"
                            class="form-control form-control-color"
                            v-model="form.menu_active"
                            title="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?= lang('菜单选中文字颜色') ?></label>
                        <input
                            type="color"
                            class="form-control form-control-color"
                            v-model="form.menu_color_active"
                            title="">
                    </div>
                </div>
            </div>


            <div class="mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i><?= lang('登录方式') ?>
                </h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><?= lang('登录方式') ?></label>
                        <div>
                            <el-select class="" multiple v-model="form.login_type" style="width: 90%;">
                                <?php
                                $login_type = [
                                    //'username' => lang('用户名登录'),
                                    'email' => lang('邮箱登录'),
                                    'phone' => lang('手机号登录'),
                                ];
                                /**
                                 * 登录方式
                                 */
                                do_action("login_type", $login_type);
                                $vue->data("login_type", json_encode($login_type));
                                ?>
                                <el-option
                                    v-for="(item, index) in login_type"
                                    :value="index"
                                    :label="item">
                                    {{ item }}
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                </div>
            </div>


            <?php
            do_action('admin.setting.form');
            ?>


            <!-- 操作按钮 -->
            <div class="text-end">
                <?php if (has_access('admin/setting/save')) { ?>
                    <button type="button" class="btn btn-primary" @click="save">
                        <i class="bi bi-floppy me-1"></i><?= lang('保存设置') ?>
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>
<?php
view_footer();
?>