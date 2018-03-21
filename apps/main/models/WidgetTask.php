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

}