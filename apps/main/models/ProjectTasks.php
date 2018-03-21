<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use yii\base\BaseObject;

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
}