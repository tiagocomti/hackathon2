<?php

namespace app\controllers;

use app\models\Equipe;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actions(): array
    {
        \Yii::setAlias("@app/v1/controllers", "/usr/local/www/hackathon/modules/api/modules/v1/controllers" );
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        return [
            'docs' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                'restUrl' => Url::to(['site/json-schema']),
            ],
            'json-schema' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                // Тhe list of directories that contains the swagger annotations.
                'scanDir' => [
                    \Yii::getAlias('@app/controllers'),
                    \Yii::getAlias('@app/v1/controllers'),
                    \Yii::getAlias('@app/models'),
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionGetAllQrcodes(){
        $this->layout = false;
        $array_equipes = [];
        $equipes = Equipe::find()->all();
        /** @var Equipe $equipe */
        foreach ($equipes as $equipe) {
            $array_equipes[] = ["id" => $equipe->id,"name" => $equipe->name,"base_64" => $equipe->getQrcode()];
        }
        return $this->render('get-all-qrcodes',["equipes" => $array_equipes]);
    }
}
