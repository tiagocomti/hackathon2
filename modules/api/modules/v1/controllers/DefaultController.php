<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\User;
use app\modules\api\controllers\DefaultController as Controller;
use yii\web\UnauthorizedHttpException;


/**
 * @SWG\Swagger(
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(version="1.0", title="Simple API"),
 * )
 *
 */
class DefaultController extends Controller
{
    protected function justStaff(){
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if(!$user->isAdmin() && !$user->isAvaliador()){throw new UnauthorizedHttpException("Apenas admins ou avaliadores podem usar essa api");}
    }
}
