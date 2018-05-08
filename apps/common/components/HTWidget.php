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

        $context['class'] = $projectName . '-' . $widgetName;

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

    public static function ensureData($entryData, $requirements, $options = []) {
        if (!Env::isDebug()) return $entryData;

        if (is_array($entryData) || $entryData === null) $entryData = new MockModel($entryData);
        $entryData->fillIfEmpty($requirements);
        if (isset($options['strict'])) $entryData->toggleStrict($options['strict']);
        return $entryData;

    }

}