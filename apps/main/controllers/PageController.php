<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:03
 */

namespace main\controllers;

use main\assets\ProjectPageAsset;
use main\models\Project;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

class PageController extends \yii\web\Controller
{

    public $layout = 'project/main.php';

    public function renderAjax($viewFile, $params = [], $context = null)
    {
        $view = $this->view;

        ob_start();
        ob_implicit_flush(false);

        $view->beginPage();
        $view->head();
        $view->beginBody();
//        echo $viewFile;
        echo $view->renderFile($viewFile, $params, $context);

        $view->assetBundles[ProjectPageAsset::class] = false;
        $view->endBody();
        $view->endPage(true);

        return ob_get_clean();
    }

    public function actionShowFile($file, $projectName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);

        Yii::$app->view->params['html-template.project.current'] = $project;

        $pathinfo = pathinfo($file);

        if (@$pathinfo['extension'] == 'html') {
            $absPath = $project->getSrcDir() . '/'. $file . '.twig';
        } else {
            $absPath = $project->getSrcDir() . '/'. $file;
        }

        switch (true) {
            case @$pathinfo['extension'] == 'html':
                if (Yii::$app->request->get('ajax') !== null) {
                    return $this->renderAjax($absPath);
                }

                $pathParts = explode('/', $pathinfo['dirname']);
                if ($pathParts[0] == 'pages') {
                    // это страница
                    $pageSid = $pathParts;
                    array_shift($pageSid);
                    $pageSid = implode($pageSid) . '/' . $pathinfo['filename'];
                    $page = $project->getWork()->getPage($pageSid);
                    Yii::$app->view->params['current.workPage'] = $page;
                    Yii::$app->view->title = $page->sid;
                }

                // используем renderContent(rendrerFile()), потому что у нас абсолютный путь к файлу
                return $this->renderContent($this->renderFile($absPath));
            default:
                $response = Yii::$app->response;
                $response->format = $response::FORMAT_RAW;
                $response->headers->add('content-type', $this->mimeTypeByFile($absPath));
                $response->data = file_get_contents($absPath);
                return $response;
        }

    }

    public function actionShow($page, $projectName) {
        /** @var Project $project */
        $project = Yii::$app->projectConfig->getProject($projectName);

        $page = $project->getWork()->getPage($page);
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
