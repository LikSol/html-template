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
        'widget' => function () {
            return call_user_func_array('\common\components\HTWidget::renderDeprecated', func_get_args());
        },
        'requireWidget' => function () {
            return call_user_func_array('\common\components\HTWidget::requireWidget', func_get_args());
        },
        'filterContext' => function ($context) {
            return call_user_func_array(\common\components\HTWidget::class . '::filterContext', func_get_args());
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

            /** @var \main\models\Project $project */
            $project = Yii::$app->view->params['html-template.project.current'];

            $absCurrentViewFile = Yii::$app->view->getViewFile();
            $absCurrentViewDir = dirname($absCurrentViewFile);
            $relCurrentViewDir = trim($project->getRelativeDir($absCurrentViewDir, $project->getSrcDir()), '/') ;

            $url = \yii\helpers\Url::to([
                'page/show-file',
                'projectName' => $project->name,
                'file' => $relCurrentViewDir . '/' . $param,
            ]);

            return $url;
        },
        'sample' => function ($what, $params = []) {
            return \common\components\HTWidget::sample($what, $params);
        },
        'mock' => function ($params = null) {
            return \common\components\HTWidget::mock($params);
        },
        'isMock' => function ($value) {
            return \common\components\HTWidget::isMock($value);
        },
        'mocksAvailable' => function () {
            return \common\components\HTWidget::mocksAvailable();
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
        },
        'ensureData' => function ($entryData, $requirements, $options = []) {
            return \common\components\HTWidget::ensureData($entryData, $requirements, $options);
        },
        'orMock' => function ($entryData, $requirements, $options = []) {
            return \common\components\HTWidget::ensureData($entryData, $requirements, $options);
        },
        'php' => function ($file) {
            return \common\components\HTWidget::php($file);
        }
    ],
    'filters' => [
        'json' => function ($value) {
            return \yii\helpers\Json::encode($value);
        }
    ],
];