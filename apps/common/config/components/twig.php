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
        'Markdown' => ['class' => \yii\helpers\Markdown::class],
    ],
    'functions' => [
        'setParam' => function (\yii\base\View $view, $param, $value) {
            $view->params[$param] = $value;
        },
        'widget' => function ($widgetName, $subclasses = null) {
            /** @var \main\models\Project $project */
            $project = Yii::$app->view->params['html-template.project.current'];

            $file = $project->getWidgetView($widgetName);

            if (!isset(Yii::$app->view->params['html-template.widget.stack'])) {
                Yii::$app->view->params['html-template.widget.stack'] = [$widgetName];
            } else {
                Yii::$app->view->params['html-template.widget.stack'][] = $widgetName;
            }

            // renderFile, потому что там не @alias, а абсолютный путь
            $result = Yii::$app->view->renderFile($file, [
                'class' => $project->name . '-' . $widgetName,
                'subclasses' => $subclasses
            ]);

            array_pop(Yii::$app->view->params['html-template.widget.stack']);

            return $result;
        },
        'img' => function ($param) {
            if (!isset(Yii::$app->view->params['html-template.widget.stack'])) {
                throw new \Exception("Not implemented");
            } else {
                $widgetName = end(Yii::$app->view->params['html-template.widget.stack']);
            }

            $project = Yii::$app->view->params['html-template.project.current'];

            $url = \yii\helpers\Url::to([
                'page/show-widget-asset',
                'projectName' => $project->name,
                'widgetName' => $widgetName,
                'asset' => "$param"
            ]);

            return $url;
            },
    ],
];