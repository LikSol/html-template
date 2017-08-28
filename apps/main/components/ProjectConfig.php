<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 27.08.17
 * Time: 19:14
 */

namespace main\components;

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
    protected $_config;
    public function getConfig() {
        if (!$this->_config) {
            $config = Yaml::parse(file_get_contents(Yii::getAlias('@root/lint-config.yaml')));
            $config_local = Yaml::parse(file_get_contents(Yii::getAlias('@root/lint-config-local.yaml')));
            $this->_config = ArrayHelper::merge($config, $config_local);
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