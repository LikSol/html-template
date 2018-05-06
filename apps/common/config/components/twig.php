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
    'uses' => [ 'yii\widgets' ],
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

            $jsFile = dirname($file) . "/$widgetName.js";
            if (file_exists($jsFile)) {
                Yii::$app->view->registerJsFile(\yii\helpers\Url::to(['page/show-widget-asset',
                    'projectName' => $project->name,
                    'widgetName' => $widgetName, 'asset' => basename($jsFile)
                ]), ['depends' => \main\assets\ProjectPageAsset::class], $widgetName);
            }

            // чтобы нельзя было в шаблонах верстки обращаться к yii.
            // Интерфейс шаблонов должен быть ограничен только HTWidget
            $context['app'] = null;
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
            if (is_array($param)) {
                return '#'; // в верстке будет #, в боевом проекте - прокси для Url.to()
            }

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
            return \common\components\HTWidget::sample($what, $params);
        },
        'jsExpression' => function ($data) {
            return \common\components\HTWidget::jsExpression($data);
        },
        'requestGet' => function ($name, $default = null) {
            return \common\components\HTWidget::requestGet($name, $default);
        },
        'plural' => function ($number, $endings) {
            $cases = [2, 0, 1, 1, 1, 2];
            $n = $number;
            return sprintf($endings[ ($n%100>4 && $n%100<20) ? 2 : $cases[min($n%10, 5)] ], $n);
        }
    ],
];