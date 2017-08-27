<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 15.07.17
 * Time: 12:24
 */

namespace common\components;

use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class ContainerOverrides extends Component implements BootstrapInterface
{
    public $overrides = [];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        foreach ($this->overrides as $override) {
            $className = ArrayHelper::getValue($override, 'class');
            if ($className == null) {
                throw new InvalidConfigException('Override items must be an array containing a "class" element.');
            }
            unset($override['class']);

            $container->set($className, $override);
        }
    }
}