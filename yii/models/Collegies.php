<?php

namespace app\models;

use yii\db\ActiveRecord;

class Collegies extends ActiveRecord
{
    public static function tableName()
    {
        return 'allcollegies';
    }

    public function getCollege(){
        return $this->hasOne(College::class , ['college_id' => 'college_id']);
    }
}