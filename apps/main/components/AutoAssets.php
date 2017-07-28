<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 27.07.17
 * Time: 10:58
 */

namespace main\components;

use yii\base\Object;
use Yii;

class AutoAssets extends Object
{
    public $page;

    protected $_css;
    protected $_js;

    const MODE_PAGE = 1;
    const MODE_LAYOUT = 2;

    public $mode;

    public function addCss($css) {
        if (!preg_match('#^https://#', $css)) {
            // см. docs/other-rules.md
            throw new \Exception("Only CDN assets allowed, but not absolute url provided: " . $css);
        }
        if (!preg_match('#\.css$#', $css)) {
            throw new \Exception($css . "is not a CSS file.");
        }
        $this->_css[$this->mode][] = trim($css, '/');
    }

    public function addJs($js) {
        if (!preg_match('#^https://#', $js)) {
            // см. docs/other-rules.md
            throw new \Exception("Only CDN assets allowed, but not absolute url provided: " . $js);
        }
        if (!preg_match('#\.css$#', $js)) {
            throw new \Exception($js . "is not a JS file.");
        }
        $this->_js[$this->mode][] = trim($js, '/');
    }

    public function getCss() {
        $css = $this->_css;

        // см. docs/other-rules.md
        // 1. Библиотеки layout.
        // 2. Компоненты - в одном файле.
        // 3. Стили layout - в одном файле.
        // 4. Библиотеки страницы.
        // 5. Стили страницы - в одном файле.

        $result = [];

        // Библиотеки layout
        if (isset($css[static::MODE_LAYOUT])) {
            $result = array_merge($result, $css[static::MODE_LAYOUT]);
        }

        // Компоненты
        $result[] = 'css/components.css';

        // Стили layout
        $result[] = 'css/layout.css';

        // Библиотеки страницы
        if (isset($css[static::MODE_PAGE])) {
            $result = array_merge($result, $css[static::MODE_PAGE]);
        }

        // Стили страницы
        $default = '/css/' . $this->page . '.css';
        if (file_exists(Yii::getAlias('@webroot') . $default)) {
            $result[] = $default;
        }

        $result = array_unique($result);

        return $result;
    }

    public function getJs() {
        $js = $this->_js;

        // см. docs/other-rules.md
        // 1. Библиотеки layout.
        // 2. JS layout - в одном файле.
        // 3. Библиотеки страницы.
        // 4. JS страницы - в одном файле.

        $result = [];

        // Библиотеки layout
        if (isset($js[static::MODE_LAYOUT])) {
            $result = array_merge($result, $js[static::MODE_LAYOUT]);
        }

        // JS layout
        $result[] = 'js/layout.js';

        // Библиотеки страницы
        if (isset($js[static::MODE_PAGE])) {
            $result = array_merge($result, $js[static::MODE_PAGE]);
        }

        // JS страницы
        $default = '/js/' . $this->page . '.js';
        if (file_exists(Yii::getAlias('@webroot') . $default)) {
            $result[] = $default;
        }

        $result = array_unique($result);

        return $result;
    }

}