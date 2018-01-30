<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 30.01.18
 * Time: 19:21
 */

namespace main\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public $layout = '@main/views/layouts/internal/main.php';

    public function actionIndex() {
        return $this->render('index');
    }
}