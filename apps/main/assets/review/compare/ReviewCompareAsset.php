<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 27.08.17
 * Time: 20:23
 */

namespace main\assets\review\compare;

use common\assets\ElevateZoomAsset;
use yii\web\AssetBundle;

class ReviewCompareAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $js = [
        'ui-internal/review/compare/index.js'
    ];

    public $depends = [
        ElevateZoomAsset::class
    ];
}