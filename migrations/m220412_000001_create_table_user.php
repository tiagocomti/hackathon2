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
use app\helpers\Password;

/*
 * m200211_000003_create_table_system
 */
class m220412_000001_create_table_user extends Migration
{
    public function safeUp(){
        $this->createTable(User::tableName(), [
            'id'        => $this->primaryKey(),
            'name'   => $this->string(40)->notNull(),
            'username'   => $this->string(40)->notNull(),
            'email' => $this->text()->notNull(),
            'phone' => $this->text()->notNull(),
            'type' => $this->string(11)->notNull(),
            'password_hash' => $this->text()->notNull(),
            'blocked_at'           => $this->bigInteger()->null(),
            'registration_ip'      => $this->string(50)->null(),
            'last_login_at'      => $this->bigInteger()->null(),
            'created_at'   => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->null(),
        ], $this->tableOptions);

        $this->insert(User::tableName(), [
            'id' => 1,
            'name' => 'Tiago Alexandre',
            'username'   => "tiagocomti",
            'email' => "tiago.alexandre.oliveira@hotmail.com",
            'phone' => "(31) 9 8365-8062",
            'type' => User::TYPE_ADMIN,
            'password_hash' => Password::hash("1234567a"),
            'blocked_at'           => null,
            'registration_ip'      => "177.10.156.226",
            'last_login_at'      => null,
            'created_at'   => time(),
            'updated_at' => time(),
        ]);
    }

    public function down(){
        $this->dropTable(User::tableName());
    }
}
