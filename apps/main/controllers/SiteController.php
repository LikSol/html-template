<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 30.01.18
 * Time: 19:21
 */

namespace main\controllers;

use main\models\DataConfig;
use main\models\MarkupComponentType;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public $layout = '@main/views/layouts/internal/main.php';

    public function actionIndex() {
        return $this->render('index.html.twig', [
            'projects' => Yii::$app->projectConfig->getProjects(),
        ]);
    }

    public function actionPagePreview($pageSid, $previewSid) {
        /** @var DataConfig $dataConfig */
        $dataConfig = Yii::$app->projectConfig->getDataConfig();
        $page = $dataConfig->getPage($pageSid);
        $preview = $page->getPreview($previewSid);

        return $this->render('page-preview', compact('dataConfig', 'page', 'preview'));
    }

    public function actionComponentAbout($sid) {
        /** @var DataConfig $dataConfig */
        $dataConfig = Yii::$app->projectConfig->getDataConfig();

        $component = $dataConfig->getComponent($sid);
        return $this->render('component-about', compact('component'));
    }

    public function actionTypeAbout($sid) {
        $componentType = new MarkupComponentType(['sid' => $sid]);
        return $this->render('type-about', compact('componentType'));
    }
}