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
use app\models\Bases;
use app\models\Avaliadores;
use app\models\User;

/*
 * m200211_000003_create_table_system
 */
class m220412_000004_create_table_bases extends Migration
{
    public function safeUp(){
        $this->createTable(Bases::tableName(), [
            'id'        => $this->primaryKey(),
            'name'   => $this->string(80)->notNull(),
            'created_at'   => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->null(),
        ], $this->tableOptions);

        $this->createTable(Avaliadores::tableName(), [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->integer()->notNull(),
            'base_id'   => $this->integer()->notNull(),
            'created_at'   => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->null(),
        ], $this->tableOptions);
        $this->addForeignKey('fk_avaliador_user', Avaliadores::tableName(), 'user_id', User::tableName(), 'id', $this->cascade, $this->restrict);
        $this->addForeignKey('fk_participantes_base', Avaliadores::tableName(), 'base_id', Bases::tableName(), 'id', $this->cascade, $this->restrict);
    }

    public function down(){
        $this->dropTable(Avaliadores::tableName());
        $this->dropTable(Bases::tableName());
    }
}
