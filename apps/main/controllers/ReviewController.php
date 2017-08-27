<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 27.08.17
 * Time: 19:13
 */

namespace main\controllers;

use main\components\ProjectConfig;
use yii\web\Controller;
use Yii;

class ReviewController extends Controller
{

    public function actionIndex() {
        /** @var ProjectConfig $projectConfig */
        $projectConfig = Yii::$app->projectConfig;
        $pages = $projectConfig->getPagesWithResolutions();

        return $this->render('index.html.twig', compact('pages'));
    }

    public function actionCompare($width, $name) {
        $this->layout = 'clean';

        $projectConfig = Yii::$app->projectConfig;
        $pages = $projectConfig->getPagesWithResolutions();

        return $this->render('compare.html.twig', [
            'design' => "/preview/design/$name/$width.png",
            'live' => "/preview/live/$name/$width.png",
            'name' => $name,
            'width' => $width,
            'pages' => $pages
        ]);
    }
}