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
use app\models\User;
use app\models\Pontos;
use app\models\Equipe;
use app\models\Bases;

/*
 * m200211_000003_create_table_system
 */
class m220605_000001_create_table_pontos extends Migration
{
    public function safeUp(){
        $this->createTable(Pontos::tableName(), [
            'id'            => $this->primaryKey(),
            'avaliador_id'  => $this->integer()->notNull(),
            'equipe_id'     => $this->integer()->notNull(),
            'base_id'     => $this->integer()->null(),
            'observacao'    => $this->text()->null(),
            'pontos'        => $this->integer()->notNull(),
            'pontos_dicas'  => $this->integer()->null(),
            'is_base'       => $this->boolean(),
            'chegada'       => $this->string()->null(),
            'created_at'    => $this->bigInteger()->notNull(),
            'updated_at'    => $this->bigInteger()->null(),
        ], $this->tableOptions);

        $this->addForeignKey('fk_pontos_user', Pontos::tableName(), 'avaliador_id', User::tableName(), 'id', $this->cascade, $this->restrict);

        $this->addForeignKey('fk_pontos_base', Pontos::tableName(), 'base_id', Bases::tableName(), 'id', $this->cascade, $this->restrict);

        $this->addForeignKey('fk_pontos_equipe', Pontos::tableName(), 'equipe_id', Equipe::tableName(), 'id', $this->cascade, $this->restrict);
    }

    public function down(){
        $this->dropTable(Pontos::tableName());
    }
}
