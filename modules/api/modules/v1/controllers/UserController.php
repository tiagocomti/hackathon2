<?php

namespace app\modules\api\modules\v1\controllers;
use app\models\User;
use tiagocomti\cryptbox\Cryptbox;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class UserController extends DefaultController
{
    /**
     * Creating a new user for my sis. Curl example:
     */
    public function actionCreate(){
        echo "s";exit;
        return ['asd'=>true];
    }
    /**
     * @return array
     * @fluxo Recebo user:password, valido se existe user e se a senha está correta
     * se sim, retorna status code 200 e um token de acesso.
     * Caso contrário, retorno 401 para o usuário
     */
    public function actionLogin(){
        $user = User::login($this->_post["username"],$this->_post["password"]);
        if(!$user){
            throw new UnauthorizedHttpException("Login ou senha incorretos");
        }
        return ["token" => $user->getMyToken()];
    }
}