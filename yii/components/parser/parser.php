<?php

namespace app\components\parser;
use phpQuery;
use Yii;

class parser
{

    public function grab(string $site){
        return phpQuery::newDocument(file_get_contents($site));
    }
    public function parse(){

    }
    public function save($dbname,$columns,$values){
        $columnsstring = implode(",",$columns);
        Yii::$app->db->createCommand("INSERT IGNORE INTO ".$dbname." (".$columnsstring.") VALUES (:value0,:value1,:value2,:value3,:value4)")
            ->bindValue('value0', $values[0])
            ->bindValue('value1', $values[1])
            ->bindValue('value2', $values[2])
            ->bindValue('value3', $values[3])
            ->bindValue('value4', $values[4])
            ->query();
    }
}
