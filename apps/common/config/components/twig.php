<?php

return [
    'class' => 'yii\twig\ViewRenderer',
    'cachePath' => '@runtime/Twig/cache',
    'options' => [
        'strict_variables' => \cronfy\asm\Asm::isDebug() ? true : false,
        'auto_reload' => true,
        'autoescape' => 'filename',
    ],
    'globals' => [
        'ArrayHelper' => ['class' => '\yii\helpers\ArrayHelper'],
        'Html' => ['class' => '\yii\helpers\Html'],
        'Url' => ['class' => '\yii\helpers\Url'],
    ],
    'functions' => [
        'setParam' => function (\yii\base\View $view, $param, $value) {
            $view->params[$param] = $value;
        }
    ],
];