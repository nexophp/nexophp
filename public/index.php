<?php 

define('WWW_PATH', __DIR__);
define('PATH', realpath(__DIR__.'/../'));

include PATH.'/vendor/autoload.php';

IRoute::get('/',function(){
	echo 1;
});   

