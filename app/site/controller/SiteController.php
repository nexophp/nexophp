<?php
 
namespace app\site\controller;

use OpenApi\Attributes as OA;

#[OA\Info(title: "API", version: "1.0")]
class SiteController extends \core\AppController
{
    protected function init()
    {
        parent::init();
        add_css('/assets/site/site.css');
        
    }
    #[OA\Get(
        path: '/api/users',
        responses: [
            new OA\Response(response: 200, description: '成功'),
            new OA\Response(response: 401, description: '未授权')
        ]
    )]
    public function actionIndex() {
       

    }
}
