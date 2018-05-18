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
        if (strpos($this->sid, '/') !== false) {
            $file = $this->getDir() . '/' . basename($this->sid) . '.html.twig';
        } else {
            $file = $this->getDir() . '/' . $this->sid . '.html.twig';
        }

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
        if (strpos($this->sid, '/') !== false) {
            $dir = $this->project->getPagesDir() . '/' . dirname($this->sid);
        } else {
            $dir = $this->project->getPagesDir() . '/' . $this->sid;
        }
        return $dir;
    }

    public function getUrl($args = []) {
        $route = array_merge(
            $args, // в таком порядке, во второй строке аргументы важнее
            ['projectName' => $this->project->name, 'page' => $this->sid . '/' . $this->sid]
        );

        $route[0] = 'page/show';

        $url = Url::to($route);
        return $url;
    }

    public function getName() {
        $parts = explode('/', $this->sid);
        return end($parts);
    }

}