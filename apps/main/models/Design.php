<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:16
 */

namespace main\models;

use yii\base\Model;

class Design extends Model
{
    public $sid;
    public $previews;

    /**
     * @param $sid
     * @return DesignPreview
     */
    public function getPreviewBySid($sid) {
        return $this->previews[$sid];
    }
}