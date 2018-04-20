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
        'widget' => function ($widgetName) {
            /** @var \main\models\Project $project */
            $project = Yii::$app->view->params['html-template.project.current'];

            $args = func_get_args();
            array_shift($args);

            switch (true) {
                case !$args:
                    $context = [
                        'arg' => null,
                    ];
                    break;
                case count($args) == 1 && !is_array(current($args)):
                    $context = [
                        'arg' => current($args),
                    ];
                    break;
                case count($args) == 1 && is_array(current($args)):
                    $context = current($args);
                    $context['arg'] = null;
                    break;
                default:
                    throw new \Exception("Too many widget arguments");
            }

            $context['class'] = $project->name . '-' . $widgetName;

            $file = $project->getWidgetView($widgetName);

            if (!isset(Yii::$app->view->params['html-template.widget.stack'])) {
                Yii::$app->view->params['html-template.widget.stack'] = [$widgetName];
            } else {
                Yii::$app->view->params['html-template.widget.stack'][] = $widgetName;
            }

            // renderFile, потому что там не @alias, а абсолютный путь
            $result = Yii::$app->view->renderFile($file, $context);

            array_pop(Yii::$app->view->params['html-template.widget.stack']);

            return $result;
        },
        'img' => function ($param) {
            /**
             * @DEPRECATED
             *
             * Use url()
             */
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
        'imgUrl' => function ($param) {
            /**
             * @DEPRECATED
             *
             * Use iurl()
             */

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
        'url' => function ($param) {
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
        'sample' => function ($what, $params = []) {
            switch ($what) {
                case 'pagination':
                    $pagination = new \yii\data\Pagination([
                            'totalCount' => @$params['totalCount'] ?: 1000,
                            'page' => @$params['page'] ?: 5,
                            'defaultPageSize' => 500,
                        ]
                    );

                    array (
                        'pageParam' => 'page',
                        'pageSizeParam' => 'per-page',
                        'forcePageParam' => true,
                        'route' => NULL,
                        'params' => NULL,
                        'urlManager' => NULL,
                        'validatePage' => true,
                        'pageSizeLimit' =>
                            array (
                                0 => 1,
                                1 => 50,
                            ),
                        '' . "\0" . 'yii\\data\\Pagination' . "\0" . '_pageSize' => 20,
                        '' . "\0" . 'yii\\data\\Pagination' . "\0" . '_page' => 0,
                    );
                    return $pagination;
                default:
                    throw new \Exception("Unknown sample name");

            }
        }
    ],
];