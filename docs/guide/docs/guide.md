# 指南

## 设置 `public` 为站点根目录

配置Nginx重写 

~~~
location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php last;
  }
}
~~~

网站即可正常访问。

## 目录说明 

- modules 官方内置应用模块
- app     用户自行开发的模块 

支持用户发布软件至`composer`  

代码结构 https://github.com/nexophp/demo_module

## 命名规则 

建议函数名以小写`_`组合，如 `get_ip()`

类名如 `SiteController` ，类中方法名以 `action` 开头，如 `actionIndex` 

除了控制器，其他类名并不强制。

## 控制器

项目的`app`目录下一般包含 `controller` 、`model` 、`view` 

以 admin为例，目录结构如下所示

~~~
app/
├── admin/
│   ├── controlle/
│   ├── model/
│   └── view/ 
~~~

控制器文件的命名规则是 `控制器名Controller.php` ，例如 `SiteController.php` 。

控制器类的命名规则是 `控制器名Controller` ，例如 `SiteController` 。

~~~
<?php

namespace app\admin\controller;
 
class SiteController extends \core\AppController
{
    public function actionIndex()
    { 
        // 什么都不写，会自动加载视图文件 view/site/index.php 或 view/index.php
        // 如果需要返回json，使用 json_success(['data'=>[],'msg'=>lang('ok')])
        // json_error(['msg'=>lang('ok')]) 
        // 或直接返回数组   

        //视图数据,在view中可直接使用$test
        $this->view_data['test'] = 'test'; 
    }
 
}
~~~ 

基础控制器

~~~
\core\AppController
~~~

后台控制器，需要登录，且有权限才能访问，默认继承`\core\AdminController` 

~~~
\core\AdminController 
~~~


类中方法名以 `action` 开头，如 `actionIndex`,即可通过请求访问，如 `/admin/site/index` 。

## AUTOLOADER

自动加载

~~~
global $autoload;
$autoload->addPsr4('yourname\\', PATH . '/yourname/');
~~~

## 多语言

默认系统开启了多语言功能，首次访问将根据浏览器来加载对应的语言。

语言包`lang/zh-cn/app.php` ，语言包的目录结构如下所示

~~~
lang/
├── zh-cn/
│   ├── app.php
│   ├── admin.php
│   └── ...
├── en-us/
│   ├── app.php
│   ├── admin.php
│   └── ...
└── ...
~~~

调用翻译

~~~
lang('hello',$name = 'app');
~~~

其中`$name` 为语言包文件，如`app.php`。默认可不传`$name`



## 数据库

配置数据库

修改 `config.dist.php` 为 `config.php`

~~~
/**
 * 数据库配置
 */
$config['db_host'] = '127.0.0.1';
$config['db_user'] = 'root';
$config['db_pwd']  = '111111';
$config['db_name'] = 'nexo';
$config['db_port'] = '3306'; 
~~~

[查看数据库操作文档](/db)
 

## 路由

- 配置Nginx重写 

~~~
location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php last;
  }
}
~~~
 

以控制器为例

~~~
<?php

namespace app\admin\controller;

use Route;

class SiteController extends \AppController
{
    public function actionIndex()
    { 
        return view('index');
    }

    public function actionTestA()
    { 
        pr(Route::getActions());
        echo Route::url("/admin/site/test-a");
        
    }
}

~~~

`actionIndex` 对应的URL是 `/admin/site/index`

`actionTestA` 对应的URL是 `/admin/site/test-a` 


~~~
view('index')
~~~

将渲染 `app/admin/views/index.php` 文件
 

## 应用商店

系统提供大量`composer`包，作为软件开发的基石，可在应用商店中安装。

## 网站备案信息输出

~~~
<?= \modules\admin\lib\Beian::output();?>
~~~


## 执行控制器方法

~~~
Route::all('/', function(){
   return Route::runController('app\site\controller\siteController', 'actionIndex');
});
~~~

## 控制器添加权限

~~~
/**
  * 权限列表页面
  * @permission 权限.管理 权限.查看
  */
public function actionList(){

} 
~~~

由`@permission` 注释 权限以`.`分隔，多个权限时用空格 

## 判断是否有权限 

~~~
has_access($str)
~~~

同 `if_access($str)`

判断是否有权限，user表id为1是超管，将会跳过权限检查。

`$str`是url，如 `admin/site/index`

当不希望判断权限时需在控制器加

~~~
/**
* 请求前，什么都不写则不检查权限
*/
public function before(){
    
}
~~~

## 添加菜单

~~~

use core\Menu;

// 设置分组（可选，默认为'admin'）
Menu::setGroup('admin');

// 添加顶级菜单
Menu::add('system', '系统管理', '', 'bi-gear', 50);

// 添加子菜单（使用'system'作为parent_name，而不是$topId）
Menu::add('module', '模块', '/admin/module', '', 100, 'system');
Menu::add('setting', '设置', '/admin/setting', '', 50, 'system');
Menu::add('user', '用户管理', '/admin/user', '', 30, 'system');
Menu::add('role', '角色管理', '/admin/role', '', 20, 'system');
 
~~~

 