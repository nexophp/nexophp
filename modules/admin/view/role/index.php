<?php
// 设置页面头部
view_header(lang('角色管理'));
global $vue;
$url = '/admin/role/list';

// 初始化Vue数据
$vue->data("height", "");
$vue->data("pop_height", "");
$vue->data("dialogVisible", false);
$vue->data("form", [
    'id' => '',
    'name' => '',
    'description' => '',
    'permissions' => []
]);
$vue->data("formTitle", lang("添加角色"));
$vue->data("permissionList", []);

// 设置Vue创建时调用的方法
$vue->created(["load()", "loadPermissions()"]);

// 设置表格高度
$vue->method("load()", "
this.height = 'calc(100vh - ".get_config('admin_table_height')."px)';
this.pop_height = 'calc(100vh - 260px - ".get_config('admin_table_height')."px)';
");

// 加载权限列表
$vue->method("loadPermissions()", "
$.post('/admin/permission/list', {}, function(res) {
    if (res.code == 0) {
        this.permissionList = res.data;
    }
}.bind(this), 'json');
");

// 显示添加角色对话框
$vue->method("showAdd()", "
this.form = {
    id: '',
    name: '',
    description: '',
    permissions: []
};
this.formTitle = '".lang('添加角色')."';
this.dialogVisible = true;
");

// 显示编辑角色对话框
$vue->method("showEdit(row)", "
$.post('/admin/role/detail', {id: row.id}, function(res) { 
    if (res.code == 0) { 
        if (typeof res.data.permissions === 'string') {
            try {
                res.data.permissions = JSON.parse(res.data.permissions);
            } catch (e) {
                res.data.permissions = [];
            }
        }
        this.form = res.data;
        this.formTitle = '".lang('编辑角色')."';
        this.dialogVisible = true;
    }  
}.bind(this), 'json');
");

// 提交角色表单
$vue->method("submitForm()", "
let url = this.form.id ? '/admin/role/edit' : '/admin/role/add'; 
$.post(url, this.form, function(res) {
    ".vue_message()."
    if (res.code == 0) { 
        this.dialogVisible = false;
        this.load_list();
    } 
}.bind(this), 'json');
");

// 删除角色
$vue->method("deleteRole(row)", "
this.\$confirm('".lang('确认删除该角色吗？')."', '".lang('提示')."', {
    confirmButtonText: '".lang('确定')."',
    cancelButtonText: '".lang('取消')."',
    type: 'warning'
}).then(() => {
    ".vue_message()."
    $.post('/admin/role/delete', {id: row.id}, function(res) {
        if (res.code == 0) { 
            this.load_list();
        } 
    }.bind(this), 'json');
}).catch(() => {});
");

// 处理权限选择
$vue->method("handlePermissionCheck(row, checked)", "
if (checked) {
    if (row.paths) {
        this.form.permissions = [...new Set([...this.form.permissions, ...row.paths])];
    }
} else {
    if (row.paths) {
        this.form.permissions = this.form.permissions.filter(path => !row.paths.includes(path));
    }
}
");

// 检查权限是否选中
$vue->method("isPermissionChecked(row)", "
return row.paths && row.paths.every(path => this.form.permissions.includes(path));
");

// 定义表格行类名，用于高亮选中行
$vue->method("tableRowClassName({row, rowIndex})", "
return this.isPermissionChecked(row) ? 'selected-row' : '';
");

$vue->data("can_add",false);
$vue->data("can_edit",false);
$vue->data("can_del",false);

if(has_access('admin/role/edit')){
   $vue->data("can_edit",true);
}
if(has_access('admin/role/add')){
   $vue->data("can_add",true);
}
if(has_access('admin/role/delete')){
   $vue->data("can_del",true);
}
?>

<?php
// 渲染搜索过滤器
echo element("filter", [
    'data' => 'list',
    'url' => $url,
    'is_page' => true,
    'init' => true,
    [
        'type' => 'input',
        'name' => 'name',
        'attr_element' => [
            'placeholder' => lang('角色名称或描述'),
        ],
    ],
    [
        'type' => 'html', 
        'html' => '<el-button type="primary" v-if="can_add" @click="showAdd()">'.lang('添加角色').'</el-button>',
    ],
]);
?>
 

<?php
// 渲染角色列表表格
echo element('table', [
    ['name' => 'open', ':data' => 'list', ':height' => 'height'],
    ['name' => 'column', 'prop' => 'name', 'label' => lang('角色名称'), 'width' => ''],
    ['name' => 'column', 'prop' => 'description', 'label' => lang('角色描述'), 'width' => ''], 
    [
        'name' => 'column',
        'prop' => 'id',
        'label' => lang('操作'),
        'width' => '200',
        'tpl' => [
            ['name' => 'button', 'label' => lang('编辑'),"v-if"=>"can_edit",  '@click' => 'showEdit(scope.row)', 'type' => 'primary', 'size' => 'small'],
            ['name' => 'button', 'label' => lang('删除'),"v-if"=>"can_del",  '@click' => 'deleteRole(scope.row)', 'type' => 'danger', 'size' => 'small', 'style' => 'margin-left: 10px;'],
        ]
    ],
    ['name' => 'close'],
]);
?> 

<?php
// 渲染分页组件
echo element("pager", [
    'data' => 'list',
    'per_page' => get_config('per_page'),
    'per_page_name' => 'per_page',
    'url' => $url,
    'reload_data' => []
]);
?>

<!-- 角色表单对话框 -->
<el-dialog :title="formTitle" :visible.sync="dialogVisible" width="50%" top="20px">
    <el-form :model="form" label-width="100px">
        <el-form-item label="<?=lang('角色名称')?>">
            <el-input v-model="form.name" placeholder="<?=lang('请输入角色名称')?>"></el-input>
        </el-form-item>
        <el-form-item label="<?=lang('角色描述')?>">
            <el-input v-model="form.description" placeholder="<?=lang('请输入角色描述')?>" type="textarea" :rows="3"></el-input>
        </el-form-item>
        <el-form-item label="<?=lang('权限设置')?>">
            <el-table :height="pop_height"
                :data="permissionList.reduce((acc, module) => acc.concat(module.children || []), [])"
                style="width: 100%"
                row-key="name"
                :row-class-name="tableRowClassName">
                <el-table-column width="50">
                    <template slot-scope="scope">
                        <el-checkbox
                            :value="isPermissionChecked(scope.row)"
                            @change="handlePermissionCheck(scope.row, $event)"
                        ></el-checkbox>
                    </template>
                </el-table-column>
                <el-table-column label="<?=lang('模块')?>" width="">

                    <template slot-scope="scope">
                        {{ scope.row.group }}
                    </template>
                </el-table-column>
                <el-table-column label="<?=lang('权限')?>" width="">
                    <template slot-scope="scope">
                        {{ scope.row.permission }}
                    </template>
                </el-table-column>
                
            </el-table>
        </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false"><?=lang('取消')?></el-button>
        <el-button type="primary" @click="submitForm()"><?=lang('确定')?></el-button>
    </div>
</el-dialog>
 

<?php
// 渲染页面底部
view_footer();
?>