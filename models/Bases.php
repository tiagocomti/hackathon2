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
 * @property string $name
 * @property string $id
 * @property string $ramo
 * @property string|null $updated_at
 * @property string|null $created_at
 * @property User[] $avaliadores
 *
 */
class Bases extends ActiveRecord
{
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
                if($user && $user->type == User::TYPE_AVALIADOR ||
                    $user && $user->type == User::TYPE_ADMIN){
                    if($avaliadores = Avaliadores::findOne(["user_id"=> $user->id])) {
                        $avaliadores->delete();
                    }
                    $avaliadores = new Avaliadores();
                    $avaliadores->user_id = $user->id;
                    $avaliadores->base_id = $this->id;
                    if(!$avaliadores->save()){
                        print_r($avaliadores->getErrors());exit;
                    }
                }
            }
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    /**
     * @return ActiveQuery|User
     * @throws \yii\base\InvalidConfigException
     */
    public function getAvaliadores()
    {
        return $this->hasMany(strtolower(User::className()), ['id' => 'user_id'])
            ->select(["name", "email","id","username","phone"])
            ->viaTable('avaliadores', ['base_id' => 'id'],
                function (ActiveQuery $query) {
                    $query->orderBy(['id' => SORT_DESC]);
                });
    }

    public function removeParticipante(array $users_id): bool
    {
        foreach ($users_id as $id) {
            $avaliadores = Avaliadores::findOne(["user_id"=>$id]);
            if ($avaliadores) {
                $avaliadores->delete();
            }
        }
        return true;
    }

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
            [
                ['name', 'unique', 'message' => 'Já tem uma base com esse nome'],
            ]);
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