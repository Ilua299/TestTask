<?php
require_once '../../phpQuery/phpQuery/phpQuery.php';

class parser
{
    public function grab(string $site){
        return phpQuery::newDocument(file_get_contents($site));
    }
    public function parse(){

    }
    public function save($dbname,$columns,$values){
        $columnsstring = implode(',',$columns);
        $valuesstring = implode(',',$values);
        Yii::$app->db->createCommand("INSERT IGNORE INTO :dbname (:columnsstring) VALUES (:valuesstring)")
            ->bindValue(':dbname' , $dbname)
            ->bindValue(':columnsstring',$columnsstring)
            ->bindValue(':valuesstring',$valuesstring)
            ->query();
    }
}
