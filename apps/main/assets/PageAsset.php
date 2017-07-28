<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:57
 */

namespace main\assets;

use yii\web\AssetBundle;
use Yii;

class PageAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $depends = [
        MandatoryAsset::class
    ];

    public function init() {
        parent::init();

        $this->js = Yii::$app->autoAssets->js;
        $this->css = Yii::$app->autoAssets->css;
    }
}