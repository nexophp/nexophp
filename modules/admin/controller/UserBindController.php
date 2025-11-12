<?php

/**
 * 绑定帐号
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;
use modules\admin\lib\UserBind;

class UserBindController extends \core\AppController
{
    use UserBind;
    
    protected function init()
    {
        parent::init();
        if (!$this->uid) {
            return json_error(['msg' => lang('请先登录')]);
        }
    }
    /**
     * 绑定 
     */
    public function actionIndex() {}

    
}
