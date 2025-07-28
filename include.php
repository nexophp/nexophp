<?php

/**
 * 框架启动后
 */

/**
 * 路由首页
 */
Route::all('/', function () { 
   return get_home_route();
});
/**
 * 路由不存在
 */
add_action("route.not_find", function () {
   if (is_local()) {
      pr(Route::$err);
   }
});
