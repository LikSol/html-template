<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 17.02.17
 * Time: 18:21
 */

return [
    'bootstrap' => ['log'],

    'language' => 'ru-RU',

    'components' => [
        'view' => [
            'renderers' => [
                'twig' => require __DIR__ . '/components/twig.php'
            ]
        ],
    ],
];
