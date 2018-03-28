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

    /**
     * @return DesignPreview
     */
    public function getPreview() {
        if ($this->_preview === null) {
            $this->_preview = $this->project->getPreviewByQid($this->definition['preview']);
        }

        return $this->_preview;
    }

    public function hasPreview($preview) {
        $previewQid = (is_object($preview)) ? $preview->qid : $preview;

        return $this->definition['preview'] == $previewQid;
    }

    public function getComment() {
        return @$this->definition['comment'];
    }

    public function getCoords() {
        // [0], потому что планируем перейти на схему: 1 appearance - одни координаты
        return $this->definition['coords'][0];
    }
}