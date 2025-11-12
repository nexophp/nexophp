<?php
view_header(lang('操作日志'));
global $vue;
$url = '/admin/log/list';
$vue->data("height", "");
$vue->data("logTypes", []);
$vue->data("detailVisible", false);
$vue->data("currentLog", null);
$vue->created(["load()"]);
$vue->method("load()", "
this.height = 'calc(100vh - ".get_config('admin_table_height')."px)';
"); 
$vue->method("showDetail(row)", "
this.currentLog = row;
this.detailVisible = true;
");
$vue->method("deleteLog(row)", "
this.\$confirm('".lang('确认删除该日志吗？')."', '".lang('提示')."', {
    confirmButtonText: '".lang('确定')."',
    cancelButtonText: '".lang('取消')."',
    type: 'warning'
}).then(() => {
    $.post('/admin/log/delete', {id: row.id}, function(res) {
        ".vue_message()."
        if (res.code == 0) { 
            this.load_list();
        } 
    }.bind(this), 'json');
}).catch(() => {});
");
$vue->method("clearLogs()", "
this.\$confirm('".lang('确认清空所有日志吗？此操作不可恢复！')."', '".lang('警告')."', {
    confirmButtonText: '".lang('确定')."',
    cancelButtonText: '".lang('取消')."',
    type: 'warning'
}).then(() => {
    $.post('/admin/log/clear', {}, function(res) {
        ".vue_message()."
        if (res.code == 0) { 
            this.load_list();
        } 
    }.bind(this), 'json');
}).catch(() => {});
");
$vue->data("can_delete", false);
if(has_access('admin/log/delete')){
   $vue->data("can_delete", true);
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
            'placeholder' => lang('搜索标题或内容'),
        ],
    ],
    [
        'type' => 'select',
        'name' => 'type',
        'attr_element' => [
            'placeholder' => lang('选择日志类型'),
            'clearable' => true,
        ],
        'value'=>$select_type,
        'attr_element'=>["style"=>"width:130px;"],
    ],
    [
        'type' => 'date-range',
        'name' => ['start_date', 'end_date'],
        'attr_element' => [
            'placeholder' => [lang('开始日期'), lang('结束日期')],
            'value-format' => 'yyyy-MM-dd',
        ],
    ],
    [
        'type' => 'html', 
        'html' => '<el-button type="danger" @click="clearLogs()" v-if="can_delete">'.lang('清空日志').'</el-button>',
    ],
]);
?>

<?php
echo element('table', [
    ['name' => 'open', ':data' => 'list', ':height' => 'height'],
    ['name' => 'column', 'prop' => 'title', 'label' => lang('标题'), 'width' => '200'],
    ['name' => 'column', 'prop' => 'content', 'label' => lang('内容'), 'width' => '',
        'tpl' => [
            'type' => 'html',
            'html' => '<div style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ scope.row.content }}</div>'
        ]
    ],
    ['name' => 'column', 'prop' => 'type', 'label' => lang('类型'), 'width' => '100',
        'tpl'=>[
            'type' => 'html',
            'html' => '<div :class="scope.row.type">{{ scope.row.type_label }}</div>'
        ]
    ],
    ['name' => 'column', 'prop' => 'username', 'label' => lang('用户'), 'width' => '120'],
    ['name' => 'column', 'prop' => 'ip', 'label' => 'IP', 'width' => '200'],
    //['name' => 'column', 'prop' => 'url', 'label' => lang('URL'), 'width' => ''],
    ['name' => 'column', 'prop' => 'created_time', 'label' => lang('创建时间'), 'width' => '180'],
    [
        'name' => 'column',
        'prop' => 'id',
        'label' => lang('操作'),
        'width' => '220',
        'tpl' => [
            ['name' => 'button', 'label' => lang('详情'), '@click' => 'showDetail(scope.row)', 'type' => 'primary', 'size' => 'small'],
            ['name' => 'button', 'v-if' => 'can_delete', 'label' => lang('删除'), '@click' => 'deleteLog(scope.row)', 'type' => 'danger', 'size' => 'small'], 
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

<!-- 日志详情对话框 -->
<el-dialog :title="'日志详情 - ' + (currentLog ? currentLog.title : '')" :visible.sync="detailVisible" width="50%">
    <div v-if="currentLog">
        <div class="mb-3">
            <strong><?=lang('标题')?>:</strong> {{ currentLog.title }}
        </div>
        <div class="mb-3">
            <strong><?=lang('内容')?>:</strong>
            <pre style="white-space: pre-wrap; word-break: break-all;">{{ currentLog.content }}</pre>
        </div>
        <div class="mb-3">
            <strong><?=lang('类型')?>:</strong> {{ currentLog.type }}
        </div>
        <div class="mb-3">
            <strong><?=lang('用户')?>:</strong> {{ currentLog.username }}
        </div>
        <div class="mb-3">
            <strong>IP:</strong> {{ currentLog.ip }}
        </div>
        <div class="mb-3">
            <strong><?=lang('创建时间')?>:</strong> {{ currentLog.created_time }}
        </div>
    </div>
</el-dialog>

<?php
view_footer();
?>