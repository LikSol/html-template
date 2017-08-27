<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:57
 */

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ElevateZoomAsset extends AssetBundle
{
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/elevatezoom/3.0.8/jquery.elevatezoom.min.js',
    ];

    public $depends = [
        JqueryAsset::class
    ];
}