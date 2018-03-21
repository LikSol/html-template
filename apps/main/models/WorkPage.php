<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use yii\base\BaseObject;
use yii\helpers\Url;

class WorkPage extends BaseObject
{
    public $sid;
    /**
     * @var Project
     */
    public $project;
    public $fileExists;

    public function getFile() {
        $file = $this->getDir() . '/' . $this->sid . '.html.twig';
        return $file;
    }

    public function getDir() {
        $dir = $this->project->getPagesDir() . '/' . $this->sid;
        return $dir;
    }

    public function getUrl() {
        $url = Url::to(['page/show', 'projectName' => $this->project->name, 'page' => $this->sid . '/' . $this->sid]);
        return $url;
    }

}