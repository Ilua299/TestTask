<?php

namespace app\controllers;

use app\models\Collegies;
use yii\data\Pagination;
use yii\web\Controller;

class CollegiesController extends Controller
{
    public function actionIndex()
    {
        $query =  Collegies::find();
        $pagination = new Pagination([
            'defaultPageSize' => 22,
            'totalCount' => $query->count(),
        ]);
        $collegies = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->render('index',compact('collegies','pagination'));
    }
}
