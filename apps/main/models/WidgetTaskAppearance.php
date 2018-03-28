<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 28.03.18
 * Time: 12:09
 */

namespace main\models;


use yii\base\BaseObject;

class WidgetTaskAppearance extends BaseObject
{
    public $sid;
    public $definition;
    public $project;

    protected $_preview;
    public function getPreview() {
        if ($this->_preview === null) {
            list($designSid, $previewSid) = explode('.', $this->definition['preview']);
            $design = $this->project->getDesignBySid($designSid);
            $this->_preview = $design->getPreviewBySid($previewSid);
        }

        return $this->_preview;
    }

    public function getComment() {
        return @$this->definition['comment'];
    }
}