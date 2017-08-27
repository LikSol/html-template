<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 14:57
 */

namespace main\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Обязательно должны быть подключены сразу, чтобы в верстке не возникло конфликтов с ними.
 *
 * 1. Bootstrap
 * 2. Jquery
 *
 * Class MandatoryAsset
 * @package main\assets
 */
class MandatoryAsset extends AssetBundle
{
    public $css = [
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
    ];

    public $js = [
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
    ];

    public $depends = [
        JqueryAsset::class
    ];
}