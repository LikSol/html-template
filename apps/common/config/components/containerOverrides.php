<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 15.07.17
 * Time: 12:26
 */

return [
    'class' => \common\components\ContainerOverrides::class,
    'overrides' => [
        [
            'class' => 'yii\web\JqueryAsset',
            'js' => [
                'https://code.jquery.com/jquery-3.2.1.min.js',
            ]
        ],
    ]
];
