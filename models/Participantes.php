<?php


namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form. Preciso trocar DPO_id para user_id no coiso
 * @property int $id
 * @property int $user_id
 * @property int $equipe_id
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Equipe $equipe
 * @property User[] $users
 */
class Participantes extends ActiveRecord
{
    /**
     * Gets query for [[Equipe]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipe()
    {
        return $this->hasOne(Equipe::className(), ['equipe_id' => 'id']);
    }
    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getNotifications()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id']);
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