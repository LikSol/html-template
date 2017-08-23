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

        Yii::$app->view->title = $page;
        $result = $this->render($page, ['assets' => $assets]);

        return $result;
    }

    public function actionLayout()
    {
        return $this->renderContent('');
    }

    public function actionAllPages()
    {
        $files = glob(Yii::getAlias('@main/views/template/*.php'));
        $pages = ['html-template' => [], 'site' => []];
        foreach ($files as $path) {
            $name = basename($path, '.php');
            switch (true) {
                case 'example' === $name:
                case preg_match('/^example-/', $name):
                    $pages['html-template'][] = $name;
                    break;
                default:
                    $pages['site'][] = $name;
                    break;
            }

        }

        return $this->render('internal/all-pages.php', compact('pages'));
    }
}
