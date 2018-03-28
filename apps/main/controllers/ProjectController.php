<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 20.03.18
 * Time: 11:36
 */

namespace main\controllers;

use main\models\Project;
use Symfony\Component\Yaml\Yaml;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
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

    public function actionShowWidgetLive($projectSid, $widgetSid) {
        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);
        $widget = $project->getTasks()->getWidget($widgetSid);

        Yii::$app->view->params['html-template.project.current'] = $project;

        return $this->render('show-widget-live.html.twig', [
            'widget' => $widget,
        ]);
    }


    public function actionCutPreviewBoard($projectSid) {
        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);

        $previewQids = [
            'main.1200',
            'main.992',
            'main.768',
            'main.480',
            'main.320',
        ];

        $previewQids = [
            'main.1200',
            'catalog.1200',
            'catalog.992',
            'catalog.768',
            'catalog.480',
            'catalog.320',
        ];

        $previewQids = [
            'main.1200',
            'catalog.1200',
            'item.1200',
            'item.992',
            'item.768',
            'item.480',
            'item.320',
        ];

        $previewQids = [
            'main.1200',
            'catalog.1200',
            'item.1200',
            'contacts.1200',
            'contacts.992',
            'contacts.768',
            'contacts.480',
            'contacts.320',
        ];

        $previewQids = [
            'main.1200',
            'elements-menu.1200',
            'elements-menu.1200-comment',
            'elements-menu.480',
        ];

//        $previewQids = [
//            'main.1200',
//            'action-popup.1200',
//        ];

        $previews = [];
        foreach ($previewQids as $previewQid) {
            $previews[$previewQid] = $project->getPreviewByQid($previewQid);
        }

        $widgetTasks = $project->getTasks()->getWidgets();
//        $tmp = [];
//        foreach ($widgetTasks as $widgetTask) {
//            if (StringHelper::startsWith($widgetTask->sid, 'layout')) {
//                $tmp[] = $widgetTask;
//            }
//        }
//        $widgetTasks = $tmp;

        return $this->render('cut-preview-board.html.twig', compact('previews', 'widgetTasks', 'project'));
    }

    public function actionSaveCutConfig() {
        if (!Yii::$app->request->isAjax) throw new BadRequestHttpException();

        $projectSid = Yii::$app->request->post('projectSid');

        $PC = Yii::$app->projectConfig;
        /** @var Project $project */
        $project = $PC->getProject($projectSid);

        $projectConfigRaw = $project->getConfig()->raw;
        $widgetTasksConfig = @$projectConfigRaw['tasks']['widgets'];

        $cutConfig = Json::decode(Yii::$app->request->post('config'));

        foreach ($widgetTasksConfig as $widgetSid => $definition) {
            if (!in_array($widgetSid, $cutConfig['components'])) continue;

            foreach ($definition['appearances'] as $key => $appearance) {
                $previewQid = $appearance['preview'];

                if (!in_array($previewQid, $cutConfig['previews'])) continue;

                unset($widgetTasksConfig[$widgetSid]['appearances'][$key]);
            }
        }

        foreach ($cutConfig['appearances'] as $appearance) {
            $previewQid = $appearance['previewQid'];
            $componentSid = $appearance['componentSid'];

            if (!isset($widgetTasksConfig[$componentSid])) {
                $widgetTasksConfig[$componentSid] = [
                    'appearances' => [],
                ];
            }

            $widgetTasksConfig[$componentSid]['appearances'][] = [
                'preview' => $previewQid,
                'coords' => [$appearance['coords']],
            ];
        }

        $widgetTasksConfig = [
            'tasks' => [
                'widgets' => $widgetTasksConfig
            ]
        ];

        $yaml = (Yaml::dump($widgetTasksConfig, 100, 2));

        file_put_contents($project->getRootDir() . '/config/tasks.widgets.yaml', $yaml);
    }
}