<?php

/**
 * 上传
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;

use lib\Upload;
use lib\Str;

class UploadController extends \core\AppController
{
    protected function init()
    {
        parent::init(); 
        if (!$this->uid) {
            json_error(['msg' => lang('请先登录')]);
        }
    }
    /**
     * 上传
     */
    public function actionIndex()
    {
        $uploader = new Upload;
        Upload::$db = true;
        $uploader->user_id = $this->uid;
        $_POST['return_arr'] = 1;
        $data  = $uploader->one(); 
        //errno参数是为了wangeditor使用
        json_success(['data' => $data, 'code' => 0, 'errno' => 0,'msg'=>lang('上传成功')]);
    }
    /**
     * 上传文件hash
     */
    public function actionHash(){
        $hash = $this->post_data['hash']??'';
        if(!$hash){
            json_error(['msg'=>lang('参数异常')]);
        }
        $data = db_get_one('upload', '*', ['hash' => $hash]);
        if($data){
            $data['size_to'] = \lib\Str::size((int)$data['size']);
            $data['http_url'] = cdn() . $data['url'];
            json_success(['data'=>$data]);
        }else{
            json_error(['msg'=>lang('文件不存在')]);
        }
    }
 
}
