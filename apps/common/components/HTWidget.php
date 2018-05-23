<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 12.04.18
 * Time: 20:10
 */

namespace common\components;


use common\components\htwidget\MockModel;
use common\components\htwidget\SampleModel;
use cronfy\env\Env;
use Yii;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;

class HTWidget
{
    static $currentWidget;
    public $defaultProject;
    public $mode = 'project';

    public static function requestGet($name, $default) {
        if (!Env::isDebug()) throw new \Exception("Request parameters can not be used in production");
        if (static::$currentWidget) throw new \Exception("Request parameters can not be used in widgets");
        // deprecated, requires refactoring in HTML Template twig.php::'widget'
        if (Yii::$app->view->params['html-template.widget.stack']) throw new \Exception("Request parameters can not be used in widgets");
        return Yii::$app->request->get($name, $default);
    }

    public static function jsExpression($data) {
        return new JsExpression($data);
    }

    public static function url($param) {
        if (is_array($param)) {
            return Url::to($param); // в верстке будет #, в боевом проекте - прокси для Url::to()
        }

        $projectName = 'als2';

        if (!static::$currentWidget) {
            throw new \Exception("No current widget");
        } else {
            $widgetName = static::$currentWidget;
        }

        return "/ui/ht/$projectName/widgets/$widgetName/$param";
    }

    protected function getCurrentViewProject() {
        $level = 0;
        $projectSrcDir = null;
        while (($dir = dirname(Yii::$app->view->getViewFile())) !== '/') {
            if (++$level > 20) throw new \Exception("nesting too deep");
            if (basename($dir) == 'src' && (is_dir($dir . '/pages') || is_dir($dir . '/widgets'))) {
                $projectSrcDir = dirname($dir);
                break;
            }
        }

        return basename($projectSrcDir);
    }

    public static function requireWidget($widgetName) {
        if (Yii::$app->HTWidget->mode == 'project') {
            $projectName = 'als2';

            $jsFileRelative = "ui/ht/$projectName/widgets/$widgetName/$widgetName.js";
            $jsFile = Yii::getAlias('@webroot/' . $jsFileRelative);
            if (file_exists($jsFile)) {
                $time = filemtime($jsFile);
                Yii::$app->view->registerJsFile(
                    '/' . $jsFileRelative . '?' . $time,
                    ['depends' => JqueryAsset::class],
                    $widgetName . '|js'
                );
            }
        } else {
            /** @var \main\models\Project $project */
            $project = Yii::$app->view->params['html-template.project.current'];

            $file = $project->getWidgetView($widgetName);

            $jsFile = dirname($file) . "/$widgetName.js";
            if (file_exists($jsFile)) {
                Yii::$app->view->registerJsFile(\yii\helpers\Url::to(['page/show-widget-asset',
                    'projectName' => $project->name,
                    'widgetName' => $widgetName, 'asset' => basename($jsFile)
                ]), ['depends' => \main\assets\ProjectPageAsset::class], $widgetName . '|js');
            }

            $cssFile = dirname($file) . "/$widgetName.css";
            if (file_exists($cssFile)) {
                Yii::$app->view->registerCssFile(\yii\helpers\Url::to(['page/show-widget-asset',
                    'projectName' => $project->name,
                    'widgetName' => $widgetName, 'asset' => basename($cssFile)
                ]), ['depends' => \main\assets\ProjectPageAsset::class], $widgetName . '|css');
            }
        }
    }

    /**
     * @deprecated нужно все переделать на render(), но здесь много логики Html Template,
     * где мы работаем не просто с файликами, а с объектом Project. Это нужно отрефакторить
     * при переделке Html Template на модуль yii.
     * @return string
     * @throws \Exception
     */
    public static function renderDeprecated($widgetName) {
        if (Yii::$app->HTWidget->mode == 'project') {
            return call_user_func_array([static::class, 'render'], func_get_args());
        }

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
                $context['arg'] = current($args);
                break;
            default:
                throw new \Exception("Too many widget arguments");
        }

