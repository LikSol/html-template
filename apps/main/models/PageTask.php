<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 13.03.18
 * Time: 13:52
 */

namespace main\models;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class PageTask extends BaseObject
{
    public $sid;
    public $definition;
    /**
     * @var Project
     */
    public $project;

    protected $_previews;
    public function getPreviews($config = []) {
        if ($this->_previews === null) {
            $previews = [];
            foreach (@$this->definition['widths'] ?: [] as $width) {
                list($designSid, $previewSid) = explode('.', $width['preview']);
                $design = $this->project->getDesignBySid($designSid);
                $preview = $design->getPreviewBySid($previewSid);
                $previews[] = $preview;
            }

            $this->_previews = $previews;
        }

        $previews = $this->_previews;

        if (@$config['sort']) {
            list ($field, $directon) = each($config['sort']);
            if (is_string($directon)) $directon = constant($directon);
            ArrayHelper::multisort($previews, $field, $directon);
        }

        return $previews;
    }
}