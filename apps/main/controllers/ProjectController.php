<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 20.03.18
 * Time: 11:36
 */

namespace main\controllers;

use main\models\Project;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;

class ProjectController extends Controller
{
    public function actionShow($projectName) {
        $PC = Yii::$app->projectConfig;
        $project = $PC->getProject($projectName);

        return $this->render('show.html.twig', compact('project'));
    }

    public function actionShowPreviewImage($projectSid, $designSid, $previewSid) {
        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);

        $design = $project->getDesignBySid($designSid);
        $preview = $design->getPreviewBySid($previewSid);

        $file = $project->getRootDir() . '/preview/' . $preview->file;

        $mime = FileHelper::getMimeType($file);

        $response = Yii::$app->response;
        $response->format = $response::FORMAT_RAW;
        $response->headers->add('content-type', $mime);
        $response->data = file_get_contents($file);
        return $response;
    }

    public function actionShowWidgetTaskPreview($projectSid, $designSid, $previewSid, $widgetSid) {
        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);
        $design = $project->getDesignBySid($designSid);
        $preview = $design->getPreviewBySid($previewSid);
        $widgetTask = $project->getTasks()->getWidget($widgetSid);

        $widgets = [$widgetTask];
        return $this->render('show-widget-task-preview', compact('widgets', 'preview'));
    }

    public function actionShowPreviewWidgets($projectSid, $designSid, $previewSid) {
        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);
        $design = $project->getDesignBySid($designSid);
        $preview = $design->getPreviewBySid($previewSid);
        $widgets = $project->getTasks()->getPreviewWidgets($preview);

        return $this->render('show-widget-task-preview', compact('widgets', 'preview'));
    }

    public function actionShowWidgetRequirements($projectSid, $widgetSid) {
        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);
        $widget = $project->getTasks()->getWidget($widgetSid);

        return $this->render('show-widget-requirements.html.twig', [
            'widget' => $widget,
        ]);
    }
}