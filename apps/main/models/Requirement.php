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

class Requirement extends Model
{
    public $sid;
    public $projectSid;

    public function getFqid() {
        return $this->projectSid . '.' . $this->sid;
    }

    protected $_project;

    /**
     * @return Project
     */
    public function getProject() {
        if (!$this->_project) {
            /** @var \main\components\ProjectConfig $PC */
            $PC = Yii::$app->projectConfig;
            $this->_project = $PC->getProject($this->projectSid);
        }

        return $this->_project;

    }

    protected function renderDoc($name, $target = null) {
        $dir = $this->getProject()->getRequirementsDir() . '/' . $this->sid;
        $file = "$dir/$name.md.php";

        if (!file_exists($file)) return null;

        $rerender = Yii::$app->view->renderFile(
            $file,
            [
                'target' => $target,
            ]
        );

        return \yii\helpers\Markdown::process($rerender, 'gfm');
    }

    public function getReadme($target = null) {
        return $this->renderDoc('README', $target);
    }

    public function getDocForTarget($target = null) {
        return $this->renderDoc('for-target', $target);
    }

    public function getTLDR($target = null) {
        return $this->renderDoc('TLDR', $target);
    }

    protected $_name;
    public function getName() {
        if (is_null($this->_name)) {
            if ($name = @$this->getConfig()['name']) {
                $this->_name = $name;
            } else {
                $dir = $this->getProject()->getRequirementsDir() . '/' . $this->sid;
                $file = "$dir/.name";

                $this->_name = file_exists($file) ? file_get_contents($file) : false;
            }
        }

        return $this->_name;
    }

    protected $_config;
    protected function getConfig() {
        if ($this->_config === null) {
            $projectConfig = $this->getProject()->getConfig()->raw;
            $this->_config = @$projectConfig['requirements'][$this->sid] ?: [];
        }

        return $this->_config;
    }

    public function iterateIncludes() {
        if (!$includes = @$this->getConfig()['includes']) return;

        /** @var \main\components\ProjectConfig $PC */
        $PC = Yii::$app->projectConfig;

        foreach ($includes as $requirementSid => $params) {
            $fqid = preg_match('/\./', $requirementSid) ? $requirementSid : $this->getProject()->sid . '.' . $requirementSid;
            $requirement = $PC->getRequirementByFqid($fqid);
            foreach ($requirement->iterateIncludes() as $include) {
                yield $include;
            }
            yield $requirement;
        }
    }

}