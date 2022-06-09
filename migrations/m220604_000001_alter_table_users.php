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
class m220604_000001_alter_table_users extends Migration
{
    public function safeUp(){
        $this->addColumn('user', 'observacoes', $this->text());
    }

    public function down(){
        $this->dropColumn("user","observacoes");
    }
}
