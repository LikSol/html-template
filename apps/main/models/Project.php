<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use Symfony\Component\Yaml\Yaml;
use Yii;
use yii\base\BaseObject;

class Project extends BaseObject
{
    public $name;

    public function getSrcDir() {
        return $this->getRootDir() . '/src';
    }

    public function getRootDir() {
        return '@root/projects/' . $this->name;
    }

    public function getPagesDir() {
        return $this->getSrcDir() . '/pages';
    }

    public function getWidgetsDir() {
        return $this->getSrcDir() . '/widgets';
    }

    public function getWidgetDir($widgetName) {
        return $this->getWidgetsDir() . '/' . $widgetName;
    }

    public function getWidgetView($widgetName) {
        return $this->getWidgetDir($widgetName) . '/' . $widgetName . '.html.twig';
    }

    protected $_config;
    public function getConfig() {
        if (!$this->_config) {
            $config = Yaml::parse(file_get_contents(Yii::getAlias($this->getRootDir() . "/config/config.yaml")));
            $dataConfig = new DataConfig(['raw' => $config]);
            $this->_config = $dataConfig;
        }

        return $this->_config;
    }
}