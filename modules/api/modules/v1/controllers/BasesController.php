<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\Bases;
use app\models\User;
use Da\QrCode\QrCode;
use yii\debug\models\search\Base;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class BasesController extends DefaultController
{

    /**
     * @SWG\Post(path="/api/v1/bases/create",
     *     tags={"Bases"},
     *     summary="Apenas avaliadores e admins podem criar bases",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="bases",
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
     *             @SWG\Property(property="user", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Bases"))),
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
        $model = new Bases($this->_post);
        if(!$model->save()){
            \Yii::error(json_encode($model->getErrors()), "api");
            throw new BadRequestHttpException(json_encode($model->getErrors()));
        }
        return ["success" => true, "bases" => $model->getAttributes()];
    }

    /**
     * @SWG\Post(path="/api/v1/bases/fill",
     *     tags={"Bases"},
     *     summary="Adiciona usuarios a lista de participantes de uma determinada bases",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="bases",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="users", type="array", description="Users_id", @SWG\Items(type="integer")),
     *         @SWG\Property(property="base_id", type="integer")
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="User", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Bases"))),
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
        $this->justAdmin();
        $base = Bases::findOne(["id" => $this->_post["base_id"]]);
        if(!$base){
            throw new BadRequestHttpException("base não encontrada");
        }

        $base->users = $this->_post["users"];
        $base->save();
        return true;
    }

    /**
     * @SWG\Get(path="/api/v1/bases/get-avaliadores",
     *     tags={"Bases"},
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
     *         description="id da bases",
     *         in="query",
     *         name="base_id",
     *         required=true,
     *         type="string",
     *         required=true,
     *         default="0, 1, 2, 3...."
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *              @SWG\Property(property="return", type="boolean", description=""),
     *              @SWG\Property(property="participantes", type="array",  @SWG\Items(ref="#/definitions/User")),
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
    public function actionGetAvaliadores($base_id){
        $this->justStaff();
        $base = Bases::findOne(["id" => $base_id]);
        if(!$base){
            throw new BadRequestHttpException("base não encontrada");
        }
        return ["avaliadores"=>$base->avaliadores];
    }

    /**
     * @SWG\Delete(path="/api/v1/bases/drain",
     *     tags={"Bases"},
     *     summary="Remove um grupo de usuários de uma determinada bases",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="bases",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="users", type="array", description="Users_id", @SWG\Items(type="integer")),
     *          @SWG\Property(property="base_id", type="string"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="user", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Bases"))),
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
        $this->justAdmin();
        $base = Bases::findOne(["id" => $this->_post["base_id"]]);
        if(!$base){
            throw new BadRequestHttpException("Base não encontrada");
        }
        $base->removeParticipante($this->_post["users"]);
        return true;
    }

    /**
     * @SWG\Get(path="/api/v1/bases/get-all",
     *     tags={"Bases"},
     *     summary="Pegar todas as bases e participantes de uma bases",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *              @SWG\Property(property="return", type="boolean", description=""),
     *              @SWG\Property(property="participantes", type="array",  @SWG\Items(ref="#/definitions/Bases")),
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
     * @throws UnauthorizedHttpException
     */
    public function actionGetAll(){
        $this->justStaff();
        $bases = [];
        $model = Bases::find()->all();
        /**
         * @var  $chave
         * @var Bases $base
         */
        foreach($model as $chave => $base){
            $bases[$chave] = $base->getAttributes();
            $bases[$chave]["avaliadores"] = $base->avaliadores;
        }

        return ["bases"=>$bases];
    }

    /**
     * @SWG\Get(path="/api/v1/bases/get",
     *     tags={"Bases"},
     *     summary="Pegar todas as bases e participantes de uma bases",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *
     *     @SWG\Parameter(
     *         description="id da bases",
     *         in="query",
     *         name="id",
     *         required=true,
     *         type="string",
     *         required=true,
     *         default="0, 1, 2, 3...."
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *              @SWG\Property(property="return", type="boolean", description=""),
     *              @SWG\Property(property="participantes", type="array",  @SWG\Items(ref="#/definitions/Bases")),
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
    public function actionGet($id){
        $this->justStaff();
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $model = Bases::findOne(["id" => $id]);
        if(!$model){
            throw new BadRequestHttpException("Base não encontrada");
        }

        $base = $model->getAttributes();
        $base["avaliadores"] = $model->avaliadores;

        return ["base"=>$base];
    }

}