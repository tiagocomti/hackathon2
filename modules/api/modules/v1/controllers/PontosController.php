<?php

namespace app\modules\api\modules\v1\controllers;

use app\models\Equipe;
use app\models\Pontos;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class PontosController extends DefaultController
{
    /**
     * @SWG\Post(path="/api/v1/pontos/pontuar",
     *     tags={"pontos"},
     *     summary="Pontuar uma equipe",
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
     *          @SWG\Property(property="equipe_id", type="integer"),
     *          @SWG\Property(property="observacao", type="string"),
     *          @SWG\Property(property="pontos", type="string"),
     *          @SWG\Property(property="pontos_dicas", type="string"),
     *          @SWG\Property(property="is_base", type="boolean"),
     *          @SWG\Property(property="chegada", type="string"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *             @SWG\Property(property="user", type="array", @SWG\Items(@SWG\Schema(ref = "#/definitions/Equipe"))),
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
    public function actionPontuar(){
        $this->justStaff();
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $is_base = (bool) $this->_post["is_base"];
        if(!isset($user->base) && $is_base){
            throw new BadRequestHttpException("User não tem base vinculado");
        }
        $chegada = $this->_post["chegada"];
        if($this->_post["chegada"] == ""){
            $chegada = date("H:i:s");
        }
        $model = Pontos::findOne(["base_id" => $user->base->id, "equipe_id" => $this->_post["equipe_id"]]);
        if(!$model || $this->_post["new"]){
            $model = new Pontos();
            $model->equipe_id = $this->_post["equipe_id"];
        }
        $model->is_base = $is_base;
        $model->avaliador_id = $user->id;
        $model->chegada = $chegada;
        $model->pontos = (int) $this->_post["pontos"];
        $model->pontos_dicas = (int) $this->_post["pontos_dicas"];
        $model->observacao = $this->_post["observacao"];
        if($model->save()){
            return true;
        }else{
            throw new BadRequestHttpException(json_encode($model->getErrors()));
        }
    }
    /**
     * @SWG\Get(path="/api/v1/pontos/get",
     *     tags={"pontos"},
     *     summary="Pegar todas as equipes e participantes de uma equipe",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *
     *      @SWG\Parameter(
     *         description="id da equipe",
     *         in="query",
     *         name="equipe_id",
     *         required=true,
     *         type="string",
     *         required=true,
     *         default="1"
     *     ),
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *          @SWG\Schema(
     *              @SWG\Property(property="return", type="boolean", description=""),
     *              @SWG\Property(property="participantes", type="array",  @SWG\Items(ref="#/definitions/Equipe")),
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
    public function actionGet($equipe_id){
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $equipe = Equipe::findOne($equipe_id);

        if(!$equipe){
            throw new NotFoundHttpException("Equipe nao encontrada");
        }
        if($user->isParticipante() && $equipe->id != $user->equipe->id){
            throw new ForbiddenHttpException("Essa não é sua equipe em amiguinho");
        }

        $pontos = [];
        $model = Pontos::find();
        if($user->hasBase() && ($user->isAvaliador())){
            $model->orWhere(["base_id" => $user->base->id]);
        }
        if(!$user->isParticipante() && !$user->isAdmin()){
            $model->orWhere(["avaliador_id" => $user->id]);
        }
        $model->andWhere(["equipe_id" => $equipe_id]);

        $pontuacao_total = 0;
        /** @var Pontos $ponto */
        $contador = 0;
        foreach($model->all() as $ponto){
            $pontos[$contador] = $ponto->getAttributes();
            $pontos[$contador]["avaliador"] = $ponto->avaliador->name;
            $pontos[$contador]["base"] = ($ponto->is_base)?$ponto->base->name:"Staff";
            $pontos[$contador]["tipo"] =($ponto->is_base)?"Pontuação de base":"Prévia";
            $pontuacao_total += ((int)$ponto->pontos+(int)$ponto->pontos_dicas);
            $contador ++;
        }
        return ["pontos" => $pontos,"equipe" => $equipe->name,"has_base"=> $user->hasBase(),"role"=>$user->type,"total" =>$pontuacao_total,"minha_base"=>$user->base->name] ;
    }
}