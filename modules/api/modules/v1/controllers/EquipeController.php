<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\Equipe;
use app\models\User;
use Da\QrCode\QrCode;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class EquipeController extends DefaultController
{

    /**
     * @SWG\Post(path="/api/v1/equipe/create",
     *     tags={"Equipe"},
     *     summary="Apenas avaliadores e admins podem criar equipe",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="equipe",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="name", type="string"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="User", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Equipe"))),
     *          ),
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *          @SWG\Schema(
     *              @SWG\Property(property="name", type="string",description="qual foi o erro, muito provavelmente será Unauthorized"),
     *              @SWG\Property(property="message", type="string", description=""),
     *              @SWG\Property(property="code", type="string", description="Esse code é para o desenvolvedor back, nao faz diferença pro front"),
     *              @SWG\Property(property="status", type="integer", description="mesmo status code http"),
     *              @SWG\Property(property="type", type="integer", description="Também pro backend, pra saber qual classe que chamou o retorno de falha"),
     *
     *          ),
     *     ),
     * )
     * @return array
     * @throws BadRequestHttpException|UnauthorizedHttpException
     */
    public function actionCreate(){
        $this->justStaff();
        $equipe = new Equipe($this->_post);
        if(!$equipe->save()){
            \Yii::error(json_encode($equipe->getErrors()), "api");
            throw new BadRequestHttpException(json_encode($equipe->getErrors()));
        }
        return ["success" => true, "users" => $equipe->getAttributes()];
    }

    /**
     * @SWG\Post(path="/api/v1/equipe/fill",
     *     tags={"Equipe"},
     *     summary="Adiciona usuarios a lista de participantes de uma determinada equipe",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="equipe",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="users", type="array", description="Users_id", @SWG\Items(type="integer")),
     *         @SWG\Property(property="equipe_id", type="string")
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="User", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Equipe"))),
     *          ),
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *          @SWG\Schema(
     *              @SWG\Property(property="name", type="string",description="qual foi o erro, muito provavelmente será Unauthorized"),
     *              @SWG\Property(property="message", type="string", description=""),
     *              @SWG\Property(property="code", type="string", description="Esse code é para o desenvolvedor back, nao faz diferença pro front"),
     *              @SWG\Property(property="status", type="integer", description="mesmo status code http"),
     *              @SWG\Property(property="type", type="integer", description="Também pro backend, pra saber qual classe que chamou o retorno de falha"),
     *
     *          ),
     *     ),
     * )
     * @return bool
     * @throws BadRequestHttpException|UnauthorizedHttpException
     */
    public function actionFill(): bool
    {
        $this->justStaff();
        $equipe = Equipe::findOne(["id" => $this->_post["equipe_id"]]);
        if(!$equipe){
            throw new BadRequestHttpException("Equipe não encontrada");
        }

        $equipe->users = $this->_post["users"];
        $equipe->save();
        return true;
    }

    /**
     * @SWG\Get(path="/api/v1/equipe/get-participantes",
     *     tags={"Equipe"},
     *     summary="Pegar todos os usuários, vai retornar um array de usuários que são do tipo participante",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="equipe",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="equipe_id", type="string"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="User", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/User"))),
     *          ),
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *          @SWG\Schema(
     *              @SWG\Property(property="name", type="string",description="qual foi o erro, muito provavelmente será Unauthorized"),
     *              @SWG\Property(property="message", type="string", description=""),
     *              @SWG\Property(property="code", type="string", description="Esse code é para o desenvolvedor back, nao faz diferença pro front"),
     *              @SWG\Property(property="status", type="integer", description="mesmo status code http"),
     *              @SWG\Property(property="type", type="integer", description="Também pro backend, pra saber qual classe que chamou o retorno de falha"),
     *
     *          ),
     *     ),
     * )
     * @return array
     * @throws BadRequestHttpException|UnauthorizedHttpException
     * @throws UnauthorizedHttpException
     * @throws BadRequestHttpException
     */
    public function actionGetParticipantes(){
        $this->justStaff();
        $equipe = Equipe::findOne(["id" => $this->_post["equipe_id"]]);
        if(!$equipe){
            throw new BadRequestHttpException("Equipe não encontrada");
        }
        return ["users"=>$equipe->participantes];
    }
    /**
     * @SWG\Delete(path="/api/v1/equipe/drain",
     *     tags={"Equipe"},
     *     summary="Remove um grupo de usuários de uma determinada equipe",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="equipe",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="users", type="array", description="Users_id", @SWG\Items(type="integer")),
     *          @SWG\Property(property="equipe_id", type="string"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="User", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Equipe"))),
     *          ),
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *          @SWG\Schema(
     *              @SWG\Property(property="name", type="string",description="qual foi o erro, muito provavelmente será Unauthorized"),
     *              @SWG\Property(property="message", type="string", description=""),
     *              @SWG\Property(property="code", type="string", description="Esse code é para o desenvolvedor back, nao faz diferença pro front"),
     *              @SWG\Property(property="status", type="integer", description="mesmo status code http"),
     *              @SWG\Property(property="type", type="integer", description="Também pro backend, pra saber qual classe que chamou o retorno de falha"),
     *
     *          ),
     *     ),
     * )
     * @return bool
     * @throws BadRequestHttpException|UnauthorizedHttpException
     */
    public function actionDrain(){
        $this->justStaff();
        $equipe = Equipe::findOne(["id" => $this->_post["equipe_id"]]);
        if(!$equipe){
            throw new BadRequestHttpException("Equipe não encontrada");
        }
        $equipe->removeParticipante($this->_post["users"]);
        return true;
    }

    /**
     * @SWG\Post(path="/api/v1/equipe/get-qrcode",
     *     tags={"Equipe"},
     *     summary="Retorna o qrcode da equipe para ser pontuada. lembre-se de usar o <img src=' e colocar dentro do src o retorno dessa API",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="x-token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         description="equipe",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="equipe_id", type="string"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *              @SWG\Property(property="status", type="integer", description="", default="200"),
     *              @SWG\Property(property="qr_code", type="string", description="", default="200"),
     *          ),
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *          @SWG\Schema(
     *              @SWG\Property(property="name", type="string",description="qual foi o erro, muito provavelmente será Unauthorized"),
     *              @SWG\Property(property="message", type="string", description=""),
     *              @SWG\Property(property="code", type="string", description="Esse code é para o desenvolvedor back, nao faz diferença pro front"),
     *              @SWG\Property(property="status", type="integer", description="mesmo status code http"),
     *              @SWG\Property(property="type", type="integer", description="Também pro backend, pra saber qual classe que chamou o retorno de falha"),
     *
     *          ),
     *     ),
     * )
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionGetQrcode(){
        $equipe = Equipe::findOne(["id" => $this->_post["equipe_id"]]);
        if(!$equipe){
            throw new BadRequestHttpException("Equipe não encontrada");
        }
        if(!\Yii::$app->cache->get("qrcode_".$equipe->id)) {
            $qrCode = (new QrCode('http://jogodacidade.com.br/equipe/pontos?id='.$equipe->id))
                ->setSize(250)
                ->setMargin(5)
                ->setForegroundColor(51, 153, 255);
            $base_64 = $qrCode->writeDataUri();
            \Yii::$app->cache->set("qrcode_".$equipe->id, $base_64, 9000);
        }else{
            $base_64 = \Yii::$app->cache->get("qrcode_".$equipe->id);
        }
        return ["base" => $base_64];
    }

}