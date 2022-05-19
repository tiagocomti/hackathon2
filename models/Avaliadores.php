<?php


namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @SWG\Definition(required={"name"})
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="id", type="string")
 * @SWG\Property(property="created_at", type="string")
 * @SWG\Property(property="updated_at", type="string")
 *
 * LoginForm is the model behind the login form. Preciso trocar DPO_id para user_id no coiso
 *
 * @property int $id
 * @property int $user_id
 * @property int $base_id
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Bases $base
 * @property User[] $users
 */
class Avaliadores extends ActiveRecord
{
    /**
     * Gets query for [[Equipe]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBase()
    {
        return $this->hasOne(Bases::className(), ['base_id' => 'id']);
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