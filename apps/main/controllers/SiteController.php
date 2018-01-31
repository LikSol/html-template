<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 30.01.18
 * Time: 19:21
 */

namespace main\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public $layout = '@main/views/layouts/internal/main.php';

    public function actionIndex() {
        $config = Yii::$app->projectConfig->getDataConfig();
        return $this->render('index', compact('config'));
    }

    public function actionPagePreview($pageSid, $previewSid) {
        $config = Yii::$app->projectConfig->getDataConfig();
        $page = $config['pages'][$pageSid];
        $preview = $page['previews'][$previewSid];

        $components = [];
        foreach ($config['components'] as $component) {
            foreach ($component['onPreviews'] as $onPreview) {
                $onThisPage = is_array($onPreview['page'])
                    ? in_array($pageSid, $onPreview['page'])
                    : ($onPreview['page'] == $pageSid);
                if (!$onThisPage) continue;
                if ($onPreview['preview'] != $previewSid) continue;
                $components[$component['sid']] = $onPreview['appearance'];
            }
        }

        return $this->render('page-preview', compact('config', 'page', 'preview', 'components'));
    }
}