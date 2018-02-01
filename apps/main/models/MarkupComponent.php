<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:04
 */

namespace main\models;

use Yii;
use yii\base\Model;

class MarkupComponent extends Model
{
    public $sid;
    public $parent;
    public $params = [];
    public $onPreviews = [];
    /** @var DataConfig */
    public $dataConfig;
    public $name;
    public $type = 'markup';

    public function getIsExistOnPagePreview($page, $preview) {
        foreach ($this->onPreviews as $sid => $onPreview) {
            $onThisPage = is_array($onPreview['page'])
                ? in_array($page->sid, $onPreview['page'])
                : ($onPreview['page'] == '*' || $onPreview['page'] == $page->sid);
            if (!$onThisPage) {
                continue;
            }

            if ($onPreview['preview'] != '*' && $onPreview['preview'] != $preview->sid) {
                continue;
            }

            return true;
        }
    }

    public function getAppearanceForPagePreview($page, $preview) {
        $appearances = [];
        foreach ($this->onPreviews as $sid => $onPreview) {
            $onThisPage = is_array($onPreview['page'])
                ? in_array($page->sid, $onPreview['page'])
                : ($onPreview['page'] == '*' || $onPreview['page'] == $page->sid);
            if (!$onThisPage) continue;

            if ($onPreview['preview'] != '*' && $onPreview['preview'] != $preview->sid) continue;

            foreach ($onPreview['appearance'] as $appearanceSid => $appearanceConfig) {
                $appearance = new MarkupComponentAppearance($appearanceConfig);
                $appearance->sid = $appearanceSid;
                $appearances[] = $appearance;
            }
        }
        return $appearances;
    }

    public function getParentComponent() {
        if (!$this->parent) return null;
        return $this->dataConfig->getComponent($this->parent);
    }

    public function getHasDocument($name) {
        $path = implode('/', explode('.', $this->sid));
        $templatePath = '@data/config/components/' . $path . '/' . $name . '.md.php';
        return file_exists(Yii::getAlias($templatePath));
    }

    public function getReadme() {
        $path = implode('/', explode('.', $this->sid));
        $templatePath = '@data/config/components/' . $path . '/README.md.php';
        if (!file_exists(Yii::getAlias($templatePath))) return null;

        $prerender = Yii::$app->view->render(
            $templatePath,
            [
                'component' => $this,
            ]
        );
        return \yii\helpers\Markdown::process($prerender, 'gfm');
    }

    public function getTldr() {
        $path = implode('/', explode('.', $this->sid));
        $templatePath = '@data/config/components/' . $path . '/TLDR.md.php';
        if (!file_exists(Yii::getAlias($templatePath))) return null;

        $prerender = Yii::$app->view->render(
            $templatePath,
            [
                'component' => $this,
            ]
        );
        return \yii\helpers\Markdown::process($prerender, 'gfm');
    }

    public function explain($component) {
        $path = implode('/', explode('.', $this->sid));
        $templatePath = '@data/config/components/' . $path . '/explain.md.php';
        if (!file_exists(Yii::getAlias($templatePath))) return null;

        $prerender = Yii::$app->view->render(
            $templatePath,
            [
                'baseComponent' => $this,
                'targetComponent' => $component
            ]
        );
        return \yii\helpers\Markdown::process($prerender, 'gfm');
    }

}