        // класс часто передается в виджет для указания вариантов отображения,
        // а вот основной класс виджета обычно указывается руками.
        // Чтобы в виджете получить его класс (чтобы руками не прописывать),
        // нужно придумать еще что-то.
//        $context['class'] = $project->name . '-' . $widgetName;

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
            ]), ['depends' => \main\assets\ProjectPageAsset::class], $widgetName . '|js');
        }

        $cssFile = dirname($file) . "/$widgetName.css";
        if (file_exists($cssFile)) {
            Yii::$app->view->registerCssFile(\yii\helpers\Url::to(['page/show-widget-asset',
                'projectName' => $project->name,
                'widgetName' => $widgetName, 'asset' => basename($cssFile)
            ]), ['depends' => \main\assets\ProjectPageAsset::class], $widgetName . '|css');
        }

        // чтобы нельзя было в шаблонах верстки обращаться к yii.
        // Интерфейс шаблонов должен быть ограничен только HTWidget
        $context['app'] = null;
        // renderFile, потому что там не @alias, а абсолютный путь
        $result = Yii::$app->view->renderFile($file, $context);

        array_pop(Yii::$app->view->params['html-template.widget.stack']);

        return $result;
    }

    public static function render($widgetName, $params = []) {
        $projectName = 'als2';

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

//        $context['class'] = $projectName . '-' . $widgetName;

        $file = Yii::getAlias("@root/ui/$projectName/widgets/$widgetName/$widgetName.html.twig");

        $previousWidget = static::$currentWidget;
        static::$currentWidget = $widgetName;

        $jsFileRelative = "ui/ht/$projectName/widgets/$widgetName/$widgetName.js";
        $jsFile = Yii::getAlias('@webroot/' . $jsFileRelative);
        if (file_exists($jsFile)) {
            $time = filemtime($jsFile);
            Yii::$app->view->registerJsFile('/' . $jsFileRelative . '?' . $time, ['depends' => JqueryAsset::class]);
        }

        // renderFile, потому что там не @alias, а абсолютный путь
        $result = Yii::$app->view->renderFile($file, $context);

        static::$currentWidget = $previousWidget;

        return $result;
    }

    public static function sample($what, $params = []) {
        if (!Env::isDebug()) throw new \Exception("No sample models in production");

        switch ($what) {
            case 'pagination':
                $pagination = new \yii\data\Pagination([
                        'totalCount' => @$params['totalCount'] ?: 1000,
                        'page' => @$params['page'] ?: 5,
                        'defaultPageSize' => 500,
                    ]
                );
                return $pagination;
            case 'model':
                $model = new SampleModel($params);
                return $model;
            default:
                throw new \Exception("Unknown sample name");

        }
    }

    public static function isMock($value) {
        return $value instanceof MockModel;
    }

    public static function mock($params = null) {
        if (!Env::isDebug()) throw new \Exception("No mocks in production");

        switch (true) {
            case is_scalar($params):
                // предполагается, что можно вызвать mock('pagination'), и он вернет
                // нужный набор данных, пока не реализовано
                throw new \Exception("scalar mocks not implemented");
            case is_array($params) || $params === null:
                $model = new MockModel($params ?: []);
                return $model;
                break;
            default:
                throw new \Exception("Unknown mock type");

        }
    }

    protected static function isAssoc(array $arr)
    {
        if ([] === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


    public static function ensureData($entryData, $requirements, $options = []) {
        if (!Env::isDebug()) return $entryData;

        // ensureData($var, 'some string or number')
        if (is_scalar($requirements)) return $entryData ?: $requirements;

//        if (@$options['debug']) D($requirements);

        // array (non-associative)
        if (!static::isAssoc($requirements)) return $entryData ?: $requirements;

        // ensureData($var, ['array of' => 'values'])
        if (is_array($entryData) || $entryData === null) $entryData = new MockModel($entryData);
        $entryData->fillIfEmpty($requirements);
        if (isset($options['strict'])) $entryData->toggleStrict($options['strict']);

        return $entryData;
    }

    public static function php($file) {
        $dir = dirname(Yii::$app->view->getViewFile());
        $file = basename($file);
        $value = require($dir . '/'. $file);
        return $value;
    }

}