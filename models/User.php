<?php


namespace app\models;

use app\helpers\Password;
use app\helpers\Strings;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\debug\models\search\Base;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;

/**
 * @SWG\Definition(required={"password", "email"})
 *
 * @SWG\Property(property="id", type="string")
 * @SWG\Property(property="username", type="string")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="phone", type="string")
 * @SWG\Property(property="type", type="string")
 * @SWG\Property(property="equipe_id", type="string")
 * @SWG\Property(property="password_hash", type="string")
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $phone
 * @property string $type
 * @property string $observacoes
 * @property string $password_hash
 * @property int $blocked_at
 * @property string $registration_ip
 * @property int $last_login_at
 * @property int $created_at
 * @property int $updated_at
 * @property Equipe $equipe caso for um participante normal
 * @property Bases $base caso for um avaliador
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const TYPE_ADMIN="admin";
    const TYPE_PARTICIPANTE = "part";
    const TYPE_AVALIADOR = "avali";

    /**
     * @param $username
     * @param $password
     * @return User|false
     */
    public static function login($username, $password)
    {
        $user = self::findOne(["username" => $username]);
        if(!$user){
            \Yii::warning("Meu amigo, o username: ".$username." não foi encontrado, beleza?", "api");
            return false;
        }
        if(!Password::validate($password, $user->password_hash)){
            \Yii::warning("Meu amigo, o password do caboclo: ".$username." não confere.", "api");
            return false;
        }
        return $user;

    }

    public function beforeValidate()
    {
        /** @var User $user */
        if(\Yii::$app->hasModule("user")) {
            $user = \Yii::$app->user->identity;
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @throws \yii\db\Exception
     */
    public static function validateToken($token){
        $user = Tokens::getUserByToken($token);
        if(!$user){
            return false;
        }
        return $user;
    }


    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('UNIX_TIMESTAMP()'),
            ]
        ];
    }

    /**
     * @throws \SodiumException
     * @throws BadRequestHttpException
     */
    public function getMyToken(): string
    {
        $token = Tokens::getUserToken($this->id);
        if(!$token){
            throw new BadRequestHttpException("Erro aconteceu, olhe no log","api");
        }
        return $token->hash;
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(["token" => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return false;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function isAdmin(): bool
    {
        return ($this->type == self::TYPE_ADMIN);
    }

    public function isAvaliador(): bool
    {
        return ($this->type == self::TYPE_AVALIADOR);
    }

    public function isParticipante(){
        return ($this->type == self::TYPE_PARTICIPANTE);
    }

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
            [
                ['username', 'unique', 'message' => 'This Username has already been taken.'],
                'emailTrim'     => ['email', 'trim'],
                ['email', 'unique', 'message' => 'This E-mail has already been taken.'],
                'emailPattern'  => ['email', 'email'],
                'emailLength'   => ['email', 'string', 'max' => 255],
                'passwordRequired' => ['password', 'required', 'on' => ['register']],
                'passwordLength'   => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
        ]);
    }

    public function logout(){
        return true;
    }

    /**
     * Gets query for [[NotificationSetups]].
     *
     * @return ActiveQuery|Equipe
     */
    public function getEquipe()
    {
        return $this->hasOne(strtolower(Equipe::className()), ['id' => 'equipe_id'])
            ->viaTable('participantes', ['user_id' => 'id'],
                function (ActiveQuery $query) {
                    $query->orderBy(['id' => SORT_DESC]);
                });
    }
    /**
     * Gets query for [[Bases]].
     *
     * @return ActiveQuery|Bases
     */
    public function getBase()
    {
        return $this->hasOne(strtolower(Bases::className()), ['id' => 'base_id'])
            ->viaTable('avaliadores', ['user_id' => 'id'],
                function (ActiveQuery $query) {
                    $query->orderBy(['id' => SORT_DESC]);
                });
    }
    /**
     * Gets query for [[NotificationSetups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBases()
    {
        return $this->hasOne(strtolower(Equipe::className()), ['id' => 'base_id'])
            ->viaTable('avaliadores', ['user_id' => 'id'],
                function (ActiveQuery $query) {
                    $query->orderBy(['id' => SORT_DESC]);
                });
    }

    public function getAttributes($names = null, $except = [])
    {
        return parent::getAttributes($names, $except); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        $this->phone = Strings::sanitizationPhone($this->phone);
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function hasBase(): bool
    {
        return (isset($this->base));
    }
}