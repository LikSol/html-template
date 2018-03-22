<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use yii\base\BaseObject;

class PageTask extends BaseObject
{
    public $sid;
    public $definition;
    /**
     * @var Project
     */
    public $project;

    public function getPreviews() {
        $previews = [];
        foreach (@$this->definition['widths'] ?: [] as $width) {
            list($designSid, $previewSid) = explode('.', $width['preview']);
            $design = $this->project->getDesignBySid($designSid);
            $preview = $design->getPreviewBySid($previewSid);
            $previews[] = $preview;
        }
        return $previews;
    }
}