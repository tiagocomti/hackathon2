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
use app\models\Bases;

/*
 * m200211_000003_create_table_system
 */

class m230418_000001_alter_tables_equipes_bases extends Migration
{
    public function safeUp()
    {
       $this->addColumn(Equipe::tableName(),"ramo",$this->string(55)->null());
       $this->addColumn(Bases::tableName(),"ramo",$this->string(55)->null());

    }

    public function down()
    {
        $this->dropColumn(Equipe::tableName(), "ramo");
        $this->dropColumn(Bases::tableName(), "ramo");
    }
}
