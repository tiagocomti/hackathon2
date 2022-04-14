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
use app\models\Tokens;

/*
 * m200211_000003_create_table_system
 */
class m220412_000004_create_table_tokens extends Migration
{
    public function safeUp(){
        $this->createTable(Tokens::tableName(), [
            'id'        => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'hash' => $this->text()->notNull(),
            'registration_ip'      => $this->string(50)->null(),
            'created_at'   => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->null(),
        ], $this->tableOptions);
        $this->addForeignKey('fk_tokens_user', Tokens::tableName(), 'user_id', User::tableName(), 'id', $this->cascade, $this->restrict);
    }

    public function down(){
        $this->dropTable(Tokens::tableName());
    }
}
