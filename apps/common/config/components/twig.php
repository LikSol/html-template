<?php

return [
    'class' => 'yii\twig\ViewRenderer',
    'cachePath' => '@runtime/Twig/cache',
    'options' => [
        'strict_variables' => \cronfy\env\Env::isDebug() ? true : false,
        'auto_reload' => true,
        // чтобы не нужно было постоянно писать |raw в шаблонах
        'autoescape' => false,
    ],
    'globals' => [
        'ArrayHelper' => ['class' => '\yii\helpers\ArrayHelper'],
        'Html' => ['class' => '\yii\helpers\Html'],
        'Url' => ['class' => '\yii\helpers\Url'],
    ],
    'functions' => [
        'setParam' => function (\yii\base\View $view, $param, $value) {
            $view->params[$param] = $value;
        },
        'component' => function ($param) {
            $namespace = Yii::$app->view->params['html-template.namespace.current'];

            $file = '@data/work/' . $namespace . '/components/' . $param . '/' . $param . '.html.twig';

            if (!isset(Yii::$app->view->params['html-template.component.stack'])) {
                Yii::$app->view->params['html-template.component.stack'] = [$param];
            } else {
                Yii::$app->view->params['html-template.component.stack'][] = $param;
            }

            $result = Yii::$app->view->render($file, [
                'class' => $namespace . '-' . $param
            ]);

            array_pop(Yii::$app->view->params['html-template.component.stack']);

            return $result;
        },
        'img' => function ($param) {
            if (!isset(Yii::$app->view->params['html-template.component.stack'])) {
                throw new \Exception("Not implemented");
            } else {
                $component = end(Yii::$app->view->params['html-template.component.stack']);
            }

            $namespace = Yii::$app->view->params['html-template.namespace.current'];

            $url = \yii\helpers\Url::to([
                'page/show-component-image',
                'namespace' => $namespace,
                'component' => $component,
                'path' => "$param"
            ]);

            return $url;
            },
    ],
];