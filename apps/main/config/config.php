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
                'template/<page:[\w_-]+>' => 'template/page'
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
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
