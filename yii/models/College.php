<?php

namespace app\models;

use yii\db\ActiveRecord;

class College extends ActiveRecord
{
    public static function tableName()
    {
        return 'college';
    }
}