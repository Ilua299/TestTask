<?php

namespace app\controllers;

use app\models\Collegies;
use yii\data\Pagination;
use yii\web\Controller;
use app\components\parser\princetonreviewParser\princetonreviewParser;

class TestexampleController extends Controller
{
    public function actionIndex(){
        $parser = new princetonreviewParser();
        $parser->gelAllCollegies();
        return $this->render('index');
    }
}