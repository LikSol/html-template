<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 15:33
 */

namespace main\models;

use Yii;
use yii\base\Model;

class MarkupComponentType extends Model
{
    public $sid;
    /**
     * @var MarkupComponent
     */
    public $component;

    public function getDesc() {
        $prerender = Yii::$app->view->render(
            '@main/models/MarkdownComponentType/docs/' . $this->sid . '.md.php',
            [
                'componentType' => $this,
            ]
        );
        return \yii\helpers\Markdown::process($prerender, 'gfm');
    }
}