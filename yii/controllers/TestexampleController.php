<?php

namespace app\controllers;

use app\models\Collegies;
use yii\data\Pagination;
use yii\web\Controller;

class TestexampleController extends Controller
{
    public function actionIndex(){
        return $this->render('index');
    }
}