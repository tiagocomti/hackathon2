<?php

namespace app\modules\api\controllers;

use app\models\User;
use app\modules\api\filters\HeaderFilter;
use yii\helpers\Url;
use yii\rest\Controller;
use Yii;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{
    protected $_post;

    public function init()
    {
        parent::init();

        Yii::$app->user->enableSession = false;
        Yii::$app->request->enableCsrfCookie = false;
        Yii::$app->request->enableCookieValidation = false;
    }

    public function actionError() {
        return new \yii\web\NotFoundHttpException("My customviews message");
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['headerFilter'] = [
            'class' => HeaderFilter::class,
            'excludedActions' => [
                "security/login",
                "user/login",
            ]
        ];
        $behaviors['contentNegotiator']['formats']['application/xml'] = Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors; // TODO: Change the autogenerated stub
    }

    public function beforeAction($action)
    {
        Yii::$app->user->logout();
        if(!empty(\Yii::$app->request->getRawBody())){
            $this->_post = json_decode(\Yii::$app->request->getRawBody(), true);
        }
        if(YII_DEBUG)
            Yii::info("Recebido : ". json_encode($this->_post). json_encode($this->request->get()), "api");
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function afterAction($action, $result)
    {
        \Yii::$app->response->getHeaders()->add("x-powered-by", "ASP net");
        Yii::info("Status retornado: ". Yii::$app->response->statusCode, "api");
        if(YII_DEBUG)
            Yii::info("Retorno : ". json_encode($result), "api");
        return parent::afterAction($action, $result); // TODO: Change the autogenerated stub
    }


}