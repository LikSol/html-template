<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:16
 */

namespace main\models;

use yii\base\Model;

class MarkupPage extends Model
{
    public $sid;


    protected $_previewsRaw;
    public function setPreviews($value) {
        $this->_previewsRaw = $value;
    }

    protected $_previews = [];
    public function getPreview($sid) {
        if (!isset($this->_previews[$sid])) {
            $pagePreview = new MarkupPagePreview($this->_previewsRaw[$sid]);
            $pagePreview['sid'] = $sid;
            $this->_previews[$sid] = $pagePreview;
        }
        return $this->_previews[$sid];
    }

    public function getPreviews() {
        $previews = [];
        foreach (array_keys($this->_previewsRaw) as $previewSid) {
            $previews[] = $this->getPreview($previewSid);
        }
        return $previews;
    }
}