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
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class Project extends BaseObject
{
    public $name;

    public function getRelativeDir($target, $relative_to) {
        $prefix = $relative_to;
        $str = $target;

        if (substr($str, 0, strlen($prefix)) == $prefix) {
            $str = substr($str, strlen($prefix));
        }

        return $str;
    }

    public function getSrcDir() {
        return $this->getRootDir() . '/src';
    }

    public function getRootDir() {
        return Yii::getAlias('@root/projects') . '/' . $this->name;
    }

    public function getPagesDir() {
        return $this->getSrcDir() . '/pages';
    }

    public function getRequirementsDir() {
        return $this->getRootDir() . '/config/requirements';
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
            $configFile = Yii::getAlias($this->getRootDir() . "/config/config.yaml");
            if (file_exists($configFile)) {
                $config = Yaml::parse(file_get_contents($configFile));
            } else {
                $config = [];
            }

            $imports = @$config['imports'] ?: [];
            foreach ($imports as $import) {
                $imported = Yaml::parse(file_get_contents(Yii::getAlias($this->getRootDir() . "/config/{$import['resource']}")));
                $config = ArrayHelper::merge($config, $imported);
            }

            $dataConfig = new DataConfig(['raw' => $config]);

            $this->_config = $dataConfig;
        }

        return $this->_config;
    }

    public function getDesc() {
        return @$this->config->raw['project']['desc'];
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
                    'sid' => $sid,
                    'project' => $this,
                ]);

                foreach ($data as $previewSid => $item) {
                    $preview = new DesignPreview($item);
                    $preview->design = $design;
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

    protected $_tasks;
    public function getTasks() {
        if (!$this->_tasks) {
            $this->_tasks = new ProjectTasks([
                'project' => $this,
            ]);
        }

        return $this->_tasks;
    }

    protected $_work;
    public function getWork() {
        if (!$this->_work) {
            $this->_work = new ProjectWork([
                'project' => $this,
            ]);
        }

        return $this->_work;
    }

    public function getPreviewByQid($qid) {
        list($designSid, $previewSid) = explode('.', $qid);
        $design = $this->getDesignBySid($designSid);
        return $design->getPreviewBySid($previewSid);
    }
}