<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 27.08.17
 * Time: 19:14
 */

namespace main\components;

use main\models\DataConfig;
use main\models\MarkupComponent;
use main\models\Project;
use main\models\Requirement;
use Symfony\Component\Yaml\Yaml;
use yii\base\Component;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ProjectConfig
 * @package main\components
 *
 * @property array $config
 */
class ProjectConfig extends Component
{

    public function getProjects() {
        $projects = [];
        foreach (glob(Yii::getAlias('@root/projects') . '/*', GLOB_ONLYDIR) as $dir) {
            $projects[] = $this->getProject(basename($dir));
        }

        return $projects;
    }

    protected $_config;
    public function getConfig() {
        if (!$this->_config) {
            $config = Yaml::parse(file_get_contents(Yii::getAlias('@root/lint-config.yaml')));
            $config_local = Yaml::parse(file_get_contents(Yii::getAlias('@root/lint-config-local.yaml')));
            $this->_config = ArrayHelper::merge($config, $config_local);
        }

        return $this->_config;
    }

    protected $_requirements;

    /**
     * @param $fqid
     * @return Requirement
     */
    public function getRequirementByFqid($fqid) {
        if (!isset($this->_requirements[$fqid])) {
            list ($projectSid, $requirementSid) = explode('.', $fqid);
            $requirement = new Requirement(['sid' => $requirementSid, 'projectSid' => $projectSid]);
            $this->_requirements[$fqid] = $requirement;
        }

        return $this->_requirements[$fqid];
    }

    protected $_projectInstances;
    public function getProject($projectSid)  {
        if (!isset($this->_projectInstances[$projectSid])) {
            $this->_projectInstances[$projectSid] = new Project(['name' => $projectSid]);
        }

        return $this->_projectInstances[$projectSid];
    }







    protected function populateDataConfig($config) {
        foreach ($config['pages'] as $sid => &$page) {
            $page['sid'] = $sid;

            foreach ($page['previews'] as $previewSid => &$preview) {
                $preview['sid'] = $previewSid;
            }
        }

        foreach ($config['components'] as $sid => &$component) {
            $markupComponent = new MarkupComponent($component);
            $markupComponent['sid'] = $sid;
            $component = $markupComponent;
//            if (!isset($component['onPreviews'])) $component['onPreviews'] = [];
//
//            foreach ($component['onPreviews'] as &$onPreview) {
//                if (!isset($onPreview['appearance'])) $onPreview['appearance'] = [];
//
//                foreach ($onPreview['appearance'] as &$appearance) {
//                    if (!isset($appearance['show'])) $appearance['show'] = 'auto';
//                }
//
//            }
        }

        return $config;
    }

    protected $_dataConfig;
    public function getDataConfig() {
        if (!$this->_dataConfig) {
            $config = Yaml::parse(file_get_contents(Yii::getAlias('@data/config/config.yaml')));
            $dataConfig = new DataConfig(['raw' => $config]);
//            $config = $this->populateDataConfig($config);
            $this->_config = $dataConfig;
        }

        return $this->_config;
    }

    public function getPagesWithResolutions() {
        $config = $this->config;
        $activePages = array_diff($config['global']['pages'], $config['global']['excludePages']);

        $result = [];

        foreach ($activePages as $page) {
            foreach ($config['global']['resolutions'] as $width) {
                if (isset($config['global']['pageSettings'][$page]['resolutions'])) {
                    if (!in_array($width, $config['global']['pageSettings'][$page]['resolutions'])) {
                        continue;
                    }
                }
                $result[$page][] = $width;
            }
        }

        return $result;
    }

    public function getOtherDesignFiles($page) {
        $files = glob(Yii::getAlias('@root/review/design/' . $page . '/other/*'));
        $result = [];
        foreach ($files as $file) {
            $result[] = basename($file);
        }
        return $result;
    }

    public function getOtherGlobalDesignFiles() {
        $files = glob(Yii::getAlias('@root/review/design/_other/*'));
        $result = [];
        foreach ($files as $file) {
            $result[] = basename($file);
        }
        return $result;
    }

    public function getOtherLiveFiles($page, $width) {
        $files = glob(Yii::getAlias(
            "@root/review/live/v{$this->config['global']['version']}/$page/$width-*"
        ));

        $result = [];
        foreach ($files as $file) {
            $file = basename($file);
            $parts = preg_match("/^[0-9]+-(?P<type>[a-z]+)\.(?P<ext>[a-z]+)$/", $file, $matches);
            if (!$matches) {
                throw new \Exception("Can't parse file name '$file' into parts");
            }
            $result[$matches['type']] = $file;
        }

        return $result;
    }

}