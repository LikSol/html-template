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
}