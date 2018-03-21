<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 11:02
 */

namespace main\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ComponentMarkerAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $js = [
        'ui-internal/component-marker/component-marker.js'
    ];

    public $css = [
        'ui-internal/component-marker/component-marker.css'
    ];

    public $depends = [
        JqueryAsset::class,
        JuiAsset::class,
    ];
}