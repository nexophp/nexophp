<?php
view_header(lang('用户'));
global $vue;
$url = '/admin/user/list';
$vue->data("height", "");
$vue->data("dialogVisible", false);
$vue->data("form", [
    'id' => '',
    'username' => '',
    'password' => '',
    'tag' => 'user',
    'roles' => []
]);
$vue->data("formTitle", lang('添加用户'));
$vue->data("roleList", []);
$vue->created(["load()", "loadRoles()"]);
$vue->method("load()", "
this.height = 'calc(100vh - ".get_config('admin_table_height')."px)';
");
$vue->method("loadRoles()", "
$.post('/admin/role/all', {}, function(res) {
    if (res.code == 0) {
        this.roleList = res.data;
    }
}.bind(this), 'json');
");
$vue->method("showAdd()", "
this.form = {
    id: '',
    username: '',
    password: '',
    tag: 'admin',
    roles: []
};
this.formTitle = '".lang('添加用户')."';
this.dialogVisible = true;
");
$vue->method("showEdit(row)", "
$.post('/admin/user/detail', {id: row.id}, function(res) { 
    if (res.code == 0) {
        this.form = res.data;
        this.form.password = ''; 
        this.formTitle = '".lang('编辑用户')."';
        this.dialogVisible = true;
    }
}.bind(this), 'json');
");
$vue->method("submitForm()", "
let url = this.form.id ? '/admin/user/edit' : '/admin/user/add';
$.post(url, this.form, function(res) {
    ".vue_message()."
    if (res.code == 0) { 
        this.dialogVisible = false;
        this.load_list();
    }  
}.bind(this), 'json');
");
$vue->method("deleteUser(row)", "
this.\$confirm('".lang('确认删除该用户吗？')."', '".lang('提示')."', {
    confirmButtonText: '".lang('确定')."',
    cancelButtonText: '".lang('取消')."',
    type: 'warning'
}).then(() => {
    $.post('/admin/user/delete', {id: row.id}, function(res) {
        ".vue_message()."
        if (res.code == 0) { 
            this.load_list();
        } 
    }.bind(this), 'json');
}).catch(() => {});
");
$vue->data("can_add",false);
$vue->data("can_edit",false);
if(has_access('admin/user/edit')){
   $vue->data("can_edit",true);
}
if(has_access('admin/user/add')){
   $vue->data("can_add",true);
}
?>

<?php
echo element("filter", [
    'data' => 'list',
    'url' => $url,
    'is_page' => true,
    'init' => true,
    [
        'type' => 'input',
        'name' => 'title',
        'attr_element' => [
            'placeholder' => lang('搜索用户名'),
        ],
    ],
    [
        'type' => 'html', 
        'html' => '<el-button type="primary"  @click="showAdd()" v-if="can_add">'.lang('添加用户').'</el-button>',
    ],
]);
?>
 

<?php
echo element('table', [
    ['name' => 'open', ':data' => 'list', ':height' => 'height'],
    ['name' => 'column', 'prop' => 'username', 'label' => lang('用户名'), 'width' => '',
        'tpl'=>[
            'type' => 'html',
            "html"=>"
                <span > {{scope.row.username}} {{scope.row.phone}}</span>
            " 
        ]
    ],
    ['name' => 'column', 'prop' => 'tag_new', 'label' => lang('用户类型'), 'width' => '200',
        'tpl'=>[
            'type' => 'html',
            "html"=>"
                <span :style='\"color:\"+scope.row.tag_new_color'> {{scope.row.tag_new}}</span>
            " 
        ]
    ],
    [
        'name' => 'column',
        'prop' => 'roles',
        'label' => lang('角色'),
        'width' => '',
        'tpl' => [
            'type' => 'html',
            'html' => '<template v-if="scope.row.roles && scope.row.roles.length">
                <el-tag v-for="roleId in scope.row.roles" :key="roleId" size="small" style="margin-right: 5px;">
                    {{ roleList.find(r => r.id == roleId)?.name || roleId }}
                </el-tag>
            </template>
            <span v-else>-</span>'
        ]
    ],
    [
        'name' => 'column',
        'prop' => 'id',
        'label' => lang('操作'),
        'width' => '200',
        'tpl' => [
            ['name' => 'button',"v-if"=>"can_edit", 'label' => lang('编辑'), '@click' => 'showEdit(scope.row)', 'type' => 'primary', 'size' => 'small'], 
        ]
    ],
    ['name' => 'close'],
]);
?> 

<?php
echo element("pager", [
    'data' => 'list',
    'per_page' => get_config('per_page'),
    'per_page_name' => 'per_page',
    'url' => $url,
    'reload_data' => []
]);
?>

<!-- 用户表单对话框 -->
<el-dialog :title="formTitle" :visible.sync="dialogVisible" width="50%">
    <el-form :model="form" label-width="100px">
        <el-form-item label="<?=lang('用户名')?>">
            <el-input v-model="form.username" placeholder="<?=lang('请输入用户名')?>"></el-input>
        </el-form-item>
        <el-form-item v-if="!form.id" :label="form.id ? '<?=lang('新密码')?>' : '<?=lang('密码')?>'">
            <el-input v-model="form.password" placeholder="<?=lang('请输入密码')?>" type="password">
                <template slot="append" v-if="form.id"><?=lang('留空表示不修改')?></template>
            </el-input>
        </el-form-item>
        <el-form-item label="<?=lang('用户类型')?>">
            <el-select v-model="form.tag" placeholder="<?=lang('请选择用户类型')?>">
                <?php $all = get_user_form_tags();
                foreach($all as $k => $v){
                    echo "<el-option label=\"$v\" value=\"$k\"></el-option>";
                }
                ?> 
            </el-select>
        </el-form-item>
        <el-form-item label="<?=lang('角色')?>" v-if="form.tag == 'admin' && form.id!=1">
            <el-select v-model="form.roles" multiple placeholder="<?=lang('请选择角色')?>">
                <el-option 
                    v-for="role in roleList" 
                    :key="role.id" 
                    :label="role.name" 
                    :value="role.id">
                </el-option>
            </el-select>
        </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false"><?=lang('取消')?></el-button>
        <el-button type="primary" @click="submitForm()"><?=lang('确定')?></el-button>
    </div>
</el-dialog>

<?php
view_footer();
?>