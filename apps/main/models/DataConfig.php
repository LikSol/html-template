<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:11
 */

namespace main\models;

use yii\base\Model;

class DataConfig extends Model
{
    public $raw = [];

    protected $_pages = [];
    public function getPage($sid) {
        if (!isset($this->_pages[$sid])) {
            $page = new MarkupPage($this->raw['pages'][$sid]);
            $page->sid = $sid;
            $this->_pages[$sid] = $page;
        }
        return $this->_pages[$sid];
    }

    public function getPages() {
        $pages = [];
        foreach (array_keys($this->raw['pages']) as $pageSid) {
            $pages[] = $this->getPage($pageSid);
        }
        return $pages;
    }

    protected $_components;

    /**
     * @return MarkupComponent[]
     */
    protected function getComponents() {
        if (!$this->_components) {
            $components = [];
            foreach ($this->raw['components'] as $sid => $component) {
                $component = new MarkupComponent($component);
                $component->sid = $sid;
                $component->dataConfig = $this;
                $components[$sid] = $component;
            }
            $this->_components = $components;
        }

        return $this->_components;
    }

    public function getComponent($sid) {
        return $this->getComponents()[$sid];
    }

    public function getComponentsForPagePreview($page, $preview) {
        $components = [];
        foreach ($this->getComponents() as $component) {
            $appearance = $component->getAppearanceForPagePreview($page, $preview);
            if (!$appearance) continue;
            $components[] = $component;
        }

        return $components;
    }
}