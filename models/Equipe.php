<?php


namespace app\models;

use Da\QrCode\QrCode;
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
 * @property string $name
 * @property string $id
 * @property string $ramo
 * @property User[] $participantes
 */
class Equipe extends ActiveRecord
{
    const RAMO_SENIOR = 'senior';
    const RAMO_LOBO = 'lobinho';
    const RAMO_ESCOTEIRO = 'escoteiro';
    const RAMO_PIO = 'pioneiro';
    /**
     * @var User
     */
    public $users;

    /**
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes){
        if($insert){
            foreach($this->users as $user_id){
                $user = User::findOne(["id" => $user_id]);
                if($user && $user->type == User::TYPE_PARTICIPANTE){
                    if($participante = Participantes::findOne(["user_id"=> $user->id])) {
                        $participante->delete();
                    }
                    $participantes = new Participantes();
                    $participantes->user_id = $user->id;
                    $participantes->equipe_id = $this->id;
                    $participantes->save();
                }
            }
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
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

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
            [
                ['name', 'unique', 'message' => 'Já tem uma equipe com esse nome'],
            ]);
    }

    public function removeParticipante(array $users_id){
        foreach ($users_id as $id) {
            $participante = Participantes::findOne(["user_id"=>$id]);
            if ($participante) {
                $participante->delete();
            }
        }
        return true;
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getParticipantes()
    {
        return $this->hasMany(strtolower(User::className()), ['id' => 'user_id'])
            ->select(["name", "email","id","username","phone","observacoes"])
            ->viaTable('participantes', ['equipe_id' => 'id'],
                function (ActiveQuery $query) {
                    $query->orderBy(['id' => SORT_DESC]);
                });
    }

    public function getQrcode(){
        if(!\Yii::$app->cache->get("qrcode_".$this->id)) {
            $qrCode = (new QrCode('https://jogodacidade.app/common/equipe/pontos.html?equipe_id='.$this->id.'&open_modal=yes'))
                ->setSize(250)
                ->setMargin(5)
                ->setForegroundColor(16, 133, 193);

            $base_64 = $qrCode->writeDataUri();
            \Yii::$app->cache->set("qrcode_".$this->id, $base_64, 9000);
        }else{
            $base_64 = \Yii::$app->cache->get("qrcode_".$this->id);
        }
        return $base_64;
    }

    public function getPontos(){
        $soma = 0;
        $pontos = Pontos::find()->andWhere(["equipe_id"=>$this->id])->all();
        /** @var Pontos $ponto */
        foreach ($pontos as $ponto){
            $soma += ((int)$ponto->pontos+(int)$ponto->pontos_dicas);
        }
        return $soma;
    }
}