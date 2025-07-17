<?php

/**
 * 框架启动后
 */

/**
 * 路由首页
 */
Route::all('/', function(){
   return Route::runController('app\site\controller\siteController', 'actionIndex');
});

