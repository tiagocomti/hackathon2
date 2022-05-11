<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use app\migrations\Migration;
use app\models\Participantes;
use app\models\Equipe;
use app\models\User;

/*
 * m200211_000003_create_table_system
 */
class m220412_000003_create_table_participantes extends Migration
{
    public function safeUp(){
        $this->createTable(Participantes::tableName(), [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->integer()->notNull(),
            'equipe_id'   => $this->integer()->notNull(),
            'created_at'   => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->null(),
        ], $this->tableOptions);
        $this->addForeignKey('fk_participantes_user', Participantes::tableName(), 'user_id', User::tableName(), 'id', $this->cascade, $this->restrict);
        $this->addForeignKey('fk_participantes_equipe', Participantes::tableName(), 'equipe_id', Equipe::tableName(), 'id', $this->cascade, $this->restrict);
    }

    public function down(){
        $this->dropTable(Participantes::tableName());
    }
}
