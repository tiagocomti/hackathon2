<?php


namespace app\models;

use app\helpers\Password;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
 * @SWG\Property(property="cpf", type="string")
 * @SWG\Property(property="password_hash", type="string")
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $phone
 * @property string $type
 * @property string $password_hash
 * @property int $blocked_at
 * @property string $registration_ip
 * @property int $last_login_at
 * @property int $created_at
 * @property int $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const TYPE_ADMIN="admin";
    const TYPE_PARTICIPANTE = "participante";

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


    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('NOW()'),
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
}