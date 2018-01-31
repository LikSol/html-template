<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 31.01.18
 * Time: 14:42
 */

namespace main\models;

use yii\base\Model;

class MarkupComponentAppearance extends Model
{
    public $x, $y, $x1, $y1, $width, $height;
    public $sid;
    public $show = 'auto';
}