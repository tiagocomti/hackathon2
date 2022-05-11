<?php


namespace app\models;

use tiagocomti\cryptbox\Cryptbox;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form. Preciso trocar DPO_id para user_id no coiso
 *
 * @property string $id
 * @property string $user_id
 * @property string $hash
 * @property string $created_at
 * @property string $registration_ip
 * @property string $updated_at
 */
class Tokens extends ActiveRecord
{
    /**
     * @throws \SodiumException
     */
    public static function getUserToken($user_id){
        $session = self::findOne(["user_id" => $user_id]);
        if(!$session){
            $session = new self();
            $session->user_id = $user_id;
            $session->hash = self::generateToken(["user_id"=>$user_id]);
            if(!$session->save()){
                \Yii::error(json_encode($session->getErrors()),"api");
                return false;
            }
        }
        return $session;
    }

    /**
     * @throws \SodiumException
     * @throws \yii\db\Exception
     */
    private static function generateToken(array $infos): string{
        $checksum = [
            "infos" => $infos,
            "secret" => Cryptbox::getOurSecret()
        ];
        $infos["checksum"] = hash("sha256", json_encode($checksum));
        return base64_encode(json_encode($infos));
    }

    /**
     * @param $token
     * @return User|false|null
     * @throws \yii\db\Exception
     */
    public static function getUserByToken($token){
        $token = self::findOne(["hash" => $token]);
        if(!$token){
            \Yii::error("Token não existe","api");
            return false;
        }
        $decode = json_decode(base64_decode($token->hash), true);
        $checksum_token = $decode["checksum"];
        unset($decode["checksum"]);
        $checksum = [
            "infos" => $decode,
            "secret" => Cryptbox::getOurSecret()
        ];
        if($checksum_token !== hash("sha256", json_encode($checksum))){
            \Yii::error("token do ".json_encode($decode)." está modificado, checksum não bate","api");
            return false;
        }
        return User::findOne($decode["user_id"]);
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
}