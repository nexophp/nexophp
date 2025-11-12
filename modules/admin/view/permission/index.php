<?php
view_header(lang('权限'));
global $vue;
$url = '/admin/permission/list';
$vue->data("height", "");
$vue->created(["load()"]);
$vue->method("load()", "
this.height = 'calc(100vh - ".get_config('admin_table_height')."px)';
"); 

?>

<?php
echo element("filter", [
    'data' => 'list',
    'url' => $url,
    'is_page' => true,
    'init' => true,
]);
?>

<?php
echo element('table', [
    ['name' => 'open', ':data' => 'list', ':height' => 'height'],
    ['name' => 'column', 'prop' => 'name', 'label' => lang('permission_name') ?: '权限名称', 'width' => ''],
  
    ['name' => 'column', 'prop' => 'description', 'label' => lang('description') ?: '描述', 'width' => ''],
   
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

<?php
view_footer();
?>