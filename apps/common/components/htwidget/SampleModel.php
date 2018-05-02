<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 01.05.18
 * Time: 1:19
 */

namespace common\components\htwidget;

use yii\base\Model;

class SampleModel extends Model
{

    protected $_properties;
    public function __get($name)
    {
        return @$this->_properties[$name];
    }

    public function __set($name, $value) {
        $this->_properties[$name] = $value;
    }
}