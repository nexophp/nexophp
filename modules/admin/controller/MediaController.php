<?php

/**
 * 模块
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\controller;

use lib\Upload;
use modules\admin\data\UploadData;

class MediaController extends \core\AppController
{
    protected function init()
    {
        parent::init();
        if (!$this->uid) {
            json_error(['msg' => lang('请先登录')]);
        }
    }
    /**
     * 媒体列表
     */
    public function actionIndex()
    {
        $js = $_GET['js'];
        $mjs = $_GET['mjs'];
        $mime = $_GET['mime'];
        $type = 'image';
        if($mime){
            $accept = \lib\Mime::get($mime); 
            $type = 'all';
        }else{
            $accept = 'image/*';
        }
        $js_code = '';
        $mjs_code = '';
        if ($js) {
            $js_code = aes_decode($js);
        }
        if ($mjs) {
            $mjs_code = aes_decode($mjs);
        }
        $this->view_data['js'] = $js_code;
        $this->view_data['mjs'] = $mjs_code;
        $this->view_data['accept'] = $accept;
        $this->view_data['type'] = $type;
    }
    /**
     * ajax
     */
    public function actionAjax()
    {
        $where = [
            'ORDER' => ['id' => 'DESC'], 
            'user_id' => $this->user_id,
        ];
        $type = $_GET['type'];
        if($type == 'image'){
            $where['ext'] = UploadData::getAllowExt('image');
        }else{
            $where['ext'] = UploadData::getAllowExt();
        }
        $all = db_pager("upload_user", "*", $where);
        foreach($all['data'] as &$v){
            UploadData::get($v);
        }
        json($all);
    }
    /**
     * 上传
     */
    public function actionUpload()
    {
        $uploader = new Upload;
        Upload::$db = true;
        $uploader->user_id = $this->uid;
        $_POST['return_arr'] = 1;
        $data  = $uploader->one();
        //errno参数是为了wangeditor使用
        json_success(['data' => $data, 'code' => 0, 'errno' => 0, 'msg' => lang('上传成功')]);
    }
    /**
     * 删除
     */
    public function actionDelete()
    {
        $id = $this->post_data['id'];
        db_delete('upload_user', ['id' => $id,'user_id'=>$this->user_id]);
        json_success(['msg' => lang('删除成功')]);
    }
}
