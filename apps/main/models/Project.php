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
use yii\helpers\FileHelper;
use yii\helpers\Url;

class Project extends BaseObject
{
    public $name;

    public function getSrcDir() {
        return $this->getRootDir() . '/src';
    }

    public function getRootDir() {
        return Yii::getAlias('@root/projects') . '/' . $this->name;
    }

    public function getPagesDir() {
        return $this->getSrcDir() . '/pages';
    }

    public function getPageDir($pageSid) {
        return dirname($this->getPagesDir() . '/' . $pageSid);
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

    public function getDesc() {
        return $this->config->raw['project']['desc'];
    }

    public function getPages() {
        $pagesDir = $this->getPagesDir();
        $files = FileHelper::findFiles($pagesDir, ['only' => ['*.html', '*.html.twig']]);
        $pages = [];
        foreach ($files as $file) {
            $preg = '/^' . preg_quote($pagesDir, '/') . '\/?/';
            $file = preg_replace($preg, '', $file);
            $preg = '/\.html(\.twig)?$/';
            $file = preg_replace($preg, '', $file);

            $pages[$file] = Url::to(['page/show', 'projectName' => $this->name, 'page' => $file]);
        }

        return $pages;
    }

    protected $_designs;
    public function getDesigns() {
        if (!isset($this->getConfig()->raw['designs'])) return [];

        if (!$this->_designs) {
            $designs = [];
            foreach ($this->getConfig()->raw['designs'] as $sid => $data) {
                $design = new Design([
                    'sid' => $sid
                ]);

                foreach ($data as $previewSid => $item) {
                    $preview = new DesignPreview($item);
                    $preview->sid = $previewSid;
                    $design->previews[$previewSid] = $preview;
                }

                $designs[$sid] = $design;
            }


            $this->_designs = $designs;
        }

        return $this->_designs;
    }

    /**
     * @param $sid
     * @return Design
     */
    public function getDesignBySid($sid) {
        return $this->getDesigns()[$sid];
    }

    public function getSid() {
        return $this->name;
    }
}