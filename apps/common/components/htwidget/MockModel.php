<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 01.05.18
 * Time: 1:19
 */

namespace common\components\htwidget;

use yii\base\Model;

class MockModel extends Model
{

    protected $_strict = true;

    protected $_properties = [];
    public function __get($name)
    {
        if ($this->_strict && !array_key_exists($name, $this->_properties)) {
            throw new \Exception("Unknown property: $name");
        }
        return @$this->_properties[$name];
    }

    public function __set($name, $value) {
        if ($name != 'validation') {
            $this->_properties[$name] = $value;
            return;
        } else {
            $this->setValidation($value);
        }
    }

    protected function setValidation($rules) {
        $this->_rules = $rules;
    }

    protected $_rules;
    public function rules()
    {
        return $this->_rules ?: [];
    }

    public function fillIfEmpty($values = []) {
        foreach ($values as $name => $value) {
            if (!array_key_exists($name, $this->_properties)) {
                $this->$name = $value;
            }
        }
    }

    public function toggleStrict($value) {
        $this->_strict = (bool) $value;
        return $this;
    }
}