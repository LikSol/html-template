<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:16
 */

namespace main\models;

use yii\base\Model;
use yii\helpers\Url;

class DesignPreview extends Model
{
    public $sid;
    public $file;
    public $width;
    /**
     * @var Design
     */
    public $design;

    public function getImageUrl() {
        return Url::to([
            'project/show-preview-image',

            'projectSid' => $this->design->project->sid,
            'designSid' => $this->design->sid,
            'previewSid' => $this->sid,

        ]);
    }

    public function getQid() {
        return $this->design->sid . '.' . $this->sid;
    }
}