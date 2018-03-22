<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:57
 */

namespace main\assets;

use yii\web\AssetBundle;

class HighlightAsset extends AssetBundle
{
    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/languages/php.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/languages/twig.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/languages/scss.min.js',
    ];

}