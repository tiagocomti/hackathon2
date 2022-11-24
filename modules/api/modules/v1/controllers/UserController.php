<?php

namespace app\modules\api\modules\v1\controllers;
use app\helpers\Password;
use app\helpers\Strings;
use app\models\Tokens;
use app\models\User;
use app\modules\api\filters\HeaderFilter;
use tiagocomti\cryptbox\Cryptbox;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class UserController extends DefaultController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['headerFilter'] = [
            'class' => HeaderFilter::class,
            'excludedActions' => [
                "security/login",
                "user/login",
                "user/health-check",
                "user/create",
            ]
        ];
        return $behaviors; // TODO: Change the autogenerated stub
    }

    /**
     * @SWG\Post(path="/api/v1/user/create",
     *     tags={"User"},
     *     summary="Cadastrar algum usuário na plataforma, lembre-se que só admin pode criar usuário do tipo admin e só admin ou avaliador pode criar usuário do tipo Participante e participando NUNCA pode criar nada :)",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="Dados do usuário",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="email", type="string"),
     *          @SWG\Property(property="name", type="string"),
     *          @SWG\Property(property="username", type="string"),
     *          @SWG\Property(property="phone", type="string"),
     *          @SWG\Property(property="type", type="string", example="TYPE_PARTICIPANTE = 'part'; TYPE_AVALIADOR = 'avali'"),
     *          @SWG\Property(property="password_hash", type="boolean"),
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
     */
    public function actionCreate(){
        $new_user = new User($this->_post);
        $new_user->phone = Strings::sanitizationPhone($new_user->phone);
        $new_user->password_hash = (Password::hash($new_user-> password_hash))??Password::hash(Password::generate(12));
        if(!$new_user->save()){
            \Yii::error(json_encode($new_user->getErrors()), "api");
            throw new BadRequestHttpException(json_encode($new_user->getErrors()));
        }
        return ["success" => true, "users" => $new_user->getAttributes(null, ["password_hash","created_at","updated_at","last_login_at"])];
    }

    /**
     * @SWG\Post(path="/api/v1/user/login",
     *     tags={"Security"},
     *     summary="Faça o login do usuário e será retornado informações desse usuário, como token para acessar outras apis. Para ambientes web, recomenda-se utilizar o localstorage para armazenar esse token, salve o token com uma criptografia assimetrica. Para ambientes web, salve numa keychain/keystore. [Recomendado] Utilize a chamada /security/get-public-key para receber uma public key e criptografar a senha antes de ser enviada e ative o campo encrypt",
     *     @SWG\Parameter(
     *         description="usuário, formato e-mail ou username",
     *         in="body",
     *         name="body",
     *         required=true,
     *         @SWG\Schema(
     *          @SWG\Property(property="username", type="string", description="E-mail que o cara cadastrou na plataforma"),
     *          @SWG\Property(property="password", type="string", description="Senha do caboclo"),
     *       )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "User collection response",
     *         @SWG\Header(header="x-next", @SWG\Schema(type="string"), description="A link to the next page of responses", type="string"),
     *          @SWG\Schema(
     *              @SWG\Property(property="user", type="object",description="asdasd", ref = "#/definitions/User"),
     *              @SWG\Property(property="token", type="integer", description=""),
     *              @SWG\Property(property="equipe_id", type="integer", description=""),
     *          ),
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "Fail to login",
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
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
     * @throws \SodiumException
     */
    public function actionLogin(): array
    {
        $user = User::login($this->_post["username"],$this->_post["password"]);
        if(!$user){
            throw new UnauthorizedHttpException("Login ou senha incorretos");
        }
        return ["user"=>$user->getAttributes(null,["password_hash"]),"token" => $user->getMyToken(),"equipe_id" => $user->equipe->id];
    }
    /**
     * @SWG\Get(path="/api/v1/user/get-all",
     *     tags={"User"},
     *     summary="Pegar todos os usuários, vai retornar um array de usuários que são do tipo participante",
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
     *         description="Filtro de tipos de usuários",
     *         in="query",
     *         name="type",
     *         required=true,
     *         type="string",
     *         required=true,
     *         default="TYPE_PARTICIPANTE = 'part'; TYPE_AVALIADOR = 'avali'"
     *     ),
     *
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
     */
    public function actionGetAll($type = User::TYPE_PARTICIPANTE){
        $this->justStaff();
        $users_array=[];
        $users =  User::find()->select(["id","username","email","type","phone","name"])->where(["type"=>$type])->all();
        /**
         * @var  $chave
         * @var User $user
         */
        foreach($users as $chave => $user) {
            $users_array[$chave] = $user->getAttributes(null, ["password_hash"]);
            $users_array[$chave]["equipe"] = ($user->equipe)??[];
        }
        return ["users" =>$users_array];

    }

    /**
     * @SWG\Get(path="/api/v1/user/get",
     *     tags={"User"},
     *     summary="Pegar só um user",
     *     @SWG\Parameter(
     *         description="Token retornado na função de login",
     *         in="header",
     *         name="token",
     *         required=true,
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         description="id do usuário",
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
     */
    public function actionGet($id){
        $this->justStaff();
        /** @var User $user */
        $user = User::find()->select(["id","username","email","type","phone"])->andWhere(["id"=> $id])->one()->getAttributes();
        $user["equipe"] = $user->equipe;
        return ["user" => $user];
    }

    /**
     * @throws \yii\db\Exception
     * @throws UnauthorizedHttpException
     */
    public function actionHealthCheck(): bool
    {
        $token = ($this->_post["token"]);
        if(!Tokens::getUserByToken($token)){
            throw new UnauthorizedHttpException("Token não encontrado");
        }
        return true;
    }
}