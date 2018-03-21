<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:03
 */

namespace main\controllers;

use main\models\Project;
use Yii;
use yii\helpers\FileHelper;

class PageController extends \yii\web\Controller
{

    public $layout = 'project/main.php';

    public function actionShow($page, $projectName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);
        $pageSid = basename($page);

        $page = $project->getWork()->getPage($pageSid);
        Yii::$app->view->params['html-template.project.current'] = $project;
        Yii::$app->view->params['current.workPage'] = $page;

        $file = $page->getFile();

        Yii::$app->view->title = $page->sid;
        $result = $this->renderContent($this->renderFile($file));

        return $result;
    }

    public function actionShowWidgetAsset($asset, $projectName, $widgetName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);

        $file = $project->getWidgetDir($widgetName) . "/$asset";
        $fileRealPath = Yii::getAlias($file);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->headers->add('content-type', mime_content_type($fileRealPath));
        $response->data = file_get_contents($fileRealPath);
        return $response;
    }

    public function actionShowPageAsset($asset, $projectName, $pageName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);

        $file = $project->getPagesDir($pageName) . "/$asset";
        $fileRealPath = Yii::getAlias($file);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->headers->add('content-type', $this->mimeTypeByFile($fileRealPath));
        $response->data = file_get_contents($fileRealPath);
        return $response;
    }

    protected function mimeTypeByFile($file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'css':
                $mime = 'text/css';
                break;
            default:
                $mime = mime_content_type($file);
                break;
        }

        return $mime;
    }

    public function actionShowProjectAsset($asset, $projectName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);

        $file = $project->getSrcDir() . "/$asset";
        $fileRealPath = Yii::getAlias($file);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->headers->add('content-type', $this->mimeTypeByFile($fileRealPath));
        $response->data = file_get_contents($fileRealPath);
        return $response;
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

    public function actionShowAllWidgetsCss($projectName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);

        $files = FileHelper::findFiles(Yii::getAlias($project->getWidgetsDir()), [
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

//    public function actionShowComponentImage($namespace, $component, $path) {
//        $file = "@data/work/$namespace/components/$component/$path";
//        $ext = pathinfo($path, PATHINFO_EXTENSION);
//
//        $response = Yii::$app->response;
//        $response->format = $response::FORMAT_RAW;
//        $response->headers->add('content-type','image/' . $ext);
//        $response->data = file_get_contents(Yii::getAlias($file));
//        return $response;
//    }

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
