<?php

namespace app\site\controller;

class SiteController extends \core\AppController
{
    protected function init(){
        parent::init();
        add_css('/assets/site/site.css');
    }
    /**
     * 获取所有权限列表
     * @permission 网站首页.首页
     */
    public function actionIndex() { 
        echo 'hello world';
    } 

}
