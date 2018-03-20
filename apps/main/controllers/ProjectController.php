<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 20.03.18
 * Time: 11:36
 */

namespace main\controllers;

use Yii;
use yii\web\Controller;

class ProjectController extends Controller
{
    public function actionShow($projectName) {
        $PC = Yii::$app->projectConfig;
        $project = $PC->getProject($projectName);

        return $this->render('show.html.twig', compact('project'));
    }
}