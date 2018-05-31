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
    'container' => [
        'definitions' => [
            // prevent bootstrap inclusion (by yii2 widgets)
            \yii\bootstrap\BootstrapAsset::class => [ 'class' => \main\assets\BootstrapVoidAsset::class],
            // prevent jquery inclusion (by yii2 widgets)
            \yii\web\JqueryAsset::class => [ 'class' => \main\assets\JqueryVoidAsset::class],
        ],
    ],

    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                /*
                 * Pages
                 */

                [
                    'class' => \yii\web\UrlRule::class,
                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/src/<file:.+>',
                    'encodeParams' => false, // чтобы можно было задавать page со слешами
                    'route' => 'page/show-file'
                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => '.html',
                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/src/pages/<page:[\w_0-9/-]+>',
                    'encodeParams' => false, // чтобы можно было задавать page со слешами
                    'route' => 'page/show'
                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/src/<asset:[\w_0-9/-]+\.(jpg|gif|ico|png|svg|css|js)>',
                    'route' => 'page/show-project-asset'
                ],

//                [
//                    'class' => \yii\web\UrlRule::class,
//                    'suffix' => null,
//                    'normalizer' => [
//                        'class' => \yii\web\UrlNormalizer::class,
//                        'action' => null,
//                    ],
//                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/work/pages/<page:[\w_0-9/-]+>',
//                    'route' => 'page/show-directory',
//                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/src/widgets/<widgetName:[\w_0-9/-]+>/<asset:[\w_0-9/-]+\.(jpg|gif|ico|png|svg|css|js)>',
                    'route' => 'page/show-widget-asset'
                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/src/pages/<pageSid:[\w_0-9/-]+>/<asset:[\w_0-9/-]+\.(jpg|gif|ico|png|svg|css|js)>',
                    'route' => 'page/show-page-asset'
                ],

                [
                    'class' => \yii\web\UrlRule::class,
                    'suffix' => null,
                    'pattern' => 'projects/<projectName:[\w_0-9/-]+>/---generated---/all-widgets.css',
                    'route' => 'page/show-all-widgets-css'
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
        'HTWidget' => [
            'class' => \common\components\HTWidget::class,
            'defaultProject' => 'als2',
            'mode' => 'template',
            'mocks' => true,
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
