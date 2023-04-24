<?php

namespace app\models\base;

use app\helpers\Crypt;
use yii\db\ActiveRecord as YiiActiveRecord;
use yii\db\Connection;
use app\helpers\Strings;

class ActiveRecord extends YiiActiveRecord
{
    private static $instance_db = null;
    public static $timeout = "30";
}