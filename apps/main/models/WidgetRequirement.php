<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:04
 */

namespace main\models;

use Yii;
use yii\base\Model;

class WidgetRequirement extends Model
{
    public $requirementFqid;
    public $params;

    public function getFqid() {
        return $this->requirementFqid;
    }

    protected $_requirement;
    public function getRequirement() {
        if (!$this->_requirement) {
            $PC = Yii::$app->projectConfig;
            $this->_requirement = $PC->getRequirementByFqid($this->requirementFqid);
        }

        return $this->_requirement;
    }

}