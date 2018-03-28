<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use yii\base\BaseObject;
use yii\helpers\Url;

class WidgetTask extends BaseObject
{
    public $sid;
    public $definition;
    /**
     * @var Project
     */
    public $project;

    /**
     * @deprecated  use getAppearances()
     *
     * @return DesignPreview[]
     */
    public function getPreviews() {
        $previews = [];
        foreach (@$this->definition['appearances'] ?: [] as $appearanceSid => $appearance) {
            list($designSid, $previewSid) = explode('.', $appearance['preview']);
            $design = $this->project->getDesignBySid($designSid);
            $preview = $design->getPreviewBySid($previewSid);
            $previews[$appearanceSid] = $preview;
        }
        return $previews;
    }

    protected $_appearances;

    /**
     * @return WidgetTaskAppearance[]
     */
    public function getAppearances() {
        if ($this->_appearances === null) {
            $appearances = [];
            foreach (@$this->definition['appearances'] ?: [] as $appearanceSid => $definition) {
                $appearance = new WidgetTaskAppearance([
                    'sid' => $appearanceSid,
                    'definition' => $definition,
                    'project' => $this->project,
                ]);
                $appearances[$appearanceSid] = $appearance;
            }

            $this->_appearances = $appearances;
        }

        return $this->_appearances;
    }

    public function hasPreview($preview) {
        foreach ($this->getAppearances() as $appearance) {
            if ($appearance->hasPreview($preview)) return true;
        }

        return false;
    }

    public function getPreviewUrl($preview) {
        $url = Url::to([
            'project/show-widget-task-preview',

            'projectSid' => $this->project->name,
            'widgetSid' => $this->sid,
            'designSid' => $preview->design->sid,
            'previewSid' => $preview->sid,
        ]);

        return $url;
    }

    /**
     * @param $preview DesignPreview
     * @return array
     */
    public function getPreviewCoords($preview) {
        $coords = [];

        $previewQid = $preview->getQid();

        foreach (@$this->definition['appearances'] ?: [] as $appearanceSid => $appearance) {
            if ($appearance['preview'] == $previewQid) {
                $coords = array_merge($coords, $appearance['coords']);
            }
        }

        return $coords;
    }

    public function getRequirementNames() {
        $names = array_keys(@$this->definition['requirements'] ?: []);
        return $names;
    }

    protected $_requirements;
    public function getRequirements() {
        if (!$this->_requirements) {
            foreach (@$this->definition['requirements'] ?: [] as $requirementFqid => $params) {
                $this->_requirements[] = new WidgetRequirement([
                    'requirementFqid' => $requirementFqid,
                    'params' => $params,
                ]);
            }

        }

        return $this->_requirements;
    }

    public function getComment() {
        return @$this->definition['comment'];
    }

}