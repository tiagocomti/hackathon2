<?php


namespace app\models;

use dektrium\user\helpers\Password;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form. Preciso trocar DPO_id para user_id no coiso
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
class User extends ActiveRecord
{
    const TYPE_ADMIN="admin";
    const TYPE_PARTICIPANTE = "part";

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
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression('extract(epoch FROM now())'),
            ],
        ];
    }

    public function getMyToken()
    {
        return hash("sha512", $this->username.":".$this->password_hash);
    }
}