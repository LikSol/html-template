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

    public function getFileExists() {
        return file_exists($this->getFile());
    }

    public function getFile($relativeTo = null) {
        $file = $this->getDir() . '/' . $this->sid . '.html.twig';
        if ($relativeTo) {
            $relativeFile = preg_replace('/^' .preg_quote(\Yii::getAlias($relativeTo), '/'). '/', '', $file);
            if ($relativeFile == $file) {
                throw new \Exception("File $file is not relative to $relativeTo");
            }
            $file = trim($relativeFile, '/');
        }
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