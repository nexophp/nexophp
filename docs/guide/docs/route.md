# 路由

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