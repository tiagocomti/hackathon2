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
use app\models\Equipe;

/*
 * m200211_000003_create_table_system
 */
class m220412_000002_create_table_equipe extends Migration
{
    public function safeUp(){
        $this->createTable(Equipe::tableName(), [
            'id'        => $this->primaryKey(),
            'name'   => $this->string(40)->notNull(),
            'created_at'   => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->null(),
        ], $this->tableOptions);
    }

    public function down(){
        $this->dropTable(Equipe::tableName());
    }
}
