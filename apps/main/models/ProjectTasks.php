<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class ProjectTasks extends BaseObject
{
    /**
     * @var Project
     */
    public $project;

    protected $_pageTasks;
    public function getPages() {
        if (!$this->_pageTasks) {
            $config = $this->project->getConfig()->raw;
            $pageTaskDefinitnions = @$config['tasks']['pages'] ?: [];
            $tasks = [];
            foreach ($pageTaskDefinitnions as $pageSid => $pageTaskDefinitnion) {
                $task = new PageTask([
                    'sid' => $pageSid,
                    'definition' => $pageTaskDefinitnion,
                    'project' => $this->project,
                ]);
                $tasks[$pageSid] = $task;
            }

            $this->_pageTasks = $tasks;
        }

        return $this->_pageTasks;
    }

    protected $_widgets;

    /**
     * @return WidgetTask[]
     */
    public function getWidgets() {
        if (!$this->_widgets) {
            $config = $this->project->getConfig()->raw;
            $widgetTaskDefinitnions = @$config['tasks']['widgets'] ?: [];
            $tasks = [];
            foreach ($widgetTaskDefinitnions as $widgetSid => $widgetTaskDefinitnion) {
                $task = new WidgetTask([
                    'sid' => $widgetSid,
                    'definition' => $widgetTaskDefinitnion,
                    'project' => $this->project,
                ]);
                $tasks[$widgetSid] = $task;
            }

            $this->_widgets = $tasks;
        }

        return $this->_widgets;
    }

    public function getWidget($widgetSid) {
        if (@$this->widgets[$widgetSid]) return $this->widgets[$widgetSid];
    }

    public function getPreviewsWithWidgets() {
        $previews = [];
        foreach ($this->getWidgets() as $widget) {
            $widgetPreviews = ArrayHelper::index($widget->getPreviews(), 'qid');
            $previews = $previews + $widgetPreviews;
        }

        return $previews;
    }

    /**
     * @param $preview DesignPreview
     * @return array
     */
    public function getPreviewWidgets($preview) {
        $widgets = [];
        foreach ($this->getWidgets() as $widget) {
            $previews = array_filter($widget->getPreviews(), function ($p) use ($preview) { return $preview->qid == $p->qid; });
            if ($previews) {
               $widgets[] = $widget;
            }
        }

        return $widgets;
    }

}