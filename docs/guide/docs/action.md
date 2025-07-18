Action

 

| 名称 | 参数 | 说明 |
| --- | --- |--- |
| index|  | 网站首页 | 
| admin.change.password | $new_pwd | 管理员修改密码 | 
| header_right | | | 管理界面头部右侧 | 
| admin_tags | $admin_tags <br> ['admin'=>['color'=>'green','title'=>lang('admin_user'),]] | 管理员tag显示 | 
| access_deny | | 无权限访问 |
| admin.welcome.index | | 后台首页 | 
| AppController.init | | 基类控制器HOOK | 
| admin.setting.form | | 后台设置表单 | 
| timezones | 数组 ['Asia/Shanghai' => '中国标准时间 (北京)'] | 设置时区 | 
| view.$module.$controller.$action | $data | 视图显示前| 
| lang | $data 有 name value file_name <br>如data中包含key为return的值时将阻止翻译 | 多语言|
| 数据库 | | |
| db_insert.$table.before |$data |写入记录前|
| db_save.$table.before |$data |写入记录前|
| db_insert.$table.after |$data 有 id data |写入记录后|
| db_save.$table.after | $data  有 id data |写入记录后|
| db_update.$table.before ||更新记录前|
| db_save.$table.before | | 更新记录前 |
| db_insert.$table.after | $data 有 id data where |更新记录后|
| db_save.$table.after | $data  有 id data where |更新记录后|
| db_del.$table.before | $where | 删除前|
| db_del.$table.after | $where | 删除前| 

数据库如不需要触发Action可用以下方法

~~~
db_insert($table, $data = [], $don_run_action = true) 
db_update($table, $data = [], $where = [], $don_run_action = true)
db_delete($table, $where = [], $don_run_action = true)
~~~