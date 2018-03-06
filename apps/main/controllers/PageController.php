<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:03
 */

namespace main\controllers;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class PageController extends \yii\web\Controller
{

    public function actionShow($page) {
        $parts = explode('/', $page);
        $namespace = array_shift($parts);
        Yii::$app->view->params['html-template.namespace.current'] = $namespace;
        $pageName = array_pop($parts);
        $file = '@data/work/' . $namespace . '/pages/' .  implode('/', $parts) . '/' . $pageName . '/' . $pageName . '.html.twig';

        Yii::$app->view->registerCssFile('/page/' . $namespace . '/' . $namespace . '.css');
        Yii::$app->view->registerCssFile(Url::to(['page/show-all-components-css', 'namespace' => $namespace]));

        Yii::$app->view->title = $page;
        $result = $this->render($file);

        return $result;
    }

    public function actionShowCss($page) {
        $parts = explode('/', $page);
        $namespace = array_shift($parts);

        if (count($parts) != 1) throw new \Exception("Not implemented");

        $file = '@data/work/' . $namespace . '/' .  implode('', $parts);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->data = file_get_contents(Yii::getAlias($file));
        $response->headers->add('content-type','text/css');

        return $response;
    }

    public function actionShowAllComponentsCss($namespace) {
        $files = FileHelper::findFiles(Yii::getAlias("@data/work/$namespace/components"), [
            'only' => ['*.css']
        ]);

        $contents = '';

        foreach ($files as $file) {
            $contents .= file_get_contents($file);
        }

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->data = $contents;
        $response->headers->add('content-type','text/css');

        return $response;
    }

    public function actionShowImage($page) {
        $parts = explode('/', $page);
        $ext = pathinfo($page, PATHINFO_EXTENSION);
        $namespace = array_shift($parts);
        $file = '@data/work/' . $namespace . '/pages/' .  implode('/', $parts);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->headers->add('content-type','image/' . $ext);
        $response->data = file_get_contents(Yii::getAlias($file));
        return $response;
    }

    public function actionShowComponentImage($namespace, $component, $path) {
        $file = "@data/work/$namespace/components/$component/$path";
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->headers->add('content-type','image/' . $ext);
        $response->data = file_get_contents(Yii::getAlias($file));
        return $response;
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
