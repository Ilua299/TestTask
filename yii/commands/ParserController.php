<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\components\parser\princetonreviewParser\princetonreviewParser;

class ParserController extends Controller
{
    public function actionIndex(){
        echo 'start parsing';
        return ExitCode::OK;
    }
    public function actionParse(){
        $parser = new princetonreviewParser();
        $parser->gelAllCollegies();
    }
}