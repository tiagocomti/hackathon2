<?php


namespace app\models;

use app\helpers\Date;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\debug\components\search\matchers\Base;
use yii\web\BadRequestHttpException;

/**
 * LoginForm is the model behind the login form. Preciso trocar DPO_id para user_id no coiso
 * @property int $id
 * @property int $avaliador_id
 * @property int $equipe_id
 * @property int $base_id
 * @property string|null $observacao
 * @property string|null $pontos
 * @property string|null $is_base
 * @property string|null $chegada
 * @property string|null $pontos_dicas
 * @property string|null $created_at
 * @property string|null $ypdated_at
 *
 * @property Equipe $equipe
 * @property User $avaliador
 * @property Bases $base
 */
class Pontos extends ActiveRecord
{

    public function beforeValidate()
    {
        if($this->isNewRecord){
            $avaliador_atual = Avaliadores::findOne(["user_id" => $this->avaliador_id]);
            if(!$avaliador_atual && $this->is_base){
                $this->addError("avaliador","Esse avaliador não existe ou não tem nenhuma base vinculada a ele");
                return false;
            }
            $pontos = self::findOne(["base_id" => $avaliador_atual->base_id, "equipe_id" => $this->equipe_id]);
            if ($pontos && ($this->is_base && $pontos->is_base)) {
                throw new BadRequestHttpException("Ja existe avaliação dessa base");
            }
            $this->base_id = $avaliador_atual->base_id;
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        $this->chegada = Date::formatDate($this->chegada,"H:i:s");
        if(!$this->is_base){
            $this->chegada = null;
            $this->pontos_dicas = null;
            $this->base_id = null;
        }else{
            $this->observacao = "";
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[Equipe]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipe()
    {
        return $this->hasOne(Equipe::className(), ['id' => 'equipe_id']);
    }
    /**
     * Gets query for [[Base]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBase()
    {
        return $this->hasOne(Bases::className(), ['id' => 'base_id']);
    }
    /**
     * Gets query for [[Equipe]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvaliador()
    {
        return $this->hasOne(User::className(), ['id' => 'avaliador_id']);
    }

    public function afterFind()
    {
        $this->observacao = ($this->observacao)??"N/A";
        $this->pontos_dicas = ($this->pontos_dicas)??"N/A";
        $this->chegada = ($this->chegada)??"N/A";
        parent::afterFind(); // TODO: Change the autogenerated stub
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