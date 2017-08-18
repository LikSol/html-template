<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:03
 */

namespace main\controllers;

use main\components\AutoAssets;
use Yii;

class TemplateController extends \yii\web\Controller
{

    public function actionPage($page) {
        /** @var AutoAssets $assets */
        $assets = Yii::$app->autoAssets;
        $assets->page = $page;
        $assets->mode = $assets::MODE_PAGE;

        if (Yii::$app->request->get('layout') === 'false') {
            $this->layout = false;
        }
        $result = $this->render($page, ['assets' => $assets]);

        return $result;
    }

    public function actionLayout()
    {
        return $this->renderContent('');
    }
}
