<?php
view_header(lang('模块'));
global $vue;
$url = '/admin/module/list';
$vue->data("height", "");
$vue->created(["load()"]);
$vue->method("load()", "
this.height = 'calc(100vh - " . get_config('admin_table_height') . "px)';
");
$vue->method("install(id)", "
    ajax('/admin/module/install',{id:id},function(res){
        " . vue_message() . "
        _this.load_list();
    });
");
$vue->method("uninstall(id)", "
    ajax('/admin/module/uninstall',{id:id},function(res){
        " . vue_message() . "
        _this.load_list();
    });
");
$vue->data("can_install", false);
if (has_access('admin/module/install')) {
    $vue->data("can_install", true);
}
$vue->data("can_uninstall", false);
if (has_access('admin/module/uninstall')) {
    $vue->data("can_uninstall", true);
}
?>

<?php
$html = '';
do_action('admin.module.filter', $html);
echo element("filter", [
    'data' => 'list',
    'url' => $url,
    'is_page' => true,
    'init' => true,
    [
        'type' => 'input',
        'name' => 'title',
        'attr_element' => [
            'placeholder' => lang('搜索模块'),
        ],
    ],
    [
        'type' => 'html',
        'html' => $html,
    ]
]);
?>

<?php
echo element('table', [
    ['name' => 'open', ':data' => 'list', ':height' => 'height'],
    ['name' => 'column', 'prop' => 'title', 'label' => lang('模块名称'), 'width' => ''],
    ['name' => 'column', 'prop' => 'name', 'label' => lang('目录名'), 'width' => '200', ":show-overflow-tooltip" => "true"],
    ['name' => 'column', 'prop' => 'module_info.version', 'label' => lang('模块版本'), 'width' => '130'],
    [
        'name' => 'column',
        'prop' => 'depends',
        'label' => lang('依赖'),
        'width' => '',
        'tpl' => [
            ['name' => 'html', 'html' => '<div v-html="scope.row.depends"></div>'],
        ]
    ],
    [
        'name' => 'column',
        'prop' => 'count',
        'label' => lang('操作'),
        'width' => '130',
        'tpl' => [
            ['name' => 'button', 'label' => lang('安装'), '@click' => 'install(scope.row.id)', "v-if" => "scope.row.status!=1 && can_install"],
            ['name' => 'button', 'label' => lang('卸载'), '@click' => 'uninstall(scope.row.id)', "v-else-if" => "scope.row.status==1 &&  can_uninstall", 'style' => 'color:red;'],
        ]
    ],
    ['name' => 'close'],
]);
?> 

<?php
echo element("pager", [
    'data' => 'list',
    'per_page' => 200,
    'per_page_name' => 'per_page',
    'url' => $url,
    'reload_data' => []
]);


?> 

<?php
view_footer();
?> 