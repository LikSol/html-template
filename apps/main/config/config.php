<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 16.03.17
 * Time: 10:09
 */

$config = [
    'id' => 'main',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'main\controllers',
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '.html',
            'rules' => [
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => '/',
                    'pattern' => 'template/layout',
                    'route' => 'template/layout'
                ],
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => '.html',
                    'pattern' => 'template/<page:[\w_-]+>',
                    'route' => 'template/page'
                ],
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'not-secure-intentionally'
        ],
        'autoAssets' => [ 'class' => \main\components\AutoAssets::class ],
    ]
];

$asm_config = require Yii::getAlias('@common/config/_asm.php');
$common_components = require Yii::getAlias('@common/config/_components-common.php');

$config = \yii\helpers\ArrayHelper::merge(
    \yii\helpers\ArrayHelper::merge($asm_config, $common_components),
    $config
);

return $config;
