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
            'rules' => [
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => null,
                    'pattern' => 'page/-/component-image/',
                    'route' => 'page/show-component-image'
                ],
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => null,
                    'pattern' => 'page/-/show-all-components-css/',
                    'route' => 'page/show-all-components-css'
                ],
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => null,
                    'pattern' => 'page/<page:[\w_0-9/-]+\.(gif|jpg|png|svg|ico)>',
                    'route' => 'page/show-image'
                ],
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => null,
                    'pattern' => 'page/<page:[\w_0-9/-]+\.(css)>',
                    'route' => 'page/show-css'
                ],
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => '.html',
                    'pattern' => 'page/<page:[\w_0-9/-]+>',
                    'route' => 'page/show'
                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => null,
                    'normalizer' => [
                        'class' => \yii\web\UrlNormalizer::class,
                        'action' => null,
                    ],
                    'pattern' => 'page/<page:[\w_0-9/-]+>',
                    'route' => 'page/show'
                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => '/',
                    'pattern' => 'template/layout',
                    'route' => 'template/layout'
                ],
                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => '/',
                    'pattern' => 'template/all-pages',
                    'route' => 'template/all-pages'
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
        'projectConfig' => [ 'class' => \main\components\ProjectConfig::class ],
    ]
];

$asm_config = require Yii::getAlias('@common/config/_asm.php');
$common_components = require Yii::getAlias('@common/config/_components-common.php');

$config = \yii\helpers\ArrayHelper::merge(
    \yii\helpers\ArrayHelper::merge($asm_config, $common_components),
    $config
);

return $config;
