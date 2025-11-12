<?php

namespace modules\admin\model;

class UserModel extends \core\AppModel
{
    protected $table = 'user';
    public function afterFind(&$data)
    {
        parent::afterFind($data);
    }
    /**
     * 生成密码
     * @param string $password 密码
     * @return string
     */
    public function genPassword($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
